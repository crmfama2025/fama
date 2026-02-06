<?php

namespace App\Services\Agreement;

use App\Models\AgreementPaymentDetail;
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

class AgreementPaymentDetailService
{
    public function __construct(
        protected AgreementRepository $agreementRepository,
        protected AgreementDocRepository $agreementDocRepository,
        protected AgreementTenantRepository $agreementTenantRepository,
        protected AgreementPaymentRepository $agreementPaymentRepository,
        protected AgreementPaymentDetailRepository $agreementPaymentDetailRepository,
        protected AgreementUnitRepository $agreementUnitRepository,
        protected AgreementTenantService $agreementTenantService,

    ) {}
    public function create($data)
    {
        $this->validate($data);
        $this->agreementPaymentDetailRepository->create($data);
    }
    private function validate(array $data, $id = null)
    {
        // dd($data);
        $validator = Validator::make($data, [
            'payment_mode_id' => 'required',
            'payment_date' => 'required',
            'payment_amount' => 'required',
            // Bank & Cheque conditional rules
            'bank_id' => [
                'nullable',
                'integer',
                Rule::requiredIf(function () use ($data) {
                    return isset($data['payment_mode_id']) && in_array($data['payment_mode_id'], [2, 3]);
                }),
            ],
            'cheque_number' => [
                'nullable',
                'string',
                'regex:/^\d{6,10}$/',
                Rule::requiredIf(function () use ($data) {
                    return isset($data['payment_mode_id']) && $data['payment_mode_id'] == 3;
                }),
                //  unique cheque number per bank_id
                Rule::unique('agreement_payment_details')
                    ->where(function ($query) use ($data) {
                        return $query->where('bank_id', $data['bank_id'] ?? null);
                    })
                    ->ignore($id),

            ]
        ], [
            'payment_mode_id.required' => 'Payment mode is required.',
            'payment_date.required'    => 'Payment date is required.',
            'payment_amount.required'  => 'Payment amount is required.',
            'payment_amount.numeric'   => 'Payment amount is required',
            'bank_id.required'         => 'Bank name is required ',
            'cheque_number.required'   => 'Cheque number is required for cheque payments.',
            'cheque_number.regex'      => 'Cheque number must be 6 to 10 digits only.',
            'cheque_number.unique'     => 'This cheque number already exists for the selected bank.',
        ]);

        // if ($validator->fails()) {
        //     throw new ValidationException($validator);
        // }
        try {
            $validator->validate();
        } catch (ValidationException $e) {
            $errors = $e->errors();

            // If cheque_number has a unique error, add cheque_number & bank_id
            if (isset($errors['cheque_number'])) {
                throw new ValidationException($validator, response()->json([
                    'success' => false,
                    'message' => $errors['cheque_number'][0],
                    'cheque_number' => $data['cheque_number'] ?? null,
                    'bank_id' => $data['bank_id'] ?? null,
                ], 422));
            }

            // Otherwise, rethrow default validation exception
            throw $e;
        }
    }
    public function update(array $data, $user_id = null)
    {
        // dd($data);
        $id = $data['id'];
        // dd($id);
        $this->validate($data, $id);
        $data['updated_by'] = $user_id ? $user_id : auth()->user()->id;
        return $this->agreementPaymentDetailRepository->update($id, $data);
    }
    public function updateOrCreate(array $data, $user_id = null)
    {
        $id = $data['id'] ?? null;
        $data['updated_by'] = $user_id ?? auth()->user()->id;

        if ($id) {
            // Update existing
            return $this->update($data, $user_id);
        } else {
            // Create new
            $data['added_by'] = $user_id ?? auth()->user()->id;
            $this->validate($data);
            return $this->agreementPaymentDetailRepository->create($data);
        }
    }

    public function deleteByIds(array $ids)
    {
        if (!empty($ids)) {
            return $this->agreementPaymentDetailRepository->deleteWhereIn('id', $ids);
        }
    }

    public function getByAgreementId($agreementId)
    {
        return $this->agreementPaymentDetailRepository->getWhere(['agreement_id' => $agreementId]);
    }
    public function getByAgreementUnitId($agreementUnitId)
    {
        return $this->agreementPaymentDetailRepository->getWhere(['agreement_unit_id' => $agreementUnitId]);
    }
}
