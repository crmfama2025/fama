<?php

namespace App\Repositories;

use App\Models\Bank;
use Illuminate\Contracts\Database\Eloquent\Builder;

class BankRepository
{
    public function all()
    {
        // return Bank::all();
        return Bank::where('status', 1)->get();
    }

    public function find($id)
    {
        return Bank::findOrFail($id);
    }

    public function getByName($bankData)
    {
        return Bank::where($bankData)->first();
    }

    public function create($data)
    {
        return Bank::create($data);
    }

    public function updateOrRestore(int $id, array $data)
    {
        $bank = Bank::withTrashed()->findOrFail($id);

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
        $existing = Bank::withTrashed()
            // ->where('company_id', $data['company_id'])
            ->where('bank_name', $data['bank_name'])
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
        $query = Bank::query()->accessible('view')
            ->select('banks.*') //, 'companies.company_name'
            ->join('companies', 'companies.id', '=', 'banks.company_id');

        if (!empty($filters['search'])) {
            $query->where('bank_name', 'like', '%' . $filters['search'] . '%')
                ->orWhere('bank_code', 'like', '%' . $filters['search'] . '%')
                ->orWhere('bank_short_code', 'like', '%' . $filters['search'] . '%')
                ->orWhereHas('company', function ($q) use ($filters) {
                    $q->where('company_name', 'like', '%' . $filters['search'] . '%');
                })
                ->orWhereRaw("CAST(banks.id AS CHAR) LIKE ?", ['%' . $filters['search'] . '%']);
        }

        // if (!empty($filters['company_id'])) {
        //     $query->Where('banks.company_id', $filters['company_id']);
        // }

        return $query;
    }

    public function insertBulk(array $rows)
    {
        return Bank::insert($rows); // bulk insert
    }
}
