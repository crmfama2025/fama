<?php

namespace App\Repositories;

use App\Models\Nationality;
use Illuminate\Contracts\Database\Eloquent\Builder;

class NationalityRepository
{
    public function all()
    {
        // return Nationality::all();
        return Nationality::orderBy('nationality_name', 'asc')->get();
    }

    public function find($id)
    {
        return Nationality::findOrFail($id);
    }

    public function getByName($bankData)
    {
        return Nationality::where($bankData)->first();
    }

    public function create($data)
    {
        return Nationality::create($data);
    }

    public function updateOrRestore(int $id, array $data)
    {
        $bank = Nationality::withTrashed()->findOrFail($id);

        if ($bank->trashed()) {
            $bank->restore();
        }

        $bank->update($data);

        return $bank;
    }

    public function delete($id)
    {
        $bank = $this->find($id);
        return $bank->delete();
    }

    public function checkIfExist($data)
    {
        $existing = Nationality::withTrashed()
            // ->where('company_id', $data['company_id'])
            ->where('nationality_name', $data['nationality_name'])
            ->first();

        // if ($existing && $existing->trashed()) {
        //     // $existing->restore();
        //     return $existing;
        // }

        return $existing;
    }

    public function getQuery(array $filters = []): Builder
    {
        // print_r($filters);
        $query = Nationality::query()
            ->select('nationalities.*'); //, 'companies.company_name'
        // ->join('companies', 'companies.id', '=', 'nationalities.company_id');

        if (!empty($filters['search'])) {
            $query->orwhere('nationality_name', 'like', '%' . $filters['search'] . '%')
                ->orWhere('nationality_code', 'like', '%' . $filters['search'] . '%')
                ->orWhere('nationality_short_code', 'like', '%' . $filters['search'] . '%')
                // ->orWhereHas('company', function ($q) use ($filters) {
                //     $q->where('company_name', 'like', '%' . $filters['search'] . '%');
                // })
                ->orWhereRaw("CAST(nationalities.id AS CHAR) LIKE ?", ['%' . $filters['search'] . '%']);
        }

        // if (!empty($filters['company_id'])) {
        //     $query->Where('nationalities.company_id', $filters['company_id']);
        // }

        return $query;
    }

    public function insertBulk(array $rows)
    {
        return Nationality::insert($rows); // bulk insert
    }
}
