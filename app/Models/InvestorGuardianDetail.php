<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestorGuardianDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'investor_guardian_code',
        'guardian_name',
        'guardian_name_arabic',
        'guardian_mobile',
        'guardian_email',
        'emirates_id_number',
        'passport_number',
        'emirates_id_copy',
        'passport_copy',
        'added_by',
        'updated_by',
        'deleted_by',
        'eid_expiry_date',
        'passport_expiry_date',
        'guardian_address',
        'guardian_address_ar'
    ];
    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
    public function investors()
    {
        return $this->hasMany(Investor::class, 'investor_guardian_id');
    }
}
