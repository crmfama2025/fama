<?php

namespace App\Models;

use App\Models\Traits\HasActivityLog;
use App\Models\Traits\HasDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Bank extends Model
{
    use HasFactory, SoftDeletes, HasActivityLog, HasDeletedBy;

    protected $fillable = ['company_id', 'bank_code', 'bank_name', 'bank_short_code', 'added_by', 'updated_by', 'deleted_by', 'status'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Single scope to filter banks based on user permissions
     * Checks if user has ANY bank permission for the company
     */
    public function scopeAccessible($query, $action = null)
    {
        if (!Auth::check()) {
            return $query->whereRaw('1 = 0');
        }

        $userId = Auth::id();

        return $query->whereExists(function ($q) use ($userId, $action) {
            $q->selectRaw(1)
                ->from('user_permissions as up')
                ->join('permissions as p', 'p.id', '=', 'up.permission_id')
                ->whereColumn('up.company_id', 'banks.company_id')
                ->where('up.user_id', $userId);

            if ($action) {
                // Check for specific action (e.g., 'add', 'edit', 'delete', 'view')
                $q->where('p.permission_name', 'bank.' . $action);
            } else {
                // Check for any bank permission
                $q->where('p.permission_name', 'like', 'bank.%');
            }
        });
    }
}
