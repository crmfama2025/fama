<?php

namespace App\Models;

use App\Models\Traits\HasActivityLog;
use App\Models\Traits\HasDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Installment extends Model
{
    use HasFactory, SoftDeletes, HasActivityLog, HasDeletedBy;

    protected $fillable = [
        'company_id',
        'installment_code',
        'installment_name',
        'interval',
        'added_by',
        'updated_by',
        'deleted_by',
        'status',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('sorted', function ($query) {
            $query->orderByRaw('CAST(installment_name AS UNSIGNED) asc');
        });
    }
}
