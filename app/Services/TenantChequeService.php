<?php

namespace App\Services;

use App\Models\AgreementPaymentDetail;
use App\Repositories\Agreement\AgreementDocRepository;
use App\Repositories\Agreement\AgreementPaymentRepository;
use App\Repositories\TenantChequeRepository;
use DateTime;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TenantChequeService
{
    public function __construct(
        protected TenantChequeRepository $tenantChequeRepository,
        protected AgreementDocRepository $agreementRepository,
        protected AgreementPaymentRepository $agreementPaymentRepository
    ) {}





    // public function getDataTable(array $filters = [])
    // {
    //     $query = $this->tenantChequeRepository->getQuery($filters);
    //     // dd($query);

    //     $columns = [
    //         ['data' => 'DT_RowIndex', 'name' => 'agreement_payment_details.id'],
    //         ['data' => 'project_number', 'name' => 'project_number'],
    //         ['data' => 'tenant_name', 'name' => 'tenant_name'],
    //         // ['data' => 'project_number', 'name' => 'project_number'],
    //         // ['data' => 'business_type', 'name' => 'business_type'],
    //         // ['data' => 'tenant_details', 'name' => 'tenant_details'],
    //         // ['data' => 'start_date', 'name' => 'start_date'],
    //         // ['data' => 'end_date', 'name' => 'end_date'],
    //         // ['data' => 'is_signed_agreement_uploaded', 'name' => 'is_signed_agreement_uploaded'],
    //         // ['data' => 'agreement_status', 'name' => 'agreement_status'],
    //         // ['data' => 'created_at', 'name' => 'created_at'],
    //         // ['data' => 'action', 'name' => 'action', 'orderable' => true, 'searchable' => true],
    //     ];
    //     // dd("test");

    //     return datatables()

    //         ->of($query)
    //         ->addIndexColumn()
    //         ->addColumn('checkbox', function ($row) {

    //             return '
    //     <div class="icheck-primary d-inline">
    //         <input type="checkbox"
    //                class="groupCheckbox"
    //                name="installment_id[]"
    //                id="ichek' . $row->apd_id . '"
    //                value="' . $row->apd_id . '">
    //         <label for="ichek' . $row->apd_id . '"></label>
    //     </div>';
    //         })
    //         ->addColumn('project_number', function ($row) {
    //             // dd($row);
    //             $number = 'P - ' . $row->project_number ?? '-';
    //             $type = $row->contract_type ?? '-';

    //             // return "<strong class=''>{$number}</strong><p class='mb-0'><span>{$type}</span></p>
    //             // </p>";
    //             $badgeClass = '';
    //             if ($row->contract_type_id == 1) {
    //                 $badgeClass = 'badge badge-df text-dark';
    //             } elseif ($row->contract_type_id == 2) {
    //                 $badgeClass = 'badge badge-ff text-dark';
    //             } else {
    //                 $badgeClass = 'badge badge-secondary';
    //             }

    //             return "<strong>{$number}</strong>
    //         <p class='mb-0'>
    //             <span class='{$badgeClass}'>{$type}</span>
    //         </p>";
    //         })
    //         ->addColumn('tenant_details', function ($row) {
    //             $name = $row->tenant_name ?? '-';
    //             $email = $row->tenant_email ?? '-';
    //             $phone = $row->tenant_mobile ?? '-';

    //             return "<strong class='text-capitalize'>{$name}</strong><p class='mb-0 text-primary'>{$email}</p><p class='text-muted small'>
    //                 <i class='fa fa-phone-alt text-danger'></i> <span class='font-weight-bold'>{$phone}</span>
    //             </p>";
    //         })
    //         ->addColumn('property_name', fn($row) => $row->property_name ?? '-')
    //         ->addColumn('unit_number', fn($row) => $row->unit_number ?? '-')
    //         ->addColumn('payment_date', fn($row) => $row->payment_date ?? '-')
    //         ->addColumn('cheque_number', fn($row) => $row->cheque_number ?? '-')
    //         ->addColumn('payment_mode_name', function ($row) {
    //             $text = $row->paymentMode ? $row->paymentMode->payment_mode_name : '';

    //             if (!empty($row->bank_id) && $row->bank) {
    //                 $text .= ' - ' . ucfirst($row->bank->bank_name);
    //             }

    //             if (!empty($row->cheque_number)) {
    //                 $text .= ' - ' . ucfirst($row->cheque_number);
    //             }

    //             return $text;
    //         })

    //         ->addColumn('payment_amount', fn($row) => $row->payment_amount ?? '-')
    //         ->addColumn('installment_name', function ($row) {
    //             $agreementUnitId = $row->agreement_unit_id;

    //             $installments = AgreementPaymentDetail::where('agreement_unit_id', $agreementUnitId)
    //                 ->orderBy('payment_date')
    //                 ->get();

    //             $current = 0;
    //             $total = $installments->count();

    //             foreach ($installments as $index => $installment) {
    //                 if ($installment->payment_date == $row->payment_date) {
    //                     $current = $index + 1;
    //                     break;
    //                 }
    //             }

    //             return "{$current}/{$total}";
    //         })
    //         ->addColumn('action', function ($row) {

    //             $action = '';



    //             $action .= '<a class="btn btn-success mr-1 btn-sm" title="Clear cheque" data-toggle="modal" data-target="#modal-success">Clear</a>';

    //             $action .= '<a class="btn btn-danger btn-sm" title="return" data-toggle="modal" data-target="#modal-return-cheque">Return</a>';





    //             return $action ?: '-';
    //         })


    //         ->rawColumns(['checkbox', 'tenant_details', 'action', 'project_number', 'business_type'])
    //         // ->rawColumns(['action'])
    //         ->with(['columns' => $columns])
    //         ->toJson();
    // }
    public function getDataTable(array $filters = [])
    {
        $query = $this->tenantChequeRepository->getQuery($filters);
        // dd($query);

        $columns = [
            ['data' => 'DT_RowIndex', 'name' => 'agreement_payment_details.id'],
            ['data' => 'project_number', 'name' => 'project_number'],
            ['data' => 'tenant_name', 'name' => 'tenant_name'],
            ['data' => 'property_name', 'name' => 'property_name'],
            ['data' => 'unit_number', 'name' => 'unit_number'],
            ['data' => 'subunit_no', 'name' => 'subunit_no'],
            ['data' => 'tenant_details', 'name' => 'tenant_details'],
            ['data' => 'payment_date', 'name' => 'payment_date'],
            ['data' => 'payment_mode_name', 'name' => 'payment_mode_name'],
            ['data' => 'payment_amount', 'name' => 'payment_amount'],
            ['data' => 'installment_name', 'name' => 'installment_name'],
            ['data' => 'status', 'name' => 'status'],
            // ['data' => 'action', 'name' => 'action', 'orderable' => true, 'searchable' => true],
        ];
        // dd("test");

        return datatables()

            ->of($query)
            ->addIndexColumn()
            ->addColumn('checkbox', function ($row) {

                return '
        <div class="icheck-primary d-inline">
            <input type="checkbox"
                   class="groupCheckbox"
                   name="installment_id[]"
                   id="ichek' . $row->id . '"
                   value="' . $row->id . '">
            <label for="ichek' . $row->id . '"></label>
        </div>';
            })
            ->addColumn('project_number', function ($row) {
                // dd($row);
                $number = 'P - ' . $row->agreement->contract->project_number ?? '-';
                $type = $row->agreement->contract->contract_type->contract_type ?? '-';
                $b_type_id = $row->agreement->contract->contract_unit->business_type;
                $b_type = $row->agreement->contract->contract_unit->business_type();

                // return "<strong class=''>{$number}</strong><p class='mb-0'><span>{$type}</span></p>
                // </p>";
                $badgeClass = '';
                if ($row->agreement->contract->contract_type_id == 1) {
                    $badgeClass = 'badge badge-df text-dark';
                } elseif ($row->agreement->contract->contract_type_id == 2) {
                    $badgeClass = 'badge badge-ff text-dark';
                } else {
                    $badgeClass = 'badge badge-secondary';
                }
                // Business type color
                $businessClass = ($b_type_id == 1) ? 'text-olive' : 'text-cyan';

                return "<strong>{$number}</strong>
            <p class='mb-0'>
                <span class='{$badgeClass}'>{$type}</span>
            </p>
           <strong class='{$businessClass}'>
            {$b_type}
        </strong>";
            })
            ->addColumn('tenant_name', function ($row) {

                $name = $row->agreement->tenant->tenant_name ?? '-';
                $email = $row->agreement->tenant->tenant_email ?? '-';
                $phone = $row->agreement->tenant->tenant_mobile ?? '-';

                return "<strong class='text-capitalize'>{$name}</strong><p class='mb-0 text-primary'>{$email}</p><p class='text-muted small'>
                    <i class='fa fa-phone-alt text-danger'></i> <span class='font-weight-bold'>{$phone}</span>
                </p>";
            })
            ->addColumn('property_name', fn($row) => $row->agreement->contract->property->property_name ?? '-')
            ->addColumn('unit_number', function ($row) {
                // Find the agreement unit that matches this payment detail
                $unit = $row->agreement->agreement_units->firstWhere('id', $row->agreement_unit_id);

                return $unit && $unit->contractUnitDetail
                    ? $unit->contractUnitDetail->unit_number
                    : '-';
            })
            ->addColumn('subunit_no', function ($row) {
                // Find the agreement unit that matches this payment detail
                $unit = $row->agreement->agreement_units->firstWhere('id', $row->agreement_unit_id);

                return $unit && $unit->contractSubunitDetail
                    ? $unit->contractSubunitDetail->subunit_no
                    : '-';
            })
            ->addColumn('payment_date', function ($row) {
                if (!$row->payment_date) {
                    return '-';
                }

                return Carbon::parse($row->payment_date)->format('d-m-Y');
            })

            // ->addColumn('cheque_number', fn($row) => $row->cheque_number ?? '-')
            ->addColumn('payment_mode_name', function ($row) {
                $text = $row->paymentMode ? $row->paymentMode->payment_mode_name : '';

                if (!empty($row->bank_id) && $row->bank) {
                    $text .= ' - ' . ucfirst($row->bank->bank_name);
                }

                if (!empty($row->cheque_number)) {
                    $text .= ' - ' . ucfirst($row->cheque_number);
                }

                return $text;
            })

            ->addColumn('payment_amount', fn($row) => getReceivableAmount($row->id))
            ->addColumn('installment_name', function ($row) {
                // dd($row->transaction_type);

                if (empty($row->agreement_unit_id)) {
                    return match ((int) $row->transaction_type) {
                        2 => '<span class="badge bg-danger">Termination Payback</span>',
                        1 => '<span class="badge bg-success">Termination Receive</span>',
                        default => '<span class="badge bg-secondary">-</span>',
                    };
                }
                $agreementUnitId = $row->agreement_unit_id;

                $installments = AgreementPaymentDetail::where('agreement_unit_id', $agreementUnitId)
                    ->orderBy('payment_date')
                    ->get();
                // dd($installments);

                $current = 0;
                $total = $installments->count();

                foreach ($installments as $index => $installment) {
                    if ($installment->payment_date == $row->payment_date) {
                        $current = $index + 1;
                        break;
                    }
                }

                return "{$current}/{$total}";
            })
            ->addColumn('status', function ($row) {
                // If any payment has bounced, show Bounced
                if ($row->has_bounced) {
                    return '<span class="badge bg-danger">Bounced</span>';
                }

                // Otherwise, check the is_payment_received status
                switch ($row->is_payment_received) {
                    case 0:
                        return '<span class="badge bg-warning">Pending</span>';
                    case 1:
                        return '<span class="badge bg-success">Paid</span>';
                    default:
                        return '<span class="badge bg-secondary">-</span>';
                }
            })

            ->addColumn('action', function ($row) {

                $action = '';
                if ($row->has_bounced) {
                    $reason = $row->bounced_reason ?? '-';
                    $date = $row->bounced_date ? \Carbon\Carbon::parse($row->bounced_date)->format('d-m-Y') : '-';

                    $action .= '<a class="btn btn-danger btn-sm bouncedInfoBtn m-1"
                        data-reason="' . htmlspecialchars($reason, ENT_QUOTES) . '"
                        data-date="' . $date . '"
                        data-bs-toggle="modal"
                        data-bs-target="#bouncedChequeModal">
                         <i class="fas fa-exclamation-triangle"></i>
                    </a>';
                }



                $action .= '<a class="btn btn-success mr-1 btn-sm clearChequeBtn m-1" title="Clear cheque"
                 data-date="' . $row->payment_date . '" data-id="' . $row->id . '" data-form="single"
                 data-amount = "' . getReceivableAmount($row->id) . '" data-payment-mode ="' . $row->payment_mode_id . '"
                  data-bank-id="' . $row->bank_id . '" data-cheque-number="' . $row->cheque_number . '"

                  data-toggle="modal" data-target="#modal-single-clear">Clear</a>';









                return $action ?: '-';
            })


            ->rawColumns(['checkbox', 'tenant_name', 'action', 'project_number', 'business_type', 'status', 'installment_name'])
            // ->rawColumns(['action'])
            ->with(['columns' => $columns])
            ->toJson();
    }
    // public function clearReceivable(array $data)
    // {
    //     DB::transaction(function () use ($data) {

    //         $payment = $this->tenantChequeRepository->getPaymentDetailById($data['payment_detail_id']);
    //         if (!$payment) {
    //             throw new \Exception('Payment detail not found');
    //         }

    //         if (empty($data['paid_mode_id'])) {
    //             $data['paid_mode_id']       = $payment->paid_mode_id;
    //             $data['paid_bank_id']       = $payment->paid_bank_id;
    //             $data['paid_cheque_number'] = $payment->paid_cheque_number;
    //         }

    //         $this->validate($data);

    //         // dd();
    //         $clearingAmount = round(floatval($data['paid_amount']), 2);
    //         $existingAmount = round(floatval($payment->payment_amount), 2);

    //         if ($existingAmount > $clearingAmount) {
    //             $pendingAmount = $existingAmount - $clearingAmount;
    //             $data['pending_amount'] = $pendingAmount;
    //             $data['is_payment_received'] = 2;
    //         } elseif ($existingAmount == $clearingAmount) {
    //             $data['pending_amount'] = 0;
    //             $data['is_payment_received'] = 1;
    //         }
    //         if (!empty($data['paid_date'])) {
    //             $data['paid_date'] = Carbon::createFromFormat('d-m-Y', $data['paid_date'])->format('Y-m-d');
    //         }
    //         $detail_date = [
    //             'paid_date' => $data['paid_date'],
    //             'paid_amount' => $data['paid_amount'],
    //             'paid_mode_id' => $data['paid_mode_id'],
    //             'paid_bank_id' => $data['paid_bank_id'] ?? null,
    //             "paid_cheque_number" => $data['paid_cheque_number'] ?? null,
    //             // 'paid_by' => auth()->user()->id,
    //             'mode_change_reason' => $data['mode_change_reason'] ?? null,
    //             'is_payment_received' => $data['is_payment_received'],
    //             'pending_amount' => $data['pending_amount'],
    //             'payment_detail_id' => $data['payment_detail_id']

    //         ];

    //         $this->tenantChequeRepository->updatePaymentDetail($detail_date);

    //         $TotalStatus = checkAgreementPayment($payment->agreement_payment_id);


    //         $paymentData = [
    //             'agreement_payment_id'       => $payment->agreement_payment_id,
    //             'has_payment_received'       => 1,
    //             'has_payment_fully_received' => $TotalStatus,
    //         ];
    //         $this->tenantChequeRepository->updatePayment($paymentData);
    //     });
    // }
    public function validate($data)
    {
        // dd($data);
        $validator = Validator::make($data, [
            // 'payment_detail_id'  => ['required', 'exists:agreement_payment_details,id'],
            'paid_date'          => ['required'],
            // 'paid_amount'        => ['required', 'numeric', 'gt:0'],
            // 'paid_mode_id'       => ['required'],
            'paid_bank_id'       => ['required_if:paid_mode_id,2,3'],
            'paid_cheque_number' => ['required_if:paid_mode_id,3'],
            'mode_change_reason' => ['nullable', 'string', 'max:255'],
        ], [
            'paid_bank_id.required_if' =>
            'Bank is required for bank transfer or cheque payments.',
            'paid_cheque_number.required_if' =>
            'Cheque number is required when payment mode is cheque.',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
    public function clearReceivable(array $data)
    {
        DB::transaction(function () use ($data) {

            // Expecting $data['payment_detail_ids'] as array of checked IDs
            $paymentIds = $data['payment_detail_ids'] ?? [];
            if (empty($paymentIds)) {
                throw new \Exception('No payment selected');
            }
            // dd($data);

            foreach ($paymentIds as $paymentId) {

                $payment = $this->tenantChequeRepository->getPaymentDetailById($paymentId);
                if (!$payment) {
                    continue;
                }
                // dd($payment);


                // Use existing mode info if none provided
                $paidModeId = $data['paid_mode_id'] ?? $payment->payment_mode_id;
                $paidBankId = $data['paid_bank_id'] ?? $payment->bank_id;
                $paidCheque = $data['paid_cheque_number'] ?? $payment->cheque_number;
                $paidAmount = $data['paid_amount'] ?? $payment->payment_amount;
                $paidDate = $data['paid_date'];
                // dd($paidAmount);

                if (!empty($paidDate)) {
                    $paidDate = Carbon::createFromFormat('d-m-Y', $paidDate)->format('Y-m-d');
                }

                // Calculate pending amount & payment status
                // $existingAmount = $payment->payment_amount;
                $existingAmount = getReceivableAmount($paymentId);
                // dd($existingAmount);


                if ($existingAmount > $paidAmount) {
                    $pendingAmount = $existingAmount - $paidAmount;
                    $isReceived = 2;
                } else {
                    $pendingAmount = 0;
                    $isReceived = 1;
                }

                $detail_data = [
                    'is_payment_received' => $isReceived,
                    'payment_detail_id' => $paymentId
                ];
                $cleared_data = [
                    'paid_date' => $paidDate,
                    'paid_amount' => $paidAmount,
                    'paid_mode_id' => $paidModeId,
                    'paid_bank_id' => $paidBankId,
                    'paid_cheque_number' => $paidCheque,
                    'payment_remarks' => $data['payment_remarks'] ?? null,
                    'pending_amount' => $pendingAmount,
                    'agreement_payment_details_id' => $paymentId,
                    'paid_by' => auth()->user()->id,
                    'agreement_id' => $payment->agreement_id
                ];
                // dd($detail_date);
                $this->validate($cleared_data);


                $this->tenantChequeRepository->createClearedReceivables($cleared_data);
                $this->tenantChequeRepository->updatePaymentDetail($detail_data);


                $TotalStatus = checkAgreementPayment($payment->agreement_payment_id);

                $paymentData = [
                    'agreement_payment_id'       => $payment->agreement_payment_id,
                    'has_payment_received'       => 1,
                    'has_payment_fully_received' => $TotalStatus,
                ];
                $agreement_unit = $payment->agreementUnit;
                // dd($payment->transaction_type);
                if ($payment->transaction_type == 1) {
                    $unit_count = $payment->agreement->agreement_units->count();
                    $amount = $cleared_data['paid_amount'] / $unit_count;
                    // dd($amount);
                    foreach ($payment->agreement->agreement_units as $unit) {

                        updateContractUnitPayments($unit->contract_unit_details_id, $amount);
                    }
                } elseif ($payment->transaction_type == 2) {
                    $unit_count = $payment->agreement->agreement_units->count();
                    $amount = $cleared_data['paid_amount'] / $unit_count;
                    // dd($amount);
                    foreach ($payment->agreement->agreement_units as $unit) {

                        updateContractUnitReceivablePayback($unit->contract_unit_details_id, $amount);
                    }
                } else {
                    $contract_unit_details_id = $agreement_unit->contract_unit_details_id;
                    updateContractUnitPayments($contract_unit_details_id, $cleared_data['paid_amount']);
                }

                $this->tenantChequeRepository->updatePayment($paymentData);
            }
        });
    }
    public function bouncedCheque(array $data)
    {
        // dd($data);
        DB::transaction(function () use ($data) {

            // Expecting $data['payment_detail_ids'] as array of checked IDs
            $paymentIds = $data['payment_detail_ids'] ?? [];
            if (empty($paymentIds)) {
                throw new \Exception('No payment selected');
            }
            // dd($data);

            foreach ($paymentIds as $paymentId) {

                $payment = $this->tenantChequeRepository->getPaymentDetailById($paymentId);
                if (!$payment) {
                    continue;
                }
                if (!empty($data['bounced_date'])) {
                    $bouncedDate = Carbon::createFromFormat('d-m-Y', $data['bounced_date'])->format('Y-m-d');
                }

                $bounce_date = [
                    'bounced_date' => $bouncedDate,
                    'bounced_reason' => $data['bounced_reason'],
                    'bounced_by' => auth()->user()->id,
                    'payment_detail_id' => $paymentId,
                    'has_bounced' => 1
                ];
                $this->bounce_data_validate($bounce_date);

                // dd($bounce_date);


                $this->tenantChequeRepository->bouncedReceivables($bounce_date);
            }
        });
    }
    public function bounce_data_validate($data)
    {
        $validator = Validator::make($data, [
            'bounced_date' => ['required'],
            'bounced_reason' => ['required'],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
    public function getReportDataTable(array $filters = [])
    {
        // Use the getReportQuery to build the base query
        $query = $this->tenantChequeRepository->getReportQuery($filters);

        // Define columns for DataTables (optional, for front-end reference)
        $columns = [
            ['data' => 'DT_RowIndex', 'name' => 'cleared_receivables.id'],
            ['data' => 'project_number', 'name' => 'project_number'],
            ['data' => 'tenant_name', 'name' => 'tenant_name'],
            ['data' => 'property_name', 'name' => 'property_name'],
            ['data' => 'unit_number', 'name' => 'unit_number'],
            ['data' => 'payment_date', 'name' => 'payment_date'],
            ['data' => 'payment_mode_name', 'name' => 'payment_mode_name'],
            ['data' => 'paid_amount', 'name' => 'paid_amount'],
            ['data' => 'pending_amount', 'name' => 'pending_amount'],
            ['data' => 'installment_name', 'name' => 'installment_name'],
            ['data' => 'is_payment_received', 'name' => 'is_payment_received'],
            ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false],
        ];

        return datatables()
            ->of($query)
            ->addIndexColumn()
            ->addColumn('project_number', function ($row) {
                $number = $row->agreementPaymentDetail->agreement->contract->project_number ?? '-';
                $type = $row->agreementPaymentDetail->agreement->contract->contract_type->contract_type ?? '-';
                $badgeClass = match ($row->agreementPaymentDetail->agreement->contract->contract_type_id ?? 0) {
                    1 => 'badge badge-df text-dark',
                    2 => 'badge badge-ff text-dark',
                    default => 'badge badge-secondary',
                };
                return "<strong>P - {$number}</strong><p class='mb-0'><span class='{$badgeClass}'>{$type}</span></p>";
            })
            ->addColumn('tenant_name', function ($row) {
                $tenant = $row->agreementPaymentDetail->agreement->tenant;
                $name = $tenant->tenant_name ?? '-';
                $email = $tenant->tenant_email ?? '-';
                $phone = $tenant->tenant_mobile ?? '-';
                return "<strong class='text-capitalize'>{$name}</strong>
                    <p class='mb-0 text-primary'>{$email}</p>
                    <p class='text-muted small'><i class='fa fa-phone-alt text-danger'></i> <span class='font-weight-bold'>{$phone}</span></p>";
            })
            ->addColumn('property_name', fn($row) => $row->agreementPaymentDetail->agreement->contract->property->property_name ?? '-')
            ->addColumn('unit_number', function ($row) {
                $unit = $row->agreementPaymentDetail->agreement->agreement_units->firstWhere('id', $row->agreementPaymentDetail->agreement_unit_id);
                // return $unit && $unit->contractUnitDetail ? $unit->contractUnitDetail->unit_number : '-';
                return $unit && $unit->contractUnitDetail
                    ? $unit->contractUnitDetail->unit_number
                    . ($unit->contractSubunitDetail
                        ? ' - ' . $unit->contractSubunitDetail->subunit_code
                        : '')
                    : '-';
            })
            ->addColumn('payment_date', fn($row) => $row->agreementPaymentDetail->payment_date ? Carbon::parse($row->agreementPaymentDetail->payment_date)->format('d-m-Y') : '-')
            ->addColumn('paid_date', fn($row) => $row->paid_date ? Carbon::parse($row->paid_date)->format('d-m-Y') : '-')

            ->addColumn('payment_mode_name', function ($row) {
                $text = $row->agreementPaymentDetail->paymentMode->payment_mode_name ?? '';
                if (!empty($row->agreementPaymentDetail->bank_id) && $row->agreementPaymentDetail->bank) {
                    $text .= ' - ' . ucfirst($row->agreementPaymentDetail->bank->bank_name);
                }
                if (!empty($row->agreementPaymentDetail->cheque_number)) {
                    $text .= ' - ' . ucfirst($row->agreementPaymentDetail->cheque_number);
                }
                return $text;
            })
            ->addColumn('paid_amount', fn($row) => $row->paid_amount)
            ->addColumn('pending_amount', fn($row) => $row->pending_amount)
            ->addColumn('installment_name', function ($row) {
                if (empty($row->agreementPaymentDetail->agreement_unit_id)) {
                    return match ((int) $row->agreementPaymentDetail->transaction_type) {
                        2 => '<span class="badge bg-danger">Termination Payback</span>',
                        1 => '<span class="badge bg-success">Termination Receive</span>',
                        default => '<span class="badge bg-secondary">-</span>',
                    };
                }
                $agreementUnitId = $row->agreementPaymentDetail->agreement_unit_id;
                $installments = AgreementPaymentDetail::where('agreement_unit_id', $agreementUnitId)->orderBy('payment_date')->get();
                $current = $installments->search(fn($i) => $i->payment_date == $row->agreementPaymentDetail->payment_date) + 1;
                $total = $installments->count();
                return "{$current}/{$total}";
            })
            ->addColumn('is_payment_received', function ($row) {
                $status = $row->agreementPaymentDetail->is_payment_received ?? null;
                // dd($status);
                switch ($status) {
                    case 0:
                        return '<span class="badge bg-warning">Pending</span>';
                    case 2:
                        return '<span class="badge bg-info">Partially Paid</span>';
                    case 1:
                        return '<span class="badge bg-success">Paid</span>';
                }
            })
            // ->addColumn('action', function ($row) {
            //     $action = '';
            //     $detail = $row->agreementPaymentDetail;
            //     if ($detail->is_payment_received == 3) {
            //         $reason = $detail->bounced_reason ?? '-';
            //         $date = $detail->bounced_date ? Carbon::parse($detail->bounced_date)->format('d-m-Y') : '-';
            //         $action .= '<a class="btn btn-danger btn-sm bouncedInfoBtn m-1" data-reason="' . htmlspecialchars($reason, ENT_QUOTES) . '" data-date="' . $date . '" data-bs-toggle="modal" data-bs-target="#bouncedChequeModal"><i class="fas fa-exclamation-triangle"></i></a>';
            //     }
            //     $action .= '<a class="btn btn-success btn-sm clearChequeBtn m-1" title="Clear cheque" data-date="' . $detail->payment_date . '" data-id="' . $detail->id . '" data-form="single" data-amount="' . getReceivableAmount($detail->id) . '" data-payment-mode ="' . $detail->payment_mode_id . '" data-bank-id="' . $detail->bank_id . '" data-cheque-number="' . $detail->cheque_number . '" data-toggle="modal" data-target="#modal-single-clear">Clear</a>';
            //     return $action ?: '-';
            // })
            ->rawColumns(['tenant_name',  'project_number', 'is_payment_received', 'installment_name'])
            ->with(['columns' => $columns])
            ->toJson();
    }
}
