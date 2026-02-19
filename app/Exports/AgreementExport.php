<?php

namespace App\Exports;

use App\Models\Agreement;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AgreementExport implements FromCollection, WithHeadings
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
        $query = Agreement::query();

        // with(
        //     'company',
        //     'contract',
        //     'tenant',
        //     'agreement_units.contractUnitDetail',
        //     'contract.contract_type'
        // );

        if ($this->search) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {

                $q->orwhere('agreement_code', 'like', "%{$search}%")
                    ->orWhereHas('contract', function ($q) use ($search) {
                        $q->where('project_number', 'like', "%{$search}%");
                    })

                    ->orWhereHas('company', function ($q) use ($search) {
                        $q->where('company_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('contract.contract_type', function ($q) use ($search) {
                        $q->where('contract_type', 'like', "%{$search}%");
                    })
                    ->orWhereHas('tenant', function ($q) use ($search) {
                        $q->where('tenant_name', 'like', "%{$search}%")
                            ->orWhere('tenant_email', 'like', "%{$search}%")
                            ->orWhere('tenant_mobile', 'like', "%{$search}%");
                    })
                    ->orWhereHas('contract.property', function ($q) use ($search) {
                        $q->where('property_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('agreement_documents', function ($q) use ($search) {
                        $q->where('document_number', 'like', "%{$search}%");
                    })
                    ->orWhereHas('contract.contract_unit', function ($q) use ($search) {
                        $q->whereRaw("
                    CASE
                        WHEN business_type = 1 THEN 'B2B'
                        WHEN business_type = 2 THEN 'B2C'
                    END LIKE ?
                ", ["%{$search}%"]);
                    })
                    ->orWhereRaw("
                    CASE
                        WHEN agreement_status = 0 THEN 'Active'
                        WHEN agreement_status = 1 THEN 'Terminated'
                        WHEN agreement_status = 2 THEN 'Expired'

                    END LIKE ?
                ", ["%{$search}%"])
                    ->orWhereHas('contract.contract_unit_details', function ($q) use ($search) {
                        $q->where('unit_number', 'like', "%{$search}%");
                    })
                    ->orWhereHas('contract.contract_subunit_details', function ($q) use ($search) {
                        $q->where('subunit_no', 'like', "%{$search}%");
                    })
                    ->orWhereRaw("CAST(agreements.id AS CHAR) LIKE ?", ["%{$search}%"]);
            });
        }
        // dd($this->filter);
        if ($this->filter) {
            $query->where('agreements.id', $this->filter);
        }
        // dd($query->get()->toArray());
        return $query->get()
            ->map(function ($agreement) {
                // $collection = $query->get()->map(function ($agreement) {
                $unitNumbers = $agreement->agreement_units
                    ->map(fn($au) => optional($au->contractUnitDetail)->unit_number)
                    ->filter()
                    ->implode(', ');
                $sub = optional(
                    $agreement->agreement_units->first()?->contractSubunitDetail
                )->subunit_no;
                $sub = $sub ?? " - ";
                // $totalRevenue = $agreement->agreement_units
                //     ->sum(fn($au) => optional($au->contractUnitDetail)->unit_revenue);
                return [
                    'Project ID' => "P - " . $agreement->contract->project_number,
                    'Agreemant CODE' => $agreement->agreement_code,
                    'Contract Type' => $agreement->contract->contract_type->contract_type,
                    'Business Type' => $agreement->contract->contract_unit->business_type(),

                    'Start Date'  => $agreement->start_date,
                    'End Date'  => $agreement->end_date,
                    'Company Name' => $agreement->company->company_name,
                    'Property' => $agreement->contract->property->property_name,
                    'Area' => $agreement->contract->area->area_name,
                    'Locality' => $agreement->contract->locality->locality_name,
                    'Tenant Name' => $agreement->tenant->tenant_name ?? '',
                    'Tenant Email' => $agreement->tenant->tenant_email ?? '',
                    'Tenant Phone' => $agreement->tenant->tenant_mobile ?? '',
                    'Passport Number' =>  optional(
                        $agreement->agreement_documents->where('document_type', 1)->first()
                    )->document_number,
                    'Emirates ID Number' => optional(
                        $agreement->agreement_documents->where('document_type', 2)->first()
                    )->document_number,
                    'Unit Numbers' => $unitNumbers ?: '-',
                    'Sub Unit'  => $sub,
                    'Total Rent Annum' => $agreement->agreement_payment->total_rent_annum ?? '',
                    'Created_at' => $agreement->created_at,


                ];
            });
        // dd($collection);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Agreement CODE',
            'Contact Type',
            'Business Type',
            'Start Date',
            'End Date',
            'Company Name',
            'Property',
            'Area',
            'Locality',
            'Tenant Name',
            'Tenant Email',
            'Tenant Phone',
            'Passport Number',
            'Emirates ID Number',
            'Unit Numbers',
            'Sub Unit',
            'Total Rent Annum',
            'Created_at',
        ];
    }
}
