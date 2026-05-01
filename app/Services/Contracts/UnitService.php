<?php

namespace App\Services\Contracts;

use App\Models\UnitType;
use App\Repositories\Contracts\UnitRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UnitService
{
    public function __construct(
        protected UnitRepository $unitRepo,
    ) {}

    public function getAll()
    {
        return $this->unitRepo->all();
    }

    public function getById($id)
    {
        return $this->unitRepo->find($id);
    }

    public function create($contract_id, array $data, array $unitdetails, $user_id = null)
    {

        $this->validate($data);
        $data['contract_id'] = $contract_id;
        $data['added_by'] = $user_id ? $user_id : auth()->user()->id;
        $data['contract_unit_code'] = $this->setUnitCode();

        $data = array_merge($data, $this->getUnitSummary($unitdetails));
        // dd($data);
        return $this->unitRepo->create($data);
    }

    public function update(array $data, array $unitdetails)
    {
        $id = $data['id'];
        $this->validate($data, $id);
        $data['updated_by'] = auth()->user()->id;
        $data = array_merge($data, $this->getUnitSummary($unitdetails));
        // dd($data);
        return $this->unitRepo->update($id, $data);
    }

    public function setUnitCode($addval = 1)
    {
        $codeService = new \App\Services\CodeGeneratorService();
        return $codeService->generateNextCode('contract_units', 'contract_unit_code', 'VCU', 5, $addval);
    }

    public function getUnitSummary(array $unitDetails)
    {
        $unitNumbers = implode(', ', array_unique($unitDetails['unit_number'] ?? []));

        $unitTypeCounts = array_count_values(array_filter($unitDetails['unit_type_id'] ?? []));

        $property_type = implode(', ', array_unique($unitDetails['property_type_id']));
        $no_of_floors = count(array_unique($unitDetails['floor_no']));
        $floor_no = implode(', ', array_unique($unitDetails['floor_no']));

        $lookupNames = UnitType::getNamesByIds(array_keys($unitTypeCounts));

        $unitTypeSummary = [];
        foreach ($unitTypeCounts as $typeId => $count) {
            $typeName = $lookupNames[$typeId] ?? 'Unknown';
            $unitTypeSummary[] = "{$count} ({$typeName})";
        }

        $unitTypeSummaryText = implode(', ', $unitTypeSummary);



        return [
            'unit_numbers' => $unitNumbers,
            'unit_type_count'   => $unitTypeSummaryText,
            'unit_property_type' => $property_type,
            'no_of_floors' => $no_of_floors,
            'floor_numbers' => $floor_no,
        ];
    }

    private function validate(array $data, $id = null)
    {
        $validator = Validator::make($data, [
            'no_of_units' => 'required',
            'business_type' => 'required',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
    public function updateOccupiedRentPerMonth($contract_unit, $rent, $exisistingRent = 0)
    {
        if ($exisistingRent == 0) {
            $newRent = $contract_unit->occupied_rent_per_month + $rent;
            return $this->unitRepo->update($contract_unit->id, [
                'occupied_rent_per_month' => $newRent
            ]);
        } else {
            $newRent = $contract_unit->occupied_rent_per_month - $exisistingRent + $rent;
            return $this->unitRepo->update($contract_unit->id, [
                'occupied_rent_per_month' => $newRent
            ]);
        }
    }
    public function updateContractUnitOnAgreementdeleteB2c($contract_unit, $agreement_units)
    {
        // dd($contract_unit, $agreement_units);
        if ($contract_unit && $agreement_units) {
            if ($contract_unit->occupied_rent_per_month > 0) {
                $newRent = $contract_unit->occupied_rent_per_month - $agreement_units->sum('rent_per_month');
                return $this->unitRepo->update($contract_unit->id, [
                    'occupied_rent_per_month' => $newRent
                ]);
            }
        }
        // return null;
    }
}
