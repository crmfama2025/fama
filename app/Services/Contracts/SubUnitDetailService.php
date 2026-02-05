<?php

namespace App\Services\Contracts;

use App\Models\AgreementPayment;
use App\Models\Contract;
use App\Models\ContractSubunitDetail;
use App\Models\ContractUnitDetail;
use App\Repositories\Contracts\SubUnitDetailRepository;
use DB;

class SubUnitDetailService
{
    public function __construct(
        protected SubUnitDetailRepository $subunitdetRepo,
    ) {}

    public function getAll()
    {
        return $this->subunitdetRepo->all();
    }

    public function getById($id)
    {
        return $this->subunitdetRepo->find($id);
    }

    public function create($detailId, array $subUnitData, $user_id)
    {
        // dd($detailId);
        // dd($subUnitData);
        $subunitArr = [];
        foreach ($subUnitData['unit_type'] as $key => $value) {
            // dd($subUnitData);
            $subunitcount = subUnitCount($subUnitData, $key);
            // if (isset($subUnitData['is_partition'][$key])) {
            //     if ($subUnitData['is_partition'][$key] == '1') {
            //         $subunitcount = $subUnitData['partition'][$key];
            //     } else if ($subUnitData['is_partition'][$key] == '2') {
            //         $subunitcount = $subUnitData['bedspace'][$key];
            //     } else {
            //         $subunitcount = $subUnitData['room'][$key];
            //     }
            // } else {
            //     $subunitcount++;
            // }

            for ($i = 1; $i <= $subunitcount; $i++) {
                // print_r($detailId);
                // $subunit_type = '0';
                $subunit_type = subUnitTypeSingle($subUnitData, $key);
                $subunitno = subunitNoGeneration($subUnitData, $key, $i);
                // if (isset($subUnitData['is_partition'][$key])) {
                //     if ($subUnitData['is_partition'][$key] == '1') {
                //         $subunitno = 'P' . $i;
                //         $subunit_type = '1';
                //     } else if ($subUnitData['is_partition'][$key] == '2') {
                //         $subunitno = 'BS' . $i;
                //         $subunit_type = '2';
                //     } else {
                //         $subunitno = 'R' . $i;
                //         $subunit_type = '3';
                //     }
                // } else {
                //     $subunitno = 'FL' . $i;
                //     $subunit_type = '4';
                // }

                $subunitcode = 'P' . $subUnitData['project_no'] . '/' . $subUnitData['company_code'] . '/' .  $subUnitData['unit_no'][$key] . '/' . $subunitno;
                // dd('aftercode');

                $subunitArr = array(
                    'contract_id' => $subUnitData['contract_id'],
                    'contract_unit_id' => $subUnitData['contract_unit_id'],
                    'contract_unit_detail_id' => $detailId[$key],
                    'subunit_type' => $subunit_type,
                    'subunit_no' => $subunitno['subunitno'],
                    'subunit_rent' => $subunitno['subunitrent'],
                    'subunit_code' => $subunitcode,
                    'added_by' => $user_id ? $user_id : auth()->user()->id,
                );
                // dd($subunitArr);

                $this->subunitdetRepo->create($subunitArr);
            }
            // print_r('after loop');
        }
    }

    public function update($detailId, array $subUnitData, $user_id)
    {
        // dd($detailId);
        foreach ($subUnitData['unit_type'] as $key => $value) {
            // print_r('update loop ' . $key);
            $this->syncSubunits($subUnitData, $key, $detailId[$key], $user_id);
            // dump('update');
        }
    }

    public function syncSubunits($subUnitData, $key, $detailId, $user_id)
    {
        DB::transaction(function () use ($detailId, $subUnitData, $key, $user_id) {

            // 1️⃣ Active subunit types with required counts
            $requestedTypes = subUnitType($subUnitData, $key);
            // example: [1 => 3, 2 => 2]

            // 2️⃣ Delete subunits of REMOVED types
            $deleteIds = $this->subunitdetRepo
                ->existPrevSubType($detailId, $subUnitData, $key);

            if (!empty($deleteIds)) {
                ContractSubunitDetail::whereIn('id', $deleteIds)->forceDelete();
            }

            // 3️⃣ Get existing subunits grouped by type
            $existing = ContractSubunitDetail::where('contract_unit_detail_id', $detailId)
                ->orderBy('id')
                ->get()
                ->groupBy('subunit_type');

            // 4️⃣ Loop PER SUBUNIT TYPE
            foreach ($requestedTypes as $subunit_type => $requiredCount) {
                $existingForType = $existing[$subunit_type] ?? collect();
                $currentCount    = $existingForType->count();

                if ($currentCount < $requiredCount) {
                    /* ========================= CASE 1: ADD ========================== */
                    // print('case 1');

                    $toAdd = $requiredCount - $currentCount;

                    for ($i = 1; $i <= $toAdd; $i++) {

                        $subunitno = subunitNoGeneration(
                            $subUnitData,
                            $key,
                            $currentCount + $i,
                            $subunit_type // 👈 IMPORTANT
                        );

                        $this->createloop(
                            $subUnitData,
                            $key,
                            $detailId,
                            $user_id,
                            $subunit_type,
                            $subunitno['subunitno'],
                            $subunitno['subunitrent']
                        );
                    }
                } elseif ($currentCount > $requiredCount) {
                    /* ========================= CASE 2: REMOVE  ========================== */

                    $toDelete = $currentCount - $requiredCount;

                    $idsToDelete = $existingForType
                        ->sortByDesc('id')
                        ->take($toDelete)
                        ->pluck('id');

                    ContractSubunitDetail::whereIn('id', $idsToDelete)->forceDelete();
                } else {
                    /* ========================= CASE 3: COUNT SAME ========================== */
                    // nothing needed – numbering already correct
                    continue;
                }
            }
        });
    }


    public function createloop($subUnitData, $key, $detailId, $user_id, $subunit_type, $subunitno, $subunitrent)
    {
        $subunitcode = 'P' . $subUnitData['project_no'] . '/' . $subUnitData['company_code'] . '/' . $subUnitData['unit_no'][$key] . '/' . $subunitno;
        // print($subunitcode);
        $oldValue = ContractSubunitDetail::where(['contract_id' => $subUnitData['contract_id'], 'subunit_no' => $subunitno, 'subunit_code' => $subunitcode])->first();
        $existing = $this->subunitdetRepo->checkIfExist(['detail_id' => $detailId, 'subunit_no' => $subunitno]);

        $subunitArr = array(
            'contract_id' => $subUnitData['contract_id'],
            'contract_unit_id' => $subUnitData['contract_unit_id'],
            'contract_unit_detail_id' => $detailId, //detailId[$key]->id,
            'subunit_type' => $subunit_type,
            'subunit_no' => $subunitno,
            'subunit_rent' => $subunitrent,
            'subunit_code' => $subunitcode,
            'added_by' => $user_id ? $user_id : auth()->user()->id,
        );
        // dd($subunitArr);
        if ($oldValue || $existing) {
            if ($existing) {
                // dd('exist');
                if ($existing->trashed()) {
                    $existing->restore();
                    // print_r('restored');
                }
                $existing->fill($subunitArr);
                $existing->save();
            } else {
                $this->subunitdetRepo->update($oldValue->id, $subunitArr);
            }
        } else {
            $this->subunitdetRepo->create($subunitArr);
        }
    }


    // public function markSubunitOccupied($subunitId)
    // {
    //     $subunit = ContractSubunitDetail::find($subunitId);

    //     if (!$subunit) {
    //         return;
    //     }

    //     $subunit->is_vacant = 1;
    //     $subunit->save();

    //     $unitId = $subunit->contract_unit_detail_id;

    //     $allVacant = ContractSubunitDetail::where('contract_unit_detail_id', $unitId)
    //         ->where('is_vacant', 0)
    //         ->doesntExist();

    //     if ($allVacant) {
    //         ContractUnitDetail::where('id', $unitId)
    //             ->update(['is_vacant' => 1]);
    //     }
    // }

    public function markSubunitOccupied($unitId, $subunitId = null)
    {
        DB::transaction(function () use ($unitId, $subunitId) {

            // Step 1: Mark subunit vacant if exists
            if ($subunitId) {
                $subunit = ContractSubunitDetail::find($subunitId);
                if ($subunit) {
                    $subunit->is_vacant = 1;
                    $subunit->save();
                    $unitId = $subunit->contract_unit_detail_id;
                }
            }

            $details = getOccupiedDetails($unitId);
            $unit = ContractUnitDetail::find($unitId);
            $unit->subunit_occupied_count = $details['occupied'];
            $unit->subunit_vacant_count = $details['vacant'];
            $unit->save();

            if ($details['totalsubunits'] === 0 || $details['occupied'] === $details['totalsubunits']) {

                if ($unit) {
                    $unit->is_vacant = 1;
                    $unit->save();
                }
            }
        });
    }

    public function allVacant($contractId)
    {
        $units = ContractUnitDetail::where('contract_id', $contractId)->get();

        foreach ($units as $unit) {
            $unit->contractSubUnitDetails()->update(['is_vacant' => 1]);
            $details = getOccupiedDetails($unit->id);
            // dd($details);
            $unit->subunit_occupied_count = $details['occupied'];
            $unit->subunit_vacant_count = $details['vacant'];
            $unit->is_vacant = 1;
            $unit->save();
        }

        // $allUnitsVacant = ContractUnitDetail::where('contract_id', $contractId)
        //     ->where('is_vacant', 0)
        //     ->doesntExist();

        // if ($allUnitsVacant) {
        //     Contract::where('id', $contractId)->update(['is_vacant' => 1]);
        // }
    }
    public function Updatepaymentdetails($paymentId, $unitId)
    {
        $details = getPaymentDetails($paymentId, $unitId);
        $unit = ContractUnitDetail::find($unitId);
        $unit->total_payment_received = $details['received'];
        $unit->total_payment_pending = $details['pending'];
        $unit->save();
    }
    public function markUnitOccupied($unitId)
    {
        $unit = ContractUnitDetail::find($unitId);
        if (!$unit) {
            return;
        }

        $unit->is_vacant = 1;
        $unit->save();
        $subunits = ContractSubunitDetail::where('contract_unit_detail_id', $unitId)->get();
        foreach ($subunits as $subunit) {
            $subunit->is_vacant = 1;
            $subunit->save();
        }


        $details = getOccupiedDetails($unitId);
        $unit->subunit_occupied_count = $details['occupied'];
        $unit->subunit_vacant_count = $details['vacant'];
        $unit->save();
    }
}
