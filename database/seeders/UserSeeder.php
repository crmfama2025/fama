<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Permission;
use App\Models\User;
use App\Models\UserPermission;
use App\Models\UserType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (['Super Admin', 'Admin', 'Sales', 'Accountant', 'Manager', 'Operations', 'Data Analyst', 'Data Entry'] as $role) {
            UserType::updateOrCreate(
                ['user_type' => $role],
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $user_id = User::updateOrCreate(
            ['email' => 'superadmin@demo.com'],
            [
                'user_code' => 'USR0001',
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'user_type_id' => 1,
                'username' => 'superadmin',
                'password' => bcrypt('captain'),
                'added_by' => 1,
            ]
        )->id;

        foreach (Permission::all() as $permissions) {
            foreach (Company::all() as $company) {
                UserPermission::updateOrCreate([
                    'user_id' => $user_id,
                    'permission_id' => $permissions->id,
                    'company_id' => $company->id,
                ]);
            }
        }
    }
}
