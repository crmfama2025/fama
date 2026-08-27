<?php

namespace App\Exports;

use App\Repositories\Reports\ReceivableReportRepository;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReceivableDetailedReportExport  implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithStyles
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        return app(ReceivableReportRepository::class)
            ->getQuery($this->filters);
    }

    public function headings(): array
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

    public function map($row): array
    {
        $projectNumber = $row->project_number
            ? 'P - ' . $row->project_number
            : '-';

        $companyName = $row->company_name ?? '-';

        $tenantName = $row->tenant_name ?? '-';

        $propertyName = $row->property_name ?? '-';

        $unitNumber = $row->unit_number ?? '-';

        $subunitNo = $row->subunit_no ?? '-';

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

        $installment = $row->installment_position ?? '-';

        $composition =  $row->installment_position
            . '/'
            . $row->installment_count;

        $totalInstallments =
            ($row->installment_position ?? 0)
            . '/'
            . ($row->installment_count ?? 0);

        $installmentName = $row->installment_name ?? '-';

        $paidAmount = $row->paid_amount_total ?? 0;

        $pendingAmount = $row->pending_amount
            ?? $row->payment_amount
            ?? 0;

        $paidDate = $row->paid_date
            ? Carbon::parse($row->paid_date)->format('d-m-Y')
            : '-';

        $paidMode = $row->paid_mode_name ?? '-';

        $paidBank = $row->paid_bank_name ?? '-';

        $paidCompany = $row->paid_company_name ?? '-';

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
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('1:1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => [
                    'rgb' => 'FFFFFF',
                ],
            ],
            'fill' => [
                'fillType' => 'solid',
                'color' => [
                    'rgb' => '1F4E78',
                ],
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(25);

        $sheet->freezePane('A2');

        return $sheet;
    }
}
