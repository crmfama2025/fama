<?php

namespace App\Services\Contracts;

use App\Repositories\Contracts\PaymentDetailRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PaymentDetailService
{
    public function __construct(
        protected PaymentDetailRepository $paymentdetRepo,
    ) {}

    public function getAll()
    {
        return $this->paymentdetRepo->all();
    }

    public function getById($id)
    {
        return $this->paymentdetRepo->find($id);
    }

    public function create($contract_id, array $dataArr, $payment_id, $user_id = null)
    {
        $data = [];

        foreach ($dataArr['payment_mode_id'] as $key => $value) {
            $data[] = array(
                'contract_id' => $contract_id,
                'contract_payment_id' => $payment_id,
                'added_by' => $user_id ? $user_id : auth()->user()->id,
                'payment_mode_id' => $value,
                'payment_date' => $dataArr['payment_date'][$key],
                'payment_amount' => $dataArr['payment_amount'][$key],
                'bank_id' => $dataArr['bank_id'][$key],
                'cheque_no' => $dataArr['cheque_no'][$key],
                // 'cheque_issuer' => $dataArr['cheque_issuer'][$key],
                // 'cheque_issuer_name' => $dataArr['cheque_issuer_name'][$key],
                // 'cheque_issuer_id' => $dataArr['cheque_issuer_id'][$key]
            );

            $this->validate($data, $key);
        }
        // dd($data);

        return $this->paymentdetRepo->createMany($data);
    }

    public function update($contract_id, array $dataArr, $payment_id, $user_id = null)
    {
        // dd($dataArr);
        $data['updated_by'] = $user_id ? $user_id : auth()->user()->id;

        $data = [];
        $insertArr = [];
        foreach ($dataArr['payment_mode_id'] as $key => $value) {
            $dataArray = [];

            $dataArray[] = array(
                'contract_id' => $contract_id,
                'contract_payment_id' => $payment_id,
                'updated_by' => $user_id ? $user_id : auth()->user()->id,
                'payment_mode_id' => $value,
                'payment_date' => $dataArr['payment_date'][$key],
                'payment_amount' => toNumeric($dataArr['payment_amount'][$key]),
                'bank_id' => $dataArr['bank_id'][$key],
                'cheque_no' => $dataArr['cheque_no'][$key],
                // 'cheque_issuer' => $dataArr['cheque_issuer'][$key],
                // 'cheque_issuer_name' => $dataArr['cheque_issuer_name'][$key],
                // 'cheque_issuer_id' => $dataArr['cheque_issuer_id'][$key]
            );

            $this->validate($dataArray);

            if (isset($dataArr['id'][$key])) {
                $id = $dataArr['id'][$key];

                $data[$id] = $dataArray[0];

                $key = $id;
            } else {
                $dataArray[0]['added_by'] = $user_id ? $user_id : auth()->user()->id;

                $insertArr[] = $dataArray[0];
            }
        }

        $paymentDetId = $this->paymentdetRepo->updateMany($data);

        if ($insertArr) {

            $detailids = $this->paymentdetRepo->createMany($insertArr);
            $paymentDetId = array_merge($paymentDetId, $detailids);
        }


        return $paymentDetId;
    }


    private function validate(array $data, $id = null)
    {
        $requireIfPaymentMode = function ($attribute, $value, $fail) use ($data) {
            if (in_array($data[0]['payment_mode_id'], [2, 3]) && empty($value)) {
                $field = str_replace('payment_detail.*.', '', $attribute); // clean field name
                $fail("The {$field} is required because payment mode is not full building.");
            }
        };

        $validator = Validator::make(['payment_detail' => $data], [
            'payment_detail' => 'required|array|min:1',
            'payment_detail.*.payment_mode_id' => 'required',
            'payment_detail.*.payment_date' => 'required',
            'payment_detail.*.payment_amount' => 'required',
            'payment_detail.*.bank_id' => ['nullable', $requireIfPaymentMode],
            'payment_detail.*.cheque_no' => ['nullable', $requireIfPaymentMode],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    public function delete($id)
    {
        return $this->paymentdetRepo->delete($id);
    }


    public function terminatePendingPayments($contractId, $terminateDate, $balanceAmount)
    {
        $retVal = $this->paymentdetRepo
            ->terminatePendingPayments($contractId, $terminateDate, $balanceAmount);

        // logger('Payments updated', ['rows' => $retVal]);

        return $retVal;
    }
}
