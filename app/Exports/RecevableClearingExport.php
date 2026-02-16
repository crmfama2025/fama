<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\AgreementPaymentDetail;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;


class RecevableClearingExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $search;
    protected $filters;

    public function __construct($search = null, array $filters = [])
    {
        $this->search = $search;
        $this->filters = $filters;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Get company IDs where user has finance.payable permission
        $permittedCompanyIds = getUserPermittedCompanyIds(auth()->id(), 'finance.receivable_cheque_clearing');

        $query = AgreementPaymentDetail::query()
            ->with([
                'agreementPayment.installment',
                'paymentMode',
                'bank',
                'agreement',
                'agreement.tenant',
                'agreement.contract',
                'agreement.contract.contract_type',
                'agreement.contract.property',
                'agreement.agreement_units.contractUnitDetail',
                'clearedReceivables'
            ])
            ->where('is_payment_received', '!=', 1)
            ->where('terminate_status', 0);
        // ->whereDate('payment_date', '>=', Carbon::today())

        $query->whereHas('agreement.company', function ($q) use ($permittedCompanyIds) {
            $q->whereIn('company_id', $permittedCompanyIds);
        });
        // Apply filters
        $filters = $this->filters;
        // dd($filters);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->orWhereHas('agreement.contract', function ($q2) use ($search) {
                    $q2->where('project_number', 'like', "%$search%");
                })
                    ->orWhereHas('agreement.contract.contract_type', function ($q2) use ($search) {
                        $q2->where('contract_type', 'like', "%$search%");
                    })
                    ->orWhereHas('agreement.tenant', function ($q2) use ($search) {
                        $q2->where('tenant_name', 'like', "%$search%")
                            ->orWhere('tenant_email', 'like', "%$search%")
                            ->orWhere('tenant_mobile', 'like', "%$search%");
                    })
                    ->orWhereHas('agreementUnit.contractUnitDetail', function ($q2) use ($search) {
                        $q2->where('unit_number', 'like', "%$search%");
                    })
                    ->orWhereHas('agreement.contract.property', function ($q2) use ($search) {
                        $q2->where('property_name', 'like', "%$search%");
                    })
                    ->orWhereHas('paymentMode', function ($q2) use ($search) {
                        $q2->where('payment_mode_name', 'like', "%$search%");
                    })
                    ->orWhereRaw("CAST(agreement_payment_details.id AS CHAR) LIKE ?", ["%$search%"]);
            });
        }

        if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
            $query->whereBetween('payment_date', [
                Carbon::createFromFormat('d-m-Y', $filters['date_from'])->format('Y-m-d'),
                Carbon::createFromFormat('d-m-Y', $filters['date_to'])->format('Y-m-d')
            ]);
        }

        if (!empty($filters['unit_id'])) {
            $query->whereHas('agreementUnit.contractUnitDetail', function ($q) use ($filters) {
                $q->where('id', $filters['unit_id']);
            });
        } elseif (!empty($filters['property_id'])) {
            $query->whereHas('agreement.contract.property', function ($q) use ($filters) {
                $q->where('id', $filters['property_id']);
            });
        }

        if (!empty($filters['mode_id'])) {
            $query->where('payment_mode_id', $filters['mode_id']);
        }

        // Fetch results
        $results = $query->get();
        // dd($results);

        // Map the results for export
        return $results->map(function ($row) {
            return [
                // 'ID' => $row->id,
                // 'Agreement ID' => $row->agreement_id ?? '-',
                'Tenant Name' => $row->agreement->tenant->tenant_name ?? '-',
                'Tenant Email' => $row->agreement->tenant->tenant_email ?? '-',
                'Tenant Mobile' => "'" . ($row->agreement->tenant->tenant_mobile ?? '-'),
                'Project Number' => $row->agreement->contract->project_number ?? '-',
                'Company name' => $row->agreement->company->company_name ?? '-',
                'Contract Type' => $row->agreement->contract->contract_type->contract_type ?? '-',
                'Business Type' => $row->agreement->contract->contract_unit->business_type() ?? '-',
                'Property' => $row->agreement->contract->property->property_name ?? '-',
                'Property' => $row->agreement->contract->property->property_name ?? '-',
                'Area' => $row->agreement->contract->area->area_name ?? '-',
                'Locality' => $row->agreement->contract->locality->locality_name ?? '-',
                'Unit Number' => optional(optional($row->agreementUnit)->contractUnitDetail)->unit_number ?? '-',

                'Payment Date' => $row->payment_date ? Carbon::parse($row->payment_date)->format('d-m-Y') : '-',
                'Payment Amount' => $row->payment_amount ?? 0,
                'Payment Mode' => $row->paymentMode->payment_mode_name ?? '-',
                'Bank' => $row->bank->bank_name ?? '-',
                'Cheque Number' => $row->cheque_number ?? '-',
                'Status' => $this->getStatusText($row->is_payment_received),
                'Bounced Reason' => $row->bounced_reason ?? '-',
                'Bounced Date' => $row->bounced_date ? Carbon::parse($row->bounced_date)->format('d-m-Y') : '-',
                'Bounced By' => optional($row->bouncedBy)->first_name . ' ' . optional($row->bouncedBy)->last_name ?? '-',
                'Transaction Type' => $row->transaction_type == 1
                    ? 'Termination Receivable'
                    : ($row->transaction_type == 2
                        ? 'Termination Payback'
                        : 'Receivable'),
            ];
        });
    }

    /**
     * Excel column headings
     */
    public function headings(): array
    {
        return [
            // 'ID',
            // 'Agreement ID',
            'Tenant Name',
            'Tenant Email',
            'Tenant Mobile',
            'Project Number',
            'Company name',
            'Contract Type',
            'Business Type',
            'Property',
            'Area',
            'Locality',
            'Unit Number',
            'Payment Date',
            'Payment Amount',
            'Payment Mode',
            'Bank',
            'Cheque Number',
            'Status',
            'Bounced Reason',
            'Bounced Date',
            'Bounced By',
            'Transaction Type'
        ];
    }

    /**
     * Convert is_payment_received to human-readable status
     */
    private function getStatusText($status)
    {
        switch ($status) {
            case 0:
                return 'Pending';
            case 1:
                return 'Paid';
            case 2:
                return 'Partially Paid';
        }
    }
}
