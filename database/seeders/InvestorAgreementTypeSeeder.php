<?php

namespace Database\Seeders;

use App\Models\InvestorAgreementType;
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
        $data = [
            ['investor_agreement_type' => "Profit-sharing investment agreement", 'short_code' => 'ProfitSharing'],
            ['investor_agreement_type' => "Addendum to mudarabah agreement", 'short_code' => 'Addendum'],
            ['investor_agreement_type' => "Partial withdrawal form", 'short_code' => 'PartialWithdrawal'],
            ['investor_agreement_type' => "Novation and restatement agreement", 'short_code' => 'Novation'],
            ['investor_agreement_type' => "Mudarabah completion and settlement agreement", 'short_code' => 'MudarabahCompletion'],
        ];

        foreach ($data as $item) {
            InvestorAgreementType::updateOrCreate(
                ['investor_agreement_type' => $item['investor_agreement_type']],
                [
                    'short_code' => $item['short_code'],
                    'added_by' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
