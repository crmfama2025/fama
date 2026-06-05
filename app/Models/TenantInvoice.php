<?php

namespace App\Models;

use App\Models\Traits\HasActivityLog;
use App\Models\Traits\HasDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TenantInvoice extends Model
{
    use HasFactory, SoftDeletes, HasActivityLog, HasDeletedBy;

    protected $table = 'tenant_invoices';

    protected $fillable = [
        'agreement_payment_detail_id',
        'tenant_id',
        'agreement_id',
        'contract_id',
        'contract_unit_details_id',
        'agreement_unit_id',

        'invoice_no',
        'invoice_date',
        // 'trn_number',

        'month_start',
        'month_end',

        'total_amount',
        'status',
        'approved_by',
        'approved_date',

        'invoice_path',
        'invoice_file_name',

        'added_by',
        'updated_by',
        'deleted_by',
    ];

    // Agreement relation
    public function agreement()
    {
        return $this->belongsTo(Agreement::class, 'agreement_id');
    }

    // Agreement Unit relation
    public function agreementUnit()
    {
        return $this->belongsTo(AgreementUnit::class, 'agreement_unit_id');
    }
    public function contract()
    {
        return  $this->belongsTo(Contract::class, 'contract_id');
    }
    public function comments()
    {
        return $this->hasMany(TenantInvoiceApprovalComments::class, 'tenant_invoice_id');
    }
    public function tenant()
    {
        return $this->belongsTo(AgreementTenant::class, 'tenant_id');
    }
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    public function agreementPaymentDetail()
    {
        return $this->belongsTo(AgreementPaymentDetail::class, 'agreement_payment_detail_id');
    }
}
