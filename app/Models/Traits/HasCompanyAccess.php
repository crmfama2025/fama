<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait HasCompanyAccess
{
    protected static function bootHasCompanyAccess()
    {
        static::addGlobalScope('company_access', function (Builder $builder) {

            if (!\Auth::check()) {
                return;
            }

            $userId = \Auth::id();
            $table  = $builder->getModel()->getTable();
            $modulePrefix = strtolower(class_basename($builder->getModel()));

            if (!\Schema::hasColumn($table, 'company_id')) {
                return;
            }

            $builder->whereExists(function ($query) use ($table, $userId, $modulePrefix) {

                $query->selectRaw(1)
                    ->from('user_permissions as up')
                    ->join('permissions as p', 'p.id', '=', 'up.permission_id')
                    ->whereColumn('up.company_id', $table . '.company_id')
                    ->where('up.user_id', $userId)
                    ->where('p.permission_name', 'like', $modulePrefix . '%');
            });
        });
    }
}
