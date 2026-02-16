<?php

namespace App\Services\Investment;

use App\Models\WhatsappMessage;
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
        protected WhatsAppMsgService $whatsApp
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
            // $templateId = '291843';
            // $templateId_ar = '291927';

            // $phone = $investor->investor_mobile ?? null;
            // $phone = preg_replace('/[^0-9]/', '', $phone);

            // $variables = [
            //     'investor_name' => $investor->investor_name ?? 'Investor',
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
            //         'templateVariable-invesor-1' => $variables['investor_name']
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


            return response()->json([
                'status'   => 'success',
                // 'whatsapp' => $response,
            ]);
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
            'id_number' => 'required',
            'payment_mode_id' => 'required',
            'investor_address' => 'required',
            'payout_batch_id' => 'required',
            'address_line2' => 'required',
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
            ->addColumn('investor_name', function ($row) {
                $name = $row->investor_name ?? '-';
                $email = $row->investor_email ?? '-';
                $phone = $row->investor_mobile ?? '-';

                $address = $row->investor_address;
                if (!empty($row->address_line2)) {
                    $address .= ', ' . $row->address_line2;
                }
                if (!empty($row->city)) {
                    $address .= ', ' . $row->city;
                }
                if (!empty($row->country_id)) {
                    $address .= ', ' . $row->country?->nationality_name;
                }
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

                if ($row->paymentMode->id == 2) {
                    // $primaryBank = $row->investorBanks->where('is_primary', 1)->first();
                    $bankName = $row->primaryBank->investor_bank_name ?? '-';
                    return $row->paymentMode->payment_mode_name . ' - ' . $bankName;
                }

                return '-';
            })
            ->addColumn('action', function ($row) {
                $action = '<a href="' . route('investor.edit', $row->id) . '" class="btn btn-info btn-sm" ><i class="fas fa-pencil-alt"></i></a>
                <a href="' . route('investor.show', $row->id) . '" class="btn btn-primary btn-sm" ><i class="fas fa-eye"></i></a>';

                if ($row->total_no_of_investments == 0) {
                    $action .= ' <button class="btn btn-danger btn-sm" data-id="' . $row->id . '" onclick="deleteConf(' . $row->id . ')"><i class="fas fa-trash-alt"></i></button>';
                }

                $action .= ' <button class="btn btn-warning btn-sm" data-id="" data-investor-id="' . $row->id . '" data-target="#modal-add-bank" data-toggle="modal" title="Add Bank"><i class="fas fa-university"></i></button>';

                return $action;
            })
            ->rawColumns(['investor_name', 'action'])
            ->with(['columns' => $columns])
            ->toJson();
    }
}
