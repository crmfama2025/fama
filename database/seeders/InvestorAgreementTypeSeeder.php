<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InvestorAgreementTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('investor_agreement_types')->upsert(
            [
                ['investor_agreement_type' => "Profit-sharing investment agreement", 'short_code' => 'ProfitSharing', 'added_by' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['investor_agreement_type' => "Addendum to mudarabah agreement", 'short_code' => 'Addendum', 'added_by' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['investor_agreement_type' => "Partial withdrawal form", 'short_code' => 'PartialWithdrawal', 'added_by' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['investor_agreement_type' => "Novation and restatement agreement", 'short_code' => 'Novation', 'added_by' => 1, 'created_at' => now(), 'updated_at' => now()],
            ],
            ['investor_agreement_type'],
            ['updated_at']
        );
    }
}
