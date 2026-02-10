<?php

namespace App\Models;

use App\Models\Traits\HasActivityLog;
use App\Models\Traits\HasDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class AgreementPaymentDetail extends Model
{
    use HasFactory, SoftDeletes, HasActivityLog, HasDeletedBy;

    protected $table = 'agreement_payment_details';

    protected $fillable = [
        'agreement_id',
        'agreement_payment_id',
        'payment_mode_id',
        'contract_unit_id',
        'agreement_unit_id',
        'bank_id',
        'cheque_number',
        'cheque_issuer',
        'cheque_issuer_name',
        'cheque_issuer_id',
        'payment_date',
        'payment_amount',
        'is_payment_received',
        'paid_amount',
        'pending_amount',
        'paid_date',
        'added_by',
        'updated_by',
        'deleted_by',
        'terminate_status',
        'paid_mode_id',
        'paid_bank_id',
        'paid_cheque_number',
        'mode_change_reason',
        'bounced_date',
        'bounced_reason',
        'bounced_by',
        'has_bounced'
    ];

    /**
     * Relationships
     */

    public function agreement()
    {
        return $this->belongsTo(Agreement::class);
    }

    public function agreementPayment()
    {
        return $this->belongsTo(AgreementPayment::class);
    }

    public function paymentMode()
    {
        return $this->belongsTo(PaymentMode::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
    public function setpaymentdateAttribute($value)
    {
        $this->attributes['payment_date'] = Carbon::parse($value)->format('Y-m-d H:i:s');
    }
    public function invoice()
    {
        return $this->hasOne(TenantInvoice::class, 'agreement_payment_detail_id', 'id');
    }
    public function agreementUnit()
    {
        return $this->belongsTo(AgreementUnit::class, 'agreement_unit_id', 'id');
    }
    public function setpaidDateAttribute($value)
    {
        $this->attributes['paid_date'] = Carbon::parse($value)->format('Y-m-d H:i:s');
    }
    public function clearedReceivables()
    {
        return $this->belongsTo(ClearedReceivable::class, 'agreement_payment_detail_id', 'id');
    }
    public function bouncedBy()
    {
        return $this->belongsTo(User::class, 'bounced_by');
    }
    public function receivedPayments()
    {
        return $this->hasMany(ClearedReceivable::class, 'agreement_payment_details_id', 'id');
    }
}
