<?php

namespace App\Services\Contracts;

use App\Models\ContractUnit;
use App\Models\Installment;
use App\Repositories\Contracts\UnitDetailRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UnitDetailService
{
    public function __construct(
        protected UnitDetailRepository $unitdetRepo,
        protected SubUnitDetailService $subUnitdetServ,
    ) {}

    public function getAll()
    {
        return $this->unitdetRepo->all();
    }

    public function getById($id)
    {
        return $this->unitdetRepo->find($id);
    }

    public function getByContractId($contractId)
    {
        return $this->unitdetRepo->getByContractId($contractId);
    }

    public function create($contractData, array $dataArr, $receivable_installments, $unit_id, $user_id = null)
    {
        $data = [];

        foreach ($dataArr['unit_type_id'] as $key => $value) {
            // dd('test');

            $data[] = $this->getDetailArray($contractData, $unit_id, $user_id, $dataArr, $key, $value, $receivable_installments, 1);
            // dd($data);
            $this->validate($data);
        }

        // dd($data);

        $returnVal = $this->createManyData($data, $dataArr, $contractData, $unit_id, $user_id);

        return $returnVal;
    }

    public function createManyData($data, $dataArr, $contractData, $unit_id, $user_id, $stage = 0)
    {
        return DB::transaction(function () use ($data, $dataArr, $contractData, $unit_id, $user_id, $stage) {
            $unitDetId = $this->unitdetRepo->createMany($data);

            // $is_partition = 0;
            // // $is_bedspace = 0;
            // if (array_key_exists('partition', $dataArr)) {
            //     if ($dataArr['partition'] == 1) {
            //         $is_partition = 1;
            //     } else if ($dataArr['partition'] == 2) {
            //         $is_partition = 2;
            //     } else {
            //         $is_partition = 3;
            //     }
            // }

            $subUnitData = $this->getsubUnitData($dataArr, $contractData, $unit_id);


            // dd($subUnitData);

            if ($stage == 0) {
                // print_r($stage);
                $this->subUnitdetServ->create($unitDetId, $subUnitData, $user_id);
            }
            // dd('after');

            return $unitDetId;
        });


        return;
    }

    public function update($contractData, array $dataArr, $receivable_installments, $unit_id, $user_id = null)
    {
        // dd($dataArr);
        $data = [];
        $insertData = [];
        foreach ($dataArr['unit_type_id'] as $key => $value) {
            $dataArray = [];

            // $partition = 0;
            // $bedspace = 0;
            // $room = 0;
            // $rent_per_unit_per_month = 0;
            // $rent_per_unit_per_annum = 0;
            // if (array_key_exists('partition', $dataArr) && isset($dataArr['partition'][$key])) {
            //     if ($dataArr['partition'][$key] == 1) {
            //         $partition = 1;
            //         $rent_per_unit_per_month = $dataArr['rent_per_partition'];
            //     } else if ($dataArr['partition'][$key] == 2) {
            //         $bedspace = 1;
            //         $rent_per_unit_per_month = $dataArr['rent_per_bedspace'];
            //     } else {
            //         $room = 1;
            //         $rent_per_unit_per_month = $dataArr['rent_per_room'];
            //     }
            // }



            // $rent_per_flat = $dataArr['rent_per_flat'];
            // $installment = Installment::find($receivable_installments);
            // if (isset($dataArr['unit_profit'])) {
            //     $rent_per_flat = $dataArr['unit_revenue'][$key] / $installment->installment_name;
            //     $rent_per_unit_per_month = $rent_per_flat;
            // }
            // // print($rent_per_unit_per_month);

            // $rent_per_unit_per_annum = $rent_per_unit_per_month * $installment->installment_name;

            // $partitionValue = getPartitionValue($dataArr, $key, $receivable_installments);
            // $partition = $partitionValue['partition'];
            // $bedspace = $partitionValue['bedspace'];
            // $room = $partitionValue['room'];
            // $rent_per_flat = $partitionValue['rent_per_flat'];
            // $rent_per_unit_per_month = $partitionValue['rent_per_unit_per_month'];
            // $rent_per_unit_per_annum = $partitionValue['rent_per_unit_per_annum'];


            $dataArray[] = $this->getDetailArray($contractData, $unit_id, $user_id, $dataArr, $key, $value, $receivable_installments, 2);

            // $dataArray[] = array(
            //     'contract_id' => $contractData->id,
            //     'contract_unit_id' => $unit_id,
            //     'updated_by' => $user_id ? $user_id : auth()->user()->id,
            //     'unit_number' => $dataArr['unit_number'][$key],
            //     'unit_type_id' => $value,
            //     'floor_no' => $dataArr['floor_no'][$key],
            //     'unit_status_id' => $dataArr['unit_status_id'][$key],
            //     'unit_rent_per_annum' => $dataArr['unit_rent_per_annum'][$key],
            //     'unit_size_unit_id' => $dataArr['unit_size_unit_id'][$key],
            //     'unit_size' => $dataArr['unit_size'][$key],
            //     'property_type_id' => $dataArr['property_type_id'][$key],
            //     'partition' => $partition,
            //     'bedspace' => $bedspace,
            //     'room' => $room,
            //     'maid_room' => $dataArr['maid_room'][$key] ?? 0,
            //     'total_partition' => $dataArr['total_partition'][$key] ?? 0,
            //     'total_bedspace' => $dataArr['total_bedspace'][$key] ?? 0,
            //     'total_room' => $dataArr['total_room'][$key] ?? 0,
            //     'rent_per_partition' => ($partition > 0) ? $dataArr['rent_per_partition'] : 0,
            //     'rent_per_bedspace' => ($bedspace > 0) ? $dataArr['rent_per_bedspace'] : 0,
            //     'rent_per_room' => ($room > 0) ?  $dataArr['rent_per_room']  : 0,
            //     'rent_per_flat' => ($bedspace == 0 && $partition == 0 && $room == 0) ? $rent_per_flat : 0,
            //     'rent_per_unit_per_month' => $rent_per_unit_per_month,
            //     'rent_per_unit_per_annum' => $rent_per_unit_per_annum,
            //     'unit_profit_perc' => isset($dataArr['unit_profit_perc']) ? $dataArr['unit_profit_perc'][$key] : 0,
            //     'unit_profit' => isset($dataArr['unit_profit']) ? $dataArr['unit_profit'][$key] : 0,
            //     'unit_revenue' => isset($dataArr['unit_revenue']) ? $dataArr['unit_revenue'][$key] : 0,
            //     'unit_amount_payable' => isset($dataArr['unit_amount_payable']) ? $dataArr['unit_amount_payable'][$key] : 0,
            //     'unit_commission' => isset($dataArr['unit_commission']) ? $dataArr['unit_commission'][$key] : 0,
            //     'unit_deposit' => isset($dataArr['unit_deposit']) ? $dataArr['unit_deposit'][$key] : 0,
            // );
            // dd($dataArray);
            // echo "</pre>";
            // print_r($dataArr);

            $this->validate($dataArray);

            if (isset($dataArr['id'][$key])) {
                $data[$dataArr['id'][$key]] = $dataArray[0];
            } else {
                $dataArray[0]['added_by'] = $user_id ? $user_id : auth()->user()->id;

                $insertData[] = $dataArray[0];
            }
        }
        // dd($dataArray);
        // dd('after unit loop');
        return DB::transaction(function () use ($data, $dataArr, $contractData, $unit_id, $user_id, $insertData) {
            $unitDetId = array();
            if ($insertData) {
                $unitDetId = $this->createManyData($insertData, $dataArr, $contractData, $unit_id, $user_id, 1);
            }


            if ($data) {
                $detailids = $this->unitdetRepo->updateMany($data);
                $unitDetId = array_merge($unitDetId, $detailids);
                $subUnitData = $this->getsubUnitData($dataArr, $contractData, $unit_id);

                $this->subUnitdetServ->update($unitDetId, $subUnitData, $user_id);
            }


            return $unitDetId;
        });


        $this->validate($dataArr, $id);
        $dataArr['updated_by'] = auth()->user()->id;
        return $this->unitdetRepo->update($id, $dataArr);
    }

    public function getsubUnitData($dataArr, $contractData, $unit_id)
    {

        // $is_partition = 0;
        // if (isset($subUnitData['is_partition'][$i])) {
        //     // if ($subUnitData['is_partition'][$i] == '1') {
        //     $is_partition = $subUnitData['partition'][$i];
        //     // }
        // }
        // if (isset($subUnitData['is_bedspace'][$i])) {
        //     // if ($subUnitData['is_bedspace'][$i] == '1') {
        //     $is_partition += $subUnitData['bedspace'][$i];
        //     // }
        // }
        // if (isset($subUnitData['room'][$i])) {
        //     // if ($subUnitData['room'][$i] == '1') {
        //     $is_partition += $subUnitData['room'][$i];
        //     // }
        // } else {
        //     $is_partition++;
        // }
        // dd($contractData);

        $subUnitData = array(
            'is_partition' => (isset($dataArr['partition'])) ? $dataArr['partition'] : '',
            'is_bedspace' => (isset($dataArr['bedspace'])) ? $dataArr['bedspace'] : '',
            'is_room' => (isset($dataArr['room'])) ? $dataArr['room'] : '',
            // 'is_flat' => (isset($dataArr['room'])) ? $dataArr['room'] : '',
            'partition' => (isset($dataArr['partition'])) ? $dataArr['total_partition'] : 0,
            'bedspace' => (isset($dataArr['bedspace'])) ? $dataArr['total_bedspace'] : 0,
            'room' => (isset($dataArr['room'])) ? $dataArr['total_room'] : 0,
            'rent_per_partition' => (isset($dataArr['partition']) > 0) ? $dataArr['rent_per_partition'] : 0,
            'rent_per_bedspace' => (isset($dataArr['bedspace']) > 0) ? $dataArr['rent_per_bedspace'] : 0,
            'rent_per_room' => (isset($dataArr['room']) > 0) ?  $dataArr['rent_per_room']  : 0,
            'rent_per_flat' => (isset($dataArr['partition']) == 0 && isset($dataArr['bedspace']) == 0 && isset($dataArr['room']) == 0) ? $dataArr['rent_per_flat'] : 0,
            'project_no' => $contractData->project_number,
            'contract_id' => $contractData->id,
            'contract_unit_id' => $unit_id,
            'company_code' => $contractData->company->company_short_code,
            'unit_no' => $dataArr['unit_number'],
            'unit_type' => $dataArr['unit_type_id'],
            'subunittype' => $contractData->subunittype,
        );

        return $subUnitData;
    }

    public function getDetailArray($contractData, $unit_id, $user_id, $dataArr, $key, $value, $receivable_installments, $action)
    {
        // dd($dataArr);

        $partitionValue = getPartitionValue($dataArr, $key, $receivable_installments);
        $partition = $partitionValue['partition'];
        $bedspace = $partitionValue['bedspace'];
        $room = $partitionValue['room'];
        $rent_per_flat = $partitionValue['rent_per_flat'];
        $rent_per_unit_per_month = $partitionValue['rent_per_unit_per_month'];
        $rent_per_unit_per_annum = $partitionValue['rent_per_unit_per_annum'];
        $subunittype = $partitionValue['subunittype'];
        $subunitcount_per_unit = $partitionValue['subunitcount_per_unit'];
        $subunit_rent_per_unit = $partitionValue['subunit_rent_per_unit'];
        $total_rent_per_unit_per_month = $partitionValue['total_rent_per_unit_per_month'];
        $total_rent_per_unit_per_annum = $partitionValue['total_rent_per_unit_per_annum'];


        // dd($partitionValue);
        $unitDetailArr = array(
            'contract_id' => $contractData->id,
            'contract_unit_id' => $unit_id,
            'unit_number' => $dataArr['unit_number'][$key],
            'unit_type_id' => $value,
            'floor_no' => $dataArr['floor_no'][$key],
            'unit_status_id' => $dataArr['unit_status_id'][$key],
            'unit_rent_per_annum' => $dataArr['unit_rent_per_annum'][$key],
            'unit_size_unit_id' => $dataArr['unit_size_unit_id'][$key],
            'unit_size' => $dataArr['unit_size'][$key],
            'property_type_id' => $dataArr['property_type_id'][$key],
            'partition' => $partition,
            'bedspace' => $bedspace,
            'room' => $room,
            'maid_room' => $dataArr['maid_room'][$key] ?? 0,
            'total_partition' => ($partition > 0) ? $dataArr['total_partition'][$key] : 0,
            'total_bedspace' => ($bedspace > 0) ? $dataArr['total_bedspace'][$key] : 0,
            'total_room' => ($room > 0) ? $dataArr['total_room'][$key] : 0,
            'rent_per_partition' => ($partition > 0) ? $dataArr['rent_per_partition'] : 0,
            'rent_per_bedspace' => ($bedspace > 0) ? $dataArr['rent_per_bedspace'] : 0,
            'rent_per_room' => ($room > 0) ?  $dataArr['rent_per_room']  : 0,
            'rent_per_flat' => ($bedspace == 0 && $partition == 0 && $room == 0) ? $rent_per_flat : 0,
            'rent_per_unit_per_month' => $rent_per_unit_per_month,
            'rent_per_unit_per_annum' => $rent_per_unit_per_annum,
            'total_rent_per_unit_per_month' => $total_rent_per_unit_per_month,
            'subunittype' => $subunittype,
            'subunitcount_per_unit' => $subunitcount_per_unit,
            'subunit_rent_per_unit' => $subunit_rent_per_unit,
            'subunit_vacant_count' => $subunitcount_per_unit,
            'unit_profit_perc' => isset($dataArr['unit_profit_perc']) ? $dataArr['unit_profit_perc'][$key] : 0,
            'unit_profit' => isset($dataArr['unit_profit']) ? $dataArr['unit_profit'][$key] : 0,
            'unit_revenue' => isset($dataArr['unit_revenue']) ? $dataArr['unit_revenue'][$key] : $total_rent_per_unit_per_annum,
            'unit_amount_payable' => isset($dataArr['unit_amount_payable']) ? $dataArr['unit_amount_payable'][$key] : 0,
            'unit_commission' => isset($dataArr['unit_commission']) ? $dataArr['unit_commission'][$key] : 0,
            'unit_deposit' => isset($dataArr['unit_deposit']) ? $dataArr['unit_deposit'][$key] : 0,
            'total_payment_pending' => isset($dataArr['unit_revenue']) ? $dataArr['unit_revenue'][$key] : $total_rent_per_unit_per_annum,
        );

        if ($action  == 1) {
            $unitDetailArr['added_by'] = $user_id ? $user_id : auth()->user()->id;
        } else {
            $unitDetailArr['updated_by'] = $user_id ? $user_id : auth()->user()->id;
        }

        return $unitDetailArr;
    }

    public function validate(array $data, $id = null)
    {
        if (empty($data)) return;

        $validator = Validator::make(['unit_detail' => $data], [
            'unit_detail' => 'required|array|min:1',
            'unit_detail.*.unit_type_id' => 'required',
            'unit_detail.*.floor_no' => 'required',
            'unit_detail.*.unit_status_id' => 'required',
            'unit_detail.*.unit_rent_per_annum' => 'required',
            'unit_detail.*.unit_size_unit_id' => 'required',
            'unit_detail.*.unit_size' => 'required',
            'unit_detail.*.property_type_id' => 'required',
            'unit_detail.*.unit_number' => [
                'nullable',
                function ($attribute, $value, $fail) use ($data) {
                    // Example: fetch the unit from DB (replace 'id' with your logic)
                    $unitId = $data['contract_unit_id'] ?? null;
                    $unit = ContractUnit::find($unitId);

                    if ($unit && $unit->unit != 1 && empty($value)) {
                        $fail('The unit number is required'); // because contract is not full building.
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated()['unit_detail'];
    }

    public function delete($unitId = null)
    {
        return $this->unitdetRepo->delete($unitId);
    }
}
