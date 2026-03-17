<?php

namespace App\Exports;

use App\Models\SalesTenantAgreement;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SalesTenantExport implements FromCollection, WithHeadings
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
        $query = SalesTenantAgreement::query()->with(['property', 'area', 'locality', 'tenant']);

        if ($this->search) {
            $search = $this->search;

            $query->where(function ($q) use ($search) {

                // agreement fields
                $q->where('sales_agreement_code', 'like', "%{$search}%")

                    // tenant relation search
                    ->orWhereHas('tenant', function ($t) use ($search) {
                        $searchLower = strtolower($search);

                        $t->where('tenant_name', 'like', "%{$search}%")
                            ->orWhere('tenant_code', 'like', "%{$search}%")
                            ->orWhere('tenant_email', 'like', "%{$search}%")
                            ->orWhere('tenant_mobile', 'like', "%{$search}%")
                            ->orWhere('tenant_address', 'like', "%{$search}%")
                            ->orWhere('tenant_street', 'like', "%{$search}%")
                            ->orWhere('tenant_city', 'like', "%{$search}%")
                            ->orWhere('contact_person', 'like', "%{$search}%")
                            ->orWhere('contact_email', 'like', "%{$search}%")
                            ->orWhere('contact_number', 'like', "%{$search}%")
                            ->orWhere('contact_person_department', 'like', "%{$search}%")
                            ->orWhereRaw("
                            CASE tenant_type
                                WHEN 1 THEN 'b2b'
                                WHEN 2 THEN 'b2c'
                            END LIKE ?
                        ", ["%{$searchLower}%"])
                            ->orWhereRaw("
                    CASE
                        WHEN is_approved = 0 THEN 'Pending'
                        WHEN is_approved = 1 THEN 'Approved'
                        WHEN is_approved = 2 THEN 'Rejected'

                    END LIKE ?
                ", ["%{$search}%"]);
                    })

                    // property search
                    ->orWhereHas('property', function ($p) use ($search) {
                        $p->where('property_name', 'like', "%{$search}%");
                    })

                    // nationality search
                    ->orWhereHas('tenant.nationality', function ($n) use ($search) {
                        $n->where('nationality_name', 'like', "%{$search}%");
                    })
                    // property search
                    ->orWhereHas('area', function ($p) use ($search) {
                        $p->where('area_name', 'like', "%{$search}%");
                    })
                    // property search
                    ->orWhereHas('locality', function ($p) use ($search) {
                        $p->where('locality_name', 'like', "%{$search}%");
                    });

                // payment mode search
                // ->orWhereHas('paymentMode', function ($pm) use ($search) {
                //     $pm->where('payment_mode_name', 'like', "%{$search}%");
                // });
            });
        }

        // if ($this->filter) {
        //     $query->where('company_id', $this->filter);
        // }


        return $query->get()
            ->map(function ($tenant) {
                return [

                    // 'ID' => $tenant->id,
                    'Tenant Code' => $tenant->sales_agreement_code,
                    'Property' => $tenant->property->property_name,
                    'Area' => $tenant->area->area_name,
                    'Locality' => $tenant->locality->locality_name,
                    'Tenant Name' => $tenant->tenant->tenant_name,
                    // 'Company' => $tenant->company->company_name ?? '',
                    'Tenant Email' => $tenant->tenant->tenant_email,
                    'Tenant Phone' => $tenant->tenant->tenant_mobile,
                    'Tenant Address' => $tenant->tenant->tenant_address,
                    'Tenant Street' => $tenant->tenant->tenant_street,
                    'Tenant City' => $tenant->tenant->tenant_city,

                    'Contact Person' => $tenant->tenant->contact_person,
                    'Contact Person Email' => $tenant->tenant->contact_email,
                    'Contact Person Number' => $tenant->tenant->contact_number,
                    'Contact Person Department' => $tenant->tenant->contact_person_department ?? '- ',
                    'Tenant Type' => $tenant->tenant->tenant_type == 1 ? 'B2B' : 'B2C',
                    // 'Security Cheque Status' => $tenant->security_cheque_status,
                    'Nationality' => $tenant->tenant->nationality->nationality_name ?? '-',
                    // 'Payment Mode' => $tenant->paymentMode->payment_mode_name ?? '-',
                    // 'Payment Frequency' => $tenant->paymentFrequency->profit_interval_name ?? '-',
                    'Trade License Number' => optional($tenant->tenant->tenantDocuments->where('document_type', 3)->first())->document_number ?? '-',
                    'Start Date' => $tenant->start_date ? Carbon::parse($tenant->start_date)->format('d-m-Y') : '-',
                    'End Date' => $tenant->end_date ? Carbon::parse($tenant->end_date)->format('d-m-Y') : '-',
                    'Status' => $tenant->is_approved == 0 ? 'Pending' : ($tenant->is_approved == 1 ? 'Approved' : 'Rejected'),




                ];
            });
    }
    public function headings(): array
    {
        return [
            'Tenant Code',
            'Property',
            'Area',
            'Locality',
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
            // 'Security Cheque Status',
            'Nationality',
            // 'Payment Mode',
            // 'Payment Frequency',
            'Trade License Number',
            'Start Date',
            'End Date',
            'Status'
        ];
    }
}
