<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\Lookup;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            PermissionSeeder::class,
            IndustrySeeder::class,
            CompanySeeder::class,
            UserSeeder::class,
            LookupTablesSeeder::class,
            PaymentModeSeeder::class,
            PropertyTypeSeeder::class,
            TenantIdentitySeeder::class,
            DocumentTypeSeeder::class,
            // DocumentTypeAcceptTypesSeeder::class,
            VendorContractTemplateSeeder::class,
            PayoutBatchSeeder::class,
            ProfitIntervalSeeder::class,
            ReferralCommissionFrequencySeeder::class,
            InvestorRelationsSeeder::class,
            EmiratesSeeder::class
        ]);
    }
}
