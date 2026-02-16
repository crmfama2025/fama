<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\UserPermission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = getModuleArray();

        foreach ($modules as $module) {
            $parent = Permission::updateOrCreate([
                'permission_name' => ucfirst($module),
                'parent_id' => null
            ]);

            if ($module == 'finance') {
                $subModule = ['payout', 'payable_cheque_clearing', 'receivable_cheque_clearing'];
            } elseif ($module == 'report') {
                $subModule = ['view'];
            } else {
                $subModule = ['add', 'view', 'edit', 'delete'];
                if (in_array($module, ['contract'])) {
                    $subModule[] = 'approve';
                    $subModule[] = 'reject';
                    $subModule[] = 'document_upload';
                    $subModule[] = 'renew';
                    $subModule[] = 'send_for_approval';
                    $subModule[] = 'sign_after_approval';
                }

                if (in_array($module, ['agreement'])) {
                    $subModule[] = 'terminate';
                    $subModule[] = 'invoice_upload';
                    $subModule[] = 'document_upload';
                    $subModule[] = 'agreement_view';
                    $subModule[] = 'renew';
                    $subModule[] = 'rent_split';
                }

                if (in_array($module, ['investment'])) {
                    $subModule[] = 'terminate';
                    $subModule[] = 'submit_pending';
                    $subModule[] = 'referrals';
                    $subModule[] = 'soa';
                }
            }

            foreach ($subModule as $action) {
                Permission::updateOrCreate([
                    'permission_name' => "{$module}.{$action}",
                    'parent_id' => $parent->id
                ]);
            }
        }
    }
}
