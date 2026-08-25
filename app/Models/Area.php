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
 * @property string $area_code
 * @property string $area_name
 * @property int $added_by
 * @property int|null $updated_by
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Company|null $company
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Locality> $localities
 * @property-read int|null $localities_count
 * @method static \Illuminate\Database\Eloquent\Builder|Area newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Area newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Area onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Area query()
 * @method static \Illuminate\Database\Eloquent\Builder|Area whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Area whereAreaCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Area whereAreaName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Area whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Area whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Area whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Area whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Area whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Area whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Area whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Area withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Area withoutTrashed()
 * @method static \Database\Factories\AreaFactory factory($count = null, $state = [])
 * @mixin \Eloquent
 */
class Area extends Model
{
    use HasFactory, SoftDeletes, HasActivityLog, HasDeletedBy;

    protected $fillable = [
        'company_id',
        'area_code',
        'area_name',
        'added_by',
        'updated_by',
        'deleted_by',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo([User::class, 'added_by', 'id'], [User::class, 'updated_by', 'id']);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function getCreatedAtAttribute($value)
    {
        $this->attributes['created_at'] = Carbon::parse($value)->format('d-m-Y');
    }

    public static function existsForCompany($area_name, $company_id)
    {
        return self::where('area_name', $area_name)
            ->where('company_id', $company_id)
            ->exists();
    }

    public function localities()
    {
        return $this->hasMany(Locality::class);
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
