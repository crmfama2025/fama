<?php

namespace Database\Seeders;

use App\Models\ProfitInterval;
use App\Models\ReferralCommissionFrequency;
use Illuminate\Database\Seeder;
use App\Models\TenantIdentity;

class ReferralCommissionFrequencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ReferralCommissionFrequency::updateOrCreate([
            'commission_frequency_name' => 'One Time',
            'no_of_installments' => 1,
            'status' => 1
        ]);
        // ReferralCommissionFrequency::updateOrCreate([
        //     'commission_frequency_name' => 'Twice',
        //     'no_of_installments' => 2,
        //     'status' => 1
        // ]);
        ReferralCommissionFrequency::updateOrCreate([
            'commission_frequency_name' => 'Ongoing',
            'no_of_installments' => 12,
            'status' => 1
        ]);
    }
}
