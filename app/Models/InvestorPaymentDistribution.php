<?php

namespace App\Models;

use App\Models\Traits\HasActivityLog;
use App\Models\Traits\HasDeletedBy;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvestorPaymentDistribution extends Model
{
    use HasFactory,  SoftDeletes, HasActivityLog, HasDeletedBy;

    protected $fillable = [
        'investor_id',
        'payout_id',
        'amount_paid',
        'paid_mode_id',
        'paid_bank',
        'paid_cheque_number',
        'payment_remarks',
        'paid_by',
        'paid_date',
        'updated_by',
        'deleted_by',
        'investment_id',
        'paid_company_id'
    ];

    public function investor()
    {
        return $this->belongsTo(Investor::class);
    }

    public function investment()
    {
        return $this->belongsTo(Investment::class);
    }

    public function investorPayout()
    {
        return $this->belongsTo(InvestorPayout::class, 'payout_id');
    }

    public function paymentMode()
    {
        return $this->belongsTo(PaymentMode::class, 'paid_mode_id');
    }

    public function paidBank()
    {
        return $this->belongsTo(Bank::class, 'paid_bank');
    }

    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
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
}
