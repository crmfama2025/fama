<?php

namespace App\Repositories\Contracts;

use App\Models\ContractUnitDetail;

class UnitDetailRepository
{
    public function all()
    {
        return ContractUnitDetail::all();
    }

    public function find($id)
    {
        return ContractUnitDetail::findOrFail($id);
    }

    public function getByName($contractUnitDet)
    {
        return ContractUnitDetail::where($contractUnitDet)->first();
    }


    public function getByContractId($contractId)
    {
        return ContractUnitDetail::where('contract_id', $contractId)->get();
    }

    public function create($data)
    {
        return ContractUnitDetail::create($data);
    }

    public function update($id, array $data)
    {
        $contractUnitDet = $this->find($id);
        $contractUnitDet->update($data);
        return $contractUnitDet;
    }

    public function createMany(array $dataArray)
    {
        $detId = [];
        foreach ($dataArray as $data) {
            // $key = $data['key'];
            // unset($data['key']);
            $detId[] = ContractUnitDetail::create($data)->id;
        }
        return  $detId;
    }

    public function updateMany(array $dataArray)
    {
        $detId = [];
        foreach ($dataArray as $id => $data) {
            // $key = $data['key']; // to identify the unit in subunit creation/updation - Geethu
            // unset($data['key']);
            ContractUnitDetail::where('id', $id)->update($data);
            $detId[] = $id;
        }
        return  $detId;
    }

    public function delete($id)
    {
        $uDet = $this->find($id);
        $uDet->deleted_by = auth()->user()->id;
        $uDet->save();
        return $uDet->delete();
    }
}
