<?php

namespace App\Exports;

use App\Models\ClearedReceivable;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class receivableReportExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    private $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * Fetch data using your repository/query method
     */
    public function collection()
    {
        $filters = $this->filters;
        // dd($filters);

        $query = ClearedReceivable::query()
            ->with([
                'agreementPaymentDetail',
                'agreementPaymentDetail.agreementPayment.installment',
                'agreementPaymentDetail.paymentMode',
                'agreementPaymentDetail.bank',
                'agreementPaymentDetail.agreement',
                'agreementPaymentDetail.agreement.tenant',
                'agreementPaymentDetail.agreement.contract',
                'agreementPaymentDetail.agreement.contract.contract_type',
                'agreementPaymentDetail.agreement.contract.contract_unit',
                'agreementPaymentDetail.agreement.contract.contract_unit_details',
                'agreementPaymentDetail.agreement.contract.property',
                'agreementPaymentDetail.agreement.agreement_units.contractUnitDetail',
                'agreementPaymentDetail.agreement.agreement_units',
            ])
            ->whereHas('agreementPaymentDetail', function ($q) {
                $q->whereIn('is_payment_received', [1, 2])
                    ->where('terminate_status', 0);
                // ->whereDate('payment_date', '>=', Carbon::today());
            });


        if (!empty($filters['search'])) {
            $search = $filters['search'];
            // dd($search);

            $query->where(function ($q) use ($search) {
                $q
                    ->orWhereHas('agreementPaymentDetail.agreement.contract', function ($q2) use ($search) {
                        $q2->where('project_number', 'like', "%$search%");
                    })
                    ->orWhereHas('agreementPaymentDetail.agreement.contract.contract_type', function ($q2) use ($search) {
                        $q2->where('contract_type', 'like', "%$search%");
                    })
                    ->orWhereHas('agreementPaymentDetail.agreement.contract.contract_unit', function ($q2) use ($search) {
                        $q2->where('business_type', 'like', "%$search%");
                    })
                    ->orWhereHas('agreementPaymentDetail.agreement.tenant', function ($q2) use ($search) {
                        $q2->where('tenant_name', 'like', "%$search%")
                            ->orWhere('tenant_email', 'like', "%$search%")
                            ->orWhere('tenant_mobile', 'like', "%$search%");
                    })

                    ->orWhereHas('agreementPaymentDetail.agreementUnit', function ($q2) use ($search) {
                        // dd($search);
                        $q2->whereHas('contractUnitDetail', function ($q3) use ($search) {
                            $q3->where('unit_number', 'like', "%$search%");
                        });
                    })

                    ->orWhereHas('agreementPaymentDetail.agreement.contract.property', function ($q2) use ($search) {
                        $q2->where('property_name', 'like', "%$search%");
                    })
                    ->orWhereHas('agreementPaymentDetail.paymentMode', function ($q2) use ($search) {
                        // dd($search);
                        $q2->where('payment_mode_name', 'like', "%$search%");
                    })
                    ->orWhereRaw("CAST(cleared_receivables.id AS CHAR) LIKE ?", ["%$search%"]);
            });
        }
        $results = $query->get();

        // dd($results);


        // Date filter
        if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
            $query->whereBetween('paid_date', [
                Carbon::createFromFormat('d-m-Y', $filters['date_from'])->format('Y-m-d'),
                Carbon::createFromFormat('d-m-Y', $filters['date_to'])->format('Y-m-d'),
            ]);
        }
        $results = $query->get();
        // dd($results);


        return $results->map(function ($row) {
            $detail = $row->agreementPaymentDetail;

            return [
                'ID' => $row->id,
                'Agreement ID' => $detail->agreement->agreement_code ?? '-',
                'Tenant Name' => $detail->agreement->tenant->tenant_name ?? '-',
                'Tenant Email' => $detail->agreement->tenant->tenant_email ?? '-',
                'Tenant Mobile' => "'" . $detail->agreement->tenant->tenant_mobile ?? '-',
                'Project Number' => $detail->agreement->contract->project_number ?? '-',
                'Contract Type' => $detail->agreement->contract->contract_type->contract_type ?? '-',
                'Business Type' => $detail->agreement->contract->contract_unit->business_type() ?? '-',
                'Property' => $detail->agreement->contract->property->property_name ?? '-',
                'Area' => $detail->agreement->contract->area->area_name ?? '-',
                'Locality' => $detail->agreement->contract->locality->locality_name ?? '-',
                'Unit Number' => optional(optional($row->agreementUnit)->contractUnitDetail)->unit_number ?? '-',
                'Payment Date' => $detail->payment_date ? Carbon::parse($detail->payment_date)->format('d-m-Y') : '-',
                'Paid Date' => $row->paid_date ? Carbon::parse($row->paid_date)->format('d-m-Y') : '-',
                'payment_amount' => $detail->payment_amount,
                'Paid Amount' => $row->paid_amount ?? 0,
                'Pending Amount' => $row->pending_amount ?? 0,
                'Payment Mode' => $row->agreementPaymentDetail->paymentMode->payment_mode_name ?? '-',
                'Bank' => $row->agreementPaymentDetail->bank->bank_name ?? '-',
                'Cheque Number' => $row->agreementPaymentDetail->cheque_number ?? '-',
                'Status' => $this->getStatusText($detail->is_payment_received),
                'Receivable cleared By' => $row->paidBy->first_name . ' ' . $row->paidBy->last_name,
                // 'Bounced Reason' => $detail->bounced_reason ?? '-',
                // 'Bounced Date' => $detail->bounced_date ? Carbon::parse($detail->bounced_date)->format('d-m-Y') : '-',
                'Transaction Type' => $detail->transaction_type == 1
                    ? 'Termination Receivable'
                    : ($row->transaction_type == 2
                        ? 'Termination Payback'
                        : 'Receivable'),
            ];
        });
    }

    /**
     * Excel headings
     */
    public function headings(): array
    {
        return [
            'ID',
            'Agreement ID',
            'Tenant Name',
            'Tenant Email',
            'Tenant Mobile',
            'Project Number',
            'Contract Type',
            'business Type',
            'Property',
            'Area',
            'Locality',
            'Unit Number',
            'Payment Date',
            'Paid Date',
            'payment Amount',
            'Paid Amount',
            'Pending Amount',
            'Payment Mode',
            'Bank',
            'Cheque Number',
            'Status',
            'Receivable cleared By',
            // 'Bounced Reason',
            // 'Bounced Date',
            'Transaction Type'
        ];
    }

    /**
     * Convert is_payment_received to human-readable text
     */
    private function getStatusText($status)
    {
        return match ($status) {
            0 => 'Pending',
            1 => 'Paid',
            2 => 'Partially Paid',
            // 3 => 'Bounced',
            // default => '-',
        };
    }
}
