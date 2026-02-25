<?php

namespace Database\Seeders;

use App\Models\ProfitInterval;
use Illuminate\Database\Seeder;
use App\Models\TenantIdentity;

class ProfitIntervalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProfitInterval::updateOrCreate([
            'profit_interval_name' => 'Every Month',
            'no_of_installments' => 12,
            'interval' => 1,
            'status' => 1
        ]);
        ProfitInterval::updateOrCreate([
            'profit_interval_name' => 'Every 3 Month',
            'no_of_installments' => 4,
            'interval' => 3,
            'status' => 1
        ]);
        ProfitInterval::updateOrCreate([
            'profit_interval_name' => 'Every 6 Month',
            'no_of_installments' => 2,
            'interval' => 6,
            'status' => 1
        ]);
        ProfitInterval::updateOrCreate([
            'profit_interval_name' => 'Every 12 Month',
            'no_of_installments' => 1,
            'interval' => 12,
            'status' => 1
        ]);
        ProfitInterval::updateOrCreate([
            'profit_interval_name' => 'Every 2 Month',
            'no_of_installments' => 6,
            'interval' => 2,
            'status' => 1
        ]);
        ProfitInterval::updateOrCreate([
            'profit_interval_name' => 'Monthly',
            'no_of_installments' => 12,
            'interval' => 1,
            'status' => 2
        ]);
        ProfitInterval::updateOrCreate([
            'profit_interval_name' => 'Quarterly',
            'no_of_installments' => 4,
            'interval' => 3,
            'status' => 2
        ]);
        ProfitInterval::updateOrCreate([
            'profit_interval_name' => 'Half Yearly',
            'no_of_installments' => 2,
            'interval' => 6,
            'status' => 2
        ]);
        ProfitInterval::updateOrCreate([
            'profit_interval_name' => 'Yearly',
            'no_of_installments' => 1,
            'interval' => 12,
            'status' => 2
        ]);
    }
}
