<?php

namespace Database\Seeders;

use App\Models\PaymentTerms;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentTermsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        PaymentTerms::updateOrCreate([
            'term_name' => 'End of The Year',
            'status' => 1
        ]);
        PaymentTerms::updateOrCreate([
            'term_name' => 'Monthly',
            'status' => 1
        ]);
        PaymentTerms::updateOrCreate([
            'term_name' => 'On Contract Date',
            'status' => 1
        ]);
        PaymentTerms::updateOrCreate([
            'term_name' => 'Every Two Months',
            'status' => 1
        ]);
        PaymentTerms::updateOrCreate([
            'term_name' => 'Twice In a Year',
            'status' => 1
        ]);
    }
}
