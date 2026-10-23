<?php

namespace App\Services;

use App\Models\Contract;
use App\Repositories\Contracts\PayableClearRepository;
use App\Repositories\Contracts\PaymentDetailRepository;
use App\Repositories\Contracts\PaymentRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PayableClearingService
{

    public function __construct(
        protected PaymentDetailRepository $paymentDetRepo,
        protected PaymentRepository $paymentRepo,
        protected PayableClearRepository $payableClearRepo,
    ) {}


    public function getByCondition($contractPaymentDet)
    {
        return $this->payableClearRepo->getByCondition($contractPaymentDet);
    }


    public function getDataTable(array $filters)
    {
        $query = $this->payableClearRepo->getPayables($filters);

        $contract = fn($col) => DB::table('contracts')
            ->select($col)
            ->whereColumn('contracts.id', 'contract_payment_details.contract_id')
            ->limit(1);


        $columns = [
            ['data' => 'checkbox', 'name' => 'checkbox'],
            ['data' => 'project_number', 'name' => 'project_number'],
            ['data' => 'company_name', 'name' => 'company_name'],
            ['data' => 'vendor_name', 'name' => 'vendor_name'],
            ['data' => 'property_name', 'name' => 'property_name'],
            ['data' => 'contract_type', 'name' => 'contract_type'],
            ['data' => 'payment_date', 'name' => 'payment_date'],
            ['data' => 'payment_mode', 'name' => 'payment_mode'],
            ['data' => 'cheque_no', 'name' => 'cheque_no'],
            ['data' => 'payment_amount', 'name' => 'payment_amount'],
            ['data' => 'has_returned', 'name' => 'has_returned'],
            ['data' => 'returned_reason', 'name' => 'returned_reason'],
            ['data' => 'action', 'name' => 'action', 'orderable' => true, 'searchable' => true],
        ];
        // dd($query);
        return datatables()
            ->of($query)
            // ->addIndexColumn()
            ->addColumn('checkbox', function ($row) {
                return '<div class="icheck-primary d-inline">
                            <input type="checkbox" id="ichek' . $row->id . '" class="groupCheckbox"
                                name="payment_detail_id[' . $row->id . ']" value="' . $row->id . '">
                            <label for="ichek' . $row->id . '">
                            </label>
                        </div>';
            })
            ->addColumn('project_number', function ($row) {
                $number = 'P - ' . $row->contract?->project_number ?? '-';
                $type = $row->contract->contract_type->contract_type ?? '-';

                // return "<strong class=''>{$number}</strong><p class='mb-0'><span>{$type}</span></p>
                // </p>";
                $badgeClass = '';

                if ($row->contract?->contract_type_id == 1) {
                    $badgeClass = 'badge badge-df';
                } elseif ($row->contract?->contract_type_id == 2) {
                    $badgeClass = 'badge badge-ff';
                } else {
                    $badgeClass = 'badge badge-secondary';
                }

                return "<strong>{$number}</strong>
            <p class='mb-0'>
                <span class='{$badgeClass}'>{$type}</span>
            </p>";
            })
            ->addColumn('company_name', fn($row) => $row->contract?->company?->company_name ?? '-')
            ->addColumn('vendor_name', fn($row) => $row->contract?->vendor?->vendor_name ?? '-')
            ->addColumn('property_name', fn($row) => $row->contract?->property?->property_name ?? '-')
            ->addColumn('contract_type', fn($row) => $row->contract?->contract_type?->shortcode ?? '-')
            ->addColumn('payment_date', fn($row) => $row->payment_date ?? '-')
            ->addColumn('payment_mode', function ($row) {
                if ($row->payment_mode) {
                    if (in_array($row->payment_mode->id, [1, 4])) {
                        $mode = $row->payment_mode->payment_mode_name;
                    } elseif ($row->payment_mode->id == 2) {
                        $mode = $row->payment_mode->payment_mode_name . ' - ' . $row->bank->bank_name;
                    } elseif ($row->payment_mode->id == 3) {
                        $mode = $row->payment_mode->payment_mode_name . ' - ' . $row->bank->bank_name . ' - ' . $row->cheque_no;
                    } else {
                        $mode = ' - ';
                    }
                } else {
                    $mode = ' - ';
                }

                return $mode;
            })
            ->addColumn('cheque_no', fn($row) => $row->cheque_no ?? '-')
            ->addColumn('has_returned', fn($row) => $row->has_returned ?? '-')
            ->addColumn('composition', fn($row) => getComposition($row->contract_id, $row->id) ?? '-')
            // ->addColumn('payment_amount', function ($row) {

            //     $paid = totalPaidPayable($row->payables);


            //     return toNumeric($row->payment_amount) - $paid;
            // })
            // ->addColumn('payment_amount', function ($row) {
            //     return toNumeric($row->payment_amount) - (float) ($row->paid_total ?? 0);
            // })
            ->addColumn('payment_amount', function ($row) {
                $pending = toNumeric($row->payment_amount) - (float) ($row->paid_total ?? 0);
                $badge = $row->is_discounted
                    ? ' <span class="badge badge-warning">Discounted</span>'
                    : '';
                return $pending . $badge;
            })
            ->addColumn('action', function ($row) {
                $action = '';
                //  $remaining = toNumeric($row->payment_amount)
                //     - (float) ($row->discount_amount ?? 0)
                //     - (float) ($row->paid_total ?? 0);

                // if ($remaining <= 0) {
                //     return '-';
                // }

                // $action .= '<a class="btn btn-success  btn-sm" title="Clear cheque"
                //                 data-toggle="modal" data-target="#modal-clear-payable"
                //                 data-clear-type="single" data-det-id="' . $row->id . '" data-amount="' . (toNumeric($row->payment_amount) - (float) ($row->paid_total ?? 0)) . '" data-mode-id="' . ($row->payment_mode_id ?? '') . '"
                // data-company-id="' . ($row->contract->company_id ?? '') . '"
                // data-bank-id="' . ($row->bank_id ?? '') . '"
                // data-cheque-no="' . e($row->cheque_no ?? '') . '">Clear</a>';

                // $action .= '<a class="btn btn-warning btn-sm" title="Give discount"
                // data-toggle="modal" data-target="#modal-discount"
                // data-det-id="' . $row->id . '"
                // data-amount="' . $remaining . '"><i class="fas fa-percent"></i></a>';


                // return $action ?: '-';
                // $remaining = toNumeric($row->payment_amount)
                //     - (float) ($row->discount_amount ?? 0)
                //     - (float) ($row->paid_total ?? 0);
                $remaining = toNumeric($row->payment_amount) - (float) ($row->paid_total ?? 0);
                $dueDate = \Carbon\Carbon::parse($row->payment_date)->format('d-m-Y');

                $action = '<div class="d-flex flex-nowrap" style="gap:4px;">';
                $action .= '<a class="btn btn-warning btn-sm lossDiscountBtn" title="Loss / Discount"
                data-det-id="' . $row->id . '"
                data-due-date="'.$dueDate.'"
                data-amount="' . $remaining . '"><i class="fas fa-percent"></i></a>';

                $action .= '<a class="btn btn-success btn-sm" title="Clear cheque"
                                data-toggle="modal" data-target="#modal-clear-payable"
                                data-clear-type="single"
                                data-det-id="' . $row->id . '"
                                data-amount="' . $remaining . '"
                                data-mode-id="' . ($row->payment_mode_id ?? '') . '"
                                data-company-id="' . ($row->contract->company_id ?? '') . '"
                                data-bank-id="' . ($row->bank_id ?? '') . '"
                                data-cheque-no="' . e($row->cheque_no ?? '') . '">Clear</a>';

                $action .= '</div>';

                return $action;
            })

            ->orderColumn('project_number', function ($q, $order) use ($contract) {
                $q->orderBy($contract('contracts.project_number'), $order)
                    ->orderBy('contract_payment_details.id', $order);
            })

            ->orderColumn('company_name', function ($q, $order) {
                $q->orderBy(
                    DB::table('contracts')
                        ->join('companies', 'companies.id', '=', 'contracts.company_id')
                        ->select('companies.company_name')
                        ->whereColumn('contracts.id', 'contract_payment_details.contract_id')
                        ->limit(1),
                    $order
                );
            })

            ->orderColumn('vendor_name', function ($q, $order) {
                $q->orderBy(
                    DB::table('contracts')
                        ->join('vendors', 'vendors.id', '=', 'contracts.vendor_id')
                        ->select('vendors.vendor_name')
                        ->whereColumn('contracts.id', 'contract_payment_details.contract_id')
                        ->limit(1),
                    $order
                );
            })

            ->orderColumn('property_name', function ($q, $order) {
                $q->orderBy(
                    DB::table('contracts')
                        ->join('properties', 'properties.id', '=', 'contracts.property_id')
                        ->select('properties.property_name')
                        ->whereColumn('contracts.id', 'contract_payment_details.contract_id')
                        ->limit(1),
                    $order
                );
            })

            ->orderColumn('contract_type', function ($q, $order) {
                $q->orderBy(
                    DB::table('contracts')
                        ->join('contract_types', 'contract_types.id', '=', 'contracts.contract_type_id')
                        ->select('contract_types.shortcode')
                        ->whereColumn('contracts.id', 'contract_payment_details.contract_id')
                        ->limit(1),
                    $order
                );
            })

            ->orderColumn('payment_mode', function ($q, $order) {
                $q->orderBy(
                    DB::table('payment_modes')
                        ->select('payment_mode_name')
                        ->whereColumn('payment_modes.id', 'contract_payment_details.payment_mode_id')
                        ->limit(1),
                    $order
                );
            })
            ->orderColumn('payment_amount', function ($q, $order) {
                $q->orderByRaw("(contract_payment_details.payment_amount - COALESCE(paid_total, 0)) $order");
            })

            ->rawColumns(['checkbox', 'project_number','payment_amount', 'action', 'status'])
            ->with(['columns' => $columns])
            ->toJson();
    }

    public function PayableSave(array $data)
    {
        $this->validate($data);

        if ($data['method'] == 'bulk') {
            $paymentDetIds = explode(',', $data['payment_detail_ids']);
        } else {
            $paymentDetIds = $data['payment_detail_ids'];
        }

        $paymentDetIds = is_array($paymentDetIds) ? $paymentDetIds : [$paymentDetIds];

        $paymentdet = $payable = [];
        foreach ($paymentDetIds as $paymentDetId) {
            $paymentdetails = $this->paymentDetRepo->find($paymentDetId);

            $alreadyPaid = $paymentdetails->payables
                ? $paymentdetails->payables->where('returned_status', 0)->sum('paid_amount')
                : 0;

            $pendingBefore = toNumeric($paymentdetails->payment_amount)
                - (float) $alreadyPaid
                - (float) ($paymentdetails->discount_amount ?? 0);

            if ($data['method'] == 'single') {
                $paidAmount = toNumeric($data['paid_amount']);

                // can't be more than what is pending
                if ($paidAmount > $pendingBefore) {
                    throw new \Exception('Clearing amount cannot be more than the pending amount.');
                }

                $pendingAmt = $pendingBefore - $paidAmount;
            } else {
                $paidAmount = $pendingBefore;
                $pendingAmt = 0;
            }
            $paidMode = $data['paid_mode'] ?? $paymentdetails->payment_mode_id;

            $payable[] = array(
                'contract_id' => $paymentdetails->contract_id,
                'company_id' => $data['company_id'],
                'contract_payment_detail_id' => $paymentDetId,
                'paid_date' => $data['paid_date'],
                'paid_amount' => $paidAmount,
                'pending_amount' => $pendingAmt,
                'paid_by' => auth()->user()->id,
                'paid_mode' => $paidMode,
                'paid_bank' => $data['paid_bank'] ?? $paymentdetails->bank_id,
                // 'paid_cheque_number' => $data['paid_cheque_number'] ?? null,
                'paid_cheque_number' => $paidMode == 3
                    ? ($data['paid_cheque_number'] ?? $paymentdetails->cheque_no)
                : null,
                'bank_reference'             => $paidMode == 1 ? null : ($data['bank_reference'] ?? null),
                'payment_remarks' => $data['payment_remarks'] ?? null,
            );

            $paymentdet[$paymentDetId] = array(
                'paid_status' => ($pendingAmt == 0) ? 1 : 2,
            );
        }
// dd($payable);
        return DB::transaction(function () use ($paymentdet, $payable) {
            // DB::enableQueryLog();
            // dump($payable);
            // dd($paymentdet);
            $paidIds = $this->paymentDetRepo->updateMany($paymentdet);

        $payableIds = $this->payableClearRepo->createMany($payable);

            foreach ($paidIds as $paidId) {
                $detail = $this->paymentDetRepo->find($paidId);

                $arr = array(
                    'has_fully_paid' => ($detail->paid_status == 1) ? 1 : 0,
                    'has_payment_started' => ($detail->paid_status >= 0) ? 1 : 0
                );
                $this->paymentRepo->update($detail->contract_payment_id, $arr);
            }

            return $paidIds;
        });
    }

    private function validate(array $data, $id = null)
    {
        $validator = Validator::make($data, [
            'paid_date' => 'required',
        ]);

        $validator->sometimes('paid_bank', 'required|string|max:255', function ($input) {
            return in_array($input->paid_mode, ['2', '3']);
        });

        $validator->sometimes(['paid_cheque_number', 'paid_amount'], 'required', function ($input) {
            return $input->paid_mode === '3';
        });

        $validator->sometimes('paid_amount', 'required', function ($input) {
            return $input->method === 'single';
        });

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    public function ReturnSave(array $data)
    {
        $paymentDetIds = explode(',', $data['payment_detail_ids']);

        $returns = [];
        foreach ($paymentDetIds as $paymentDetId) {
            // $paymentdetails = $this->paymentDetRepo->find($paymentDetId);

            $returns[$paymentDetId] = array(
                'has_returned' => 1,
                'returned_date' => $data['returned_date'],
                'returned_by' => auth()->user()->id,
                'returned_reason' => $data['returned_reason'] ?? null,
            );
        }
        // dd($returns);
        $paidIds = $this->paymentDetRepo->updateMany($returns);

        return $paidIds;
    }

    public function getClearedList(array $filters)
    {
        $query = $this->payableClearRepo->getClearedData($filters);

        $columns = [
            ['data' => 'project_number', 'name' => 'project_number'],
            ['data' => 'company_name', 'name' => 'company_name'],
            ['data' => 'vendor_name', 'name' => 'vendor_name'],
            ['data' => 'property_name', 'name' => 'property_name'],
            ['data' => 'contract_type', 'name' => 'contract_type'],
            ['data' => 'payment_date', 'name' => 'payment_date'],
            ['data' => 'payment_mode', 'name' => 'payment_mode'],
            ['data' => 'cheque_no', 'name' => 'cheque_no'],
            ['data' => 'bank_name', 'name' => 'bank_name'],
            ['data' => 'paid_amount', 'name' => 'paid_amount'],
            ['data' => 'pending_amount', 'name' => 'pending_amount'],
            ['data' => 'action', 'name' => 'action', 'orderable' => true, 'searchable' => true],
        ];

        return datatables()
            ->of($query)
            ->addIndexColumn()
            ->addColumn('checkbox', function ($row) {
                return '<div class="icheck-primary d-inline">
                            <input type="checkbox" id="ichek' . $row->id . '" class="groupCheckbox"
                                name="payment_detail_id[' . $row->id . ']" value="' . $row->id . '">
                            <label for="ichek' . $row->id . '">
                            </label>
                        </div>';
            })
            ->addColumn('project_number', function ($row) {
                // dump($row);
                $number = 'P - ' . $row->contract->project_number ?? '-';
                $type = $row->contract->contract_type->contract_type ?? '-';

                // return "<strong class=''>{$number}</strong><p class='mb-0'><span>{$type}</span></p>
                // </p>";
                $badgeClass = '';

                if ($row->contract->contract_type_id == 1) {
                    $badgeClass = 'badge badge-df';
                } elseif ($row->contract->contract_type_id == 2) {
                    $badgeClass = 'badge badge-ff';
                } else {
                    $badgeClass = 'badge badge-secondary';
                }

                return "<strong>{$number}</strong>
            <p class='mb-0'>
                <span class='{$badgeClass}'>{$type}</span>
            </p>";
            })
            ->addColumn('company_name', fn($row) => $row->contract->company->company_name ?? '-')
            ->addColumn('vendor_name', fn($row) => $row->contract->vendor->vendor_name ?? '-')
            ->addColumn('property_name', fn($row) => $row->contract->property->property_name ?? '-')
            ->addColumn('paid_date', fn($row) => $row->paid_date ?? '-')
            ->addColumn('payment_mode', function ($row) {

                if (in_array($row->paidMode?->id, [1, 4])) {
                    $mode = $row->paidMode?->payment_mode_name;
                } elseif ($row->paidMode?->id == 2) {
                    $mode = $row->paidMode?->payment_mode_name . ' - ' . $row->paidBank?->bank_name;
                } elseif ($row->paidMode?->id == 3) {
                    $mode = $row->paidMode?->payment_mode_name . ' - ' . $row->paidBank?->bank_name . ' - ' . $row->cheque_no;
                } else {
                    $mode = ' - ';
                }

                return $mode;
            })
            // ->addColumn('payment_mode', fn($row) => $row->cheque_no ?? '-')
            ->addColumn('paid_amount', function ($row) {

                if ($row->returned_status == 1) {
                    // dump('-' . $row->paid_amount);
                    return  '-' . $row->paid_amount;
                } else {
                    return $row->paid_amount ?? '-';
                }
            })
            ->addColumn('pending_amount', fn($row) => $row->pending_amount ?? '-')
            ->addColumn('payment_date', fn($row) => Carbon::parse($row->contractPaymentDetail?->payment_date)->format('d-m-Y') ?? '-')
            ->addColumn('composition', fn($row) => getComposition($row->contract_id, $row->contract_payment_detail_id) ?? '-')
            ->rawColumns(['checkbox', 'project_number', 'action', 'status'])
            ->with(['columns' => $columns])
            ->toJson();
    }


    public function terminateContractPayables(array $data)
    {
        try {
            $createData = [
                'contract_id' => $data['contract_id'],
                'contract_payment_detail_id' => 0,
                'paid_date' => $data['terminated_date'],
                'paid_amount' => $data['balance_amount'],
                'paid_mode' => $data['paid_mode'], // Assuming 1 is for termination or a specific mode
                'paid_bank' => $data['paid_bank'] ?? null,
                'paid_cheque_number' => $data['paid_cheque_number'] ?? null,
                'paid_by' => $data['user_id'] ?? auth()->user()->id,
                'company_id' => $data['company_id'],
                'returned_status' => 1,
            ];
            $this->payableClearRepo->create($createData);
        } catch (\Exception $e) {
            logger('Error terminating contract payables: ' . $e->getMessage(), ['exception' => $e]);
            throw $e; // rethrow the exception after logging
        }
    }
}
