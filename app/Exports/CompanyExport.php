<?php

namespace App\Exports;

use App\Models\Company;
use App\Models\Vendor;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CompanyExport implements FromCollection, WithHeadings
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
        $query = Company::with('industry');

        if ($this->search) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                    ->orWhere('company_code', 'like', "%{$search}%")
                    ->orWhere('company_short_code', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('website', 'like', "%{$search}%")
                    ->orWhere('trade_license_number', 'like', "%{$search}%")
                    ->orWhere('reagistration_no', 'like', "%{$search}%")

                    ->orWhereHas('industry', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereRaw("CAST(companies.id AS CHAR) LIKE ?", ["%{$search}%"]);
            });
        }

        if ($this->filter) {
            $query->where('companies.id', $this->filter);
        }
        return $query->get()
            ->map(function ($company) {
                return [
                    'ID' => $company->id,
                    'Company Code' => $company->company_code,
                    'Company Name' => $company->company_name ?? '',
                    'Company Arabic Name' => $company->company_arabic_name ?? '',
                    'Company Short Code' => $company->company_short_code ?? '',
                    'Industry' => $company->industry->name ?? '',
                    'Company Phone' => $company->phone,
                    'Company Email' => $company->email,
                    'Company Address' => $company->address,
                    'Webiste' => $company->website,
                    'TRN Number' => $company->trade_license_number ?? '',
                    'Registration Number' => $company->registration_no ?? '',



                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Comapny Code',
            'Company Name',
            'Company Arabic Name',
            'company Short Code',
            'Industry',
            'Company Phone',
            'Company Email',
            'Company Address',
            'Website',
            'TRN Number',
            'Registration Number'

        ];
    }
}
