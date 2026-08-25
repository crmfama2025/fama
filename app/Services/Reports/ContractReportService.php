<?php

namespace App\Services\Reports;

use App\Repositories\Reports\ContractReportRepository;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContractReportService
{
    public function __construct(
        protected ContractReportRepository $contractReportRepository,
    ) {}

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
}
