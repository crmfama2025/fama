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
use Log;
use Throwable;

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
    // public function novationOfAllExistingInvestors($data)
    // {
    //     $docExist = $this->InvAgreementRepo->findByType($data['investor_agreement_type_id']);

    //     if ($docExist && $docExist->version_no < $data['version_no']) {

    //         $investments = Investment::activeLongTerm()->get();

    //         // Investor => Company IDs
    //         $grouped = $investments
    //             ->groupBy('investor_id')
    //             ->map(function ($items) {
    //                 return $items->pluck('company_id')->unique();
    //             });

    //         // Investor => Company => Investment IDs
    //         $investmentIds = $investments
    //             ->groupBy('investor_id')
    //             ->map(function ($investorItems) {
    //                 return $investorItems
    //                     ->groupBy('company_id')
    //                     ->map(function ($companyItems) {
    //                         return $companyItems->pluck('id')->values()->toArray();
    //                     })
    //                     ->toArray();
    //             })
    //             ->toArray();

    //         $activeInvestorIds = $this->investorRepo
    //             ->allActive()
    //             ->pluck('id')
    //             ->toArray();

    //         foreach ($activeInvestorIds as $investorId) {

    //             if (!isset($grouped[$investorId])) {
    //                 continue;
    //             }

    //             foreach ($grouped[$investorId] as $companyId) {

    //                 $docInsertData = [
    //                     'investment_id' => 0,
    //                     'applied_investments' => json_encode($investmentIds[$investorId][$companyId] ?? []),
    //                     'investor_id' => $investorId,
    //                 ];

    //                 $document = $this->investmentContractDocumentService
    //                     ->createInvestorDocument($investorId, $companyId, $docInsertData);

    //                 if (!$document || !$document->generated_date) {
    //                     throw new \RuntimeException(
    //                         "Novation document creation failed for company {$companyId}."
    //                     );
    //                 }

    //                 $companyInvestments = $investments
    //                     ->where('investor_id', $investorId)
    //                     ->where('company_id', $companyId);

    //                 foreach ($companyInvestments as $investment) {
    //                     $this->refreshProfitRecordsAfterNovation(
    //                         $investment,
    //                         $document->generated_date
    //                     );
    //                 }
    //             }
    //         }

    //         $this->InvAgreementRepo->update($docExist->id, [
    //             'is_active' => 0,
    //             'updated_by' => auth()->id(),
    //         ]);
    //     }
    // }

    /*
    |--------------------------------------------------------------------------
    | Global novation workflow
    |--------------------------------------------------------------------------
    */
    public function novationOfAllExistingInvestors(array $data): void
    {
        $docExist = $this->InvAgreementRepo->findByType($data['investor_agreement_type_id']);

        if (!$docExist || $docExist->version_no >= $data['version_no']) {
            return;
        }

        DB::transaction(function () use ($docExist) {
            $investments = Investment::query()
                ->activeLongTerm()
                ->lockForUpdate()
                ->get();

            if ($investments->isEmpty()) {
                return;
            }

            $activeInvestorIds = $this->investorRepo
                ->allActive()
                ->pluck('id')
                ->map(fn($id) => (int) $id)
                ->all();

            foreach (
                $investments->groupBy('investor_id')
                as $investorId => $investorInvestments
            ) {
                if (!in_array((int) $investorId, $activeInvestorIds, true)) {
                    continue;
                }

                foreach ($investorInvestments->groupBy('company_id') as $companyId => $companyInvestments) {
                    $docInsertData = [
                        'investment_id' => 0,
                        'investor_id' => (int) $investorId,
                        'applied_investments' => json_encode(
                            $companyInvestments
                                ->pluck('id')
                                ->values()
                                ->all()
                        ),
                    ];

                    $document = $this->investmentContractDocumentService
                        ->createInvestorDocument(
                            (int) $investorId,
                            (int) $companyId,
                            $docInsertData
                        );

                    if (!$document || !$document->generated_date) {
                        throw new \RuntimeException(
                            "Novation document creation failed for company {$companyId}."
                        );
                    }

                    foreach ($companyInvestments as $investment) {
                        $this->refreshProfitRecordsAfterNovation(
                            $investment,
                            $document->generated_date
                        );
                    }
                }
            }

            $this->InvAgreementRepo->update($docExist->id, [
                'is_active' => 0,
                'updated_by' => auth()->id(),
            ]);
        });
    }


    // Manual investor-wise workflow
    // public function novationOfSelectedInvestorInvestments(
    //     int $investorId,
    //     array $selectedInvestmentIds
    // ): void {
    //     $selectedInvestmentIds = array_values(
    //         array_unique(array_map('intval', $selectedInvestmentIds))
    //     );

    //     if (empty($selectedInvestmentIds)) {
    //         throw ValidationException::withMessages([
    //             'investment_ids' => 'Please select at least one investment.',
    //         ]);
    //     }

    //     /*
    //     * Only load active investments that:
    //     * 1. Belong to the selected investor
    //     * 2. Were explicitly selected
    //     */
    //     $investments = Investment::query()
    //         ->where('investor_id', $investorId)
    //         ->where('investment_status', 1)
    //         ->whereIn('id', $selectedInvestmentIds)
    //         ->get();

    //     /*
    //     * Prevent invalid, inactive, or another investor's investment
    //     * from being silently ignored.
    //     */
    //     $validInvestmentIds = $investments
    //         ->pluck('id')
    //         ->map(fn($id) => (int) $id)
    //         ->all();

    //     $invalidInvestmentIds = array_values(
    //         array_diff($selectedInvestmentIds, $validInvestmentIds)
    //     );

    //     if (!empty($invalidInvestmentIds)) {
    //         throw ValidationException::withMessages([
    //             'investment_ids' => sprintf(
    //                 'These investments are invalid, inactive, or do not belong to the investor: %s',
    //                 implode(', ', $invalidInvestmentIds)
    //             ),
    //         ]);
    //     }

    //     DB::transaction(function () use ($investorId, $investments) {
    //         /*
    //         * One document is created per company because an investor's
    //         * selected investments may belong to different companies.
    //         */
    //         foreach ($investments->groupBy('company_id') as $companyId => $companyInvestments) {
    //             $docInsertData = [
    //                 'investment_id' => 0,
    //                 'investor_id' => $investorId,
    //                 'applied_investments' => json_encode(
    //                     $companyInvestments
    //                         ->pluck('id')
    //                         ->values()
    //                         ->all()
    //                 ),
    //             ];
    //             // dump($docInsertData);


    //             // document Creation is handled by the InvestmentContractDocumentService
    //             $document = $this->investmentContractDocumentService
    //                 ->createInvestorDocument(
    //                     $investorId,
    //                     (int) $companyId,
    //                     $docInsertData
    //                 );

    //             if (!$document || !$document->generated_date) {
    //                 throw new \RuntimeException(
    //                     "Novation document creation failed for company {$companyId}."
    //                 );
    //             }

    //             /*
    //             * Every investment receives its own maturity date and
    //             * future profit records, using the same novation date.
    //             */
    //             foreach ($companyInvestments as $investment) {
    //                 $this->refreshProfitRecordsAfterNovation(
    //                     $investment,
    //                     $document->generated_date
    //                 );
    //             }
    //         }
    //     });
    // }

    /*
    |--------------------------------------------------------------------------
    | Manual novation workflow
    |--------------------------------------------------------------------------
    */

    public function novationOfSelectedInvestorInvestments(
        int $investorId,
        array $selectedInvestmentIds
    ): void {
        $selectedInvestmentIds = array_values(
            array_unique(
                array_filter(
                    array_map('intval', $selectedInvestmentIds),
                    fn(int $id) => $id > 0
                )
            )
        );

        if (empty($selectedInvestmentIds)) {
            throw ValidationException::withMessages([
                'investment_ids' => 'Please select at least one investment.',
            ]);
        }


        DB::transaction(function () use ($investorId, $selectedInvestmentIds) {
            $investments = Investment::query()
                ->activeLongTerm()
                ->where('investor_id', $investorId)
                ->whereIn('id', $selectedInvestmentIds)
                ->lockForUpdate()
                ->get();

            $validInvestmentIds = $investments
                ->pluck('id')
                ->map(fn($id) => (int) $id)
                ->all();

            $invalidInvestmentIds = array_values(
                array_diff(
                    $selectedInvestmentIds,
                    $validInvestmentIds
                )
            );

            if (!empty($invalidInvestmentIds)) {
                throw ValidationException::withMessages([
                    'investment_ids' => sprintf(
                        'These investments are invalid, inactive, terminated, short-term, or do not belong to the investor: %s',
                        implode(', ', $invalidInvestmentIds)
                    ),
                ]);
            }

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

                $document = $this->investmentContractDocumentService->createInvestorDocument(
                    $investorId,
                    (int) $companyId,
                    $docInsertData
                );

                if (!$document || !$document->generated_date) {
                    throw new \RuntimeException(
                        "Novation document creation failed for company {$companyId}."
                    );
                }

                foreach ($companyInvestments as $investment) {
                    $this->refreshProfitRecordsAfterNovation(
                        $investment,
                        $document->generated_date
                    );
                }
            }
        });
    }

    // public function refreshProfitRecordsAfterNovation(
    //     Investment $investment,
    //     $novationDate
    // ): void {
    //     $novationDate = Carbon::parse($novationDate);

    //     $newMaturityDate = $novationDate->copy()->addMonths((int) $investment->investment_tenure);

    //     $profitResult = $this->refreshFutureProfitRecords(
    //         $investment,
    //         $novationDate,
    //         $newMaturityDate
    //     );

    //     $oldMaturityDate = Carbon::parse($investment->maturity_date);

    //     $investment->update([
    //         'maturity_date' => $newMaturityDate->toDateString(),
    //         'investor_novation_applied_at' => $novationDate->toDateString(),
    //         'investor_novation_applied_by' => auth()->id()
    //     ]);

    //     DB::table('investment_profit_record_renewal_logs')->insert([
    //         'investment_id' => $investment->id,
    //         'investor_id' => $investment->investor_id,

    //         'old_maturity_date' => $oldMaturityDate->toDateString(),
    //         'new_maturity_date' => $newMaturityDate->toDateString(),

    //         'first_profit_date' => $profitResult['first_profit_date'],
    //         'last_profit_date' => $profitResult['last_profit_date'],
    //         'created_profit_records' => $profitResult['created_count'],
    //         'updated_profit_records' => $profitResult['updated_count'],

    //         'renewal_type' => 'novation',
    //         'processed_at' => now(),
    //         'created_at' => now(),
    //         'updated_at' => now(),
    //     ]);

    //     Log::info('Investment renewed successfully', [
    //         'investment_id' => $investment->id,
    //         'investment_code' => $investment->investment_code,
    //         'old_maturity_date' => $oldMaturityDate->toDateString(),
    //         'new_maturity_date' => $newMaturityDate->toDateString(),
    //         'first_profit_date' => $profitResult['first_profit_date'],
    //         'last_profit_date' => $profitResult['last_profit_date'],
    //         'created_profit_records' => $profitResult['created_count'],
    //         'updated_profit_records' => $profitResult['updated_count'],
    //     ]);
    // }

    /*
    |--------------------------------------------------------------------------
    | Refresh investment after novation
    |--------------------------------------------------------------------------
    */

    public function refreshProfitRecordsAfterNovation(Investment $investment, $novationDate): void
    {
        $novationDate = Carbon::parse($novationDate)->startOfDay();

        $tenureMonths = (int) $investment->investment_tenure;

        if ($tenureMonths <= 0) {
            throw new \RuntimeException(
                "Invalid tenure for investment {$investment->investment_code}."
            );
        }

        $oldMaturityDate = Carbon::parse($investment->maturity_date)->endOfDay();

        $newMaturityDate = $novationDate
            ->copy()
            ->addMonthsNoOverflow($tenureMonths)
            ->endOfDay();

        /*
        * Novation generates the new schedule from the novation date.
        * The payout months and payout day still come from the previous
        * profit records.
        */
        $profitResult = $this->refreshFutureProfitRecords(
            $investment,
            $novationDate,
            $newMaturityDate
        );

        $investment->update([
            'maturity_date' => $newMaturityDate->toDateString(),
            'investor_novation_applied_at' =>
            $novationDate->toDateString(),
            'investor_novation_applied_by' => auth()->id(),
        ]);

        $this->createProfitRenewalLog(
            investment: $investment,
            oldMaturityDate: $oldMaturityDate,
            newMaturityDate: $newMaturityDate,
            profitResult: $profitResult,
            renewalType: 'novation'
        );

        Log::info('Investment novation completed successfully', [
            'investment_id' => $investment->id,
            'investment_code' => $investment->investment_code,
            'old_maturity_date' => $oldMaturityDate->toDateString(),
            'new_maturity_date' => $newMaturityDate->toDateString(),
            'first_profit_date' => $profitResult['first_profit_date'],
            'last_profit_date' => $profitResult['last_profit_date'],
            'created_profit_records' => $profitResult['created_count'],
            'updated_profit_records' => $profitResult['updated_count'],
        ]);
    }

    // private function refreshFutureProfitRecords(
    //     Investment $investment,
    //     Carbon $effectiveDate,
    //     Carbon $maturityDate
    // ): array {
    //     $createdCount = 0;
    //     $updatedCount = 0;
    //     $processedDates = [];

    //     $effectiveDate = $effectiveDate->copy()->startOfDay();
    //     $maturityDate  = $maturityDate->copy()->endOfDay();

    //     /*
    //  * Generate a maximum of one year, or the investment tenure
    //  * when the tenure is shorter than one year.
    //  */
    //     $scheduleMonths = min(
    //         12,
    //         (int) $investment->investment_tenure
    //     );

    //     $scheduleEnd = $effectiveDate->copy()
    //         ->addMonths($scheduleMonths);

    //     if ($scheduleEnd->greaterThan($maturityDate)) {
    //         $scheduleEnd = $maturityDate->copy();
    //     }

    //     /*
    //  * Take the latest existing profit record for each calendar month.
    //  * Its profit date and amount become the template for that month.
    //  */
    //     $profitTemplates = $investment->profitRecords()
    //         ->whereDate(
    //             'profit_release_month',
    //             '<=',
    //             $effectiveDate->toDateString()
    //         )
    //         ->orderByDesc('profit_release_month')
    //         ->get()
    //         ->unique(function ($record) {
    //             return Carbon::parse(
    //                 $record->profit_release_month
    //             )->format('m');
    //         })
    //         ->keyBy(function ($record) {
    //             return Carbon::parse(
    //                 $record->profit_release_month
    //             )->format('m');
    //         });

    //     if ($profitTemplates->isEmpty()) {
    //         return [
    //             'first_profit_date' => null,
    //             'last_profit_date' => null,
    //             'created_count' => 0,
    //             'updated_count' => 0,
    //         ];
    //     }

    //     $currentMonth = $effectiveDate->copy()->startOfMonth();

    //     while ($currentMonth->lessThanOrEqualTo($scheduleEnd)) {
    //         $monthNumber = $currentMonth->format('m');

    //         if (!$profitTemplates->has($monthNumber)) {
    //             $currentMonth->addMonth();
    //             continue;
    //         }

    //         $template = $profitTemplates->get($monthNumber);

    //         /*
    //      * Preserve the original profit release day.
    //      *
    //      * Example:
    //      * Template date: 16-07-2026
    //      * New date:      16-07-2027
    //      */
    //         $templateDate = Carbon::parse(
    //             $template->profit_release_month
    //         );

    //         $profitDay = min(
    //             $templateDate->day,
    //             $currentMonth->daysInMonth
    //         );

    //         $profitDate = $currentMonth->copy()
    //             ->day($profitDay)
    //             ->startOfDay();

    //         if (
    //             $profitDate->greaterThan($effectiveDate) &&
    //             $profitDate->lessThanOrEqualTo($scheduleEnd) &&
    //             $profitDate->lessThanOrEqualTo($maturityDate)
    //         ) {
    //             /*
    //          * Find by year and month, not exact date.
    //          *
    //          * This finds an incorrectly created 01-07-2027 record
    //          * and updates it to 16-07-2027.
    //          */
    //             $existingRecord = $investment->profitRecords()
    //                 ->whereYear(
    //                     'profit_release_month',
    //                     $profitDate->year
    //                 )
    //                 ->whereMonth(
    //                     'profit_release_month',
    //                     $profitDate->month
    //                 )
    //                 ->first();

    //             $recordData = [
    //                 'profit_release_month'  => $profitDate,
    //                 'profit_amount'         => $template->profit_amount,
    //                 'investor_id'           => $investment->investor_id,
    //                 'released_total_amount' => 0,
    //                 'has_profit_amount'     => 1,
    //             ];

    //             if ($existingRecord) {
    //                 $existingRecord->update($recordData);
    //                 $updatedCount++;
    //             } else {
    //                 $investment->profitRecords()->create($recordData);
    //                 $createdCount++;
    //             }

    //             $processedDates[] = $profitDate->copy();
    //         }

    //         $currentMonth->addMonth();
    //     }

    //     $firstProcessedDate = !empty($processedDates) ? collect($processedDates)->sortBy(fn(Carbon $date) => $date->timestamp)->first() : null;
    //     $lastProcessedDate = !empty($processedDates) ? collect($processedDates)->sortByDesc(fn(Carbon $date) => $date->timestamp)->first() : null;

    //     return [
    //         'first_profit_date' => $firstProcessedDate?->toDateString(),
    //         'last_profit_date' => $lastProcessedDate?->toDateString(),
    //         'created_count' => $createdCount,
    //         'updated_count' => $updatedCount,
    //     ];
    // }

    /*
    |--------------------------------------------------------------------------
    | Generate future profit records
    |--------------------------------------------------------------------------
    */

    private function refreshFutureProfitRecords(
        Investment $investment,
        Carbon $effectiveDate,
        Carbon $maturityDate
    ): array {
        $createdCount = 0;
        $updatedCount = 0;
        $processedDates = [];

        $effectiveDate = $effectiveDate->copy()->startOfDay();
        $maturityDate = $maturityDate->copy()->endOfDay();

        $tenureMonths = (int) $investment->investment_tenure;

        if ($tenureMonths <= 0) {
            throw new \RuntimeException(
                "Invalid tenure for investment {$investment->investment_code}."
            );
        }

        /*
        * Create a maximum of one year's schedule. When the tenure is
        * shorter than one year, create records only for that tenure.
        */
        $scheduleMonths = min(12, $tenureMonths);

        $scheduleEnd = $effectiveDate
            ->copy()
            ->addMonthsNoOverflow($scheduleMonths)
            ->endOfDay();

        if ($scheduleEnd->greaterThan($maturityDate)) {
            $scheduleEnd = $maturityDate->copy();
        }

        /*
        * Use the latest previous profit record for each calendar month
        * as that month's recurring template.
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
            return $this->emptyProfitResult();
        }

        $currentMonth = $effectiveDate
            ->copy()
            ->startOfMonth();

        while ($currentMonth->lessThanOrEqualTo($scheduleEnd)) {
            $monthNumber = $currentMonth->format('m');

            if (!$profitTemplates->has($monthNumber)) {
                $currentMonth->addMonthNoOverflow();
                continue;
            }

            $template = $profitTemplates->get($monthNumber);

            $templateDate = Carbon::parse(
                $template->profit_release_month
            );

            /*
         * Preserve the payout day from the existing schedule.
         * For example, the 16th remains the 16th.
         */
            $profitDay = min($templateDate->day, $currentMonth->daysInMonth);

            $profitDate = $currentMonth->copy()->day($profitDay)->startOfDay();

            if (
                $profitDate->greaterThan($effectiveDate) &&
                $profitDate->lessThanOrEqualTo($scheduleEnd) &&
                $profitDate->lessThanOrEqualTo($maturityDate)
            ) {
                $existingRecord = $investment->profitRecords()
                    ->whereYear('profit_release_month', $profitDate->year)
                    ->whereMonth('profit_release_month', $profitDate->month)
                    ->lockForUpdate()
                    ->first();

                if ($existingRecord) {
                    $existingDate = Carbon::parse($existingRecord->profit_release_month)->startOfDay();

                    if ($existingDate->notEqualTo($profitDate)) {
                        if ((float) $existingRecord->released_total_amount > 0) {
                            throw new \RuntimeException(
                                "Cannot change the date of released profit record {$existingRecord->id}."
                            );
                        }

                        $existingRecord->update([
                            'profit_release_month' => $profitDate,
                        ]);

                        $updatedCount++;
                        $processedDates[] = $profitDate->copy();
                    }
                } else {
                    $investment->profitRecords()->create([
                        'profit_release_month' => $profitDate,
                        'profit_amount' => $template->profit_amount,
                        'investor_id' => $investment->investor_id,
                        'released_total_amount' => 0,
                        'has_profit_amount' => 1,
                    ]);

                    $createdCount++;
                    $processedDates[] = $profitDate->copy();
                }
            }

            $currentMonth->addMonthNoOverflow();
        }

        if (empty($processedDates)) {
            return $this->emptyProfitResult();
        }

        $processedDatesCollection = collect($processedDates)
            ->sortBy(fn(Carbon $date) => $date->timestamp)
            ->values();

        return [
            'first_profit_date' => $processedDatesCollection->first()->toDateString(),
            'last_profit_date' => $processedDatesCollection->last()->toDateString(),
            'created_count' => $createdCount,
            'updated_count' => $updatedCount,
        ];
    }

    private function emptyProfitResult(): array
    {
        return [
            'first_profit_date' => null,
            'last_profit_date' => null,
            'created_count' => 0,
            'updated_count' => 0,
        ];
    }


    // public function autoRenewInvestment(
    //     Investment $investment
    // ): Investment {
    //     return DB::transaction(function () use ($investment) {
    //         $investment = Investment::query()
    //             ->activeLongTerm()
    //             ->lockForUpdate()
    //             ->findOrFail($investment->id);

    //         $oldMaturityDate = Carbon::parse(
    //             $investment->maturity_date
    //         )->endOfDay();

    //         /*
    //         * Process only:
    //         * - overdue investments, or
    //         * - investments maturing within the next seven days.
    //         *
    //         * Anything more than seven days away is ignored.
    //         */
    //         $renewalThreshold = now()
    //             ->addWeek()
    //             ->endOfDay();

    //         if ($oldMaturityDate->greaterThan($renewalThreshold)) {
    //             return $investment;
    //         }

    //         /*
    //         * Do not process the same maturity period twice.
    //         */
    //         if (
    //             $investment->last_renewed_maturity_date &&
    //             Carbon::parse($investment->last_renewed_maturity_date)
    //             ->isSameDay($oldMaturityDate)
    //         ) {
    //             return $investment;
    //         }

    //         $newMaturityDate = $oldMaturityDate->copy()
    //             ->addMonths(
    //                 (int) $investment->investment_tenure
    //             );

    //         /*
    //         * Generate missing profit records from the old maturity date
    //         * until the renewed maturity/schedule end.
    //         */
    //         // $this->refreshFutureProfitRecords(
    //         //     $investment,
    //         //     $oldMaturityDate,
    //         //     $newMaturityDate
    //         // );
    //         $lastProfitRecord = $investment->profitRecords()
    //             ->orderByDesc('profit_release_month')
    //             ->first();
    //         $profitResult = [
    //             'first_profit_date' => null,
    //             'last_profit_date' => null,
    //             'created_count' => 0,
    //             'updated_count' => 0,
    //         ];

    //         if ($lastProfitRecord) {
    //             $lastProfitDate = Carbon::parse(
    //                 $lastProfitRecord->profit_release_month
    //             )->startOfDay();

    //             $profitResult = $this->refreshFutureProfitRecords(
    //                 $investment,
    //                 $lastProfitDate,
    //                 $newMaturityDate
    //             );
    //         }

    //         $investment->update([
    //             'maturity_date' => $newMaturityDate->toDateString(),
    //             'last_renewed_maturity_date' => $oldMaturityDate->toDateString(),
    //             'renewed_at' => now(),
    //         ]);

    //         DB::table('investment_profit_record_renewal_logs')->insert([
    //             'investment_id' => $investment->id,
    //             'investor_id' => $investment->investor_id,
    //             'old_maturity_date' => $oldMaturityDate->toDateString(),
    //             'new_maturity_date' => $newMaturityDate->toDateString(),

    //             'first_profit_date' => $profitResult['first_profit_date'],
    //             'last_profit_date' => $profitResult['last_profit_date'],
    //             'created_profit_records' => $profitResult['created_count'],
    //             'updated_profit_records' => $profitResult['updated_count'],

    //             'renewal_type' => 'automatic',
    //             'processed_at' => now(),
    //             'created_at' => now(),
    //             'updated_at' => now(),
    //         ]);

    //         Log::info('Investment renewed successfully', [
    //             'investment_id' => $investment->id,
    //             'investment_code' => $investment->investment_code,
    //             'old_maturity_date' => $oldMaturityDate->toDateString(),
    //             'new_maturity_date' => $newMaturityDate->toDateString(),
    //             'first_profit_date' => $profitResult['first_profit_date'],
    //             'last_profit_date' => $profitResult['last_profit_date'],
    //             'created_profit_records' => $profitResult['created_count'],
    //             'updated_profit_records' => $profitResult['updated_count'],
    //         ]);

    //         return $investment->refresh();
    //     });
    // }

    /*
    |--------------------------------------------------------------------------
    | Automatic renewal
    |--------------------------------------------------------------------------
    */

    public function autoRenewInvestment(Investment $investment): Investment
    {
        return DB::transaction(function () use ($investment) {
            $investment = Investment::query()
                ->activeLongTerm()
                ->lockForUpdate()
                ->findOrFail($investment->id);

            $tenureMonths = (int) $investment->investment_tenure;

            if ($tenureMonths <= 0) {
                throw new \RuntimeException(
                    "Invalid tenure for investment {$investment->investment_code}."
                );
            }

            $oldMaturityDate = Carbon::parse(
                $investment->maturity_date
            )->endOfDay();

            /*
         * Includes overdue investments and investments maturing
         * within the next seven days.
         */
            $renewalThreshold = now()
                ->addWeek()
                ->endOfDay();

            if ($oldMaturityDate->greaterThan($renewalThreshold)) {
                return $investment;
            }

            /*
         * A zero or null value means the investment has never been
         * automatically renewed.
         */
            if (
                !empty($investment->last_renewed_maturity_date) &&
                $investment->last_renewed_maturity_date !== '0000-00-00' &&
                Carbon::parse($investment->last_renewed_maturity_date)->isSameDay($oldMaturityDate)
            ) {
                return $investment;
            }

            $newMaturityDate = $oldMaturityDate
                ->copy()
                ->addMonthsNoOverflow($tenureMonths)
                ->endOfDay();

            /*
            * Use the latest profit date up to the old maturity date.
            * A record beyond maturity must not become the schedule start.
            */
            $lastProfitRecord = $investment->profitRecords()
                ->whereDate(
                    'profit_release_month',
                    '<=',
                    $oldMaturityDate->toDateString()
                )
                ->orderByDesc('profit_release_month')
                ->lockForUpdate()
                ->first();

            $profitResult = $this->emptyProfitResult();

            if ($lastProfitRecord) {
                $lastProfitDate = Carbon::parse($lastProfitRecord->profit_release_month)->startOfDay();

                /*
                * Starting from maturity skipped the first renewed profit
                * month. The schedule must continue after the last profit
                * record.
                */
                $profitResult = $this->refreshFutureProfitRecords(
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

            $this->createProfitRenewalLog(
                investment: $investment,
                oldMaturityDate: $oldMaturityDate,
                newMaturityDate: $newMaturityDate,
                profitResult: $profitResult,
                renewalType: 'automatic'
            );

            Log::info('Investment automatically renewed', [
                'investment_id' => $investment->id,
                'investment_code' => $investment->investment_code,
                'old_maturity_date' => $oldMaturityDate->toDateString(),
                'new_maturity_date' => $newMaturityDate->toDateString(),
                'first_profit_date' => $profitResult['first_profit_date'],
                'last_profit_date' => $profitResult['last_profit_date'],
                'created_profit_records' => $profitResult['created_count'],
                'updated_profit_records' => $profitResult['updated_count'],
            ]);

            return $investment->refresh();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Renewal audit log
    |--------------------------------------------------------------------------
    */

    private function createProfitRenewalLog(
        Investment $investment,
        Carbon $oldMaturityDate,
        Carbon $newMaturityDate,
        array $profitResult,
        string $renewalType
    ): void {
        DB::table('investment_profit_record_renewal_logs')
            ->insert([
                'investment_id' => $investment->id,
                'investor_id' => $investment->investor_id,

                'old_maturity_date' => $oldMaturityDate->toDateString(),
                'new_maturity_date' => $newMaturityDate->toDateString(),
                'first_profit_date' => $profitResult['first_profit_date'],
                'last_profit_date' => $profitResult['last_profit_date'],
                'created_profit_records' => $profitResult['created_count'],
                'updated_profit_records' => $profitResult['updated_count'],

                'renewal_type' => $renewalType,
                'processed_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Scheduled automatic-renewal processor
    |--------------------------------------------------------------------------
    */

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
                    try {
                        $this->autoRenewInvestment($investment);
                    } catch (Throwable $exception) {
                        report($exception);

                        Log::error(
                            'Investment automatic renewal failed',
                            [
                                'investment_id' => $investment->id,
                                'investment_code' => $investment->investment_code,
                                'maturity_date' => $investment->maturity_date,
                                'message' => $exception->getMessage(),
                            ]
                        );
                    }
                }
            });
    }
}
