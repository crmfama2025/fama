<?php

namespace App\Exports;

use App\Models\Agreement;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AgreementExpiryExport implements FromCollection, WithHeadings
{
    public function __construct(
        protected array $filters = []
    ) {}

    public function collection()
    {
        $oneMonthsLater = Carbon::today()
            ->addMonths(1)
            ->format('Y-m-d');

        $query = Agreement::query()
            ->select([
                'agreements.*',
                'contracts.project_number',
                'companies.company_name',
                'agreement_tenants.tenant_name',
                'agreement_tenants.tenant_email',
                'agreement_tenants.tenant_mobile',
                'contract_types.contract_type',
                'contract_units.business_type as business_type',
            ])
            ->join(
                'contracts',
                'contracts.id',
                '=',
                'agreements.contract_id'
            )
            ->join(
                'properties',
                'properties.id',
                '=',
                'contracts.property_id'
            )
            ->join(
                'companies',
                'companies.id',
                '=',
                'agreements.company_id'
            )
            ->join(
                'agreement_tenants',
                'agreement_tenants.id',
                '=',
                'agreements.tenant_id'
            )
            ->join(
                'contract_types',
                'contract_types.id',
                '=',
                'contracts.contract_type_id'
            )
            ->join(
                'contract_units',
                'contract_units.contract_id',
                '=',
                'contracts.id'
            )

            // Same expiry condition as getExpired()
            ->whereIn('agreements.agreement_status', [0, 2])
            ->where('agreements.end_date', '<=', $oneMonthsLater)

            // Don't include agreements which already have a renewal
            ->whereNotIn('agreements.id', function ($q) {
                $q->select('parent_agreement_id')
                    ->from('agreements')
                    ->whereNotNull('parent_agreement_id');
            });

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        $search = $this->filters['search'] ?? null;

        if (!empty($search)) {

            $query->where(function ($q) use ($search) {

                $q->where(
                    'agreements.agreement_code',
                    'like',
                    "%{$search}%"
                )

                    ->orWhere(
                        'contracts.project_number',
                        'like',
                        "%{$search}%"
                    )

                    ->orWhere(
                        'companies.company_name',
                        'like',
                        "%{$search}%"
                    )

                    ->orWhere(
                        'agreement_tenants.tenant_name',
                        'like',
                        "%{$search}%"
                    )

                    ->orWhere(
                        'agreement_tenants.tenant_email',
                        'like',
                        "%{$search}%"
                    )

                    ->orWhere(
                        'agreement_tenants.tenant_mobile',
                        'like',
                        "%{$search}%"
                    )

                    ->orWhere(
                        'contract_types.contract_type',
                        'like',
                        "%{$search}%"
                    )

                    ->orWhere(
                        'contract_units.business_type',
                        'like',
                        "%{$search}%"
                    )

                    ->orWhereRaw(
                        "CAST(agreements.id AS CHAR) LIKE ?",
                        ["%{$search}%"]
                    );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        if (
            isset($this->filters['status']) &&
            $this->filters['status'] !== 'all'
        ) {
            $query->where(
                'agreements.agreement_status',
                $this->filters['status']
            );
        }

        /*
        |--------------------------------------------------------------------------
        | End Date From
        |--------------------------------------------------------------------------
        */

        if (!empty($this->filters['end_date_from'])) {

            $from = Carbon::createFromFormat(
                'd-m-Y',
                $this->filters['end_date_from']
            )->format('Y-m-d');

            $query->whereDate(
                'agreements.end_date',
                '>=',
                $from
            );
        }

        /*
        |--------------------------------------------------------------------------
        | End Date To
        |--------------------------------------------------------------------------
        */

        if (!empty($this->filters['end_date_to'])) {

            $to = Carbon::createFromFormat(
                'd-m-Y',
                $this->filters['end_date_to']
            )->format('Y-m-d');

            $query->whereDate(
                'agreements.end_date',
                '<=',
                $to
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Company
        |--------------------------------------------------------------------------
        */

        if (!empty($this->filters['companyId'])) {

            $query->where(
                'agreements.company_id',
                $this->filters['companyId']
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Contract
        |--------------------------------------------------------------------------
        */

        if (!empty($this->filters['contractId'])) {

            $query->where(
                'agreements.contract_id',
                $this->filters['contractId']
            );
        }

        $agreements = $query
            ->orderBy('agreements.id', 'desc')
            ->get();

        return $agreements->map(function ($agreement) {

            return [
                'Project ID' => 'P - ' . $agreement->project_number,

                'Agreement Code' =>
                $agreement->agreement_code,

                'Contract Type' =>
                $agreement->contract_type,

                'Business Type' =>
                $agreement->business_type == 1
                    ? 'B2B'
                    : ($agreement->business_type == 2
                        ? 'B2C'
                        : ''),

                'Start Date' =>
                $agreement->start_date,

                'End Date' =>
                $agreement->end_date,

                'Company Name' =>
                $agreement->company_name,

                'Tenant Name' =>
                $agreement->tenant_name,

                'Tenant Email' =>
                $agreement->tenant_email,

                'Tenant Phone' =>
                $agreement->tenant_mobile,

                'Agreement Status' =>
                match ((int) $agreement->agreement_status) {
                    0 => 'Active',
                    1 => 'Terminated',
                    2 => 'Expired',
                    default => '',
                },

                'Created At' =>
                $agreement->created_at,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Project ID',
            'Agreement Code',
            'Contract Type',
            'Business Type',
            'Start Date',
            'End Date',
            'Company Name',
            'Tenant Name',
            'Tenant Email',
            'Tenant Phone',
            'Agreement Status',
            'Created At',
        ];
    }
}
