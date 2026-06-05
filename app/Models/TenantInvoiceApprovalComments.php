<?php

namespace App\Models;

use App\Models\Traits\HasActivityLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantInvoiceApprovalComments extends Model
{
    use HasFactory, HasActivityLog;

    protected $fillable = [
        'tenant_invoice_id',
        'comment',
        'added_by',
        'comment'
    ];

    public function tenantInvoice()
    {
        return $this->belongsTo(TenantInvoice::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
