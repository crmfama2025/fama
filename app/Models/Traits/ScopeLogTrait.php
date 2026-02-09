<?php

namespace App\Models\Traits;

use App\Models\ContractScopeLog;
use App\Models\ScopeLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait ScopeLogTrait
{
    protected function addScopeLog(string $action, array $oldValues = [], array $newValues = [], string $description = '')
    {
        $description = $description ?? ucfirst($action) . " performed on Scope: {$this->name}";
        $currentUrl = Request::fullUrl();
        $description .= " (URL: {$currentUrl})";


        ContractScopeLog::create([
            'contract_scope_id'   => $this->id,
            'user_id'    => Auth::id(),
            'action'     => $action,
            'description' => $description,
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => $newValues ? json_encode($newValues) : null,
        ]);
    }

    public function scopeLogs()
    {
        return $this->hasMany(ContractScopeLog::class, 'contract_scope_id')->latest();
    }

    public static function bootScopeLogTrait()
    {
        static::created(function ($scope) {
            $scope->addScopeLog('created', [], $scope->getAttributes());
        });

        static::updated(function ($scope) {
            $scope->addScopeLog('updated', $scope->getOriginal(), $scope->getChanges());
        });

        static::deleted(function ($scope) {
            $scope->addScopeLog('deleted', $scope->getOriginal(), []);
        });

        if (in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive(static::class))) {
            static::restored(function ($scope) {
                $scope->addScopeLog('restored', [], $scope->getAttributes());
            });
        }
    }
}
