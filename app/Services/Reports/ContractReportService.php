<?php

namespace App\Services\Reports;

use App\Repositories\Reports\ContractReportRepository;
use Carbon\Carbon;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContractReportService
{
    public function __construct(
        protected ContractReportRepository $contractReportRepository,
    ) {}


    // project report
    public function getProjectDataTable(array $filters)
    {
        $dataTable = datatables()
            ->query($this->contractReportRepository->getProjectReport($filters))
            ->filter(function ($query) {})
            ->addIndexColumn()
            ->editColumn('project_number', fn($row) => 'P-' . $row->project_number);

        foreach (
            [
                'start_date',
                'end_date',
                'closing_date',
                'terminated_date',
                'receivable_start_date'
            ] as $column
        ) {
            $dataTable->editColumn($column, fn($row) => filled($row->{$column})
                ? dateFormatChange($row->{$column}, 'd/m/Y') : '');
        }

        foreach (
            [
                'contract_fee',
                'rent_per_annum_payable',
                'rent_receivable_per_month',
                'rent_receivable_per_annum',
                'commission',
                'deposit',
                'cost_of_development',
                'cost_of_bed',
                'cost_of_matress',
                'appliances',
                'decoration',
                'dewa_deposit',
                'cost_of_cabinets',
                'total_otc',
                'final_cost',
                'initial_investment',
                'expected_profit',
                'paid_amount',
                'total_payment_to_vendor',
                'occupied_rent_per_month',
                'total_payment_pending',
                'total_payment_received',
                'balance_amount'
            ] as $column
        ) {
            $dataTable->editColumn($column, fn($row) => number_format((float) $row->{$column}, 2));
        }

        $dataTable->editColumn(
            'vendor_balance',
            fn($row) => number_format((float) $row->vendor_balance, 2, '.', ',')
        );

        foreach (
            [
                'commission_percentage',
                'deposit_percentage',
                'roi_perc',
                'profit_percentage'
            ] as $column
        ) {
            $dataTable->editColumn($column, fn($row) => number_format((float) $row->{$column}, 2) . '%');
        }

        return $dataTable->toJson();
    }

    public function exportProject(array $filters = []): StreamedResponse
    {
        $filename = 'project-report-' . now()->format('Y-m-d-His') . '.csv';
        $columns = [
            'Project Number' => 'project_number',
            'Project Code' => 'project_code',
            'Project Type' => 'project_type',
            'Contract Status' => 'contract_status_name',
            'Company' => 'company_name',
            'Vendor' => 'vendor_name',
            'Contract Type' => 'contract_type',
            'Contract Number' => 'contract_number',
            'Contract Person' => 'contract_person',
            'Property Code' => 'property_code',
            'Property' => 'property_name',
            'Area' => 'area_name',
            'Locality' => 'locality_name',
            'Start Date' => 'start_date',
            'End Date' => 'end_date',
            'Duration Months' => 'duration_in_months',
            'Duration Days' => 'duration_in_days',
            'Contract Fee' => 'contract_fee',
            'Grace Period' => 'grace_period',
            'Rent Payable / Annum' => 'rent_per_annum_payable',
            'Total Payment to Vendor' => 'total_payment_to_vendor',
            'Payable Paid to Vendor' => 'paid_amount',
            'Payable Pending to Vendor' => 'vendor_balance',
            'Rent Receivable / Month' => 'rent_receivable_per_month',
            'Rent Receivable / Annum' => 'rent_receivable_per_annum',
            'Commission %' => 'commission_percentage',
            'Commission' => 'commission',
            'Deposit %' => 'deposit_percentage',
            'Deposit' => 'deposit',
            'Development Cost' => 'cost_of_development',
            'Bed Cost' => 'cost_of_bed',
            'Mattress Cost' => 'cost_of_mattress',
            'Appliances' => 'appliances',
            'Decoration' => 'decoration',
            'DEWA Deposit' => 'dewa_deposit',
            'Cabinets Cost' => 'cost_of_cabinets',
            'Total OTC' => 'total_otc',
            'Final Cost' => 'final_cost',
            'Initial Investment' => 'initial_investment',
            'Expected Profit' => 'expected_profit',
            'Profit %' => 'profit_percentage',
            'ROI %' => 'roi_perc',
            'Paid Amount' => 'paid_amount',
            'No. of Units' => 'no_of_units',
            'Unit Numbers' => 'unit_numbers',
            'No. of Floors' => 'no_of_floors',
            'Floor Numbers' => 'floor_numbers',
            'Subunit Count' => 'total_subunit_count_per_contract',
            'Payment Pending' => 'total_payment_pending',
            'Payment Received' => 'total_payment_received',
            'Installments' => 'installment_name',
            'Installment Payment Progress' => 'installment_payment_progress',
            'Renewal Count' => 'renewal_count',
            'Parent Project Number' => 'parent_project_number',
            'Terminated Date' => 'terminated_date',
            'Termination Reason' => 'terminated_reason',
            'Balance Amount' => 'balance_amount',
            'Balance Received' => 'balance_received',
            'Direct / Indirect' => 'direct_status',
        ];
        $dateColumns = ['start_date', 'end_date', 'terminated_date'];

        return response()->streamDownload(function () use ($filters, $columns, $dateColumns) {
            set_time_limit(0);
            $output = fopen('php://output', 'wb');
            if ($output === false) {
                throw new RuntimeException('Unable to open CSV output stream.');
            }
            try {
                fwrite($output, "\xEF\xBB\xBF");
                fputcsv($output, array_keys($columns));
                $this->contractReportRepository
                    ->getProjectReport($filters)
                    ->reorder()
                    ->chunkById(
                        500,
                        function ($rows) use ($output, $columns, $dateColumns) {
                            foreach ($rows as $row) {
                                $values = [];

                                foreach ($columns as $column) {
                                    $value = $row->{$column} ?? '';

                                    if ($column === 'project_number') {
                                        $value = filled($value) ? 'P-' . $value : '';
                                    } elseif ($column === 'installment_payment_progress' && filled($value)) {
                                        $value = '="' . $value . '"';
                                    } elseif (in_array($column, $dateColumns, true) && filled($value)) {
                                        $value = dateFormatChange($value, 'd/m/Y');
                                    }

                                    $values[] = $value;
                                }

                                fputcsv($output, $values);
                            }
                        },
                        'c.id',
                        'contract_id'
                    );
            } finally {
                fclose($output);
            }
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache',
            'X-Accel-Buffering' => 'no'
        ]);
    }

    // project report

    // payable report
    public function getPayableDataTable(array $filters)
    {
        $query = $this->contractReportRepository->getPayablesReport($filters);

        return datatables()
            ->query($query)
            ->filter(function ($query) {
                // Intentionally empty.
            })

            ->addIndexColumn()
            ->editColumn(
                'project_number',
                fn($row) => 'P-' . $row->project_number
            )
            ->addColumn('payment_status', function ($row) {
                if ((float) $row->outstanding_amount <= 0) {
                    return '<span class="badge badge-success">Paid</span>';
                }

                if ((float) $row->total_paid > 0) {
                    return '<span class="badge badge-warning">Partially paid</span>';
                }

                if ($row->due_date < now()->toDateString()) {
                    return '<span class="badge badge-danger">Overdue</span>';
                }

                return '<span class="badge badge-secondary">Unpaid</span>';
            })
            ->editColumn(
                'payable_amount',
                fn($row) => number_format($row->payable_amount, 2)
            )
            ->editColumn(
                'total_paid',
                fn($row) => number_format($row->total_paid, 2)
            )
            ->editColumn(
                'outstanding_amount',
                fn($row) => number_format($row->outstanding_amount, 2)
            )
            ->editColumn(
                'contract_start_date',
                fn($row) => dateFormatChange($row->contract_start_date, 'd/m/Y')
            )
            ->editColumn(
                'contract_end_date',
                fn($row) => dateFormatChange($row->contract_end_date, 'd/m/Y')
            )
            ->editColumn(
                'due_date',
                fn($row) => dateFormatChange($row->due_date, 'd/m/Y')
            )
            ->editColumn(
                'last_paid_date',
                fn($row) => dateFormatChange($row->last_paid_date, 'd/m/Y')
            )
            ->editColumn(
                'latest_paid_date',
                fn($row) => dateFormatChange($row->latest_paid_date, 'd/m/Y')
            )
            ->editColumn(
                'returned_date',
                fn($row) => dateFormatChange($row->returned_date, 'd/m/Y')
            )
            ->addColumn('contract_status_name', function ($row) {
                $name = contractStatusName($row->contract_status);
                $class = contractStatusClass($row->contract_status);

                return "<span class=\"{$class}\">{$name}</span>";
            })
            ->addColumn('contract_source_name', function ($row) {
                if ((int) $row->indirect_status === 1) {
                    $company = $row->indirect_company_name ?: '-';

                    return "Indirect - {$company}";
                }

                return 'Direct';
            })

            ->rawColumns(['payment_status', 'contract_status_name',])
            ->toJson();
    }

    public function export(array $filters = []): StreamedResponse
    {
        $filename = 'contract-payables-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($filters) {
            $handle = fopen('php://output', 'w');

            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Payment Detail ID',
                'Contract ID',
                'Project Number',
                'Project Code',
                'Company',
                'Vendor',
                'Contract Type',
                'Contract Source',
                'Indirect Company',
                'Contract Status',
                'Composition',
                'Area',
                'Locality',
                'Property Code',
                'Property',
                'Contract Start',
                'Contract End',
                'Due Date',
                'Payable Amount',
                'Scheduled Mode',
                'Scheduled Bank',
                'Original Cheque Number',
                'Total Paid',
                'Outstanding',
                'Payment Status',
                'Payment Count',
                'Latest Paid Date',
                'Latest Paid Amount',
                'Paid Mode',
                'Paid Bank',
                'Paid Cheque Number',
                'Paying Company',
                'Payment Remarks',
                'Returned',
                'Returned Date',
                'Returned Reason',
                'Terminated',
            ]);

            $this->contractReportRepository
                ->getPayablesReport($filters)
                ->orderBy('cpd.id')
                ->chunk(500, function ($rows) use ($handle) {
                    foreach ($rows as $row) {
                        $status = match (true) {
                            (float) $row->outstanding_amount <= 0 => 'Paid',
                            (float) $row->total_paid > 0 => 'Partially Paid',
                            $row->due_date < now()->toDateString() => 'Overdue',
                            default => 'Unpaid',
                        };

                        fputcsv($handle, [
                            $row->payment_detail_id,
                            $row->contract_id,
                            'P-' . $row->project_number,
                            $row->project_code,
                            $row->company_name,
                            $row->vendor_name,
                            $row->contract_type,
                            $row->contract_source,
                            $row->indirect_company_name,
                            contractStatusName($row->contract_status),
                            '="' . $row->composition . '"',
                            $row->area_name,
                            $row->locality_name,
                            $row->property_code,
                            $row->property_name,

                            filled($row->contract_start_date)
                                ? dateFormatChange($row->contract_start_date, 'd/m/Y')
                                : '',

                            filled($row->contract_end_date)
                                ? dateFormatChange($row->contract_end_date, 'd/m/Y')
                                : '',

                            filled($row->due_date)
                                ? dateFormatChange($row->due_date, 'd/m/Y')
                                : '',

                            $row->payable_amount,
                            $row->scheduled_payment_mode,
                            $row->scheduled_bank,
                            $row->cheque_no,
                            $row->total_paid,
                            $row->outstanding_amount,
                            $status,
                            $row->payment_count,

                            filled($row->latest_paid_date)
                                ? dateFormatChange($row->latest_paid_date, 'd/m/Y')
                                : '',

                            $row->latest_paid_amount,
                            $row->paid_payment_mode,
                            $row->paid_bank_name,
                            $row->paid_cheque_number,
                            $row->paying_company_name,
                            $row->payment_remarks,
                            $row->has_returned ? 'Yes' : 'No',

                            filled($row->returned_date)
                                ? dateFormatChange($row->returned_date, 'd/m/Y')
                                : '',

                            $row->returned_reason,
                            $row->terminate_status ? 'Yes' : 'No',
                        ]);
                    }
                });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
    // payable report


    // inventory report
    public function getInventoryDataTable(array $filters)
    {
        $query = $this->contractReportRepository
            ->getInventoryReport($filters);

        return datatables()
            ->query($query)
            ->filter(function ($query) {
                // Searching is handled by the repository.
            })
            ->addIndexColumn()
            ->editColumn(
                'project_number',
                fn($row) => 'P-' . $row->project_number
            )
            ->editColumn(
                'contract_start_date',
                fn($row) => filled($row->contract_start_date)
                    ? dateFormatChange($row->contract_start_date, 'd/m/Y')
                    : ''
            )
            ->editColumn(
                'contract_end_date',
                fn($row) => filled($row->contract_end_date)
                    ? dateFormatChange($row->contract_end_date, 'd/m/Y')
                    : ''
            )
            ->editColumn(
                'maid_room',
                fn($row) => $row->maid_room ? 'Yes' : 'No'
            )
            ->editColumn(
                'unit_rent_per_annum',
                fn($row) => number_format(
                    (float) $row->unit_rent_per_annum,
                    2
                )
            )
            ->editColumn(
                'unit_rent_per_month',
                fn($row) => number_format(
                    (float) $row->unit_rent_per_month,
                    2
                )
            )
            ->editColumn(
                'rent_per_partition_bedspace_room',
                fn($row) => $row->rent_per_partition_bedspace_room
            )
            ->editColumn(
                'rent_per_flat',
                fn($row) => number_format(
                    (float) $row->rent_per_flat,
                    2
                )
            )
            ->editColumn(
                'unit_profit_percentage',
                fn($row) => number_format(
                    (float) $row->unit_profit_percentage,
                    2
                ) . '%'
            )
            ->editColumn(
                'unit_profit',
                fn($row) => number_format(
                    (float) $row->unit_profit,
                    2
                )
            )
            ->editColumn(
                'unit_revenue',
                fn($row) => number_format(
                    (float) $row->unit_revenue,
                    2
                )
            )
            ->toJson();
    }

    public function exportInventory(array $filters = []): StreamedResponse
    {
        $filename = 'contract-inventory-'
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

                fwrite($output, "\xEF\xBB\xBF");

                fputcsv($output, [
                    'Project Number',
                    'Project Code',
                    'Renewal Status',
                    'Company',
                    'Vendor',
                    'Property',
                    'Area',
                    'Locality',
                    'Contract Start',
                    'Contract End',
                    'Unit Number',
                    'Unit Type',
                    'Maid Room',
                    'Property Type',
                    'Floor Number',
                    'Unit Status',
                    'Unit Rent Per Annum',
                    'Unit Rent Per Month',
                    'Partition / Bedspace / Room',
                    'No. of Partition / Bedspace / Room',
                    'Rent per Partition / Bedspace / Room',
                    'Rent per Flat',
                    'Unit Profit %',
                    'Unit Profit',
                    'Unit Revenue',
                ]);

                $rows = $this->contractReportRepository
                    ->getInventoryReport($filters)
                    ->reorder()
                    ->lazyById(
                        1000,
                        'cu.id',
                        'contract_unit_detail_id'
                    );

                foreach ($rows as $row) {
                    fputcsv($output, [
                        'P-' . $row->project_number,
                        $row->project_code,
                        $row->renewal_status,
                        $row->company_name,
                        $row->vendor_name,
                        $row->property_name,
                        $row->area_name,
                        $row->locality_name,

                        filled($row->contract_start_date)
                            ? dateFormatChange(
                                $row->contract_start_date,
                                'd/m/Y'
                            )
                            : '',

                        filled($row->contract_end_date)
                            ? dateFormatChange(
                                $row->contract_end_date,
                                'd/m/Y'
                            )
                            : '',

                        $row->unit_number,
                        $row->unit_type,
                        $row->maid_room ? 'Yes' : 'No',
                        $row->property_type,
                        $row->floor_number,
                        $row->unit_status,
                        $row->unit_rent_per_annum,
                        $row->unit_rent_per_month,
                        $row->partition_bedspace_room,
                        $row->no_of_partition_bedspace_room,
                        $row->rent_per_partition_bedspace_room,
                        $row->rent_per_flat,
                        $row->unit_profit_percentage,
                        $row->unit_profit,
                        $row->unit_revenue,
                    ]);

                    /*
                 * Send data periodically instead of retaining the output
                 * in PHP/server buffers.
                 */
                    if (($row->contract_unit_detail_id % 500) === 0) {
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
    // inventory report


    // Occupancy report
    public function getOccupancyDataTable(array $filters)
    {
        return datatables()
            ->query($this->contractReportRepository->getOccupancyReport($filters))
            ->filter(function ($query) {})
            ->addIndexColumn()
            ->editColumn('project_number', fn($row) => 'P-' . $row->project_number)
            ->editColumn('contract_start_date', fn($row) => filled($row->contract_start_date)
                ? dateFormatChange($row->contract_start_date, 'd/m/Y') : '')
            ->editColumn('contract_end_date', fn($row) => filled($row->contract_end_date)
                ? dateFormatChange($row->contract_end_date, 'd/m/Y') : '')
            ->editColumn('maid_room', fn($row) => $row->maid_room ? 'Yes' : 'No')
            ->editColumn('is_sales_agreement_added', fn($row) => $row->is_sales_agreement_added ? 'Yes' : 'No')
            ->editColumn('unit_rent_per_annum', fn($row) => number_format((float) $row->unit_rent_per_annum, 2))
            ->editColumn('unit_rent_per_month', fn($row) => number_format((float) $row->unit_rent_per_month, 2))
            ->editColumn('rent_per_flat', fn($row) => number_format((float) $row->rent_per_flat, 2))
            ->editColumn('unit_profit_percentage', fn($row) => number_format((float) $row->unit_profit_percentage, 2) . '%')
            ->editColumn('unit_profit', fn($row) => number_format((float) $row->unit_profit, 2))
            ->editColumn('unit_revenue', fn($row) => number_format((float) $row->unit_revenue, 2))
            ->toJson();
    }

    public function exportOccupancy(array $filters = []): StreamedResponse
    {
        $filename = 'occupancy-report-' . now()->format('Y-m-d-His') . '.csv';
        $headings = [
            'Project Number',
            'Unit Number',
            'Unit Type',
            'Subunit Number',
            'Subunit Code',
            'Subunit Type',
            'Occupancy Status',
            'Maid Room',
            'Property Type',
            'Floor Number',
            'Unit Status',
            'Unit Rent Per Annum',
            'Unit Rent Per Month',
            'Rent Per Flat',
            'Unit Profit %',
            'Unit Profit',
            'Unit Revenue',
            'Project Code',
            'Company',
            'Vendor',
            'Property Code',
            'Property',
            'Area',
            'Locality',
            'Contract Start',
            'Contract End'
        ];

        return response()->streamDownload(function () use ($filters, $headings) {
            set_time_limit(0);
            $output = fopen('php://output', 'wb');
            if ($output === false) {
                throw new RuntimeException('Unable to open CSV output stream.');
            }
            try {
                fwrite($output, "\xEF\xBB\xBF");
                fputcsv($output, $headings);
                $rows = $this->contractReportRepository
                    ->getOccupancyReport($filters)
                    ->reorder('su.id')
                    ->cursor();

                $processed = 0;

                foreach ($rows as $row) {
                    fputcsv($output, [
                        'P-' . $row->project_number,
                        $row->unit_number,
                        $row->unit_type,
                        $row->subunit_no,
                        $row->subunit_code,
                        $row->subunit_type_name,
                        $row->occupancy_status,
                        $row->maid_room ? 'Yes' : 'No',
                        $row->property_type,
                        $row->floor_number,
                        $row->unit_status,
                        $row->unit_rent_per_annum,
                        $row->unit_rent_per_month,
                        $row->rent_per_flat,
                        $row->unit_profit_percentage,
                        $row->unit_profit,
                        $row->unit_revenue,
                        $row->project_code,
                        $row->company_name,
                        $row->vendor_name,
                        $row->property_code,
                        $row->property_name,
                        $row->area_name,
                        $row->locality_name,
                        filled($row->contract_start_date)
                            ? dateFormatChange($row->contract_start_date, 'd/m/Y') : '',
                        filled($row->contract_end_date)
                            ? dateFormatChange($row->contract_end_date, 'd/m/Y') : '',
                    ]);

                    if (++$processed % 500 === 0) {
                        fflush($output);
                        flush();
                    }
                }
            } finally {
                fclose($output);
            }
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache',
            'X-Accel-Buffering' => 'no'
        ]);
    }
    // Occupancy report
}
