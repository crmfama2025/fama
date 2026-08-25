<?php

namespace App\Services\Investment;

use App\Models\Investment;
use App\Models\InvestmentContractDocuments;
use App\Models\PartialWithdrawalBifurcation;
use App\Models\WhatsappMessage;
use App\Repositories\Investment\InvestmentContractDocumentRepository;
use App\Repositories\Investment\InvestmentRepository;
use App\Repositories\Investment\InvestorAgreementRepository;
use App\Repositories\Investment\InvestorLedgerRepository;
use App\Repositories\Investment\InvestorRepository;
use App\Services\Investment\WhatsAppMsgService;
use App\Services\WhatsAppService;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class InvestorService
{
    public function __construct(
        protected InvestorRepository $investorRepo,
        protected InvestorBankService $investorBankServ,
        protected InvestorDocumentService $investorDocServ,
        // protected InfobipWhatsAppService $infobipService,
        protected WhatsAppMsgService $whatsApp,
        protected InvestmentContractDocumentRepository $investmentContractDocumentRepo,
        protected InvestorLedgerRepository $investorLedgerRepo,
        protected InvestorAgreementRepository $investorAgreementRepository,
        protected InvestmentRepository $investmentRepository,
        protected InvestmentContractDocumentService $investmentContractDocumentService
    ) {}


    public function getAll()
    {
        return $this->investorRepo->all();
    }

    public function getAllActive()
    {
        return $this->investorRepo->allActive();
    }

    public function getById($id)
    {
        return $this->investorRepo->find($id);
    }

    public function getByName($name)
    {
        return $this->investorRepo->getByName($name);
    }

    public function create(array $data, $user_id = null)
    {
        $this->validate($data['investor']);

        $dataArr = [];
        return DB::transaction(function () use ($data, $dataArr) {
            $dataArr = $data['investor'];
            $dataArr['created_by'] = auth()->user()->id;
            $dataArr['investor_code'] = $this->setInvestorCode();

            $investor = $this->investorRepo->create($dataArr);

            $this->investorBankServ->create($data['investor_bank'] ?? [], $investor->id);
            $this->investorDocServ->create($data['inv_doc'] ?? [], $investor);

            // $response = $this->infobipService->sendTemplateMessage(
            //     '971507376124',
            //     "first_purchase_thank_you",
            //     ['Rasmiya']
            // );

            // whatsapp messages commended for data entry
            // $templateId = '397327';
            // $templateId_ar = '397337';

            // $phone = $investor->investor_mobile ?? null;
            // $phone = preg_replace('/[^0-9]/', '', $phone);

            // // $variables = [
            // //     'investor_name' => $investor->investor_name ?? 'Investor',
            // // ];
            // $variables = [
            //     'investor_name_en' => $investor->investor_name ?? 'Investor',
            //     'investor_name_ar' => transliterateToArabic($investor->investor_name ?? 'Investor'),
            // ];

            // $templates = [
            //     'en' => $templateId,
            //     'ar' => $templateId_ar,
            // ];

            // foreach ($templates as $lang => $tid) {

            //     $payload = [
            //         'apiToken' => env('WHATCHIMP_API_KEY'),
            //         'phone_number_id' => env('WHATSAPP_NUMBER_ID'),
            //         'template_id' => $tid,
            //         'phone_number' => $phone,
            //         // Whatchimp variable syntax: templateVariable-<name>-1
            //         // 'templateVariable-invesor-1' => $variables['investor_name']
            //         'templateVariable-investorName-1' => $lang === 'ar'
            //             ? $variables['investor_name_ar']
            //             : $variables['investor_name_en'],
            //     ];
            //     $response = $this->whatsApp->sendTemplateById($payload);

            //     $status = isset($response['status']) && $response['status'] == '1' ? 1 : 0;

            //     WhatsappMessage::create([
            //         'investor_id' => $investor->id,
            //         'phone'       => $phone,
            //         'template_id' => $tid,
            //         'variables'   => json_encode($variables),
            //         'payload'     => json_encode($payload),
            //         'response'    => json_encode($response),
            //         'status'      => $status,
            //     ]);

            //     \Log::info("WhatsApp {$lang} response", ['response' => $response]);
            // }


            // return response()->json([
            //     'status'   => 'success',
            //     // 'whatsapp' => $response,
            // ]);
        });
    }

    public function update($id, array $data)
    {
        $this->validate($data['investor'], $id);

        $dataArr = [];
        return DB::transaction(function () use ($data, $dataArr, $id) {
            $dataArr = $data['investor'];
            $dataArr['updated_by'] = auth()->user()->id;

            $investor = $this->investorRepo->update($id, $dataArr);

            $this->investorBankServ->update($data['investor_bank']['bank_id'], $data['investor_bank'] ?? []);
            $this->investorDocServ->update($data['inv_doc'] ?? [], $investor);
        });
    }

    public function delete($id)
    {
        return $this->investorRepo->delete($id);
    }

    public function setInvestorCode($addval = 1)
    {
        $codeService = new \App\Services\CodeGeneratorService();
        return $codeService->generateNextCode('investors', 'investor_code', 'INVR', 5, $addval);
    }

    private function validate(array $data, $id = null)
    {
        $validator = Validator::make($data, [
            'investor_name' => 'required',
            'investor_mobile' => [
                'required',
                'numeric',
                'regex:/^[1-9][0-9]{9,14}$/'
            ],
            'investor_email' => 'required',
            'nationality_id' => 'required',
            'id_number' => [
                'required',
                Rule::unique('investors', 'id_number')->ignore($id),
            ],
            'payment_mode_id' => 'required',
            'investor_address' => 'required',
            'payout_batch_id' => 'required',
            // 'address_line2' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country_id' => 'required',
        ], [
            'id_number.required' => 'Emirates ID/Other ID id required',
            'payment_mode_id.required' => 'Payment Mode required'
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    public function getDataTable(array $filters = [])
    {
        $query = $this->investorRepo->getQuery($filters);

        $columns = [
            ['data' => 'DT_RowIndex', 'name' => 'id'],
            ['data' => 'investor_name', 'name' => 'investor_name'],
            ['data' => 'investor_code', 'name' => 'investor_code'],
            ['data' => 'investor_mobile', 'name' => 'investor_mobile'],
            ['data' => 'investor_email', 'name' => 'investor_email'],
            ['data' => 'nationality_name', 'name' => 'nationality_name'],
            ['data' => 'country_of_residence', 'name' => 'country_of_residence'],
            ['data' => 'referral', 'name' => 'referral'],
            ['data' => 'investor_address', 'name' => 'investor_address'],
            ['data' => 'id_number', 'name' => 'id_number'],
            ['data' => 'payment_mode', 'name' => 'payment_mode'],
            ['data' => 'investor_bank_name', 'name' => 'investor_bank_name'],
            ['data' => 'action', 'name' => 'action', 'orderable' => true, 'searchable' => true],
        ];

        return datatables()
            ->of($query)
            ->addIndexColumn()
            ->addColumn('investor_code', fn($row) => $row->investor_code ?? '-')
            ->addColumn('investor_name', function ($row) {
                $name = $row->investor_name ?? '-';
                $email = $row->investor_email ?? '-';
                $phone = $row->investor_mobile ?? '-';

                $address = $row->investor_address;
                if (!empty($row->address_line2)) {
                    $address .= ', ' . $row->address_line2;
                }
                // if (!empty($row->city)) {
                //     $address .= ', ' . $row->city;
                // }
                // if (!empty($row->country_id)) {
                //     $address .= ', ' . $row->country?->nationality_name;
                // }
                if (!empty($row->postal_code)) {
                    $address .= ' - ' . $row->postal_code;
                }


                $address = $address ?? '-';

                return "<strong class='text-capitalize'>{$name}</strong><p class='mb-0 text-primary'>{$email}</p>
            <p class='text-muted small'><i class='fa fa-phone-alt text-danger'></i> <span class='font-weight-bold'>{$phone}</span> </p><p class='text-muted small'><i class='fas fa-home text-danger'></i> <span class='font-weight-bold'>{$address}</span></p>";
            })
            ->addColumn('nationality_name', fn($row) => $row->nationality->nationality_name ?? '-')
            ->addColumn('country_of_residence', fn($row) => $row->countryOfResidence->nationality_name ?? '-')
            ->addColumn('id_number', fn($row) => $row->id_number ?? '-')
            ->addColumn('referral', fn($row) => $row->referral->investor_name ?? '-')
            ->addColumn('payment_mode', function ($row) {
                if (!$row->paymentMode) return '-';

                if (in_array($row->paymentMode->id, [1, 4])) return $row->paymentMode->payment_mode_name;

                if (in_array($row->paymentMode->id, [3, 2])) {
                    // $primaryBank = $row->investorBanks->where('is_primary', 1)->first();
                    $bankName = $row->primaryBank->investor_bank_name ?? '-';
                    return $row->paymentMode->payment_mode_name . ' - ' . $bankName;
                }
                // dump($row->paymentMode->payment_mode_name);

                return '-';
            })
            ->addColumn('added_by', function ($row) {
                if ($row->addedBy) {
                    $name = $row->addedBy->first_name . ' ' . $row->addedBy->last_name;
                    $image = $row->addedBy->profile_path
                        ? asset('storage/' . $row->addedBy->profile_path)
                        : asset('images/default-avatar.png');

                    return '
            <div style="display:flex; align-items:center; gap:8px;">
                <img src="' . $image . '"
                     style="width:30px; height:30px; border-radius:50%; object-fit:cover;">
                <span>' . $name . '</span>
            </div>
        ';
                }

                return '-';
            })
            ->addColumn('action', function ($row) {
                $action = '';
                if (auth()->user()->hasAnyPermission(['investor.edit'], $row->company_id)) {
                    $action .= '<a href="' . route('investor.edit', $row->id) . '" class="btn btn-info btn-sm" title="Edit Investor"><i class="fas fa-pencil-alt"></i></a>';
                }
                if (auth()->user()->hasAnyPermission(['investor.view'], $row->company_id)) {
                    $action .= '
                <a href="' . route('investor.show', $row->id) . '" class="btn btn-primary btn-sm" title="View Investor"><i class="fas fa-eye"></i></a>';
                }

                if ($row->total_no_of_investments == 0 && auth()->user()->hasAnyPermission(['investor.delete'], $row->company_id)) {
                    $action .= ' <button class="btn btn-danger btn-sm" data-id="' . $row->id . '" onclick="deleteConf(' . $row->id . ')"><i class="fas fa-trash-alt"></i></button>';
                }
                if (auth()->user()->hasAnyPermission(['investor.add'], $row->company_id)) {
                    $action .= ' <button class="btn btn-warning btn-sm" data-id="" data-investor-id="' . $row->id . '" data-target="#modal-add-bank" data-toggle="modal" title="Add Bank"><i class="fas fa-university"></i></button>';
                }
                if (auth()->user()->hasAnyPermission(['investor.withdrawal'], $row->company_id)) {
                    $action .= ' <a href="' . route('investor.partial_withdrawal', $row->id) . '"
                    title="Withdrawal/Settlement"
                    class="btn bg-orange btn-sm">
                    <i class="fas fa-wallet"></i>
                </a>';
                }
                // $action .= ' <a href="' . route('investor.investment_annexture', $row->id) . '"
                //     title="Investment Annexture"
                //     class="btn bg-pink btn-sm">
                //     <i class="fas fa-file"></i>
                // </a>';

                return $action;
            })
            ->rawColumns(['investor_name', 'action', 'payment_mode', 'added_by'])
            ->with(['columns' => $columns])
            ->toJson();
    }
    public function getInvestedCompanies($investor_id)
    {
        return $this->investorRepo->getInvestedCompanies($investor_id);
    }
    public function getInvestorLedger(array $filters = [])
    {
        $query = $this->investorRepo->getLedgerQuery($filters);

        return datatables()
            ->of($query)
            ->addIndexColumn()


            ->addColumn('date', function ($row) {
                return $row->transaction_date
                    ? \Carbon\Carbon::parse($row->transaction_date)->format('d M Y h:i A')
                    : '-';
            })

            ->addColumn('type', function ($row) {
                return $row->transactionType->transaction_type ?? '-';
            })

            ->addColumn('debit', function ($row) {
                return !$row->is_credit ? number_format($row->transaction_amount, 2) : '-';
            })

            ->addColumn('credit', function ($row) {
                return $row->is_credit ? number_format($row->transaction_amount, 2) : '-';
            })

            // ->addColumn('balance', function ($row) {
            //     // Optional: calculate running balance in frontend OR backend separately
            //     return '-';
            // })

            ->rawColumns(['date', 'type'])
            ->toJson();
    }
    // public function getCompanyInvestments($investorId, $companyId,)
    // {
    //     return Investment::where('investor_id', $investorId)
    //         ->where('company_id', $companyId)
    //         ->where('terminate_status', 0)
    //         ->with('latestBifurcation')
    //         ->get()

    //         ->map(function ($inv) {
    //             $availableBalance = ($inv->has_partial_withdrawal == 1 && $inv->latestBifurcation)
    //                 ? $inv->latestBifurcation->balance_amount
    //                 : $inv->investment_amount;

    //             return [
    //                 'id' => $inv->id,
    //                 'reference' => $inv->investment_code ?? ('INV-' . $inv->id),
    //                 'invested_date' => optional($inv->investment_date)->format('Y-m-d'),
    //                 'available_balance' => $availableBalance,
    //             ];
    //         });
    // }
    public function getCompanyInvestments($investorId, $companyId, $editLedgerId = null)
    {
        $query = Investment::where('investor_id', $investorId)
            ->where('company_id', $companyId)
            ->where('investment_term_type', 1)
            ->with('latestBifurcation');

        if ($editLedgerId) {
            $bifurcatedIds = PartialWithdrawalBifurcation::where('ledger_id', $editLedgerId)
                ->pluck('investment_id');

            $query->where(function ($q) use ($bifurcatedIds) {
                $q->where('terminate_status', 0)
                    ->orWhereIn('id', $bifurcatedIds);
            });
        } else {
            $query->where('terminate_status', 0);
        }

        return $query->get()->map(function ($inv) {
            $availableBalance = ($inv->has_partial_withdrawal == 1 && $inv->latestBifurcation)
                ? $inv->latestBifurcation->balance_amount
                : $inv->investment_amount;

            return [
                'id' => $inv->id,
                'reference' => $inv->investment_code ?? ('INV-' . $inv->id),
                'invested_date' => optional($inv->investment_date)->format('Y-m-d'),
                'available_balance' => $availableBalance,
            ];
        });
    }
    // public function partialWithdrawal($investorId, array $data)
    // {
    //     // dd($data);
    //     return DB::transaction(function () use ($investorId, $data) {

    //         // create partial withdrawal contract document for the withdrawal
    //         $appliedInvestments = json_encode(array_keys($data['investments'] ?? []));

    //         $Document_data = [
    //             'investor_id' => $investorId,
    //             'company_id' => $data['company_id'],
    //             'investor_agreement_template_id' => $this->investorAgreementRepository->getActiveIdBytype(3),
    //             'investor_agreement_type_id' => 3,
    //             'added_by' => auth()->user()->id,
    //             'applied_investments' => $appliedInvestments,
    //             'investment_id' => 0,
    //         ];
    //         // dd($Document_data);
    //         $document = $this->investmentContractDocumentService->createInvestorDocument($investorId, $data['company_id'], $Document_data);

    //         // create ledger entry for the withdrawal

    //         $ledger_data = [
    //             'investment_contract_document_id' => $document->id,
    //             'investor_id' => $investorId,
    //             'company_id' => $data['company_id'],
    //             'investor_transaction_type_id' => '3',
    //             'transaction_amount' => $data['withdrawal_amount'],
    //             'is_credit' => 0,
    //             'transaction_date' => parseDate($data['requested_date']),
    //             'added_by' => auth()->user()->id,
    //             'investment_id' => 0,
    //             'withdrawal_status' => 1,
    //             'requested_date' => parseDate($data['requested_date']),
    //             'duration_days' => $data['duration_days'],
    //             'withdrawal_date' => parseDate($data['withdrawal_date'])
    //         ];
    //         $ledger = $this->investorLedgerRepo->create($ledger_data);


    //         // Update Investments
    //         // $investments = $data['investments'] ?? [];
    //         $investments = collect($data['investments'] ?? [])
    //             ->filter(function ($row) {
    //                 return isset($row['selected'])
    //                     && $row['selected'] == '1'
    //                     && isset($row['amount'])
    //                     && $row['amount'] !== ''
    //                     && (float) $row['amount'] > 0;
    //             })
    //             ->toArray();
    //         // dd($investments);
    //         foreach ($investments as $investmentId => $row) {

    //             $withdrawalAmount = $row['amount'];
    //             $availableBalance = $row['available_amount'];

    //             $balance_investment_amount = $availableBalance - $withdrawalAmount;
    //             $totalWithdrawnAmount = getTotalWithdrawnAmount($investmentId) + $withdrawalAmount;

    //             $investmentData = [
    //                 'has_partial_withdrawal' => 1,
    //                 'investment_amount' => $balance_investment_amount,
    //                 'total_withdrawn_amount' => $totalWithdrawnAmount,
    //             ];
    //             // @dump($investmentData);

    //             // Full withdrawal of this investment = termination
    //             $isTermination = abs($availableBalance - $withdrawalAmount) < 0.01;

    //             if ($isTermination && !empty($data['requested_date']) && !empty($data['withdrawal_date'])) {
    //                 $investmentData['investment_status'] = 1;
    //             }
    //             $investment = $this->investmentRepository->update($investmentId, $investmentData);


    //             // $checkTermination = $this->checkIftermination($investorId, $data['company_id']);
    //             // if ($checkTermination) {
    //             //     foreach ($investments as $investmentId => $row) {
    //             //         $termData['termination_requested_date'] = parseDate($data['requested_date']);
    //             //         $termData['termination_date'] = parseDate($data['withdrawal_date']);
    //             //         $termData['termination_duration'] = $data['duration_days'];
    //             //         $termData['termination_requested_by'] = auth()->user()->id;
    //             //         $twrmData['terminate_status'] = 1;


    //             //         $investment = $this->investmentRepository->update($investmentId, $termData);
    //             //     }

    //             //     $termination_data = [
    //             //         'investor_id' => $investorId,
    //             //         'company_id' => $data['company_id'],
    //             //         'investor_agreement_template_id' => $this->investorAgreementRepository->getActiveIdBytype(5),
    //             //         'investor_agreement_type_id' => 5,
    //             //         'added_by' => auth()->user()->id,
    //             //         'applied_investments' => $appliedInvestments,
    //             //         'investment_id' => $investmentId,
    //             //     ];
    //             //     // dump($termination_data);


    //             //     $terminationDoc = $this->investmentContractDocumentService->createInvestorDocument($investorId, $data['company_id'], $termination_data);
    //             // }

    //             // dd("test");



    //             // 3. Create bifurcation record

    //             $partialWithdrawalData = [
    //                 'investment_id' => $investmentId,
    //                 'ledger_id' => $ledger->id,
    //                 'company_id' => $data['company_id'],
    //                 'withdrawal_amount' => $withdrawalAmount,
    //                 'previous_amount' => $availableBalance,
    //                 'balance_amount' => $balance_investment_amount,
    //                 'added_by' => auth()->user()->id,
    //                 'requested_date' => parseDate($data['requested_date']),
    //                 'withdrawal_date' => parseDate($data['withdrawal_date']),
    //                 'duration_days' => $data['duration_days'],
    //             ];
    //             $partial_Withdrawal = $this->investorLedgerRepo->createPartialWithdrawal($partialWithdrawalData);
    //         }
    //         $checkTermination = $this->checkIftermination($investorId, $data['company_id']);
    //         if ($checkTermination) {
    //             foreach ($investments as $investmentId => $row) {
    //                 $termData['termination_requested_date'] = parseDate($data['requested_date']);
    //                 $termData['termination_date'] = parseDate($data['withdrawal_date']);
    //                 $termData['termination_duration'] = $data['duration_days'];
    //                 $termData['termination_requested_by'] = auth()->user()->id;
    //                 $termData['terminate_status'] = 1;


    //                 $investment = $this->investmentRepository->update($investmentId, $termData);
    //             }

    //             $termination_data = [
    //                 'investor_id' => $investorId,
    //                 'company_id' => $data['company_id'],
    //                 'investor_agreement_template_id' => $this->investorAgreementRepository->getActiveIdBytype(5),
    //                 'investor_agreement_type_id' => 5,
    //                 'added_by' => auth()->user()->id,
    //                 'applied_investments' => $appliedInvestments,
    //                 // 'investment_id' => $investmentId,
    //             ];
    //             // dump($termination_data);


    //             $terminationDoc = $this->investmentContractDocumentService->createInvestorDocument($investorId, $data['company_id'], $termination_data);
    //         }
    //         // dd($checkTermination);
    //     });
    // }

    public function partialWithdrawal($investorId, array $data)
    {
        // dd($data);
        return DB::transaction(function () use ($investorId, $data) {

            $appliedInvestments = json_encode(array_keys($data['investments'] ?? []));

            // Only keep investments that were actually selected with a valid amount
            $investments = collect($data['investments'] ?? [])
                ->filter(function ($row) {
                    return isset($row['selected'])
                        && $row['selected'] == '1'
                        && isset($row['amount'])
                        && $row['amount'] !== ''
                        && (float) $row['amount'] > 0;
                })
                ->toArray();

            // --- 1. Update each investment's balance/withdrawn totals first ---
            // (bifurcation records get created later, once we know which ledger they attach to)
            $investmentUpdates = [];
            $total_profit = 0;
            foreach ($investments as $investmentId => $row) {

                $withdrawalAmount = $row['amount'];
                $availableBalance = $row['available_amount'];
                $total_profit += $row['profit'];

                $balance_investment_amount = $availableBalance - $withdrawalAmount;
                $totalWithdrawnAmount = getTotalWithdrawnAmount($investmentId) + $withdrawalAmount;

                $investmentData = [
                    'has_partial_withdrawal' => 1,
                    'investment_amount' => $balance_investment_amount,
                    'total_withdrawn_amount' => $totalWithdrawnAmount,
                ];

                $isTermination = abs($availableBalance - $withdrawalAmount) < 0.01;
                if ($isTermination && !empty($data['requested_date']) && !empty($data['withdrawal_date'])) {
                    $investmentData['investment_status'] = 1;
                }

                $this->investmentRepository->update($investmentId, $investmentData);

                // stash what's needed for the bifurcation record, created below
                $investmentUpdates[$investmentId] = [
                    'withdrawalAmount' => $withdrawalAmount,
                    'availableBalance' => $availableBalance,
                    'balance_investment_amount' => $balance_investment_amount,
                    'withdrawal_month_profit' => $row['profit']
                ];
            }

            // --- 2. Decide: is this a termination, or a plain partial withdrawal? ---
            $checkTermination = $this->checkIftermination($investorId, $data['company_id']);
            // dd($checkTermination);

            if ($checkTermination) {
                // Mark termination fields on each investment involved
                foreach ($investments as $investmentId => $row) {
                    $this->investmentRepository->update($investmentId, [
                        'termination_requested_date' => parseDate($data['requested_date']),
                        'termination_date' => parseDate($data['withdrawal_date']),
                        'termination_duration' => $data['duration_days'],
                        'termination_requested_by' => auth()->user()->id,
                        'terminate_status' => 1,
                    ]);
                }

                // dd("test");
                $Document_data = [
                    'investor_id' => $investorId,
                    'company_id' => $data['company_id'],
                    'investor_agreement_template_id' => $this->investorAgreementRepository->getActiveIdBytype(5),
                    'investor_agreement_type_id' => 5,
                    'added_by' => auth()->user()->id,
                    'applied_investments' => $appliedInvestments,
                    'investment_id' => 0,
                ];
                // dd("test");

                $document = $this->investmentContractDocumentService->createInvestorDocument($investorId, $data['company_id'], $Document_data);
                // dd($document);
                $ledger_data = [
                    'investment_contract_document_id' => $document->id,
                    'investor_id' => $investorId,
                    'company_id' => $data['company_id'],
                    'investor_transaction_type_id' => '4',
                    'transaction_amount' => $data['withdrawal_amount'],
                    'is_credit' => 0,
                    'transaction_date' => parseDate($data['requested_date']),
                    'added_by' => auth()->user()->id,
                    'investment_id' => 0,
                    'withdrawal_status' => 1,
                    'requested_date' => parseDate($data['requested_date']),
                    'duration_days' => $data['duration_days'],
                    'withdrawal_date' => parseDate($data['withdrawal_date']),
                    'withdrawal_month_profit' => $total_profit
                ];
                $ledger = $this->investorLedgerRepo->create($ledger_data);
            } else {
                $Document_data = [
                    'investor_id' => $investorId,
                    'company_id' => $data['company_id'],
                    'investor_agreement_template_id' => $this->investorAgreementRepository->getActiveIdBytype(3),
                    'investor_agreement_type_id' => 3,
                    'added_by' => auth()->user()->id,
                    'applied_investments' => $appliedInvestments,
                    'investment_id' => 0,
                ];
                $document = $this->investmentContractDocumentService->createInvestorDocument($investorId, $data['company_id'], $Document_data);

                $ledger_data = [
                    'investment_contract_document_id' => $document->id,
                    'investor_id' => $investorId,
                    'company_id' => $data['company_id'],
                    'investor_transaction_type_id' => '3',
                    'transaction_amount' => $data['withdrawal_amount'],
                    'is_credit' => 0,
                    'transaction_date' => parseDate($data['requested_date']),
                    'added_by' => auth()->user()->id,
                    'investment_id' => 0,
                    'withdrawal_status' => 1,
                    'requested_date' => parseDate($data['requested_date']),
                    'duration_days' => $data['duration_days'],
                    'withdrawal_date' => parseDate($data['withdrawal_date']),
                    'withdrawal_month_profit' => $total_profit
                ];
                $ledger = $this->investorLedgerRepo->create($ledger_data);
            }

            // --- 3. Create the bifurcation records against whichever ledger was just made ---
            foreach ($investmentUpdates as $investmentId => $vals) {
                // dd($vals);
                $partialWithdrawalData = [
                    'investment_id' => $investmentId,
                    'ledger_id' => $ledger->id,
                    'company_id' => $data['company_id'],
                    'withdrawal_amount' => $vals['withdrawalAmount'],
                    'previous_amount' => $vals['availableBalance'],
                    'balance_amount' => $vals['balance_investment_amount'],
                    'added_by' => auth()->user()->id,
                    'requested_date' => parseDate($data['requested_date']),
                    'withdrawal_date' => parseDate($data['withdrawal_date']),
                    'duration_days' => $data['duration_days'],
                    'withdrawal_month_profit' => $vals['withdrawal_month_profit'] ?? 0,
                    'balance_to_pay' => $vals['withdrawalAmount']
                ];
                $this->investorLedgerRepo->createPartialWithdrawal($partialWithdrawalData);
            }
        });
    }

    public function getPartialWithdrawals(array $filters = [])
    {
        $query = $this->investorRepo->getPartialWithdrawalsQuery($filters);

        $columns = [
            ['data' => 'DT_RowIndex', 'name' => 'id'],
            ['data' => 'investor_name', 'name' => 'investor_name'],
            ['data' => 'action', 'name' => 'action', 'orderable' => true, 'searchable' => true],
        ];

        return datatables()
            ->of($query)
            ->addIndexColumn()
            ->addColumn('investor_name', function ($row) {
                $name = $row->investor
                    ? ($row->investor->investor_name ?? '-') . ' - ' . ($row->investor->investor_code ?? '-')
                    : '-';

                $url = route('investor.show', $row->investor->id); // adjust route name if needed

                return "<a href='{$url}' title='View Investor Details' target='_blank' class='text-primary text-decoration-none'>
                    <strong class='text-capitalize'>{$name}</strong>
                </a>";
            })
            ->addColumn('status', function ($row) {
                switch ($row->withdrawal_status) {
                    case 1:
                        return '<span class="badge badge-warning">Requested</span>';
                    case 2:
                        return '<span class="badge badge-info">Approved</span>';
                    case 3:
                        return '<span class="badge badge-success">Withdrawal Done</span>';
                    default:
                        return '<span class="badge badge-secondary">Not Requested</span>';
                }
            })
            ->addColumn('transaction_amount', function ($row) {
                return $row->transaction_amount
                    ? number_format($row->transaction_amount)
                    : '-';
            })
            ->addColumn('transaction_type', function ($row) {
                $typeId = $row->transactionType->id ?? null;
                $typeName = $row->transactionType->transaction_type ?? ' - ';

                if ($typeId == 3) {
                    return '<span class="badge bg-warning">' . $typeName . '</span>';
                } elseif ($typeId == 4) {
                    return '<span class="badge bg-danger">' . $typeName . '</span>';
                }

                return '<span class="badge bg-secondary">' . $typeName . '</span>';
            })
            ->addColumn('requested_date', function ($row) {
                return $row->requested_date
                    ? getFormattedDate($row->requested_date)
                    : '-';
            })

            ->addColumn('duration_days', fn($row) => $row->duration_days ?? '-')
            ->addColumn('withdrawal_date', function ($row) {
                return $row->withdrawal_date
                    ? getFormattedDate($row->withdrawal_date)
                    : '-';
            })
            ->addColumn('company_name', fn($row) => $row->company->company_name ?? '-')
            ->addColumn('added_by', function ($row) {
                return $row->addedBy
                    ? $row->addedBy->first_name . ' ' . $row->addedBy->last_name
                    : '-';
            })

            ->addColumn('action', function ($row) {
                $action = ' ';
                if (auth()->user()->hasAnyPermission(['investor.withdrawal'], $row->company_id) && $row->investor_transaction_type_id == 3 && $row->withdrawal_status == 1) {
                    $action .= '<a href="' . route('investor.partial-withdrawals.edit', $row->id) . '" class="btn btn-info btn-sm mr-1" ><i class="fas fa-pencil-alt"></i></a>';
                }
                if (auth()->user()->hasAnyPermission(['investor.withdrawal-approve'], $row->company_id) && $row->withdrawal_status == 1 &&  $row->investor_transaction_type_id == 3) {
                    $action .= '<button
                    class="btn btn-success btn-sm open-approval-modal" title="Approve Withdrawal"
                    data-id="' . $row->id . '">
                    <i class="fas fa-check"></i>
                </button>';
                }
                // Delete termination / withdrawal
                if (
                    auth()->user()->hasAnyPermission(['investor.withdrawal-delete'], $row->company_id) &&
                    $row->investor_transaction_type_id == 4
                    && $row->withdrawal_status == 1
                ) {
                    $action .= '<button
                        type="button"
                        class="btn btn-danger btn-sm delete-withdrawal"
                        title="Delete Termination"  onclick="confirmDelete(' . $row->id . ')"
                        data-id="' . $row->id . '">
                        <i class="fas fa-trash"></i>
                    </button>';
                }
                return $action;
            })
            ->rawColumns(['transaction_amount', "added_by", 'company_name', 'transaction_type', 'requested_date', 'withdrawal_date', 'investor_name', 'action', 'status'])
            ->with(['columns' => $columns])
            ->toJson();
    }
    public function getPartialWithdrawalForEdit($id)
    {
        $ledger = $this->investorLedgerRepo->find($id);

        if (!$ledger || (int) $ledger->withdrawal_status !== 1) {
            return null; // let the controller decide how to respond (404, redirect, etc.)
        }

        $investor = $this->investorRepo->find($ledger->investor_id);
        $bifurcations = $this->investorLedgerRepo->getBifurcationsByLedgerId($id);
        // dd($bifurcations);

        return [
            'ledger'       => $ledger,
            'investor'     => $investor,
            'bifurcations' => $bifurcations,
        ];
    }

    public function updatePartialWithdrawal($ledgerId, array $data)
    {
        // dd($data);
        return DB::transaction(function () use ($ledgerId, $data) {

            $ledger = $this->investorLedgerRepo->find($ledgerId);

            if (!$ledger || (int) $ledger->withdrawal_status !== 1) {
                throw new \Exception('Only pending partial withdrawal requests can be edited.');
            }

            $investorId = $ledger->investor_id;

            // Only keep investments that were actually selected with a valid amount
            $investments = collect($data['investments'] ?? [])
                ->filter(function ($row) {
                    return isset($row['selected'])
                        && $row['selected'] == '1'
                        && isset($row['amount'])
                        && $row['amount'] !== ''
                        && (float) $row['amount'] > 0;
                })
                ->toArray();

            $appliedInvestments = json_encode(array_keys($investments));

            // dd($appliedInvestments);

            // --- 1. Snapshot old bifurcations for this ledger, keyed by investment ---
            $oldBifurcations = $this->investorLedgerRepo->getBifurcationsByLedgerId($ledgerId);
            $oldBifurcationsByInvestment = collect($oldBifurcations)->keyBy('investment_id');
            $newInvestmentIds = array_keys($investments);

            // dd($newInvestmentIds);

            // --- 2. Revert investments that were dropped from the selection ---
            foreach ($oldBifurcations as $old) {
                if (!in_array($old->investment_id, $newInvestmentIds)) {

                    $stillReferenced = $this->investorLedgerRepo->investmentHasOtherActiveBifurcation($old->investment_id, $ledgerId);

                    $investment = $this->investmentRepository->find($old->investment_id);

                    // dd($stillReferenced);
                    if (!$stillReferenced) {
                        // dd($investment);

                        $this->investmentRepository->update($old->investment_id, [
                            'has_partial_withdrawal'     => 0,
                            'terminate_status'            => 0,
                            'termination_requested_date'  => null,
                            'termination_date'            => null,
                            'termination_duration'        => null,
                            'termination_requested_by'    => null,
                            'investment_status'           => 0,
                            'investment_amount'           => $investment->investment_amount + $old->withdrawal_amount,
                            'total_withdrawn_amount'      => $investment->total_withdrawn_amount - $old->withdrawal_amount,
                        ]);
                    } else {
                        // dd($stillReferenced);
                        // PARTIAL revert
                        $this->investmentRepository->update($old->investment_id, [
                            'investment_amount'          => $investment->investment_amount + $old->withdrawal_amount,
                            'total_withdrawn_amount'     => $investment->total_withdrawn_amount - $old->withdrawal_amount,
                        ]);
                    }
                }
            }

            // --- 3. Clear old bifurcation rows for this ledger (recreated in step 6) ---
            $this->investorLedgerRepo->deleteBifurcationsByLedgerId($ledgerId);

            // --- 4. Update each currently-selected investment's balance/withdrawn totals ---
            // (delta-adjusted against what THIS ledger previously contributed, mirrors create's step 1)
            $investmentUpdates = [];
            $total_profit = 0;
            foreach ($investments as $investmentId => $row) {

                $withdrawalAmount = $row['amount'];
                $availableBalance = $row['available_amount'];
                $balance_investment_amount = $availableBalance - $withdrawalAmount;
                $total_profit += $row['profit'];

                $previouslyWithdrawn = optional($oldBifurcationsByInvestment->get($investmentId))->withdrawal_amount ?? 0;
                $totalWithdrawnAmount = getTotalWithdrawnAmount($investmentId) - $previouslyWithdrawn + $withdrawalAmount;

                $investmentData = [
                    'has_partial_withdrawal' => 1,
                    'investment_amount' => $balance_investment_amount,
                    'total_withdrawn_amount' => $totalWithdrawnAmount,
                    'withdrawal_month_profit' => $row['profit']
                ];

                $isTermination = abs($availableBalance - $withdrawalAmount) < 0.01;
                $investmentData['investment_status'] = ($isTermination && !empty($data['requested_date']) && !empty($data['withdrawal_date'])) ? 1 : 0;

                $this->investmentRepository->update($investmentId, $investmentData);

                $investmentUpdates[$investmentId] = [
                    'withdrawalAmount' => $withdrawalAmount,
                    'availableBalance' => $availableBalance,
                    'balance_investment_amount' => $balance_investment_amount,
                ];
            }

            // --- 5. Investor-level termination check — same branch structure as create() ---
            $checkTermination = $this->checkIftermination($investorId, $data['company_id']);

            if ($checkTermination) {
                foreach ($investments as $investmentId => $row) {
                    $this->investmentRepository->update($investmentId, [
                        'termination_requested_date' => parseDate($data['requested_date']),
                        'termination_date'            => parseDate($data['withdrawal_date']),
                        'termination_duration'        => $data['duration_days'],
                        'termination_requested_by'    => auth()->user()->id,
                        'terminate_status'             => 1,
                    ]);
                }

                $Document_data = [
                    'investor_id' => $investorId,
                    'company_id' => $data['company_id'],
                    'investor_agreement_template_id' => $this->investorAgreementRepository->getActiveIdBytype(5),
                    'investor_agreement_type_id' => 5,
                    'added_by' => auth()->user()->id,
                    'applied_investments' => $appliedInvestments,
                    'investment_id' => 0,
                ];
                $this->investmentContractDocumentRepo->update($ledger->investment_contract_document_id, $Document_data);

                $ledgerData = [
                    'company_id' => $data['company_id'],
                    'investor_transaction_type_id' => '4',
                    'transaction_amount' => $data['withdrawal_amount'],
                    'transaction_date' => parseDate($data['requested_date']),
                    'requested_date' => parseDate($data['requested_date']),
                    'duration_days' => $data['duration_days'],
                    'withdrawal_date' => parseDate($data['withdrawal_date']),
                    'withdrawal_month_profit' => $total_profit

                ];
                $this->investorLedgerRepo->update($ledgerId, $ledgerData);
            } else {
                // Termination may have applied on the original save but no longer does — clear it
                foreach ($investments as $investmentId => $row) {
                    $this->investmentRepository->update($investmentId, [
                        'termination_requested_date' => null,
                        'termination_date'            => null,
                        'termination_duration'        => null,
                        'termination_requested_by'    => null,
                        'terminate_status'             => 0,
                    ]);
                }

                $Document_data = [
                    'investor_id' => $investorId,
                    'company_id' => $data['company_id'],
                    'investor_agreement_template_id' => $this->investorAgreementRepository->getActiveIdBytype(3),
                    'investor_agreement_type_id' => 3,
                    'added_by' => auth()->user()->id,
                    'applied_investments' => $appliedInvestments,
                    'investment_id' => 0,
                ];
                $this->investmentContractDocumentRepo->update($ledger->investment_contract_document_id, $Document_data);

                $ledgerData = [
                    'company_id' => $data['company_id'],
                    'investor_transaction_type_id' => '3',
                    'transaction_amount' => $data['withdrawal_amount'],
                    'transaction_date' => parseDate($data['requested_date']),
                    'requested_date' => parseDate($data['requested_date']),
                    'duration_days' => $data['duration_days'],
                    'withdrawal_date' => parseDate($data['withdrawal_date']),
                    'withdrawal_month_profit' => $total_profit
                ];
                $this->investorLedgerRepo->update($ledgerId, $ledgerData);
            }

            // --- 6. Recreate bifurcation records against this ledger ---
            foreach ($investmentUpdates as $investmentId => $vals) {
                $this->investorLedgerRepo->createPartialWithdrawal([
                    'investment_id' => $investmentId,
                    'ledger_id' => $ledgerId,
                    'company_id' => $data['company_id'],
                    'withdrawal_amount' => $vals['withdrawalAmount'],
                    'previous_amount' => $vals['availableBalance'],
                    'balance_amount' => $vals['balance_investment_amount'],
                    'added_by' => auth()->user()->id,
                    'requested_date' => parseDate($data['requested_date']),
                    'withdrawal_date' => parseDate($data['withdrawal_date']),
                    'duration_days' => $data['duration_days'],
                    'withdrawal_month_profit' => $vals['withdrawal_month_profit']

                ]);
            }

            return $ledger->fresh();
        });
    }

    public function checkIftermination($investorId, $companyId)
    {
        $investments = $this->investmentRepository->getAllByCondition([
            'investor_id' => $investorId,
            'investment_term_type' => 1,
            'company_id' => $companyId,
        ]);
        $allTerminated = true;

        foreach ($investments as $investment) {

            $totalInvested = $investment->total_invested_amount;

            $totalWithdrawn = $investment->total_withdrawn_amount;
            // @dump($totalInvested, $totalWithdrawn);

            // bccomp returns 0 if equal, avoids float drift
            $isFullyWithdrawn = bccomp((string) $totalInvested, (string) $totalWithdrawn, 2) === 0;
            // @dump($isFullyWithdrawn);

            if ($isFullyWithdrawn) {
                // if ($investment->investment_status != 1) {
                //     $investment->update(['investment_status' => 1]);
                // }
                $allTerminated = true;
            } else {
                $allTerminated = false;
            }
        }

        return $allTerminated;
    }

    public function approvePartialWithdrawal($id, $data)
    {
        return DB::transaction(function () use ($id, $data) {

            $ledger = $this->investorLedgerRepo->find($id);

            if (!$ledger) {
                throw new \Exception('Ledger not found');
            }

            // Prevent double approval
            if ($ledger->status == 'approved') {
                throw new \Exception('Already approved');
            }

            // Update ledger status
            $ledger->withdrawal_status = 2;
            $ledger->approved_date = now();
            $ledger->approved_by = auth()->id();
            $ledger->approval_remarks = $data['remarks'] ?? null;
            $ledger->save();



            return true;
        });
    }
    public function getCompanyTotalInvestments($investorId)
    {
        $data = Investment::select(
            'investments.company_id',
            'companies.company_name as company_name',
            \DB::raw('SUM(investment_amount) as total_invested')
        )
            ->join('companies', 'companies.id', '=', 'investments.company_id')
            ->where('investments.investor_id', $investorId)
            ->where('investments.terminate_status', '!=', 2)
            ->where('investments.investment_term_type', 1)
            ->groupBy('investments.company_id', 'companies.company_name')
            ->get();

        $grandTotal = $data->sum('total_invested');

        return [
            'companies'   => $data,
            'grand_total' => $grandTotal,
        ];
        // dd($test);
        // return $test;
    }
    public function deleteTermination($ledgerId)
    {
        return DB::transaction(function () use ($ledgerId) {

            $ledger = $this->investorLedgerRepo->find($ledgerId);

            // Make sure this is a termination
            if ($ledger->investor_transaction_type_id != 4) {
                throw new \Exception('This is not a termination transaction.');
            }

            $partialWithdrawals = PartialWithdrawalBifurcation::where(
                'ledger_id',
                $ledger->id
            )->get();

            foreach ($partialWithdrawals as $withdrawal) {
                // dd($withdrawal);

                $investment = $this->investmentRepository->find($withdrawal->investment_id);

                if (!$investment) {
                    continue;
                }

                // Restore investment amount
                $investment->investment_amount = $withdrawal->previous_amount;

                // Reverse withdrawn amount
                $investment->total_withdrawn_amount =
                    max(
                        0,
                        $investment->total_withdrawn_amount
                            - $withdrawal->withdrawal_amount
                    );

                // Check if this investment has another withdrawal
                $hasOtherWithdrawal = PartialWithdrawalBifurcation::where(
                    'investment_id',
                    $investment->id
                )
                    ->where('ledger_id', '!=', $ledger->id)
                    ->exists();

                $investment->has_partial_withdrawal =
                    $hasOtherWithdrawal ? 1 : 0;

                // Remove termination status
                $investment->terminate_status = 0;
                $investment->termination_requested_date = null;
                $investment->termination_date = null;
                $investment->termination_duration = null;
                $investment->termination_requested_by = null;

                // Restore active status
                $investment->investment_status = 0;

                $investment->save();
            }

            // Delete bifurcation records
            PartialWithdrawalBifurcation::where('ledger_id', $ledger->id)->delete();

            // Delete ledger
            $ledger->delete();

            // Optionally delete/cancel document
            if ($ledger->investment_contract_document_id) {
                InvestmentContractDocuments::where(
                    'id',
                    $ledger->investment_contract_document_id
                )->delete();
            }
        });
    }
}
