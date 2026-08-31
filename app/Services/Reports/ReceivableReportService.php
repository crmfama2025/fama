<?php

namespace App\Services\Reports;

use App\Models\AgreementPaymentDetail;
use App\Repositories\Reports\ReceivableReportRepository;
use Illuminate\Support\Carbon;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReceivableReportService
{
    public function __construct(
        protected ReceivableReportRepository $receivableReportRepository,
    ) {}




    // public function getDataTable(array $filters = [])
    // {
    //     // dd('test');
    //     $query = $this->receivableReportRepository->getQuery($filters);
    //     // dd($query);

    //     $columns = [
    //         ['data' => 'DT_RowIndex', 'name' => 'agreement_payment_details.id'],
    //         ['data' => 'project_number', 'name' => 'project_number'],
    //         ['data' => 'company_name', 'name' => 'company_name'],
    //         ['data' => 'tenant_name', 'name' => 'tenant_name'],
    //         ['data' => 'property_name', 'name' => 'property_name'],
    //         ['data' => 'unit_number', 'name' => 'unit_number'],
    //         ['data' => 'subunit_no', 'name' => 'subunit_no'],
    //         ['data' => 'tenant_details', 'name' => 'tenant_details'],
    //         ['data' => 'payment_date', 'name' => 'payment_date'],
    //         ['data' => 'payment_mode_name', 'name' => 'payment_mode_name'],
    //         ['data' => 'payment_amount', 'name' => 'payment_amount'],
    //         ['data' => 'installment_name', 'name' => 'installment_name'],
    //         ['data' => 'status', 'name' => 'status'],
    //         // ['data' => 'action', 'name' => 'action', 'orderable' => true, 'searchable' => true],
    //     ];
    //     // dd("test");

    //     return datatables()

    //         ->of($query)
    //         ->addIndexColumn()
    //         ->addColumn('checkbox', function ($row) {
    //             return '
    //             <div class="icheck-primary d-inline">
    //                 <input type="checkbox"
    //                     class="groupCheckbox"
    //                     name="installment_id[]"
    //                     id="ichek' . $row->id . '"
    //                     value="' . $row->id . '">
    //                 <label for="ichek' . $row->id . '"></label>
    //             </div>';
    //         })
    //         ->addColumn('project_number', function ($row) {
    //             // dd($row);
    //             $number = 'P - ' . $row->agreement?->contract?->project_number ?? '-';
    //             $type = $row->agreement?->contract?->contract_type->contract_type ?? '-';
    //             $b_type_id = $row->agreement?->contract?->contract_unit->business_type;
    //             $b_type = $row->agreement?->contract?->contract_unit->business_type();

    //             // return "<strong class=''>{$number}</strong><p class='mb-0'><span>{$type}</span></p>
    //             // </p>";
    //             $badgeClass = '';
    //             if ($row->agreement?->contract?->contract_type_id == 1) {
    //                 $badgeClass = 'badge badge-df text-dark';
    //             } elseif ($row->agreement?->contract?->contract_type_id == 2) {
    //                 $badgeClass = 'badge badge-ff text-dark';
    //             } else {
    //                 $badgeClass = 'badge badge-secondary';
    //             }
    //             // Business type color
    //             $businessClass = ($b_type_id == 1) ? 'text-olive' : 'text-cyan';

    //             return "<strong>{$number}</strong>
    //         <p class='mb-0'>
    //             <span class='{$badgeClass}'>{$type}</span>
    //         </p>
    //        <strong class='{$businessClass}'>
    //         {$b_type}
    //     </strong>";
    //         })
    //         ->addColumn('company_name', fn($row) => $row->agreement?->contract?->company->company_name ?? '-')
    //         ->addColumn('tenant_name', function ($row) {

    //             $name = $row->agreement?->tenant->tenant_name ?? '-';
    //             $email = $row->agreement?->tenant->tenant_email ?? '-';
    //             $phone = $row->agreement?->tenant->tenant_mobile ?? '-';

    //             return "<strong class='text-capitalize'>{$name}</strong>>";
    //         })
    //         ->addColumn('property_name', fn($row) => $row->agreement?->contract?->property->property_name ?? '-')
    //         ->addColumn('unit_number', function ($row) {
    //             // Find the agreement unit that matches this payment detail
    //             $unit = $row->agreement?->agreement_units->firstWhere('id', $row->agreement_unit_id);

    //             return $unit && $unit->contractUnitDetail
    //                 ? $unit->contractUnitDetail->unit_number
    //                 : '-';
    //         })
    //         ->addColumn('subunit_no', function ($row) {
    //             // Find the agreement unit that matches this payment detail
    //             $unit = $row->agreement?->agreement_units->firstWhere('id', $row->agreement_unit_id);

    //             return $unit && $unit->contractSubunitDetail
    //                 ? $unit->contractSubunitDetail->subunit_no
    //                 : '-';
    //         })
    //         ->addColumn('payment_date', function ($row) {
    //             if (!$row->payment_date) {
    //                 return '-';
    //             }

    //             return Carbon::parse($row->payment_date)->format('d-m-Y');
    //         })

    //         // ->addColumn('cheque_number', fn($row) => $row->cheque_number ?? '-')
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

    //         ->addColumn('payment_amount', fn($row) => $row->payment_amount)
    //         ->addColumn('installment_name', function ($row) {
    //             // dd($row->transaction_type);

    //             // if (empty($row->agreement_unit_id)) {
    //             //     return match ((int) $row->transaction_type) {
    //             //         2 => '<span class="badge bg-danger">Termination Payback</span>',
    //             //         1 => '<span class="badge bg-success">Termination Receive</span>',
    //             //         default => '<span class="badge bg-secondary">-</span>',
    //             //     };
    //             // }
    //             // $agreementUnitId = $row->agreement_unit_id;

    //             // $installments = AgreementPaymentDetail::where('agreement_unit_id', $agreementUnitId)
    //             //     ->orderBy('payment_date')
    //             //     ->get();
    //             // dd($installments);

    //             // $current = 0;
    //             // $total = $installments->count();

    //             // foreach ($installments as $index => $installment) {
    //             //     if ($installment->payment_date == $row->payment_date) {
    //             //         $current = $index + 1;
    //             //         break;
    //             //     }
    //             // }
    //             $name = $row->agreementPayment->installment->installment_name;
    //             return "$name";

    //             // return "{$current}/{$total}";
    //         })

    //         ->addColumn('installment', function ($row) {

    //             // if (!$row->agreement_unit_id) {
    //             //     return '-';
    //             // }

    //             // $installments = AgreementPaymentDetail::where(
    //             //     'agreement_unit_id',
    //             //     $row->agreement_unit_id
    //             // )
    //             //     ->orderBy('id')
    //             //     ->pluck('id');

    //             // $position = $installments->search($row->id);

    //             // return $position !== false ? $position + 1 : '-';
    //             $installment = AgreementPaymentDetail::where(
    //                 'agreement_unit_id',
    //                 $row->agreement_unit_id
    //             )
    //                 ->where('id', '<=', $row->id)
    //                 ->count();
    //             return $installment;
    //         })
    //         ->addColumn('status', function ($row) {
    //             // If any payment has bounced, show Bounced
    //             if ($row->has_bounced) {
    //                 return '<span class="badge bg-danger">Bounced</span>';
    //             }

    //             // Otherwise, check the is_payment_received status
    //             switch ($row->is_payment_received) {
    //                 case 0:
    //                     return '<span class="badge bg-warning">Pending</span>';
    //                 case 1:
    //                     return '<span class="badge bg-success">Paid</span>';
    //                 default:
    //                     return '<span class="badge bg-secondary">-</span>';
    //             }
    //         })

    //         ->addColumn('paid_amount', fn($row) => $row->paid_amount_total ?? 0)

    //         ->addColumn('pending_amount', function ($row) {
    //             return $row->latestClearedReceivable?->pending_amount
    //                 ?? $row->payment_amount
    //                 ?? 0;
    //         })

    //         ->addColumn('paid_date', function ($row) {

    //             $date = $row->latestClearedReceivable?->paid_date;

    //             return $date
    //                 ? Carbon::parse($date)->format('d-m-Y')
    //                 : '-';
    //         })

    //         ->addColumn('paid_mode', function ($row) {
    //             return $row->latestClearedReceivable?->paidMode?->payment_mode_name ?? '-';
    //         })

    //         ->addColumn('paid_bank', function ($row) {
    //             return $row->latestClearedReceivable?->paidBank?->bank_name ?? '-';
    //         })
    //         ->addColumn('paid_company', function ($row) {
    //             return $row->latestClearedReceivable?->paidCompany?->company_name ?? '-';
    //         })
    //         ->addColumn('paid_cheque_number', function ($row) {
    //             return $row->latestClearedReceivable?->paid_cheque_number ?? '-';
    //         })

    //         ->rawColumns(['checkbox', 'tenant_name', 'installment', 'paid_amount', 'paid_company', 'pending_amount', 'paid_date', 'paid_mode', 'paid_bank', 'paid_cheque_number', 'project_number', 'business_type', 'status', 'installment_name'])
    //         // ->rawColumns(['action'])
    //         ->with(['columns' => $columns])
    //         ->toJson();
    // }
    public function getReceivableExportData(array $filters = [])
    {

        $query = $this->receivableReportRepository->getQuery($filters);

        // dd('test');
        return $query->get()->map(function ($row) {


            // TEMP DEBUG — dump raw row before any processing
            // dump([
            //     'id'                    => $row->id ?? null,
            //     'project_number'        => $row->project_number ?? null,
            //     'company_name'          => $row->company_name ?? null,
            //     'tenant_name'           => $row->tenant_name ?? null,
            //     'tenant_email'          => $row->tenant_email ?? null,
            //     'tenant_mobile'         => $row->tenant_mobile ?? null,
            //     'property_name'         => $row->property_name ?? null,
            //     'unit_number'           => $row->unit_number ?? null,
            //     'subunit_no'            => $row->subunit_no ?? null,
            //     'payment_date'          => $row->payment_date ?? null,
            //     'payment_amount'        => $row->payment_amount ?? null,
            //     'payment_mode_id'       => $row->payment_mode_id ?? null,
            //     'bank_id'               => $row->bank_id ?? null,
            //     'cheque_number'         => $row->cheque_number ?? null,
            //     'payment_mode_name'     => $row->payment_mode_name ?? null,
            //     'bank_name'             => $row->bank_name ?? null,
            //     'installment_name'      => $row->installment_name ?? null,
            //     'installment_position'  => $row->installment_position ?? null,
            //     'installment_count'     => $row->installment_count ?? null,
            //     'is_payment_received'   => $row->is_payment_received ?? null,
            //     'is_invoice_added'      => $row->is_invoice_added ?? null,
            //     'terminate_status'      => $row->terminate_status ?? null,
            //     'has_bounced'           => $row->has_bounced ?? null,
            //     'paid_amount_total'     => $row->paid_amount_total ?? null,
            //     'pending_amount'        => $row->pending_amount ?? null,
            //     'paid_date'             => $row->paid_date ?? null,
            //     'paid_mode_name'        => $row->paid_mode_name ?? null,
            //     'paid_bank_name'        => $row->paid_bank_name ?? null,
            //     'paid_company_name'     => $row->paid_company_name ?? null,
            //     'paid_cheque_number'    => $row->paid_cheque_number ?? null,
            // ]);

            // Project
            $projectNumber = $row->project_number
                ? 'P - ' . $row->project_number
                : '-';

            // Company
            $companyName = $row->company_name ?? '-';

            // Tenant
            $tenantName = $row->tenant_name ?? '-';

            // Building / Property
            $propertyName = $row->property_name ?? '-';

            // Unit
            $unitNumber = $row->unit_number ?? '-';

            // Subunit
            $subunitNo = $row->subunit_no ?? '-';

            // Due Date
            $paymentDate = $row->payment_date
                ? Carbon::parse($row->payment_date)->format('d-m-Y')
                : '-';

            // Payment Mode
            $paymentMode = $row->payment_mode_name ?? '';

            if (!empty($row->bank_name)) {
                $paymentMode .= ' - ' . ucfirst($row->bank_name);
            }

            if (!empty($row->cheque_number)) {
                $paymentMode .= ' - ' . $row->cheque_number;
            }

            $paymentMode = $paymentMode ?: '-';

            // Installment Number
            $installment = $row->installment_position ?? '-';

            // Total Installments
            $totalInstallments = $row->installment_position
                . '/'
                . $row->installment_count;

            // dd($totalInstallments);
            // Installment Name
            $installmentName = $row->installment_name ?? '-';

            // Paid Amount
            $paidAmount = $row->paid_amount_total ?? 0;

            // Pending Amount
            $pendingAmount = $row->pending_amount
                ?? $row->payment_amount
                ?? 0;

            // Paid Date
            $paidDate = $row->paid_date
                ? Carbon::parse($row->paid_date)->format('d-m-Y')
                : '-';

            // Paid Mode
            $paidMode = $row->paid_mode_name ?? '-';

            // Paid Bank
            $paidBank = $row->paid_bank_name ?? '-';

            // Paid Company
            $paidCompany = $row->paid_company_name ?? '-';

            // Paid Cheque
            $paidChequeNumber = $row->paid_cheque_number ?? '-';

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

                $installment,
                $totalInstallments,
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
            'Installment Number',
            'Total Installments',
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

    public function getDataTable(array $filters = [])
    {
        $query = $this->receivableReportRepository->getQuery($filters);

        $columns = [
            ['data' => 'DT_RowIndex', 'name' => 'apd.id'],

            ['data' => 'project_number', 'name' => 'c.project_number'],

            ['data' => 'company_name', 'name' => 'co.company_name'],

            ['data' => 'tenant_name', 'name' => 't.tenant_name'],

            ['data' => 'property_name', 'name' => 'p.property_name'],

            ['data' => 'unit_number', 'name' => 'cud.unit_number'],

            ['data' => 'subunit_no', 'name' => 'cusd.subunit_no'],

            ['data' => 'payment_date', 'name' => 'apd.payment_date'],

            ['data' => 'payment_mode_name', 'name' => 'pm.payment_mode_name'],

            ['data' => 'payment_amount', 'name' => 'apd.payment_amount'],

            ['data' => 'composition', 'name' => 'composition.installment_position'],

            ['data' => 'installment_name', 'name' => 'installment.installment_name'],

            ['data' => 'paid_amount', 'name' => 'paid_amount_total'],

            ['data' => 'pending_amount', 'name' => 'last_clear.pending_amount'],

            ['data' => 'paid_date', 'name' => 'last_clear.paid_date'],

            ['data' => 'paid_mode', 'name' => 'paid_mode_name'],

            ['data' => 'paid_bank', 'name' => 'paid_bank_name'],

            ['data' => 'paid_cheque_number', 'name' => 'last_clear.paid_cheque_number'],

            ['data' => 'paid_company', 'name' => 'paid_company_name'],

            ['data' => 'status', 'name' => 'apd.is_payment_received'],

            ['data' => 'has_bounced', 'name' => 'apd.has_bounced'],

            ['data' => 'bounced_reason', 'name' => 'apd.bounced_reason'],

            ['data' => 'bounced_date', 'name' => 'apd.bounced_date'],
        ];

        return datatables()
            ->of($query)

            /*
        |--------------------------------------------------------------------------
        | Index
        |--------------------------------------------------------------------------
        */

            ->addIndexColumn()

            /*
        |--------------------------------------------------------------------------
        | Project
        |--------------------------------------------------------------------------
        */

            ->addColumn('project_number', function ($row) {

                $number = !empty($row->project_number)
                    ? 'P - ' . $row->project_number
                    : '-';

                /*
             * Contract type is not currently selected in repository.
             * So only project number is displayed here.
             */

                return "<strong>{$number}</strong>";
            })

            /*
        |--------------------------------------------------------------------------
        | Company
        |--------------------------------------------------------------------------
        */

            ->addColumn('company_name', function ($row) {

                return $row->company_name ?? '-';
            })

            /*
        |--------------------------------------------------------------------------
        | Tenant
        |--------------------------------------------------------------------------
        */

            ->addColumn('tenant_name', function ($row) {

                $name = $row->tenant_name ?? '-';

                return "<strong class='text-capitalize'>{$name}</strong>";
            })

            /*
        |--------------------------------------------------------------------------
        | Property
        |--------------------------------------------------------------------------
        */

            ->addColumn('property_name', function ($row) {

                return $row->property_name ?? '-';
            })

            /*
        |--------------------------------------------------------------------------
        | Unit
        |--------------------------------------------------------------------------
        */

            ->addColumn('unit_number', function ($row) {

                return $row->unit_number ?? '-';
            })

            /*
        |--------------------------------------------------------------------------
        | Subunit
        |--------------------------------------------------------------------------
        */

            ->addColumn('subunit_no', function ($row) {

                return $row->subunit_no ?? '-';
            })

            /*
        |--------------------------------------------------------------------------
        | Payment Date
        |--------------------------------------------------------------------------
        */

            ->addColumn('payment_date', function ($row) {

                if (empty($row->payment_date)) {
                    return '-';
                }

                return Carbon::parse($row->payment_date)
                    ->format('d-m-Y');
            })

            /*
        |--------------------------------------------------------------------------
        | Payment Mode
        |--------------------------------------------------------------------------
        */

            ->addColumn('payment_mode_name', function ($row) {

                $text = $row->payment_mode_name ?? '';

                if (!empty($row->bank_name)) {
                    $text .= ' - ' . ucfirst($row->bank_name);
                }

                if (!empty($row->cheque_number)) {
                    $text .= ' - ' . ucfirst($row->cheque_number);
                }

                return $text ?: '-';
            })

            /*
        |--------------------------------------------------------------------------
        | Payment Amount
        |--------------------------------------------------------------------------
        */

            ->addColumn('payment_amount', function ($row) {

                return $row->payment_amount ?? 0;
            })

            /*
        |--------------------------------------------------------------------------
        | Composition
        |--------------------------------------------------------------------------
        |
        | Example:
        | 1/12
        | 2/12
        | 3/12
        |
        */

            ->addColumn('composition', function ($row) {

                if (
                    empty($row->installment_position) ||
                    empty($row->installment_count)
                ) {
                    return '-';
                }

                return $row->installment_position
                    . '/'
                    . $row->installment_count;
            })

            /*
        |--------------------------------------------------------------------------
        | Installment Name
        |--------------------------------------------------------------------------
        */

            ->addColumn('installment_name', function ($row) {

                return $row->installment_name ?? '-';
            })

            /*
        |--------------------------------------------------------------------------
        | Paid Amount
        |--------------------------------------------------------------------------
        */

            ->addColumn('paid_amount', function ($row) {

                return $row->paid_amount_total ?? 0;
            })

            /*
        |--------------------------------------------------------------------------
        | Pending Amount
        |--------------------------------------------------------------------------
        */

            ->addColumn('pending_amount', function ($row) {

                /*
             * If there is a latest cleared record,
             * use its pending amount.
             *
             * Otherwise use the original payment amount.
             */

                if (
                    isset($row->pending_amount) &&
                    $row->pending_amount !== null
                ) {
                    return $row->pending_amount;
                }

                return $row->payment_amount ?? 0;
            })

            /*
        |--------------------------------------------------------------------------
        | Paid Date
        |--------------------------------------------------------------------------
        */

            ->addColumn('paid_date', function ($row) {

                if (empty($row->paid_date)) {
                    return '-';
                }

                return Carbon::parse($row->paid_date)
                    ->format('d-m-Y');
            })

            /*
        |--------------------------------------------------------------------------
        | Paid Mode
        |--------------------------------------------------------------------------
        */

            ->addColumn('paid_mode', function ($row) {

                return $row->paid_mode_name ?? '-';
            })

            /*
        |--------------------------------------------------------------------------
        | Paid Bank
        |--------------------------------------------------------------------------
        */

            ->addColumn('paid_bank', function ($row) {

                return $row->paid_bank_name ?? '-';
            })

            /*
        |--------------------------------------------------------------------------
        | Paid Cheque Number
        |--------------------------------------------------------------------------
        */

            ->addColumn('paid_cheque_number', function ($row) {

                return $row->paid_cheque_number ?? '-';
            })

            /*
        |--------------------------------------------------------------------------
        | Paid Company
        |--------------------------------------------------------------------------
        */

            ->addColumn('paid_company', function ($row) {

                return $row->paid_company_name ?? '-';
            })

            /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

            ->addColumn('status', function ($row) {

                /*
             * Bounced has highest priority
             */

                // if ((int) ($row->has_bounced ?? 0) === 1) {

                //     return '<span class="badge bg-danger">
                //             Bounced
                //         </span>';
                // }

                /*
             * Payment status
             */

                switch ((int) ($row->is_payment_received ?? 0)) {

                    case 0:

                        return '<span class="badge bg-warning">
                                Pending
                            </span>';

                    case 1:

                        return '<span class="badge bg-success">
                                Paid
                            </span>';

                    case 2:

                        return '<span class="badge bg-info">
                                Partially Paid
                            </span>';

                    default:

                        return '<span class="badge bg-secondary">
                                -
                            </span>';
                }
            })



            ->addColumn('bounced_date', function ($row) {
                if (empty($row->bounced_date)) {
                    return '-';
                }

                return Carbon::parse($row->bounced_date)->format('d-m-Y');
            })

            ->addColumn('bounced_reason', function ($row) {
                return $row->bounced_reason ?? '-';
            })


            /*
        |--------------------------------------------------------------------------
        | Raw HTML Columns
        |--------------------------------------------------------------------------
        */

            ->rawColumns([
                'tenant_name',
                'project_number',
                'status',
            ])

            /*
        |--------------------------------------------------------------------------
        | Extra Data
        |--------------------------------------------------------------------------
        */

            ->with([
                'columns' => $columns
            ])

            ->toJson();
    }

    public function exportReceivables(array $filters = []): StreamedResponse
    {
        $filename = 'receivables-'
            . now()->format('Y-m-d-His')
            . '.csv';

        return response()->streamDownload(
            function () use ($filters) {

                set_time_limit(0);

                $output = fopen('php://output', 'wb');

                if ($output === false) {
                    throw new RuntimeException(
                        'Unable to open the CSV output stream.'
                    );
                }

                // UTF-8 BOM for Excel
                fwrite($output, "\xEF\xBB\xBF");

                // Headings
                fputcsv($output, [
                    'Project',
                    'Company',
                    'Tenant',
                    'Building',
                    'Unit',
                    'Subunit',
                    'Due Date',
                    'Payment Mode',
                    'Amount',
                    // 'Installment Number',
                    // 'Total Installments',
                    // 'Installment Name',
                    'Composition',
                    'Paid Amount',
                    'Pending Amount',
                    'Paid Date',
                    'Paid Mode',
                    'Paid Bank',
                    'Paid Cheque Number',
                    'Paid Company Name',
                    'Status',
                    'Has Bounced',
                    'Bounced Date',
                    'Bounced Reason',
                    'Terminate Status'
                ]);

                /*
             * Get query without executing it.
             */
                $query = $this->receivableReportRepository
                    ->getQuery($filters)
                    ->reorder();

                /*
             * IMPORTANT:
             *
             * Replace `id` below with the unique numeric column
             * from your receivable query.
             */
                $rows = $query->lazyById(
                    1000,
                    'apd.id',
                    'id'
                );

                foreach ($rows as $row) {

                    // Project
                    $projectNumber = $row->project_number
                        ? 'P - ' . $row->project_number
                        : '-';

                    // Company
                    $companyName = $row->company_name ?? '-';

                    // Tenant
                    $tenantName = $row->tenant_name ?? '-';

                    // Building
                    $propertyName = $row->property_name ?? '-';

                    // Unit
                    $unitNumber = $row->unit_number ?? '-';

                    // Subunit
                    $subunitNo = $row->subunit_no ?? '-';

                    // Due Date
                    $paymentDate = $row->payment_date
                        ? Carbon::parse($row->payment_date)->format('d-m-Y')
                        : '-';

                    // Payment Mode
                    $paymentMode = $row->payment_mode_name ?? '';

                    if (!empty($row->bank_name)) {
                        $paymentMode .= ' - ' . ucfirst($row->bank_name);
                    }

                    if (!empty($row->cheque_number)) {
                        $paymentMode .= ' - ' . $row->cheque_number;
                    }

                    $paymentMode = $paymentMode ?: '-';

                    // Installment
                    $installment = $row->installment_position ?? '-';

                    $totalInstallments =
                        ($row->installment_position ?? 0)
                        . '/'
                        . ($row->installment_count ?? 0);

                    // Installment Name
                    $installmentName = $row->installment_name ?? '-';


                    $composition = (
                        $row->installment_position !== null &&
                        $row->installment_count !== null
                    )
                        ? "'" . $row->installment_position . '/' . $row->installment_count
                        : '-';

                    // Paid Amount
                    $paidAmount = $row->paid_amount_total ?? 0;

                    // Pending Amount
                    $pendingAmount = $row->pending_amount
                        ?? $row->payment_amount
                        ?? 0;

                    // Paid Date
                    $paidDate = $row->paid_date
                        ? Carbon::parse($row->paid_date)->format('d-m-Y')
                        : '-';

                    // Paid Mode
                    $paidMode = $row->paid_mode_name ?? '-';

                    // Paid Bank
                    $paidBank = $row->paid_bank_name ?? '-';

                    // Paid Company
                    $paidCompany = $row->paid_company_name ?? '-';

                    // Paid Cheque
                    $paidChequeNumber = $row->paid_cheque_number ?? '-';

                    // Status


                    $status = match ((int) $row->is_payment_received) {
                        0 => 'Pending',
                        1 => 'Received',
                        2 => 'Half Received',
                        default => '-',
                    };

                    $hasBounced = ((int) $row->has_bounced === 1)
                        ? 'Bounced'
                        : 'Not Bounced';

                    $bouncedDate = (
                        (int) $row->has_bounced === 1 &&
                        !empty($row->bounced_date)
                    )
                        ? Carbon::parse($row->bounced_date)->format('d-m-Y')
                        : '-';

                    $bouncedReason = (
                        (int) $row->has_bounced === 1
                    )
                        ? ($row->bounced_reason ?? '-')
                        : '-';
                    $terminateStatus = ((int) $row->terminate_status === 1)
                        ? 'Terminated'
                        : 'Not Terminated';

                    fputcsv($output, [
                        $projectNumber,
                        $companyName,
                        $tenantName,
                        $propertyName,
                        $unitNumber,
                        $subunitNo,
                        $paymentDate,
                        $paymentMode,
                        $row->payment_amount ?? 0,
                        // $installment,
                        // $totalInstallments,
                        // $installmentName,
                        $composition,
                        $paidAmount,
                        $pendingAmount,
                        $paidDate,
                        $paidMode,
                        $paidBank,
                        $paidChequeNumber,
                        $paidCompany,
                        $status,
                        $hasBounced,
                        $bouncedDate,
                        $bouncedReason,
                        $terminateStatus
                    ]);

                    /*
                 * Flush periodically.
                 */
                    if (($row->id % 500) === 0) {
                        fflush($output);
                        flush();
                    }
                }

                fclose($output);
            },
            $filename,
            [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Cache-Control' => 'no-store, no-cache',
                'X-Accel-Buffering' => 'no',
            ]
        );
    }
}
