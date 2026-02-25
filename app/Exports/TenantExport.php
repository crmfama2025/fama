<?php

namespace App\Exports;

use App\Models\AgreementTenant;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TenantExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function __construct(
        protected $search = null,
        protected $filter = null,
    ) {}
    public function collection()
    {
        // $query = Vendor::with('company');
        $query = AgreementTenant::query()->with(['nationality', 'paymentMode', 'paymentFrequency', 'tenantDocuments']);

        if ($this->search) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('tenant_name', 'like', "%{$search}%")
                    ->orWhere('tenant_email', 'like', "%{$search}%")
                    ->orWhere('tenant_mobile', 'like', "%{$search}%")
                    ->orWhere('tenant_address', 'like', "%{$search}%")
                    ->orWhere('tenant_street', 'like', "%{$search}%")
                    ->orWhere('tenant_city', 'like', "%{$search}%")
                    ->orWhere('contact_person', 'like', "%{$search}%")
                    ->orWhere('contact_email', 'like', "%{$search}%")
                    ->orWhere('contact_number', 'like', "%{$search}%")
                    ->orWhere('contact_person_department', 'like', "%{$search}%")
                    ->orWhere('tenant_type', 'like', "%{$search}%")
                    ->orWhere('security_cheque_status', 'like', "%{$search}%")
                    ->orWhereHas('nationality', function ($q2) use ($search) {
                        $q2->where('nationality_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('paymentMode', function ($q2) use ($search) {
                        $q2->where('payment_mode_name', 'like', "%{$search}%");
                    })
                    ->orWhereRaw("CAST(agreement_tenants.id AS CHAR) LIKE ?", ["%{$search}%"]);
            });
        }

        // if ($this->filter) {
        //     $query->where('company_id', $this->filter);
        // }


        return $query->get()
            ->map(function ($tenant) {
                return [

                    // 'ID' => $tenant->id,
                    'Tenant Name' => $tenant->tenant_name,
                    // 'Company' => $tenant->company->company_name ?? '',
                    'Tenant Email' => $tenant->tenant_email,
                    'Tenant Phone' => $tenant->tenant_phone,
                    'Tenant Address' => $tenant->tenant_address,
                    'Tenant Street' => $tenant->tenant_street,
                    'Tenant City' => $tenant->tenant_city,

                    'Contact Person' => $tenant->contact_person,
                    'Contact Person Email' => $tenant->contact_email,
                    'Contact Person Number' => $tenant->contact_number,
                    'Contact Person Department' => $tenant->contact_person_department,
                    'Tenant Type' => $tenant->tenant_type,
                    'Security Cheque Status' => $tenant->security_cheque_status,
                    'Nationality' => $tenant->nationality->nationality_name ?? '-',
                    'Payment Mode' => $tenant->paymentMode->payment_mode_name ?? '-',
                    'Payment Frequency' => $tenant->paymentFrequency->profit_interval_name ?? '-',
                    'Trade License Number' => optional($tenant->tenantDocuments->where('document_type', 3)->first())->document_number ?? '-',




                ];
            });
    }
    public function headings(): array
    {
        return [
            'Tenant Name',
            'Tenant Email',
            'Tenant Phone',
            'Tenant Address',
            'Tenant Street',
            'Tenant City',
            'Contact Person',
            'Contact Person Email',
            'Contact Person Number',
            'Contact Person Department',
            'Tenant Type',
            'Security Cheque Status',
            'Nationality',
            'Payment Mode',
            'Payment Frequency',
            'Trade License Number',
        ];
    }
}
