<?php

namespace App\Models;

use App\Models\Traits\HasActivityLog;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractPayableClear extends Model
{
    use HasFactory, HasActivityLog;

    protected $fillable = [
        'contract_id',
        'contract_payment_detail_id',
        'paid_amount',
        'paid_date',
        'pending_amount',
        'paid_by',
        'paid_mode',
        'paid_bank',
        'paid_cheque_number',
        'payment_remarks',
        'company_id',
        'returned_status'
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id', 'id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    public function contractPaymentDetail()
    {
        return $this->belongsTo(ContractPaymentDetail::class, 'contract_payment_detail_id', 'id');
    }

    public function paidMode()
    {
        return $this->belongsTo(PaymentMode::class, 'paid_mode', 'id');
    }

    public function paidBank()
    {
        return $this->belongsTo(Bank::class, 'paid_bank', 'id');
    }

    public function setpaidDateAttribute($value)
    {
        $this->attributes['paid_date'] = $value
            ? Carbon::parse($value)->format('Y-m-d H:i:s')
            : null;
    }

    public function getPaidDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('d-m-Y') : null;
    }

    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by', 'id');
    }
}
