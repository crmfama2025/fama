<?php

namespace App\Services\Reports;

use App\Models\AgreementPaymentDetail;
use App\Repositories\Reports\ReceivableReportRepository;
use Illuminate\Support\Carbon;

class ReceivableReportService
{
    public function __construct(
        protected ReceivableReportRepository $receivableReportRepository,
    ) {}




    public function getDataTable(array $filters = [])
    {
        // dd('test');
        $query = $this->receivableReportRepository->getQuery($filters);
        // dd($query);

        $columns = [
            ['data' => 'DT_RowIndex', 'name' => 'agreement_payment_details.id'],
            ['data' => 'project_number', 'name' => 'project_number'],
            ['data' => 'company_name', 'name' => 'company_name'],
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
                $number = 'P - ' . $row->agreement?->contract?->project_number ?? '-';
                $type = $row->agreement?->contract?->contract_type->contract_type ?? '-';
                $b_type_id = $row->agreement?->contract?->contract_unit->business_type;
                $b_type = $row->agreement?->contract?->contract_unit->business_type();

                // return "<strong class=''>{$number}</strong><p class='mb-0'><span>{$type}</span></p>
                // </p>";
                $badgeClass = '';
                if ($row->agreement?->contract?->contract_type_id == 1) {
                    $badgeClass = 'badge badge-df text-dark';
                } elseif ($row->agreement?->contract?->contract_type_id == 2) {
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
            ->addColumn('company_name', fn($row) => $row->agreement?->contract?->company->company_name ?? '-')
            ->addColumn('tenant_name', function ($row) {

                $name = $row->agreement?->tenant->tenant_name ?? '-';
                $email = $row->agreement?->tenant->tenant_email ?? '-';
                $phone = $row->agreement?->tenant->tenant_mobile ?? '-';

                return "<strong class='text-capitalize'>{$name}</strong>>";
            })
            ->addColumn('property_name', fn($row) => $row->agreement?->contract?->property->property_name ?? '-')
            ->addColumn('unit_number', function ($row) {
                // Find the agreement unit that matches this payment detail
                $unit = $row->agreement?->agreement_units->firstWhere('id', $row->agreement_unit_id);

                return $unit && $unit->contractUnitDetail
                    ? $unit->contractUnitDetail->unit_number
                    : '-';
            })
            ->addColumn('subunit_no', function ($row) {
                // Find the agreement unit that matches this payment detail
                $unit = $row->agreement?->agreement_units->firstWhere('id', $row->agreement_unit_id);

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

            ->addColumn('payment_amount', fn($row) => $row->payment_amount)
            ->addColumn('installment_name', function ($row) {
                // dd($row->transaction_type);

                // if (empty($row->agreement_unit_id)) {
                //     return match ((int) $row->transaction_type) {
                //         2 => '<span class="badge bg-danger">Termination Payback</span>',
                //         1 => '<span class="badge bg-success">Termination Receive</span>',
                //         default => '<span class="badge bg-secondary">-</span>',
                //     };
                // }
                // $agreementUnitId = $row->agreement_unit_id;

                // $installments = AgreementPaymentDetail::where('agreement_unit_id', $agreementUnitId)
                //     ->orderBy('payment_date')
                //     ->get();
                // dd($installments);

                // $current = 0;
                // $total = $installments->count();

                // foreach ($installments as $index => $installment) {
                //     if ($installment->payment_date == $row->payment_date) {
                //         $current = $index + 1;
                //         break;
                //     }
                // }
                $name = $row->agreementPayment->installment->installment_name;
                return "$name";

                // return "{$current}/{$total}";
            })

            ->addColumn('installment', function ($row) {

                // if (!$row->agreement_unit_id) {
                //     return '-';
                // }

                // $installments = AgreementPaymentDetail::where(
                //     'agreement_unit_id',
                //     $row->agreement_unit_id
                // )
                //     ->orderBy('id')
                //     ->pluck('id');

                // $position = $installments->search($row->id);

                // return $position !== false ? $position + 1 : '-';
                $installment = AgreementPaymentDetail::where(
                    'agreement_unit_id',
                    $row->agreement_unit_id
                )
                    ->where('id', '<=', $row->id)
                    ->count();
                return $installment;
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

            ->addColumn('paid_amount', fn($row) => $row->paid_amount_total ?? 0)

            ->addColumn('pending_amount', function ($row) {
                return $row->latestClearedReceivable?->pending_amount
                    ?? $row->payment_amount
                    ?? 0;
            })

            ->addColumn('paid_date', function ($row) {

                $date = $row->latestClearedReceivable?->paid_date;

                return $date
                    ? Carbon::parse($date)->format('d-m-Y')
                    : '-';
            })

            ->addColumn('paid_mode', function ($row) {
                return $row->latestClearedReceivable?->paidMode?->payment_mode_name ?? '-';
            })

            ->addColumn('paid_bank', function ($row) {
                return $row->latestClearedReceivable?->paidBank?->bank_name ?? '-';
            })
            ->addColumn('paid_company', function ($row) {
                return $row->latestClearedReceivable?->paidCompany?->company_name ?? '-';
            })
            ->addColumn('paid_cheque_number', function ($row) {
                return $row->latestClearedReceivable?->paid_cheque_number ?? '-';
            })

            ->rawColumns(['checkbox', 'tenant_name', 'installment', 'paid_amount', 'paid_company', 'pending_amount', 'paid_date', 'paid_mode', 'paid_bank', 'paid_cheque_number', 'project_number', 'business_type', 'status', 'installment_name'])
            // ->rawColumns(['action'])
            ->with(['columns' => $columns])
            ->toJson();
    }
    public function getReceivableExportData(array $filters = [])
    {
        // dd("test");
        $query = $this->receivableReportRepository->getQuery($filters);
        // dd("test");

        return $query->get()->map(function ($row) {

            // Project
            $projectNumber = 'P - ' .
                ($row->agreement?->contract?->project_number ?? '-');

            // Company
            $companyName = $row->agreement?->contract?->company?->company_name ?? '-';

            // Tenant
            $tenantName = $row->agreement?->tenant?->tenant_name ?? '-';

            // Property
            $propertyName = $row->agreement?->contract?->property?->property_name ?? '-';

            // Unit / Subunit
            $unit = $row->agreement?->agreement_units
                ->firstWhere('id', $row->agreement_unit_id);

            $unitNumber = $unit?->contractUnitDetail?->unit_number ?? '-';

            $subunitNo = $unit?->contractSubunitDetail?->subunit_no ?? '-';

            // Payment date
            $paymentDate = $row->payment_date
                ? Carbon::parse($row->payment_date)->format('d-m-Y')
                : '-';

            // Payment mode
            $paymentMode = $row->paymentMode?->payment_mode_name ?? '';

            if (!empty($row->bank_id) && $row->bank) {
                $paymentMode .= ' - ' . ucfirst($row->bank->bank_name);
            }

            if (!empty($row->cheque_number)) {
                $paymentMode .= ' - ' . ucfirst($row->cheque_number);
            }

            $paymentMode = $paymentMode ?: '-';

            // Installment & Installment Name
            $installment = '-';
            $installmentName = '-';

            if ($row->agreement_unit_id) {

                // $installments = AgreementPaymentDetail::where(
                //     'agreement_unit_id',
                //     $row->agreement_unit_id
                // )
                //     ->orderBy('id')
                //     ->get(['id', 'agreement_payment_id']);

                // // Find current installment position
                // $position = $installments->search(
                //     fn($item) => $item->id == $row->id
                // );

                // if ($position !== false) {
                //     $installment = $position + 1;
                // }

                $installment = AgreementPaymentDetail::where(
                    'agreement_unit_id',
                    $row->agreement_unit_id
                )
                    ->where('id', '<=', $row->id)
                    ->count();


                // Installment name
                $installmentName = $row->agreementPayment?->installment?->installment_name
                    ?? '-';
            }

            // Status
            if ($row->has_bounced) {
                $status = 'Bounced';
            } else {

                $status = match ((int) $row->is_payment_received) {
                    0 => 'Pending',
                    1 => 'Received',
                    2 => 'Half Received',
                    default => '-',
                };
            }

            // Paid amount
            $paidAmount = $row->paid_amount_total ?? 0;

            // Pending amount
            $pendingAmount = $row->latestClearedReceivable?->pending_amount
                ?? $row->payment_amount
                ?? 0;

            // Paid date
            $paidDate = $row->latestClearedReceivable?->paid_date;

            $paidDate = $paidDate
                ? Carbon::parse($paidDate)->format('d-m-Y')
                : '-';

            // Paid mode
            $paidMode = $row->latestClearedReceivable?->paidMode?->payment_mode_name
                ?? '-';

            // Paid bank
            $paidBank = $row->latestClearedReceivable?->paidBank?->bank_name
                ?? '-';

            // Paid company
            $paidCompany = $row->latestClearedReceivable?->paidCompany?->company_name
                ?? '-';

            // Paid cheque
            $paidChequeNumber =
                $row->latestClearedReceivable?->paid_cheque_number
                ?? '-';

            return [
                $projectNumber,
                $companyName,
                $tenantName,
                $propertyName,
                $unitNumber,
                $subunitNo,
                $paymentDate,
                $paymentMode,
                $row->payment_amount ?? 0,
                // Installment columns
                $installment,
                $installmentName,

                $paidAmount,
                $pendingAmount,
                $paidDate,
                $paidMode,
                $paidBank,
                $paidChequeNumber,
                $paidCompany,
                $status,
            ];
        });
    }
    public function receivableExportHeadings()
    {
        return [
            'Project',
            'Company',
            'Tenant',
            'Building',
            'Unit',
            'Subunit',
            'Due Date',
            'Payment Mode',
            'Amount',
            'Installment  Number',
            'Installment Name',
            'Paid Amount',
            'Pending Amount',
            'Paid Date',
            'Paid Mode',
            'Paid Bank',
            'Paid Cheque Number',
            'Paid Company Name',
            'Status',
        ];
    }
}
