<?php

namespace App\Models;

use App\Models\Traits\HasActivityLog;
use App\Models\Traits\HasDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgreementTenant extends Model
{
    use HasFactory, SoftDeletes, HasActivityLog, HasDeletedBy;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'agreement_tenants';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'agreement_id',
        'tenant_name',
        'tenant_mobile',
        'tenant_email',
        'nationality_id',
        'tenant_address',
        'added_by',
        'updated_by',
        'deleted_by',
        'contact_person',
        'contact_number',
        'contact_email',
        'tenant_street',
        'tenant_city',
        'emirate_id',
        'tenant_code',
        'tenant_type',
        'contact_person_department',
        'payment_mode_id',
        'payment_frequency_id',
        'security_cheque_status',
        'no_of_owners',
        'tenant_code',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */


    /**
     * Relationship: belongs to Agreement
     */
    public function agreement()
    {
        return $this->belongsTo(Agreement::class, 'agreement_id');
    }

    public function nationality()
    {
        return $this->belongsTo(Nationality::class, 'nationality_id');
    }

    public function paymentMode()
    {
        return $this->belongsTo(PaymentMode::class, 'payment_mode_id');
    }

    public function paymentFrequency()
    {
        return $this->belongsTo(ProfitInterval::class, 'payment_frequency_id');
    }
    public function tenantDocuments()
    {
        return $this->hasMany(TenantDocument::class, 'tenant_id');
    }
    protected static function booted()
    {
        // dd("test");
        static::deleting(function ($agreementTenant) {
            // dd("test");
            $userId = auth()->user()->id;
            $hasManyRelations = ['tenantDocuments'];

            if (!$agreementTenant->isForceDeleting()) {
                // dd("test");
                foreach ($hasManyRelations as $relation) {
                    // dd($relation);
                    foreach ($agreementTenant->$relation as $related) {
                        $related->update(['deleted_by' => $userId]);
                        // dd($related);
                        $related->delete();
                    }
                }
            } else {
                foreach ($hasManyRelations as $relation) {
                    $agreementTenant->$relation()->withTrashed()->forceDelete();
                }
            }
        });
    }
}
