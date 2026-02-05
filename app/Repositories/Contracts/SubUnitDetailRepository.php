<?php

namespace App\Repositories\Contracts;

use App\Models\ContractSubunitDetail;

class SubUnitDetailRepository
{
    public function all()
    {
        return ContractSubunitDetail::all();
    }

    public function find($id)
    {
        return ContractSubunitDetail::findOrFail($id);
    }

    public function getByName($contractSubun)
    {
        return ContractSubunitDetail::where($contractSubun)->first();
    }

    public function create($data)
    {
        return ContractSubunitDetail::create($data);
    }

    public function update($id, array $data)
    {
        $contract = $this->find($id);
        $contract->update($data);
        return $contract;
    }

    public function delete($id)
    {
        $area = $this->find($id);
        return $area->delete();
    }

    public function getCountOfSubunitType($detailId)
    {
        return ContractSubunitDetail::select('subunit_type', \DB::raw('count(*) as total'))
            ->where('contract_unit_detail_id', $detailId)      // your condition
            ->groupBy('subunit_type')        // group by the column
            ->get();
    }

    public function deleteLastNRows($cond1, $n)
    {
        return ContractSubunitDetail::where('status', $cond1)
            ->orderBy('created_at', 'desc')
            ->take($n)
            ->delete();
    }

    public function checkIfExist($data)
    {
        $existing = ContractSubunitDetail::withTrashed()
            ->where('contract_unit_detail_id', $data['detail_id'])
            ->where('subunit_no', $data['subunit_no'])
            ->first();

        if ($existing && $existing->trashed()) {
            // $existing->restore();
            return $existing;
        }
    }

    public function existPrevSubType($detailId, $subUnitData, $key)
    {
        $subunit_type = array();
        if (isset($subUnitData['is_partition'][$key]) && $subUnitData['is_partition'][$key] == '1') {
            $subunit_type[] = 1;
        }
        if (isset($subUnitData['is_bedspace'][$key]) && $subUnitData['is_bedspace'][$key] == '2') {
            $subunit_type[] = 2;
        }

        if (isset($subUnitData['is_room'][$key]) &&  $subUnitData['is_room'][$key] == '3') {
            $subunit_type[] = 3;
        }

        return ContractSubunitDetail::where('contract_unit_detail_id', $detailId)
            ->whereNotIn('subunit_type', $subunit_type)
            ->pluck('id')
            ->toArray();
    }
}
