<?php

namespace App\Repositories\Agreement;

use App\Models\Agreement;
use App\Models\AgreementDocument;
use App\Models\AgreementTenant;
use App\Models\Contract;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class AgreementTenantRepository
{


    public function create(array $data)
    {
        return AgreementTenant::create($data);
    }
    public function update($id, array $data)
    {
        $tenant = $this->find($id);
        // dd($tenant);
        $tenant->update($data);
        return $tenant;
    }
    public function find($id)
    {
        // dd($id);
        return AgreementTenant::findOrFail($id);
    }


    public function getQuery(array $filters = []): Builder
    {
        $query = AgreementTenant::query()
            ->with(['nationality', 'paymentMode', 'paymentFrequency']); // eager load relationships

        // List of searchable columns in the tenants table
        $searchable = [
            'tenant_name',
            'tenant_email',
            'tenant_mobile',
            'tenant_address',
            'tenant_street',
            'tenant_city',
            'contact_person',
            'contact_email',
            'contact_number',
            'contact_person_department',
            'tenant_type',
            'security_cheque_status'
        ];

        // Apply search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            // dd($search);

            $query->where(function ($q) use ($search, $searchable) {
                foreach ($searchable as $column) {
                    $q->orWhere($column, 'like', "%{$search}%");
                }

                // Search in related tables
                // dump($search);
                $q->orWhereHas('nationality', fn($q2) => $q2->where('nationality_name', 'like', "%{$search}%"));
                $q->orWhereHas('paymentMode', fn($q2) => $q2->where('payment_mode_name', 'like', "%{$search}%"));
                $q->orWhereHas('paymentFrequency', fn($q2) => $q2->where('profit_interval_name', 'like', "%{$search}%"));
            });
        }

        // Apply specific column filters if sent
        foreach ($filters as $key => $value) {
            if (in_array($key, $searchable) && !empty($value)) {
                $query->where($key, 'like', "%{$value}%");
            }
        }
        $results = $query->get();
        // dd($results);

        return $query;
    }
    public function delete($id)
    {
        return DB::transaction(function () use ($id) {
            $tenant = $this->find($id);
            $tenant->delete();
        });
    }
}
