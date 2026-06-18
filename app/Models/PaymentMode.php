<?php

namespace App\Models;

use App\Models\Traits\HasActivityLog;
use App\Models\Traits\HasDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentMode extends Model
{
    use HasFactory, SoftDeletes, HasActivityLog, HasDeletedBy;

    protected $fillable = [
        'company_id',
        'payment_mode_code',
        'payment_mode_name',
        'payment_mode_short_code',
        'added_by',
        'updated_by',
        'deleted_by',
        'status',
        'payment_mode_arabic_name'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function paymentDetails()
    {
        return $this->hasMany(ContractPaymentDetail::class);
    }
    public function agreementPaymentdetails()
    {
        return $this->hasMany(AgreementPaymentDetail::class);
    }
}
