<?php

namespace App\Models;

use App\Models\Traits\HasActivityLog;
// use App\Models\Traits\HasCompanyAccess;
use App\Models\Traits\HasDeletedBy;
use App\Services\CodeGeneratorService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Company extends Model
{
    protected $table = 'companies';

    use HasFactory, SoftDeletes, HasActivityLog, HasDeletedBy;

    protected $fillable = [
        'company_code',
        'company_short_code',
        'company_name',
        'industry_id',
        'address',
        'phone',
        'email',
        'website',
        'added_by',
        'updated_by',
        'deleted_by',
        'status'
    ];

    public function areas()
    {
        return $this->hasMany(Area::class);
    }

    public function user()
    {
        return $this->belongsTo([User::class, 'added_by', 'id'], [User::class, 'updated_by', 'id']);
    }

    public function setAddedDateAttribute($value)
    {
        $this->attributes['added_date'] = Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    public function setUpdatedDateAttribute($value)
    {
        $this->attributes['updated_date'] = Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    public function industry()
    {
        return $this->belongsTo(Industry::class, 'industry_id', 'id');
    }
    public function banks()
    {
        return $this->hasMany(Bank::class, 'company_id');
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    // is that company has any permission in that module
    public function scopePermittedForModule(Builder $query, $module, $submodule = null)
    {
        if (!Auth::check()) {
            return $query->whereRaw('1 = 0'); // no user → no data
        }

        return $query->whereIn('companies.id', function ($q) use ($module, $submodule) {
            $q->select('up.company_id')
                ->from('user_permissions as up')
                ->join('permissions as p', 'p.id', '=', 'up.permission_id')
                ->where('up.user_id', Auth::id());
            $q->where('p.permission_name', 'like', $module . '.%');
            if ($submodule) {
                $q->where('p.permission_name', 'like', $module . '.' . $submodule . '%');
            }
            $q->whereNotNull('up.company_id');
        });
    }
}
