<?php

namespace App\Models;

use App\Models\Traits\HasActivityLog;
use App\Models\Traits\HasDeletedBy;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $company_id
 * @property int $area_id
 * @property string $locality_code
 * @property string $locality_name
 * @property int $added_by
 * @property int|null $updated_by
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Area|null $area
 * @property-read \App\Models\Company|null $company
 * @property-write mixed $added_date
 * @method static \Illuminate\Database\Eloquent\Builder|Locality newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Locality newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Locality onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Locality query()
 * @method static \Illuminate\Database\Eloquent\Builder|Locality whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Locality whereAreaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Locality whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Locality whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Locality whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Locality whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Locality whereLocalityCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Locality whereLocalityName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Locality whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Locality whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Locality whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Locality withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Locality withoutTrashed()
 * @method static \Database\Factories\LocalityFactory factory($count = null, $state = [])
 * @mixin \Eloquent
 */
class Locality extends Model
{
    use HasFactory, SoftDeletes, HasActivityLog, HasDeletedBy;

    protected $fillable = ['company_id', 'area_id', 'locality_code', 'locality_name', 'added_by', 'updated_by', 'deleted_by', 'status'];

    public function user()
    {
        return $this->belongsTo([User::class, 'added_by', 'id'], [User::class, 'updated_by', 'id']);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function setAddedDateAttribute($value)
    {
        $this->attributes['added_date'] = Carbon::parse($value)->format('Y-m-d H:i:s');
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
