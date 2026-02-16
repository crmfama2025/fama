<?php

namespace App\Services\Investment;

use App\Models\Company;
use App\Models\InvestmentReferral;
use App\Models\Investor;
use App\Models\PaymentTerms;
use App\Models\PayoutBatch;
use App\Models\ProfitInterval;
use App\Models\ReferralCommissionFrequency;
use App\Repositories\Investment\InvestmentDocumentRepository;
use App\Repositories\Investment\InvestmentRepository;
use App\Repositories\Investment\InvestorRepository;
use App\Services\BrevoService;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class InvestmentService
{
    public function __construct(
        protected InvestmentRepository $investmentRepository,
        protected InvestorRepository $investorRepository,
        protected InvestmentDocumentRepository $investmentDocumentRepository,
        protected InvestmentRepository $investmentReferralRepository,
        protected InvestmentDocumentService $investmentDocumentService,
        protected InvestmentReferralService $investmentReferralService,
        protected InvestmentReceivedPaymentService $investmentReceivedPaymentService,
        protected BrevoService $brevoService


    ) {}


    public function getAll()
    {
        return $this->investmentRepository->all();
    }

    public function getById($id)
    {
        return $this->investmentRepository->find($id);
    }

    public function getByName($name)
    {
        return $this->investmentRepository->getByName($name);
    }


    public function createOrRestore(array $data, $user_id = null)
    {
        // dd($data);
        return DB::transaction(function () use ($data, $user_id) {


            $userId = $user_id ?: auth()->user()->id;
            $investment_code = $this->setInvestmentCode();

            $has_fully_received = investmentStatus(
                $data['investment_amount'],
                $data['received_amount']
            );
            // $next_profit_release_date = calculateNextProfitReleaseDate($data['grace_period'], $data['profit_interval_id'], $data['investment_date']);

            // dd("test");

            $investmentType = InvestmentTypestatus($data['investor_id']);
            $balance_amount = $data['investment_amount'] - $data['received_amount'];

            // dd("test");
            // ---------------- Investment ----------------
            $investmentData = [
                'investment_code' => $investment_code,
                'investor_id' => $data['investor_id'],
                'investment_amount' => $data['investment_amount'],
                'received_amount' => $data['received_amount'],
                'total_received_amount' => $data['received_amount'],
                'balance_amount' => $balance_amount,
                'investment_date' => parseDate($data['investment_date']),
                'investment_tenure' => $data['investment_tenure'],
                'grace_period' => $data['grace_period'],
                'maturity_date' => parseDate($data['maturity_date']),
                'profit_perc' => $data['profit_perc'],
                'profit_amount' => $data['profit_amount'],
                'profit_interval_id' => $data['profit_interval_id'],
                'profit_amount_per_interval' => $data['profit_amount_per_interval'],
                // 'profit_release_date' => parseDate($data['profit_release_date']),
                'profit_release_date' => $data['profit_release_date'],
                'payout_batch_id' => $data['payout_batch_id'],
                'investor_bank_id' => $data['investor_bank_id'],
                'nominee_name' => $data['nominee_name'],
                'nominee_email' => $data['nominee_email'],
                'nominee_phone' => $data['nominee_phone'],
                'company_id' => $data['company_id'],
                'company_bank_id' => $data['company_bank_id'],
                'added_by' => $userId,
                'has_fully_received' => $has_fully_received,
                'reinvestment_or_not' => $data['reinvestment_or_not'],
                'parent_investment_id' => $data['parent_investment_id'],
                'investment_type' => $investmentType,
                'next_profit_release_date' => parseDate($data['next_profit_release_date']),
                // 'next_referral_commission_release_date' => $next_profit_release_date,
                'initial_profit_release_month' => Carbon::parse($data['next_profit_release_date'])->format('M Y'),
                'invested_company_id' => $data['invested_company_id']
            ];
            $this->validate($investmentData);
            // dd($investmentData);



            $investment = $this->investmentRepository->create($investmentData);

            if ($data['parent_investment_id']) {
                $parent = $this->investmentRepository->find($data['parent_investment_id']);
                $parentInv = [
                    'has_reinvestment' => 1,
                    'reinvested_count' => $parent->reinvested_count + 1,
                ];
                $this->investmentRepository->update($data['parent_investment_id'], $parentInv);
            }

            // ---------------- Investment Received Payment ----------------

            $investment_received_payments = [
                'investment_id' => $investment->id,
                'investor_id' => $investment->investor_id,
                'received_amount' => $investment->received_amount,
                'received_date' => $investment->investment_date,
                'status' => 1,
                'added_by' => $userId,
                'is_initial_payment' => 1
            ];

            $this->investmentReceivedPaymentService->create($investment_received_payments);

            // ---------------- Update Investor Totals ----------------
            updateInvestor($data['investor_id'], $investment->id);
            $investor = Investor::find($data['investor_id']);

            // // ---------------- Investment Documents ----------------
            if (!empty($data['contract_file'])) {
                $file = $data['contract_file'];

                $validator = Validator::make(['file' => $file], [
                    'file' => 'required|file|mimes:pdf|max:10240',
                ], [
                    'file.required' => 'Contract file is required.',
                    'file.file' => 'The uploaded file must be a valid file.',
                    'file.mimes' => 'The contract file must be a PDF.',
                    'file.max' => 'The contract file size cannot exceed 10 MB.',
                ]);

                if ($validator->fails()) {
                    throw new ValidationException($validator);
                }

                $fileName = uniqid() . '_' . $file->getClientOriginalName();

                $path = $file->storeAs(
                    'investments/' . $investor->investor_code . '/investments/' . $investment_code,
                    $fileName,
                    'public'
                );

                $investorDocData = [
                    'investment_id' => $investment->id,
                    'investor_id' => $data['investor_id'],
                    'investment_contract_file_name' => $fileName,
                    'investment_contract_file_path' => $path,
                    'added_by' => $userId,
                ];

                $this->investmentDocumentService->create($investorDocData);
            }



            // ---------------- Investment Referrals ----------------
            if (!empty($data['referral_commission_perc']) && $data['referral_commission_perc'] > 0) {
                // dd("test");
                $investorReferraldata = [
                    'investment_id' => $investment->id,
                    'investor_id' => $data['investor_id'],
                    'investor_referror_id' => $data['referral_id'],
                    'referral_commission_perc' => $data['referral_commission_perc'],
                    'referral_commission_amount' => $data['referral_commission_amount'],
                    // 'referral_commission_pending_amount' => $data['referral_commission_amount'],
                    'referral_commission_frequency_id' => $data['referral_commission_frequency_id'],
                    'referral_commission_status' => 0,
                    'total_commission_pending' => $data['referral_commission_amount'],
                    'added_by' => $userId,
                    'payment_terms_id' => $data['payment_terms_id']
                ];
                // dd($investorReferraldata);
                $this->investmentReferralService->create($investorReferraldata);
                $this->investmentRepository->update($investment->id, [
                    'next_referral_commission_release_date' => Carbon::createFromFormat('d-m-Y', $data['next_referral_commission_release_date'])
                        ->format('Y-m-d'),
                ]);

                UpdateReferralCommission($data['referral_id']);
            }
            $viewUrl = route('investment.show', [
                'investment' => $investment->id
            ]);

            $result = $this->brevoService->sendEmail(
                [
                    ['email' => 'geethufama@gmail.com', 'name' => 'Test User']
                ],
                'New Investment Added – Ref #' . $investment->id,
                'admin.emails.add-investment-email',
                [
                    'name'           => $investor->investor_name,
                    'amount' => $investment->investment_amount,
                    'url'    => $viewUrl
                ]
            );

            return $investment;
        });
    }

    public function update($id, array $data)
    {
        // dd($data);
        return DB::transaction(function () use ($id, $data) {

            $investment = $this->investmentRepository->find($id);
            $userId = auth()->user()->id;

            $has_fully_received = investmentStatus(
                $data['investment_amount'],
                $data['received_amount']
            );

            // $investmentType = InvestmentTypestatus($data['investor_id']);
            // $next_profit_release_date = calculateNextProfitReleaseDate($data['grace_period'], $data['profit_interval_id'], $data['investment_date']);

            $investmentData = [
                'investor_id' => $data['investor_id'],
                'investment_amount' => $data['investment_amount'],
                'received_amount' => $data['received_amount'],
                // 'balance_amount' => $balance_amount,
                'investment_date' => parseDate($data['investment_date']),
                'investment_tenure' => $data['investment_tenure'],
                'grace_period' => $data['grace_period'],
                'maturity_date' => parseDate($data['maturity_date']),
                'profit_perc' => $data['profit_perc'],
                'profit_amount' => $data['profit_amount'],
                'profit_interval_id' => $data['profit_interval_id'],
                'profit_amount_per_interval' => $data['profit_amount_per_interval'],
                // 'profit_release_date' => parseDate($data['profit_release_date']),
                'profit_release_date' => $data['profit_release_date'],
                'payout_batch_id' => $data['payout_batch_id'],
                'investor_bank_id' => $data['investor_bank_id'],
                'nominee_name' => $data['nominee_name'],
                'nominee_email' => $data['nominee_email'],
                'nominee_phone' => $data['nominee_phone'],
                'company_id' => $data['company_id'],
                'company_bank_id' => $data['company_bank_id'],
                'updated_by' => $userId,
                'has_fully_received' => $has_fully_received,
                'reinvestment_or_not' => $data['reinvestment_or_not'],
                // 'investment_type' => $investmentType,
                'next_profit_release_date' => parseDate($data['next_profit_release_date']),
                // 'next_referral_commission_release_date' => $data['next_profit_release_date'],
                'initial_profit_release_month' => Carbon::parse($data['next_profit_release_date'])->format('M Y'),
                'invested_company_id' => $data['invested_company_id']
            ];

            $this->validate($investmentData);

            $investment = $this->investmentRepository->update($id, $investmentData);

            // Update Investment Documents
            if (!empty($data['contract_file'])) {
                $file = $data['contract_file'];

                $validator = Validator::make(['file' => $file], [
                    'file' => 'required|file|mimes:pdf|max:10240',
                ]);

                if ($validator->fails()) {
                    throw new ValidationException($validator);
                }

                $fileName = uniqid() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs(
                    'investments/' . $investment->investor->investor_code . '/investments/' . $investment->investment_code,
                    $fileName,
                    'public'
                );

                $investorDocData = [
                    'investment_id' => $investment->id,
                    'investor_id' => $investment->investor_id,
                    'investment_contract_file_name' => $fileName,
                    'investment_contract_file_path' => $path,
                    // 'updated_by' => $userId,
                ];
                // dd($data['document_id']);
                if (isset($data['document_id']) && $data['document_id']) {
                    $this->investmentDocumentService->update($data['document_id'], $investorDocData);
                } else {
                    $this->investmentDocumentService->create($investorDocData);
                }
            }

            // Update Referral
            if (!empty($data['referral_commission_perc']) && $data['referral_commission_perc'] > 0) {
                $existingReferral = $this->investmentReferralService->getById($data['investment_referral_id']);
                // dd($existingReferral);
                // dd($data['investment_referral_id']);
                $investorReferralData = [
                    // 'investment_id' => $investment->id,
                    'investor_id' => $data['investor_id'],
                    'investor_referror_id' => $data['referral_id'],
                    'referral_commission_perc' => $data['referral_commission_perc'],
                    'referral_commission_amount' => $data['referral_commission_amount'],
                    // 'referral_commission_pending_amount' => $data['referral_commission_amount'],
                    'referral_commission_frequency_id' => $data['referral_commission_frequency_id'],
                    'referral_commission_status' => 0,
                    'updated_by' => $userId,
                    'total_commission_pending' => $data['referral_commission_amount'],
                    'payment_terms_id' => $data['payment_terms_id']
                ];
                // dd($investorReferralData);


                if ($existingReferral) {
                    // $existingReferral->update($investorReferralData);
                    $this->investmentReferralService->update($data['investment_referral_id'], $investorReferralData);
                }
                $this->investmentRepository->update($investment->id, [
                    // 'next_referral_commission_release_date' => $investment['next_profit_release_date'],
                    'next_referral_commission_release_date' => Carbon::createFromFormat('d-m-Y', $data['next_referral_commission_release_date'])
                        ->format('Y-m-d'),
                ]);
                updateReferralCommission($data['referral_id']);
            }

            $receivedPaymentData = [
                'received_amount' => $investment->received_amount,
                'received_date' => $investment->investment_date,
                'updated_by' => $userId,
            ];
            $this->investmentReceivedPaymentService->updateInitial($investment->id, $receivedPaymentData);

            updateInvestor($data['investor_id'], $investment->id);
            updateInvestmentBalance($investment->id);

            return $investment;
        });
    }


    public function delete($id)
    {
        $investment = $this->investmentRepository->find($id);
        $this->investorRepository->updateOnInvestmentDelete($investment->investor_id, $investment);
        return $this->investmentRepository->delete($id);
    }

    public function setInvestmentCode($addval = 1)
    {
        $codeService = new \App\Services\CodeGeneratorService();
        return $codeService->generateNextCode('investments', 'investment_code', 'INVM', 5, $addval);
    }

    private function validate(array $data, $id = null)
    {

        $validator = Validator::make($data, [
            'investor_id' => 'required',
            'investment_amount' => 'required|numeric|min:1',
            'received_amount' => 'nullable|numeric|min:0|lte:investment_amount',
            'investment_date' => 'required|date',
            'investment_tenure' => 'required|integer|min:1',
            'grace_period' => 'nullable|integer|min:0',
            'maturity_date' => 'required|date|after_or_equal:investment_date',
            'profit_perc' => 'required|numeric|min:0|max:100',
            'profit_amount' => 'required|numeric|min:0',
            'profit_interval_id' => 'required|exists:profit_intervals,id',
            'profit_amount_per_interval' => 'required|numeric|min:0',
            // 'profit_release_date' => 'nullable|date|after_or_equal:investment_date',
            'profit_release_date' => 'nullable|integer|min:1',
            'payout_batch_id' => 'nullable|exists:payout_batches,id',
            'investor_bank_id' => 'required|exists:investor_banks,id',
            'nominee_name' => 'nullable|string|max:255',
            'nominee_phone' => 'nullable|string|max:20',
            'nominee_email' => 'nullable|email|max:254',
            'company_id' => 'required',
            'company_bank_id' => 'required',
        ], [
            'investor_id.required' => 'Investor is required.',
            'investment_amount.required' => 'Investment amount is required.',
            'investment_amount.numeric' => 'Investment amount must be a number.',
            'investment_amount.min' => 'Investment amount must be at least 1.',
            'received_amount.numeric' => 'Received amount must be a number.',
            'received_amount.min' => 'Received amount cannot be negative.',
            'received_amount.lte' => 'Received amount cannot exceed the investment amount.',
            'investment_date.required' => 'Investment date is required.',
            'investment_date.date' => 'Investment date must be a valid date.',
            'investment_tenure.required' => 'Investment tenure is required.',
            'investment_tenure.integer' => 'Investment tenure must be an integer.',
            'investment_tenure.min' => 'Investment tenure must be at least 1.',
            'grace_period.integer' => 'Grace period must be an integer.',
            'grace_period.min' => 'Grace period cannot be negative.',
            'maturity_date.required' => 'Maturity date is required.',
            'maturity_date.date' => 'Maturity date must be a valid date.',
            'maturity_date.after_or_equal' => 'Maturity date must be after or equal to the investment date.',
            'profit_perc.required' => 'Profit percentage is required.',
            'profit_perc.numeric' => 'Profit percentage must be a number.',
            'profit_perc.min' => 'Profit percentage cannot be negative.',
            'profit_perc.max' => 'Profit percentage cannot exceed 100.',
            'profit_amount.required' => 'Profit amount is required.',
            'profit_amount.numeric' => 'Profit amount must be a number.',
            'profit_amount.min' => 'Profit amount cannot be negative.',
            'profit_interval_id.required' => 'Profit interval is required.',
            'profit_interval_id.exists' => 'Selected profit interval is invalid.',
            'profit_amount_per_interval.required' => 'Profit amount per interval is required.',
            'profit_amount_per_interval.numeric' => 'Profit amount per interval must be a number.',
            'profit_amount_per_interval.min' => 'Profit amount per interval cannot be negative.',
            // 'profit_release_date.date' => 'Profit release date must be a valid date.',
            // 'profit_release_date.after_or_equal' => 'Profit release date must be after or equal to the investment date.',
            'profit_release_date.integer' => 'Profit release date must be between 1 and 31.',
            'payout_batch_id.exists' => 'Selected payout batch is invalid.',
            'investor_bank_id.required' => 'Investor bank is required.',
            'investor_bank_id.exists' => 'Selected investor bank is invalid.',
            'nominee_name.string' => 'Nominee name must be a string.',
            'nominee_name.max' => 'Nominee name cannot exceed 255 characters.',
            'nominee_phone.string' => 'Nominee phone must be a string.',
            'nominee_phone.max' => 'Nominee phone cannot exceed 20 characters.',
            'nominee_email.email' => 'Nominee email must be a valid email address.',
            // 'nominee_email.max' => 'Nominee email cannot exceed 254 characters.',
            'company_id.required' => 'Company is required.',
            'company_bank_id.required' => 'Company bank is required.',
        ]);


        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }


    public function getDataTable(array $filters = [])
    {
        $query = $this->investmentRepository->getQuery($filters);

        $columns = [
            ['data' => 'DT_RowIndex', 'name' => 'id'],
            ['data' => 'company_name', 'name' => 'company.company_name'],
            ['data' => 'invested_company_name', 'name' => 'investedCompany.company_name'],
            ['data' => 'investor_name', 'name' => 'investor.investor_name'],
            ['data' => 'investment_amount', 'name' => 'investment_amount'],
            ['data' => 'total_received_amount', 'name' => 'total_received_amount'],
            ['data' => 'investment_date', 'name' => 'investment_date'],
            ['data' => 'profit_interval', 'name' => 'profit_interval_name'],
            ['data' => 'profit_perc', 'name' => 'profit_perc'],
            ['data' => 'maturity_date', 'name' => 'maturity_date'],
            ['data' => 'profit_release_date', 'name' => 'profit_release_date'],
            ['data' => 'grace_period', 'name' => 'grace_period'],
            ['data' => 'payout_batch', 'name' => 'payoutBatch.batch_name'],
            ['data' => 'nominee_name', 'name' => 'nominee_name'],
            ['data' => 'total_profit_released', 'name' => 'total_profit_released'],
            ['data' => 'current_month_released', 'name' => 'current_month_released'],
            ['data' => 'outstanding_profit', 'name' => 'outstanding_profit'],
            ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false],
        ];

        return datatables()
            ->of($query)
            ->addIndexColumn()
            ->addColumn('company_name', fn($row) => $row->company->company_name ?? '-')
            ->addColumn(
                'invested_company_name',
                fn($row) =>
                $row->investedCompany->company_name ?? '-'
            )
            ->addColumn('investor_name', fn($row) => $row->investor->investor_name . " - " . $row->investor->investor_code ?? '-')

            ->addColumn('investment_amount', fn($row) => number_format($row->investment_amount, 2))
            ->addColumn('received_amount', fn($row) => number_format($row->total_received_amount, 2))
            ->addColumn('investment_date', fn($row) => getFormattedDate($row->investment_date))
            ->addColumn('profit_interval', fn($row) => $row->profitInterval->profit_interval_name ?? '-')
            ->addColumn('profit_perc', fn($row) => $row->profit_perc . '%')
            ->addColumn('maturity_date', fn($row) => getFormattedDate($row->maturity_date))
            ->addColumn('profit_release_date', fn($row) => $row->profit_release_date)

            ->addColumn('grace_period', fn($row) => $row->grace_period ?? '-')
            ->addColumn('batch_name', fn($row) => 'Batch ' . $row->payout_batch_id . ' (' . $row->payoutBatch->batch_name . ')' ?? '-')
            ->addColumn('nominee_details', function ($row) {
                $name  = $row->nominee_name ?? '-';
                $email = $row->nominee_email ?? '-';
                $phone = $row->nominee_phone ?? '-';

                return "
                    <strong class='text-capitalize'>{$name}</strong>
                    <p class='mb-0 text-primary'>{$email}</p>
                    <p class='text-muted small mb-0'>
                        <i class='fa fa-phone-alt text-danger'></i>
                        <span class='font-weight-bold'>{$phone}</span>
                    </p>
                ";
            })
            ->addColumn('referral_commission_amount', fn($row) => $row->investmentReferral->referral_commission_amount ?? '-')
            ->addColumn('referral_commission_perc', fn($row) => $row->investmentReferral->referral_commission_perc ?? '-')

            ->addColumn('action', function ($row) use ($filters) {
                $action = '';

                if (!empty($filters['investor_id'])) {
                    if (auth()->user()->hasAnyPermission(['investment.view'], $row->company_id)) {
                        $action .= '<a href="' . route('investment.show', $row->id) . '"
                            class="btn btn-sm btn-primary m-1"
                            title="View Investment" target="_blank">
                            <i class="fas fa-eye"></i>
                        </a>';
                    }
                } else {
                    if (auth()->user()->hasAnyPermission(['investment.edit'], $row->company_id) && $row->is_profit_processed == 0) {
                        $action .= '<a href="' . route('investment.edit', $row->id) . '"
                            class="btn btn-sm btn-info m-1 editInvestment"
                            title="Edit Investment">
                            <i class="fas fa-edit"></i>
                        </a>';
                    }
                    if (auth()->user()->hasAnyPermission(['investment.view'], $row->company_id)) {
                        $action .= '<a href="' . route('investment.show', $row->id) . '"
                            class="btn btn-sm btn-primary m-1"
                            title="View Investment">
                            <i class="fas fa-eye"></i>
                        </a>';
                    }

                    if (auth()->user()->hasAnyPermission(['investment.delete'], $row->company_id) && $row->is_profit_processed == 0 && $row->terminate_status == 0) {
                        $action .= '<button
                            class="btn btn-sm btn-danger m-1"
                            title="Delete Investment"
                            onclick="confirmDelete(' . $row->id . ')">
                            <i class="fas fa-trash-alt"></i>
                        </button>';
                    }
                    if (!paymentFullyReceived($row->id) && auth()->user()->hasAnyPermission(['investment.submit_pending'], $row->company_id)) {
                        $action .= '
                            <button class="btn btn-sm btn-success m-1 openPendingModal"
                                data-id="' . $row->id . '" data-balance="' . $row->balance_amount . '"
                                title="Submit Pending Investment">
                                <i class="fas fa-money-check-alt"></i>
                            </button>
                        ';
                    }
                    if (($row->terminate_status == 1) && auth()->user()->hasAnyPermission(['investment.terminate'], $row->company_id)) {
                        $action .= '
                                <button class="btn btn-sm btn-warning m-1 openTerminationModal"
                                data-status = "' . $row->terminate_status . '"
                                    data-id="' . $row->id . '"
                                    data-requested-date="' . ($row->termination_requested_date ? \Carbon\Carbon::parse($row->termination_requested_date)->format('d-m-Y') : '') . '"
                                    data-duration="' . ($row->termination_duration ?? '') . '"
                                    data-termination-date="' . ($row->termination_date ? \Carbon\Carbon::parse($row->termination_date)->format('d-m-Y') : '') . '"
                                   data-file-path="' . ($row->termination_document ? Storage::url($row->termination_document) : '') . '"
                                   data-principal="' . ($row->investment_amount) . '"
                                   data-outstanding="' . ($row->termination_outstanding) . '"
                                   data-commission-outstanding="' . ($row->termination_referral_commission_outstanding) . '"
                                   data-outstanding-profit = "' . ($row->outstanding_profit) . '"
                                    title="Edit termination Details">
                                    <i class="fas fa-file-signature"></i>
                                </button>
                            ';
                    } elseif (auth()->user()->hasAnyPermission(['investment.terminate'], $row->company_id) && ($row->terminate_status == 0) && ($row->is_profit_processed == 1)) {
                        $action .= '
                            <button class="btn btn-sm btn-danger m-1 openTerminationModal"
                                data-id="' . $row->id . '"
                                data-balance="' . $row->balance_amount . '"
                                data-principal="' . ($row->investment_amount) . '"
                                data-outstanding="' . ($row->termination_outstanding) . '"
                                data-commission-outstanding="' . ($row->termination_referral_commission_outstanding) . '"
                                data-outstanding-profit = "' . ($row->outstanding_profit) . '"
                                data-status = "' . $row->terminate_status . '"
                                title="Terminate Investment">
                                <i class="fas fa-ban"></i>
                            </button>
                        ';
                    }
                }



                return $action;
            })
            ->rawColumns(['action', 'nominee_details'])
            ->toJson();
    }
    public function getFormData()
    {
        $investors = $this->investorRepository->getInvestorsWithDetails();
        // dd($investors);
        $payoutBatches = PayoutBatch::where('status', 1)->get();

        $permittedCompanyIds = getUserPermittedCompanyIds(auth()->user()->id, 'investment');
        $companyBanks = Company::with('banks')->whereIn('id', $permittedCompanyIds)->get();
        $investedCompanyBanks = Company::with('banks')->get();

        $profitInterval = ProfitInterval::where('status', 1)->get();
        $referralFrequency = ReferralCommissionFrequency::where('status', 1)->get();
        $paymentTerms = PaymentTerms::where('status', 1)->get();
        // dd($profitInterval);
        // dd($companyBanks);
        return [
            'investors' => $investors,
            'payoutBatches' => $payoutBatches,
            'companyBanks' => $companyBanks,
            'investedCompanyBanks' => $investedCompanyBanks,
            'profitInterval' => $profitInterval,
            'frequency' => $referralFrequency,
            'paymentTerms' => $paymentTerms
        ];
    }
    public function submitPending($data)
    {
        DB::transaction(function () use ($data) {
            $investment = $this->investmentRepository->find($data['investment_id']);
            if (!$investment) {
                throw new \Exception("Investment not found");
            }


            $totalReceived = $this->investmentRepository->getTotalReceivedAmount($investment);
            $balanceAmount = $investment->investment_amount - $totalReceived;

            $userId = auth()->id();
            $balance_amount = $balanceAmount - $data['received_amount'];

            $insertData = [
                'investment_id'  => $investment->id,
                'investor_id'    => $investment->investor_id,
                'received_amount' => $data['received_amount'],
                'received_date'  => parseDate($data['received_date']),
                'status'         => 1,
                'added_by'       => $userId,
                'is_initial_payment' => 0
            ];
            $this->investmentReceivedPaymentService->create($insertData);

            $newTotalReceived = $totalReceived + $data['received_amount'];
            $has_fully_received = investmentStatus(
                $investment->investment_amount,
                $newTotalReceived
            );
            $this->investmentRepository->updateById($investment->id, [
                'total_received_amount' => $newTotalReceived,
                'has_fully_received' => $has_fully_received,
                'balance_amount' => $balance_amount,

            ]);
        });
    }
    public function getFormDataEdit()
    {
        $investors = $this->investorRepository->getInvestorsWithDetails();
        // dd($investors);
        $payoutBatches = PayoutBatch::where('status', 1)->get();
        $companyBanks = Company::with('banks')->get();
        $profitInterval = ProfitInterval::where('status', 1)->get();
        $referralFrequency = ReferralCommissionFrequency::where('status', 1)->get();
        // dd($profitInterval);
        // dd($companyBanks);
        return [
            'investors' => $investors,
            'payoutBatches' => $payoutBatches,
            'companyBanks' => $companyBanks,
            'profitInterval' => $profitInterval,
            'frequency' => $referralFrequency
        ];
    }
    public function getDetails($id)
    {
        return $this->investmentRepository->getDetails($id);
    }

    public function updatePending($data)
    {
        // dd($data);
        return DB::transaction(function () use ($data) {
            $investment = $this->investmentRepository->find($data['investment_id']);
            $payment = $this->investmentReceivedPaymentService->getById($data['payment_id']);
            // dd($payment);
            if (!$investment) {
                throw new \Exception("Investment not found");
            }
            $totalReceived = $investment->total_received_amount;
            $balanceAmount = $investment->balance_amount;


            if ($payment->received_amount > $data['received_amount']) {
                $balance_amount = $balanceAmount + ($payment->received_amount - $data['received_amount']);
                $newTotalReceived = $totalReceived - ($payment->received_amount - $data['received_amount']);
            } else if ($payment->received_amount < $data['received_amount']) {
                $balance_amount = $balanceAmount - ($data['received_amount'] - $payment->received_amount);
                $newTotalReceived = $totalReceived + ($data['received_amount'] - $payment->received_amount);
            } else {
                $balance_amount = $balanceAmount;
                $newTotalReceived = $totalReceived;
            }
            // dd($balanceAmount, $balance_amount);



            $userId = auth()->id();
            // $balance_amount = $balanceAmount - $data['received_amount'];

            $updatedData = [
                'received_amount' => $data['received_amount'],
                'received_date'  => parseDate($data['received_date']),
                'updated_by'       => $userId,
            ];
            $this->investmentReceivedPaymentService->update($data['payment_id'], $updatedData);

            $has_fully_received = investmentStatus(
                $investment->investment_amount,
                $newTotalReceived
            );
            $this->investmentRepository->updateById($investment
                ->id, [
                'total_received_amount' => $newTotalReceived,
                'has_fully_received' => $has_fully_received,
                'balance_amount' => $balance_amount,

            ]);
            return $payment;
        });
    }
    public function terminateRequest($data)
    {
        // dd($data);
        // Validate the request
        $this->validateTermination($data);
        // dd($data);

        $investment = DB::transaction(function () use ($data) {
            $investment = $this->investmentRepository->getWithDetails($data['investment_id']);
            // dd($investment);

            // Find the investment

            // Prepare termination data
            $terminationData = [
                'terminate_status' => 1,
                'termination_requested_date' => Carbon::createFromFormat('d-m-Y', $data['termination_requested_date']),
                'termination_date' => Carbon::createFromFormat('d-m-Y', $data['termination_date']),
                'termination_duration' => $data['duration'],
                'termination_requested_by' => auth()->id(),
            ];
            if ($data['termination_outstanding']) {
                $terminationData['termination_outstanding'] = $data['termination_outstanding'];
            }
            if ($data['termination_referral_commission_outstanding']) {
                $terminationData['termination_referral_commission_outstanding'] = $data['termination_referral_commission_outstanding'];
            }
            // dd($terminationData);


            // Handle file upload if exists
            if (isset($data['termination_file'])) {
                $fileName = uniqid() . '_' . $investment->investment_code . $data['termination_file']->getClientOriginalName();
                $path = $data['termination_file']->storeAs(
                    'investments/' . $investment->investor->investor_code . '/terminations/' . $investment->investment_code,
                    $fileName,
                    'public'
                );
                $terminationData['termination_document'] = $path;
            }

            // Update investment
            return  $this->investmentRepository->update($data['investment_id'], $terminationData);
        });

        $investor = $investment->investor;
        $doc = $investment->termination_document;
        $url = config('app.url');
        $document_path = $url . '/storage/' . $doc;
        // dd($document_path);

        $result = $this->brevoService->sendEmail(
            [
                ['email' => 'geethufama@gmail.com', 'name' => 'Test User']
            ],
            'Investment Termination Request Created',
            'admin.emails.terminate-investment-email',
            [
                'name'           => $investor->investor_name,
                'amount' => $investment->investment_amount,
                'requested_date' => $investment->termination_requested_date,
                'termination_date' => $investment->termination_date,
                'duration' => $investment->termination_duration,
                'document_path' => $document_path
            ]
        );

        return $investment;
    }

    public function validateTermination($data)
    {
        // dd($data);
        $validator = Validator::make($data, [
            'termination_requested_date' => 'required|date',
            'duration' => 'required|integer|min:1',
            'termination_date' => 'required|date|after:termination_requested_date',
            'termination_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
            'termination_outstanding' => 'nullable|numeric',
            'termination_referral_commission_outstanding' => 'nullable|numeric'

        ], [
            'termination_requested_date.required' => 'Requested date is required.',
            'termination_requested_date.date' => 'Requested date must be a valid date.',
            'duration.required' => 'Duration is required.',
            'duration.integer' => 'Duration must be a number.',
            'duration.min' => 'Duration must be at least 1 day.',
            'termination_date.required' => 'Termination date is required.',
            'termination_date.date' => 'Termination date must be a valid date.',
            'termination_date.after' => 'Termination date must be after the requested date.',
            // 'termination_file.required' => 'Termination document is required.',
            'termination_file.file' => 'The uploaded file must be a valid file.',
            'termination_file.mimes' => 'Allowed file types: PDF, JPG, JPEG, PNG.',
            // 'termination_file.max' => 'File size must not exceed 2MB.',
        ]);



        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
    public function getReferrals(array $filters = [])
    {
        $query = $this->investmentRepository->getReferralQuery($filters);

        $columns = [
            ['data' => 'DT_RowIndex', 'name' => 'id'],
            ['data' => 'investment_date', 'name' => 'investment.investment_date'],
            ['data' => 'investor_name', 'name' => 'referrer.investor_name'],
            ['data' => 'company_name', 'name' => 'referrer.investment.company.company_name'],
            ['data' => 'referral_commission_perc', 'name' => 'referral_commission_perc'],
            ['data' => 'referral_commission_amount', 'name' => 'referral_commission_amount'],
            ['data' => 'referral_commission_frequency', 'name' => 'commissionFrequency.commission_frequency_name'],
            ['data' => 'term_name', 'name' => 'paymentTerm.term_name'],
            ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false],
        ];

        return datatables()
            ->of($query)
            ->addIndexColumn()
            ->addColumn('investment_date', fn($row) => getFormattedDate($row->investment->investment_date))

            // investor name
            // ->addColumn('investor_name', function ($row) {
            //     return ($row->referrer->investor_name ?? '-') .
            //         ' - ' .
            //         ($row->referrer->investor_code ?? '');
            // })

            // referral commission percentage
            ->addColumn('referral_commission_perc', function ($row) {
                return $row->referral_commission_perc
                    ? $row->referral_commission_perc . '%'
                    : '-';
            })

            // referral commission amount
            ->addColumn('referral_commission_amount', function ($row) {
                return $row->referral_commission_amount
                    ? number_format($row->referral_commission_amount, 2)
                    : '-';
            })

            // referral commission frequency
            ->addColumn('referral_commission_frequency', function ($row) {
                return $row->commissionFrequency->commission_frequency_name ?? '-';
            })
            ->addColumn('term_name', function ($row) {
                return $row->PaymentTerm->term_name ?? '-';
            })
            ->addColumn('referral_status', function ($row) {
                if (strtolower($row->referral_commission_frequency_id ?? '') === 1) {
                    $status = ($row->referral_commission_status == 1) ? 'Expired' : 'Active';
                } else {
                    $status = 'Active';
                }

                $class = ($status == 'Active') ? 'badge-success' : 'badge-danger';

                return "<span class='badge {$class}'>" . $status . "</span>";
            })
            ->addColumn('investor_name', function ($row) {
                if (!$row->referrer) {
                    return '-';
                }

                $url = route('investor.show', $row->referrer->id);

                return '<a href="' . $url . '" class="text-primary fw-semibold text-decoration-none">'
                    . e($row->referrer->investor_name) . ' - ' . e($row->referrer->investor_code) .
                    '</a>';
            })
            ->addColumn('company_name', function ($row) {
                if (!$row->referrer) {
                    return '-';
                }

                $url = route('company.show', $row->investment?->company_id);

                return '<a href="' . $url . '" class="text-primary fw-semibold text-decoration-none">'
                    . e($row->investment?->company->company_name) .
                    '</a>';
            })
            ->addColumn('referred_investor_name', function ($row) {
                if (!$row->investor) {
                    return '-';
                }

                $url = route('investor.show', $row->investor->id);

                return '<a href="' . $url . '" class="text-green fw-semibold text-decoration-none">'
                    . e($row->investor->investor_name) . ' - ' . e($row->investor->investor_code) .
                    '</a>';
            })
            ->addColumn('referred_investment_amount', function ($row) {
                if (!$row->investment) {
                    return '-';
                }



                return $row->investment->investment_amount;
            })


            // action buttons
            ->addColumn('action', function ($row) {
                $action = '';

                // if (Gate::allows('investment.view')) {
                $action .= '<a href="' . route('referrals.show', $row->id) . '"
                class="btn btn-sm btn-primary">
                <i class="fas fa-eye"></i>
            </a>';
                // }

                return $action;
            })

            ->rawColumns(['action', 'referral_status', 'investor_name', 'referred_investor_name', 'referred_investment_amount', 'term_name', 'company_name'])
            ->toJson();
    }
    public function getReferralDetails($id)
    {
        return InvestmentReferral::with([
            'investor',
            'referrer',
            'investment',
            'referrer',
            'investorPayouts.investorPayoutDistribution'

        ])->findOrFail($id);
    }
}
