<?php

namespace App\Models;

use App\Models\Traits\HasActivityLog;
use App\Models\Traits\HasDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $company_id
 * @property string $vendor_code
 * @property string $vendor_name
 * @property string|null $vendor_phone
 * @property string|null $vendor_email
 * @property string|null $vendor_address
 * @property string|null $accountant_name
 * @property string|null $accountant_phone
 * @property string|null $accountant_email
 * @property string|null $contact_person
 * @property string|null $contact_person_phone
 * @property string|null $contact_person_email
 * @property int|null $added_by
 * @property int|null $updated_by
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Company|null $company
 * @method static \Database\Factories\VendorFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor query()
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereAccountantEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereAccountantName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereAccountantPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereContactPerson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereContactPersonEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereContactPersonPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereVendorAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereVendorCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereVendorEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereVendorName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereVendorPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor withoutTrashed()
 * @mixin \Eloquent
 */
class Vendor extends Model
{
    use HasFactory, SoftDeletes, HasActivityLog, HasDeletedBy;

    protected $fillable = [
        'company_id',
        'vendor_code',
        'vendor_name',
        'vendor_phone',
        'vendor_email',
        'vendor_address',
        'accountant_name',
        'accountant_phone',
        'accountant_email',
        'contact_person',
        'contact_person_phone',
        'contact_person_email',
        'added_by',
        'updated_by',
        'deleted_by',
        'status',
        'contract_template_id',
        'location',
        'landline_number',
        'remarks',
        'trade_license_number',
        'trade_license',
        'trade_license_expiry',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function contract_template()
    {
        return $this->belongsTo(VendorContractTemplate::class, 'contract_template_id');
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }
    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function contractTemplate()
    {
        return $this->belongsTo(VendorContractTemplate::class, 'contract_template_id');
    }
}
