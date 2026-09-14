<?php

namespace App\Repositories\Investment;

use App\Models\InvestorGuardianDetail;
use Illuminate\Contracts\Database\Eloquent\Builder;

class InvestorGuardianRepository
{
    public function all()
    {
        return InvestorGuardianDetail::all();
    }
    public function allActive()
    {
        return InvestorGuardianDetail::where('status', 1)->get();
    }

    public function find($id)
    {
        return InvestorGuardianDetail::findOrFail($id);
    }

    public function getByName($investorData)
    {
        return InvestorGuardianDetail::where($investorData)->first();
    }

    public function create($data)
    {
        return InvestorGuardianDetail::create($data);
    }

    public function update(int $id, array $data)
    {
        $investor = InvestorGuardianDetail::findOrFail($id);
        $investor->update($data);

        return $investor;
    }

    public function delete($id)
    {
        $investor = $this->find($id);
        $investor->deleted_by = auth()->user()->id;
        return $investor->delete();
    }

    public function uniqInvestorName($investor_name, $company_id)
    {
        return InvestorGuardianDetail::where('area_name', $investor_name)
            ->where('company_id', $company_id)
            ->first();
    }

    public function getByCompany($company_id)
    {
        return InvestorGuardianDetail::where('company_id', $company_id)->get();
    }

    public function getQuery(array $filters = []): Builder
    {
        $query = InvestorGuardianDetail::query();
        // ->with([
        //     'nationality',
        //     'paymentMode',
        //     'countryOfResidence',
        //     'payoutBatch',
        //     'referral',
        //     'investorBanks',
        //     'addedBy'
        // ]);

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $searchLike = str_replace('-', '%', $search);
            $query->where(function ($q) use ($search, $searchLike) {
                $q->where('investor_guardian_code', 'like', "%{$search}%")
                    ->orWhere('guardian_name', 'like', "%{$search}%")
                    ->orWhere('guardian_name_arabic', 'like', "%{$search}%")
                    ->orWhereRaw("CAST(guardian_mobile AS CHAR) LIKE ?", ["%{$searchLike}%"])
                    ->orWhere('guardian_email', 'like', "%{$search}%")
                    ->orWhereRaw("CAST(emirates_id_number AS CHAR) LIKE ?", ["%{$searchLike}%"])
                    ->orWhereRaw("DATE_FORMAT(eid_expiry_date, '%Y-%m-%d') LIKE ?", ["%{$searchLike}%"])
                    ->orWhereRaw("CAST(passport_number AS CHAR) LIKE ?", ["%{$searchLike}%"])
                    ->orWhereRaw("DATE_FORMAT(passport_expiry_date, '%Y-%m-%d') LIKE ?", ["%{$searchLike}%"])
                    ->orWhereHas('addedBy', function ($q) use ($search) {
                        $q->where('first_name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        return $query; //  ALWAYS return Builder
    }

    public function updateGuardian($id, $data)
    {
        $guardian = $this->find($id);
        // dd($data);

        if (!$guardian) {
            $data['added_by'] = auth()->user()->id;
            return InvestorGuardianDetail::create($data);
        }
        $data['updated_by'] = auth()->user()->id;

        $guardian->update($data);

        return $guardian->fresh();
    }
    public function createGuardian($data)
    {
        return InvestorGuardianDetail::create($data);
    }
}
