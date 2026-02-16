<?php

namespace App\Services\Investment;

use App\Models\InvestorPayout;
use App\Models\WhatsappMessage;
use App\Repositories\Investment\InvestmentRepository;
use App\Repositories\Investment\InvestorPaymentDistributionRepository;
use App\Repositories\Investment\InvestorRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class InvestorPaymentDistributionService
{
    public function __construct(
        protected InvestorPaymentDistributionRepository $investorDistRepo,
        protected InvestmentRepository $investmentRepository,
        protected InvestorRepository $investorRepo,
        protected WhatsAppMsgService $whatsApp
    ) {}


    public function getAll()
    {
        return $this->investorDistRepo->all();
    }

    public function getById($id)
    {
        return $this->investorDistRepo->find($id);
    }

    // public function getByName($name)
    // {
    //     return $this->investorDistRepo->getByName($name);
    // }

    public function create(array $data, $user_id = null)
    {
        $this->validate($data);

        $record = $this->investorDistRepo->create($data);
        return $record;
    }

    // public function update($id, array $data)
    // {
    //     $this->validate($data, $id);
    //     $data['updated_by'] = auth()->user()->id;
    //     return $this->investorDistRepo->update($id, $data);
    // }


    public function getPendingList(array $filters = [])
    {
        $query = $this->investorDistRepo->getPendings($filters);

        $columns = [
            ['data' => 'checkbox', 'name' => 'checkbox'],
            ['data' => 'investor_name', 'name' => 'investor.investor_name'],
            ['data' => 'company_name', 'name' => 'investment.company.company_name'],
            ['data' => 'investment_code', 'name' => 'investment.investment_code'],
            ['data' => 'payout_date', 'name' => 'payout_date'],
            ['data' => 'payout_type', 'name' => 'payout_type'],
            ['data' => 'payout_amount', 'name' => 'amount_pending'],
            ['data' => 'payment_mode', 'name' => 'payment_mode'],
            ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false],
        ];

        return datatables()
            ->of($query)
            ->addColumn('checkbox', function ($row) {
                return '<div class="icheck-primary d-inline">
                            <input type="checkbox" id="ichek' . $row->id . '" class="groupCheckbox"
                                name="investor_payout_id[' . $row->id . ']" value="' . $row->id . '">
                            <label for="ichek' . $row->id . '">
                            </label>
                        </div>';
            })

            ->addColumn('investor_name', function ($row) {
                $investor = $row->investor;

                if (!$investor) return '-';

                return "
                <a href='" . route('investor.show', $investor->id) . "' target='_blank'>
            <strong class='text-capitalize'>{$investor->investor_name}</strong>
            <p class='mb-0 text-primary'>{$investor->investor_email}</p>
            <p class='text-muted small'>
                <i class='fa fa-phone-alt text-danger'></i>
                <span class='font-weight-bold'>{$investor->investor_mobile}</span>
            </p>
            </a>
        ";
            })
            ->addColumn('company_name', fn($row) => ($row->investment->company->company_name) ? "<a href='" . route('company.show', $row->investment->company->id) . "' target='_blank'>" . $row->investment->company->company_name . "</a>" : '-')

            ->addColumn('investment_code', fn($row) => ($row->investment->investment_code) ? "<a href='" . route('investment.show', $row->investment->id) . "' target='_blank'>" . $row->investment->investment_code . "</a>" : '-')
            ->addColumn('payout_date', function ($row) {
                return getPayoutDate($row);
            })

            ->addColumn('payout_type', function ($row) {
                return match ($row->payout_type) {
                    1 => '<span class="badge badge-success">Profit</span>',
                    2 => '<span class="badge badge-info">Commission</span>',
                    3 => '<span class="badge badge-warning">Principal</span>',
                    4 => '<span class="badge badge-secondary">Pending Profit</span>',
                    default => '-',
                };
            })

            ->addColumn('payout_amount', function ($row) {
                return number_format($row->amount_pending, 2);
            })

            ->addColumn('payment_mode', function ($row) {
                $investor = $row->investor;

                if (!$investor || !$investor->paymentMode) return '-';

                if (in_array($investor->paymentMode->id, [1, 4])) {
                    return $investor->paymentMode->payment_mode_name;
                }

                if ($investor->paymentMode->id == 2) {
                    $bankName = $investor->primaryBank->investor_bank_name ?? '-';
                    return $investor->paymentMode->payment_mode_name . ' - ' . $bankName;
                }

                return '-';
            })

            ->addColumn('action', function ($row) {
                return '
                <a class="btn btn-success btn-sm bulktriggerbtn" title="Pay now"
                                data-toggle="modal" data-target="#modal-payout"
                                data-clear-type="single" data-reinvest="0" data-det-id="' . $row->id . '"
                                data-amount="' . $row->amount_pending . '">
                                <i class="fas fa-dollar-sign"></i></a>  <a class="btn btn-secondary btn-sm bulktriggerbtn" title="Re-Invest"
                                data-toggle="modal" data-target="#modal-payout"
                                data-clear-type="single" data-det-id="' . $row->id . '" data-reinvest="1" data-investmentid="' . $row->investment_id . '"
                                data-amount="' . $row->amount_pending . '">
                                <i class="fas fa-redo"></i></a>';
            })

            ->rawColumns(['investor_name', 'payout_type', 'action', 'checkbox', 'investment_code', 'company_name'])
            ->with(['columns' => $columns])
            ->toJson();
    }

    public function savePayout(array $data)
    {
        $this->validate($data);

        if ($data['method'] == 'bulk') {
            $payoutIds = explode(',', $data['payout_ids']);
        } else {
            $payoutIds = $data['payout_ids'];
        }

        $payoutIds = is_array($payoutIds) ? $payoutIds : [$payoutIds];

        $distributions = [];
        foreach ($payoutIds as $payoutId) {
            $payoutDetails = InvestorPayout::find($payoutId);

            $pendingAmt = 0;
            if ($data['method'] == 'single') {
                $pendingAmt = toNumeric($payoutDetails->amount_pending) - toNumeric($data['paid_amount']);
            }

            $distributions[] = array(
                'payout_id' => $payoutDetails->id,
                'investor_id' => $payoutDetails->investor_id,
                'amount_paid' => $data['paid_amount'] ?? toNumeric($payoutDetails->amount_pending),
                'investment_id' => $payoutDetails->investment_id,
                'paid_company_id' => $data['paid_company_id'] ?? null,
                // 'is_processed' => $pendingAmt == 0 ? 1 : 0,
                'paid_date' => $data['paid_date'],
                'paid_mode_id' => $data['paid_mode'] ?? 0,
                'paid_bank' => $data['paid_bank'] ?? null,
                'paid_cheque_number' => $data['paid_cheque_number'] ?? null,
                'payment_remarks' => $data['payment_remarks'] ?? null,
                'paid_by' => auth()->user()->id,
            );
        }

        $distr_data = DB::transaction(function () use ($distributions) {
            // DB::enableQueryLog();
            // $paidIds = $this->investorDistRepo->updateMany($paymentdet);

            $distributionDatas = $this->investorDistRepo->createMany($distributions);
            // dd($distributionDatas);
            foreach ($distributionDatas as $distributionData) {



                $payoutData = InvestorPayout::find($distributionData->payout_id);

                $payoutDataArr = $payoutData;
                $balance = $payoutDataArr->amount_pending - $distributionData->amount_paid;
                // payout update
                $payoutDataArr->amount_paid = $payoutDataArr->amount_paid + $distributionData->amount_paid;
                $payoutDataArr->amount_pending = $balance;
                $payoutDataArr->is_processed = $balance == 0 ? 1 : 0;
                $payoutDataArr->update();


                // dd($payoutData);





                // investment update
                $investment = updateInvestmentOnDistribution($payoutData, $distributionData);

                // referral update
                if ($payoutData->payout_type == 2) {
                    $refComm = refCommUpdateOnDistribution($payoutData, $distributionData);
                }


                // investor update
                $investor = investorUpdateOnDistribution($payoutData, $distributionData);
            }


            return $distributionDatas;
        });

        // if ($data['reinvest'] != 1) {
        //     foreach ($distr_data as $distributionData) {
        //         $this->sendDistributionMessages($distributionData);
        //     }
        // }

        return $distr_data;
    }

    private function validate(array $data, $id = null)
    {
        $validator = Validator::make($data, [

            'paid_date' => 'required',
            'paid_mode' => 'required',
        ], [

            'referral_commission_perc.required' => 'Date is required.',
            'paid_mode.required' => 'Payment mode is required.',

        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    public function getDistributedList(array $filters = [])
    {
        $query = $this->investorDistRepo->getDistributedList($filters);

        $columns = [
            ['data' => 'investor_name', 'name' => 'investor_name'],
            ['data' => 'company_name', 'name' => 'company_name'],
            ['data' => 'paid_date', 'name' => 'paid_date'],
            ['data' => 'payout_type', 'name' => 'payout_type'],
            ['data' => 'amount_paid', 'name' => 'amount_paid'],
            ['data' => 'payment_mode', 'name' => 'payment_mode'],
            ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false],
        ];

        return datatables()
            ->of($query)
            ->addIndexColumn()
            ->addColumn('investor_name', function ($row) {
                $investor = $row->investor;

                if (!$investor) return '-';

                return "
            <strong class='text-capitalize'>{$investor->investor_name}</strong>
            <p class='mb-0 text-primary'>{$investor->investor_email}</p>
            <p class='text-muted small'>
                <i class='fa fa-phone-alt text-danger'></i>
                <span class='font-weight-bold'>{$investor->investor_mobile}</span>
            </p>
        ";
            })

            ->addColumn('company_name', fn($row) => $row->investment?->company?->company_name ?? '-')
            ->addColumn('paid_date', fn($row) => $row->paid_date ?? '-')
            ->addColumn('payout_type', function ($row) {
                return match ($row->investorPayout->payout_type) {
                    1 => '<span class="badge badge-success">Profit</span>',
                    2 => '<span class="badge badge-info">Commission</span>',
                    3 => '<span class="badge badge-warning">Principal</span>',
                    default => '-',
                };
            })

            ->addColumn('amount_paid', function ($row) {
                return number_format($row->amount_paid, 2);
            })

            ->addColumn('payment_mode', function ($row) {

                if (in_array($row->paymentMode?->id, [1, 4])) {
                    $mode = $row->paymentMode?->payment_mode_name;
                } elseif ($row->paymentMode?->id == 2) {
                    $mode = $row->paymentMode?->payment_mode_name . ' - ' . $row->paidBank?->bank_name;
                } elseif ($row->paymentMode?->id == 3) {
                    $mode = $row->paymentMode?->payment_mode_name . ' - ' . $row->paidBank?->bank_name . ' - ' . $row->cheque_no;
                } else {
                    $mode = ' - ';
                }

                return $mode;
            })

            ->rawColumns(['investor_name', 'payout_type'])
            ->with(['columns' => $columns])
            ->toJson();
    }
    public function sendDistributionMessages($distributionData)
    {
        $investor = $distributionData->investor;
        $phone = preg_replace('/[^0-9]/', '', $investor->investor_mobile ?? '');
        $date = Carbon::createFromFormat('Y-m', $distributionData->payout->payout_release_month ?? now()->format('Y-m'));

        $variables = [
            'investor_name' => $investor->investor_name ?? 'Investor',
            'profit' => $distributionData->amount_paid,
            'month' => $date->format('F'),
            'year' => $date->year
        ];

        $templates = [
            'en' => '291926',
            'ar' => '291930',
        ];

        foreach ($templates as $lang => $templateId) {
            $payload = [
                'apiToken' => env('WHATCHIMP_API_KEY'),
                'phone_number_id' => env('WHATSAPP_NUMBER_ID'),
                'template_id' => $templateId,
                'phone_number' => $phone,
                'templateVariable-invesor-1' => $variables['investor_name'],
                'templateVariable-profit-2' => $variables['profit'],
                'templateVariable-month-3' => $variables['month'],
                'templateVariable-year-4' => $variables['year']
            ];

            $response = $this->whatsApp->sendTemplateById($payload);

            $status = isset($response['status']) && $response['status'] == '1' ? 1 : 0;

            WhatsappMessage::create([
                'investor_id' => $investor->id,
                'phone' => $phone,
                'template_id' => $templateId,
                'variables' => json_encode($variables),
                'payload' => json_encode($payload),
                'response' => json_encode($response),
                'status' => $status,
            ]);

            \Log::info("WhatsApp {$lang} response", ['response' => $response]);
        }
    }
}
