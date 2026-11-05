<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClearedReceivable extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'cleared_receivables';

    protected $fillable = [
        'agreement_id',
        'agreement_payment_details_id',
        'paid_amount',
        'pending_amount',
        'paid_date',
        'paid_mode_id',
        'paid_bank_id',
        'paid_cheque_number',
        'payment_remarks',
        'paid_by',
        'paid_company_id',
        'bank_reference',
        'receipt_allocation_id',
        'have_other_adjustments',
        'loss_or_discount',
        'company_id','deleted_by'
    ];



    public function agreementPaymentDetail()
    {
        return $this->belongsTo(
            AgreementPaymentDetail::class,
            'agreement_payment_details_id'
        );
    }

    public function paidMode()
    {
        return $this->belongsTo(PaymentMode::class, 'paid_mode_id');
    }

    public function paidBank()
    {
        return $this->belongsTo(Bank::class, 'paid_bank_id');
    }
    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }
    public function paidCompany()
    {
        return $this->belongsTo(Company::class, 'paid_company_id', 'id');
    }
    protected static function booted()
{
    static::deleting(function ($allocation) {
        $allocation->deleted_by = auth()->id();
        $allocation->saveQuietly();
    });
}
}
