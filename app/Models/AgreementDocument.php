<?php

namespace App\Models;

use App\Models\Traits\HasActivityLog;
use App\Models\Traits\HasDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgreementDocument extends Model
{
    use HasFactory, SoftDeletes, HasActivityLog, HasDeletedBy;

    protected $table = 'agreement_documents';

    protected $fillable = [
        'agreement_id',
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

    /**
     * Relationships
     */

    // Each document belongs to an agreement
    public function agreement()
    {
        return $this->belongsTo(Agreement::class);
    }

    public function TenantIdentity()
    {
        return $this->belongsTo(TenantIdentity::class, 'document_type', 'id');
    }

    /**
     * Accessors
     */

    // Get full file URL if stored in public/storage
    public function getDocumentUrlAttribute()
    {
        return asset('storage/' . $this->original_document_path);
    }
}
