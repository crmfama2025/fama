<?php

namespace App\Services\Agreement;

use App\Models\Contract;
use App\Repositories\Agreement\AgreementDocRepository;
use App\Repositories\Agreement\AgreementPaymentDetailRepository;
use App\Repositories\Agreement\AgreementPaymentRepository;
use App\Repositories\Agreement\AgreementRepository;
use App\Repositories\Agreement\AgreementTenantRepository;
use App\Repositories\Agreement\AgreementUnitRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AgreementUnitService
{
    public function __construct(
        protected AgreementRepository $agreementRepository,
        protected AgreementDocRepository $agreementDocRepository,
        protected AgreementUnitRepository $agreementUnitRepository,


    ) {}
    public function create($data)
    {
        // dd($data);
        $this->validate($data);
        // dd("test");
        return  $this->agreementUnitRepository->create($data);
    }
    private function validate(array $data, $id = null)
    {
        $validator = Validator::make($data, [
            'unit_type_id' => 'required',
            'contract_unit_details_id' => 'required',
            // 'contract_subunit_details_id' => $'required',
            'rent_per_month' => 'required',
        ], [
            'unit_type_id.required' => 'Unit type is required.',
            'contract_unit_details_id.required' => 'Unit Number is required.',
            'rent_per_month.required' => 'Rent per month is required.',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
    public function update(array $data, $user_id = null)
    {
        // dd($data);
        $id = $data['id'];
        $this->validate($data, $id);
        $data['updated_by'] = $user_id ? $user_id : auth()->user()->id;
        return $this->agreementUnitRepository->update($id, $data);
    }
    public function getById($id)
    {
        return $this->agreementUnitRepository->find($id);
    }
    public function deleteUnit($unitId, $contract_id)
    {
        return DB::transaction(function () use ($unitId, $contract_id) {
            $agreement_unit = $this->getById($unitId);
            $ct_unit_id = $agreement_unit->contract_unit_details_id;
            $deletedAgreementUnitId = null;
            if ($agreement_unit->agreement_payment_details->isNotEmpty()) {
                $deletedAgreementUnitId = $agreement_unit->agreement_payment_details->first()->agreement_unit_id;
                foreach ($agreement_unit->agreement_payment_details as $payment) {
                    $payment->delete();
                }
            }
            makeUnitVacant($ct_unit_id, $contract_id);
            makeContractAvailable($contract_id);
            deleteBifurcations($ct_unit_id);

            $deleteResult = $this->agreementUnitRepository->delete($unitId, $contract_id);

            $vacantUnits = getVacantUnits($contract_id);
            // dd($vacantUnits);

            return [
                'success' => true,
                'vacant_units' => $vacantUnits,
                'deleted_agreement_unit_id' => $deletedAgreementUnitId,
            ];
        });
    }
}
