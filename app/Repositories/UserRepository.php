<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;

class UserRepository
{
    public function all()
    {
        return User::all();
    }

    public function find($id)
    {
        return User::findOrFail($id);
    }

    public function getByName($userData)
    {
        return User::where($userData)->first();
    }

    public function create($data)
    {
        return User::create($data);
    }

    public function updateOrRestore(int $id, array $data)
    {
        $user = User::withTrashed()->findOrFail($id);

        if ($user->trashed()) {
            $user->restore();
        }

        $user->update($data);

        return $user;
    }

    public function delete($id)
    {
        $user = $this->find($id);
        return $user->delete();
    }

    public function checkIfExist($data)
    {
        $existing = User::withTrashed()
            ->where('company_id', $data['company_id'])
            ->where('username', $data['username'])
            ->first();

        if ($existing && $existing->trashed()) {
            // $existing->restore();
            return $existing;
        }
    }

    public function getQuery(array $filters = []): Builder
    {
        // print_r($filters);
        $query = User::query()
            ->select('users.*', 'companies.company_name', 'user_types.user_type')
            ->join('companies', 'companies.id', '=', 'users.company_id')
            ->join('user_types', 'user_types.id', '=', 'users.user_type_id');

        if (!empty($filters['search'])) {
            $query->orwhere('first_name', 'like', '%' . $filters['search'] . '%')
                ->orWhere('last_name', 'like', '%' . $filters['search'] . '%')
                ->orWhere('users.phone', 'like', '%' . $filters['search'] . '%')
                ->orWhere('users.email', 'like', '%' . $filters['search'] . '%')
                ->orWhere('username', 'like', '%' . $filters['search'] . '%')
                ->orWhereHas('company', function ($q) use ($filters) {
                    $q->where('company_name', 'like', '%' . $filters['search'] . '%');
                })
                ->orWhereHas('user_type', function ($q) use ($filters) {
                    $q->where('user_type', 'like', '%' . $filters['search'] . '%');
                })
                ->orWhereRaw("CAST(users.id AS CHAR) LIKE ?", ['%' . $filters['search'] . '%']);
        }

        if (!empty($filters['company_id'])) {
            $query->Where('company_id', $filters['company_id']);
        }

        // if (auth()->user()->company_id != NULL) {
        //     $query->Where('users.added_by', auth()->user()->id);
        // }

        return $query;
    }

    public function insertBulk(array $rows)
    {
        return User::insert($rows); // bulk insert
    }

    public function findByType($type)
    {
        return User::where('user_type_id', $type)->get();
    }
}
