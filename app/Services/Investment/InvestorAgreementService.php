<?php

namespace App\Services\Investment;

use App\Models\Investment;
use App\Repositories\Investment\InvestorAgreementRepository;
use App\Repositories\Investment\InvestorRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class InvestorAgreementService
{
    public function __construct(
        protected InvestorAgreementRepository $InvAgreementRepo,
        protected InvestorRepository $investorRepo,
        protected InvestmentContractDocumentService $investmentContractDocumentService,
    ) {}

    private function validate(array $data, $id = null)
    {
        $validator = Validator::make($data, [
            'investor_agreement_type_id' => 'required',
            'version_no' => [
                'required',
                Rule::unique('investor_agreement_templates')
                    ->where(function ($query) use ($data) {
                        return $query->where(
                            'investor_agreement_type_id',
                            $data['investor_agreement_type_id']
                        );
                    })
                    ->ignore($id)
            ],
            'effective_from' => 'required',
            'is_active' => 'required'
        ], [
            'version_no.unique' => 'This version already exists for the selected document type.'
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {

            $this->validate($data);
            $data['added_by'] = auth()->user()->id;

            // novation to all active investments
            $this->novationOfAllExistingInvestors($data);

            return $this->InvAgreementRepo->create($data);
        });
    }

    public function update($id, array $data)
    {
        $this->validate($data, $id);
        $data['updated_by'] = auth()->user()->id;

        return $this->InvAgreementRepo->update($id, $data);
    }

    public function getDataTable(array $filters = [])
    {
        $query = $this->InvAgreementRepo->getQuery($filters);

        $columns = [
            ['data' => 'DT_RowIndex', 'name' => 'iddd', 'title' => '#'],
            ['data' => 'agreement_type', 'name' => 'agreement_type', 'title' => 'Type'],
            ['data' => 'version_no', 'name' => 'version_no', 'title' => 'Version No'],
            ['data' => 'effective_from', 'name' => 'effective_from', 'title' => 'Effective From'],
            ['data' => 'is_active', 'name' => 'is_active', 'title' => 'Status'],
            ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false],
        ];
        return datatables()
            ->of($query)
            ->addIndexColumn()
            ->addColumn('agreement_type', function ($row) {
                return $row->agreementType->investor_agreement_type ?? '-';
            })
            ->addColumn('version_no', function ($row) {
                return 'V' . $row->version_no ?? '-';
            })
            ->addColumn('effective_from', function ($row) {
                return $row->effective_from;
            })
            ->addColumn('is_active', function ($row) {
                return $row->is_active;
            })

            ->addColumn('action', function ($row) {
                $action = '<div class="d-flex flex-column flex-md-row ">';
                if (Gate::allows('investor_legal_documents.edit')) {
                    $action .= '<a href="' . route('legal_template.edit', $row->id) . '" class="btn btn-info btn-sm mb-1 mr-md-1" >Edit</a>';
                }

                if (Gate::allows('investor_legal_documents.view')) {
                    $action .= '<a href="' . route('legal_template.show', $row->id) . '" class="btn btn-primary btn-sm mb-1 mr-md-1" >View</a>';
                }
                $action .= '</div>';

                return $action;
            })
            ->rawColumns(['is_active', 'action'])
            ->with(['columns' => $columns])
            ->toJson();
    }

    public function getById($id)
    {
        return $this->InvAgreementRepo->findById($id);
    }

    public function getActiveIdBytype($tdocTpeId)
    {
        // dd($tdocTpeId);
        return $this->InvAgreementRepo->getActiveIdBytype($tdocTpeId);
    }

    // New Mudarabah/version workflow
    public function novationOfAllExistingInvestors($data)
    {
        $docExist = $this->InvAgreementRepo->findByType($data['investor_agreement_type_id']);

        if ($docExist && $docExist->version_no < $data['version_no']) {

            $investments = Investment::activeLongTerm()->get();

            // Investor => Company IDs
            $grouped = $investments
                ->groupBy('investor_id')
                ->map(function ($items) {
                    return $items->pluck('company_id')->unique();
                });

            // Investor => Company => Investment IDs
            $investmentIds = $investments
                ->groupBy('investor_id')
                ->map(function ($investorItems) {
                    return $investorItems
                        ->groupBy('company_id')
                        ->map(function ($companyItems) {
                            return $companyItems->pluck('id')->values()->toArray();
                        })
                        ->toArray();
                })
                ->toArray();

            $activeInvestorIds = $this->investorRepo
                ->allActive()
                ->pluck('id')
                ->toArray();

            foreach ($activeInvestorIds as $investorId) {

                if (!isset($grouped[$investorId])) {
                    continue;
                }

                foreach ($grouped[$investorId] as $companyId) {

                    $docInsertData = [
                        'investment_id' => 0,
                        'applied_investments' => json_encode($investmentIds[$investorId][$companyId] ?? []),
                        'investor_id' => $investorId,
                    ];

                    $this->investmentContractDocumentService
                        ->createInvestorDocument($investorId, $companyId, $docInsertData);
                }
            }

            $this->InvAgreementRepo->update($docExist->id, [
                'is_active' => 0,
                'updated_by' => auth()->id(),
            ]);
        }
    }

    // Manual investor-wise workflow
    public function novationOfSelectedInvestorInvestments(
        int $investorId,
        array $selectedInvestmentIds
    ): void {
        $selectedInvestmentIds = array_values(
            array_unique(array_map('intval', $selectedInvestmentIds))
        );

        if (empty($selectedInvestmentIds)) {
            throw ValidationException::withMessages([
                'investment_ids' => 'Please select at least one investment.',
            ]);
        }

        /*
        * Only load active investments that:
        * 1. Belong to the selected investor
        * 2. Were explicitly selected
        */
        $investments = Investment::query()
            ->where('investor_id', $investorId)
            ->where('investment_status', 1)
            ->whereIn('id', $selectedInvestmentIds)
            ->get();

        /*
        * Prevent invalid, inactive, or another investor's investment
        * from being silently ignored.
        */
        $validInvestmentIds = $investments
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->all();

        $invalidInvestmentIds = array_values(
            array_diff($selectedInvestmentIds, $validInvestmentIds)
        );

        if (!empty($invalidInvestmentIds)) {
            throw ValidationException::withMessages([
                'investment_ids' => sprintf(
                    'These investments are invalid, inactive, or do not belong to the investor: %s',
                    implode(', ', $invalidInvestmentIds)
                ),
            ]);
        }

        DB::transaction(function () use ($investorId, $investments) {
            /*
            * One document is created per company because an investor's
            * selected investments may belong to different companies.
            */
            foreach ($investments->groupBy('company_id') as $companyId => $companyInvestments) {
                $docInsertData = [
                    'investment_id' => 0,
                    'investor_id' => $investorId,
                    'applied_investments' => json_encode(
                        $companyInvestments
                            ->pluck('id')
                            ->values()
                            ->all()
                    ),
                ];
                // dump($docInsertData);


                // document Creation is handled by the InvestmentContractDocumentService
                $document = $this->investmentContractDocumentService
                    ->createInvestorDocument(
                        $investorId,
                        (int) $companyId,
                        $docInsertData
                    );

                if (!$document || !$document->generated_date) {
                    throw new \RuntimeException(
                        "Novation document creation failed for company {$companyId}."
                    );
                }

                /*
                * Every investment receives its own maturity date and
                * future profit records, using the same novation date.
                */
                foreach ($companyInvestments as $investment) {
                    $this->refreshProfitRecordsAfterNovation(
                        $investment,
                        $document->generated_date
                    );
                }
            }
        });
    }

    public function refreshProfitRecordsAfterNovation(
        Investment $investment,
        $novationDate
    ): void {
        $novationDate = Carbon::parse($novationDate);

        $newMaturityDate = $novationDate->copy()->addMonths((int) $investment->investment_tenure);

        $this->refreshFutureProfitRecords(
            $investment,
            $novationDate,
            $newMaturityDate
        );

        $investment->update([
            'maturity_date' => $newMaturityDate->toDateString(),
        ]);
    }

    private function refreshFutureProfitRecords(
        Investment $investment,
        Carbon $effectiveDate,
        Carbon $maturityDate
    ): void {
        $effectiveDate = $effectiveDate->copy()->startOfDay();
        $maturityDate  = $maturityDate->copy()->endOfDay();

        /*
     * Generate a maximum of one year, or the investment tenure
     * when the tenure is shorter than one year.
     */
        $scheduleMonths = min(
            12,
            (int) $investment->investment_tenure
        );

        $scheduleEnd = $effectiveDate->copy()
            ->addMonths($scheduleMonths);

        if ($scheduleEnd->greaterThan($maturityDate)) {
            $scheduleEnd = $maturityDate->copy();
        }

        /*
     * Take the latest existing profit record for each calendar month.
     * Its profit date and amount become the template for that month.
     */
        $profitTemplates = $investment->profitRecords()
            ->whereDate(
                'profit_release_month',
                '<=',
                $effectiveDate->toDateString()
            )
            ->orderByDesc('profit_release_month')
            ->get()
            ->unique(function ($record) {
                return Carbon::parse(
                    $record->profit_release_month
                )->format('m');
            })
            ->keyBy(function ($record) {
                return Carbon::parse(
                    $record->profit_release_month
                )->format('m');
            });

        if ($profitTemplates->isEmpty()) {
            return;
        }

        $currentMonth = $effectiveDate->copy()->startOfMonth();

        while ($currentMonth->lessThanOrEqualTo($scheduleEnd)) {
            $monthNumber = $currentMonth->format('m');

            if (!$profitTemplates->has($monthNumber)) {
                $currentMonth->addMonth();
                continue;
            }

            $template = $profitTemplates->get($monthNumber);

            /*
         * Preserve the original profit release day.
         *
         * Example:
         * Template date: 16-07-2026
         * New date:      16-07-2027
         */
            $templateDate = Carbon::parse(
                $template->profit_release_month
            );

            $profitDay = min(
                $templateDate->day,
                $currentMonth->daysInMonth
            );

            $profitDate = $currentMonth->copy()
                ->day($profitDay)
                ->startOfDay();

            if (
                $profitDate->greaterThan($effectiveDate) &&
                $profitDate->lessThanOrEqualTo($scheduleEnd) &&
                $profitDate->lessThanOrEqualTo($maturityDate)
            ) {
                /*
             * Find by year and month, not exact date.
             *
             * This finds an incorrectly created 01-07-2027 record
             * and updates it to 16-07-2027.
             */
                $existingRecord = $investment->profitRecords()
                    ->whereYear(
                        'profit_release_month',
                        $profitDate->year
                    )
                    ->whereMonth(
                        'profit_release_month',
                        $profitDate->month
                    )
                    ->first();

                $recordData = [
                    'profit_release_month'  => $profitDate,
                    'profit_amount'         => $template->profit_amount,
                    'investor_id'           => $investment->investor_id,
                    'released_total_amount' => 0,
                    'has_profit_amount'     => 1,
                ];

                if ($existingRecord) {
                    $existingRecord->update($recordData);
                } else {
                    $investment->profitRecords()->create($recordData);
                }
            }

            $currentMonth->addMonth();
        }
    }

    public function autoRenewInvestment(
        Investment $investment
    ): Investment {
        return DB::transaction(function () use ($investment) {
            $investment = Investment::query()
                ->activeLongTerm()
                ->lockForUpdate()
                ->findOrFail($investment->id);

            $oldMaturityDate = Carbon::parse(
                $investment->maturity_date
            )->endOfDay();

            /*
            * Process only:
            * - overdue investments, or
            * - investments maturing within the next seven days.
            *
            * Anything more than seven days away is ignored.
            */
            $renewalThreshold = now()
                ->addWeek()
                ->endOfDay();

            if ($oldMaturityDate->greaterThan($renewalThreshold)) {
                return $investment;
            }

            /*
            * Do not process the same maturity period twice.
            */
            if (
                $investment->last_renewed_maturity_date &&
                Carbon::parse($investment->last_renewed_maturity_date)
                ->isSameDay($oldMaturityDate)
            ) {
                return $investment;
            }

            $newMaturityDate = $oldMaturityDate->copy()
                ->addMonths(
                    (int) $investment->investment_tenure
                );

            /*
            * Generate missing profit records from the old maturity date
            * until the renewed maturity/schedule end.
            */
            // $this->refreshFutureProfitRecords(
            //     $investment,
            //     $oldMaturityDate,
            //     $newMaturityDate
            // );
            $lastProfitRecord = $investment->profitRecords()
                ->orderByDesc('profit_release_month')
                ->first();

            if ($lastProfitRecord) {
                $lastProfitDate = Carbon::parse(
                    $lastProfitRecord->profit_release_month
                )->startOfDay();

                $this->refreshFutureProfitRecords(
                    $investment,
                    $lastProfitDate,
                    $newMaturityDate
                );
            }

            $investment->update([
                'maturity_date' => $newMaturityDate->toDateString(),
                'last_renewed_maturity_date' => $oldMaturityDate->toDateString(),
                'renewed_at' => now(),
            ]);

            return $investment->refresh();
        });
    }

    // Run auto-renewal one week before maturity
    public function processUpcomingAutoRenewals(): void
    {
        $renewalThreshold = now()->addWeek()->endOfDay();

        Investment::query()
            ->activeLongTerm()
            ->whereDate(
                'maturity_date',
                '<=',
                $renewalThreshold->toDateString()
            )
            ->chunkById(100, function ($investments) {
                foreach ($investments as $investment) {
                    // dump($investment->id . ' - ' . $investment->maturity_date);
                    $this->autoRenewInvestment($investment);
                }
            });
    }
}
