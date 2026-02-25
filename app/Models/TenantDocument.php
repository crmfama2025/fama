<?php

namespace App\Models;

use App\Models\Traits\HasActivityLog;
use App\Models\Traits\HasDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TenantDocument extends Model
{
    use HasFactory, SoftDeletes, HasActivityLog, HasDeletedBy;
    protected $table = 'tenant_documents';

    protected $fillable = [
        'tenant_id',
        'owner_index',
        'document_type',
        'document_number',
        'original_document_path',
        'original_document_name',
        'added_by',
        'updated_by',
        'deleted_by',
        'issued_date',
        'expiry_date'
    ];
}
