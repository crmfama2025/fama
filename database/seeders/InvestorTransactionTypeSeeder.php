<?php

namespace Database\Seeders;

use App\Models\InvestorTransactionTypes;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InvestorTransactionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        InvestorTransactionTypes::updateOrCreate([
            "transaction_type" => "New Investment",
        ]);

        InvestorTransactionTypes::updateOrCreate([
            "transaction_type" => "Addendum",
        ]);

        InvestorTransactionTypes::updateOrCreate([
            "transaction_type" => "Partial Withdrawal",
        ]);
        InvestorTransactionTypes::updateOrCreate([
            "transaction_type" => "Full Withdrawal",
        ]);
    }
}
