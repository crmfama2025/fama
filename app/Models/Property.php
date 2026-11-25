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
 * @property int $area_id
 * @property int $locality_id
 * @property int $property_type_id
 * @property string $property_code
 * @property string $property_name
 * @property string|null $property_size
 * @property int|null $property_size_unit
 * @property string $plot_no
 * @property int $added_by
 * @property int|null $updated_by
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Area|null $area
 * @property-read \App\Models\Company|null $company
 * @property-read \App\Models\Locality|null $locality
 * @property-read \App\Models\PropertySizeUnit|null $propertySizeUnit
 * @property-read \App\Models\PropertyType|null $propertyType
 * @method static \Illuminate\Database\Eloquent\Builder|Property newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Property newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Property onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Property query()
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereAreaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereLocalityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property wherePlotNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property wherePropertyCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property wherePropertyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property wherePropertySize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property wherePropertySizeUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property wherePropertyTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Property withoutTrashed()
 * @method static \Database\Factories\PropertyFactory factory($count = null, $state = [])
 * @mixin \Eloquent
 */
class Property extends Model
{
    use HasFactory, SoftDeletes, HasActivityLog, HasDeletedBy;

    protected $fillable = [
        'company_id',
        'area_id',
        'locality_id',
        'property_type_id',
        'property_code',
        'property_name',
        'property_size',
        'property_size_unit',
        'plot_no',
        'added_by',
        'updated_by',
        'deleted_by',
        'status',
        'latitude',
        'longitude',
        'location',
        'address',
        'remarks',
        'makani_number',
        'ref_property_id',
        'source'
    ];

    public function user()
    {
        return $this->belongsTo([User::class, 'added_by', 'id'], [User::class, 'updated_by', 'id']);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function locality()
    {
        return $this->belongsTo(Locality::class);
    }

    public function propertyType()
    {
        return $this->belongsTo(PropertyType::class);
    }

    public function propertySizeUnit()
    {
        return $this->belongsTo(PropertySizeUnit::class, 'property_size_unit');
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
}
