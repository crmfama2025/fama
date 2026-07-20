<?php

namespace App\Models;

use App\Models\Traits\HasActivityLog;
use App\Models\Traits\HasDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class ContractPaymentDetail extends Model
{
    use HasFactory, SoftDeletes, HasActivityLog, HasDeletedBy;

    protected $fillable = [
        'contract_id',
        'contract_payment_id',
        'payment_mode_id',
        'payment_date',
        'payment_amount',
        'bank_id',
        'cheque_no',
        'cheque_issuer',
        'cheque_issuer_name',
        'cheque_issuer_id',
        'paid_status',
        'added_by',
        'updated_by',
        'deleted_by',
        'has_returned',
        'returned_date',
        'returned_reason',
        'returned_by',
        'beneficiary_id'
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function contract_payment()
    {
        return $this->belongsTo(ContractPayment::class);
    }

    public function payment_mode()
    {
        return $this->belongsTo(PaymentMode::class, 'payment_mode_id', 'id');
    }


    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by', 'id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by', 'id');
    }

    public function setPaymentDateAttribute($value)
    {
        $this->attributes['payment_date'] = Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    private function formatNumber($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = (float) $value;

        if (fmod($value, 1) !== 0.0) {
            return rtrim(rtrim(number_format($value, 2, '.', ','), '0'), '.');
        }

        return number_format((int) $value);
    }
    public function getPaymentAmountAttribute($value)
    {
        return $this->formatNumber($value);
    }

    public function getPaymentDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('d-m-Y') : null;
    }

    public function payables()
    {
        return $this->hasMany(ContractPayableClear::class, 'contract_payment_detail_id', 'id');
    }

    public function setReturnedDateAttribute($value)
    {
        $this->attributes['returned_date'] = $value
            ? Carbon::parse($value)->format('Y-m-d H:i:s')
            : null;
    }

    public function getReturnedDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('d-m-Y') : null;
    }

    public function returnedBy()
    {
        return $this->belongsTo(User::class, 'returned_by', 'id');
    }
}
