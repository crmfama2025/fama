<?php

namespace App\Repositories\Contracts;

use App\Models\ContractPaymentDetail;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;

class PaymentDetailRepository
{
    public function all()
    {
        return ContractPaymentDetail::all();
    }

    public function find($id)
    {
        return ContractPaymentDetail::findOrFail($id);
    }

    public function getByName($contractPaymentDet)
    {
        return ContractPaymentDetail::where($contractPaymentDet)->first();
    }

    public function create($data)
    {
        return ContractPaymentDetail::create($data);
    }

    public function createMany(array $dataArray)
    {
        $detId = [];
        foreach ($dataArray as $data) {
            $detId[] = ContractPaymentDetail::create($data);
        }
        return  $detId;
    }

    public function updateMany(array $data)
    {
        $detId = [];
        foreach ($data as $key => $value) {
            $paymentdet = $this->find($key);
            $paymentdet->update($value);

            $detId[] = $key;
        }
        return  $detId;
    }

    public function delete($id)
    {
        $pDet = $this->find($id);
        $pDet->deleted_by = auth()->user()->id;
        $pDet->save();
        return $pDet->delete();
    }

    public function terminatePendingPayments($contractId, $terminateDate, $balanceAmount)
    {
        // logger('Terminating payments', [
        //     'contract_id' => $contractId,
        //     'date' => $terminateDate
        // ]);

        $details = ContractPaymentDetail::where('contract_id', $contractId)
            ->where('paid_status', 0)
            ->update([
                'terminate_status'        => 1,
                // 'terminated_at' => $terminateDate,
                'updated_at'    => now()
            ]);

        // if ($balanceAmount) {
        // }

        return $details;
    }
}
