<?php

namespace App\Repositories;

use App\Models\Company;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class CompanyRepository
{
    public function all($module = null, $submodule = null)
    {
        // return Company::all();
        $query = Company::where('status', 1);
        if ($module) {
            $query->permittedForModule($module, $submodule);
        }
        return $query->get();
    }

    public function find($id)
    {
        return Company::findOrFail($id);
    }

    public function findId($data)
    {
        return Company::where($data)->first();
    }

    public function create(array $data)
    {
        return Company::create($data);
    }

    public function update($id, array $data)
    {
        $company = $this->find($id);
        $company->update($data);
        return $company;
    }

    public function delete($id)
    {
        $company = $this->find($id);
        $company->deleted_by = auth()->user()->id;
        $company->save();
        return $company->delete();
    }

    public function checkIfExist($data)
    {
        $existing = Company::withTrashed()
            ->where('company_name', $data['company_name'])
            ->first();

        if ($existing && $existing->trashed()) {
            // $existing->restore();
            return $existing;
        }
    }

    public function getByData($companyData)
    {
        return Company::where($companyData)->first();
    }
    public function getQuery(array $filters = []): Builder
    {
        $query = Company::query()
            ->select('companies.*', 'industries.name as industry_name')
            ->join('industries', 'industries.id', '=', 'companies.industry_id');

        if (!empty($filters['search'])) {
            $query->orwhere('company_name', 'like', '%' . $filters['search'] . '%')
                ->orWhere('company_code', 'like', '%' . $filters['search'] . '%')
                ->orWhere('company_short_code', 'like', '%' . $filters['search'] . '%')
                ->orWhere('phone', 'like', '%' . $filters['search'] . '%')
                ->orWhere('email', 'like', '%' . $filters['search'] . '%')
                ->orWhere('address', 'like', '%' . $filters['search'] . '%')
                ->orWhere('website', 'like', '%' . $filters['search'] . '%')

                ->orWhereHas('industry', function ($q) use ($filters) {
                    $q->where('name', 'like', '%' . $filters['search'] . '%');
                })
                ->orWhereRaw("CAST(companies.id AS CHAR) LIKE ?", ['%' . $filters['search'] . '%']);
        }

        if (!empty($filters['company_id'])) {
            $query->Where('companies.id', $filters['company_id']);
        }

        return $query;
    }
    public function getWithIndustry($module = null, $submodule = null)
    {
        $query = Company::where('industry_id', 1);
        if ($module) {
            $query->permittedForModule($module, $submodule);
        }

        return $query->get();
    }
}
