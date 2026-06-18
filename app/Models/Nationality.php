<?php

namespace App\Models;

use App\Models\Traits\HasActivityLog;
use App\Models\Traits\HasDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Nationality extends Model
{
    use HasFactory, SoftDeletes, HasActivityLog, HasDeletedBy;

    protected $fillable = ['company_id', 'nationality_code', 'nationality_name', 'nationality_short_code', 'added_by', 'updated_by', 'deleted_by', 'status', 'nationality_arabic_name'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
