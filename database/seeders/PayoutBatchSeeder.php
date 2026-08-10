<?php

namespace Database\Seeders;

use App\Models\PayoutBatch;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PayoutBatchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PayoutBatch::updateOrCreate([
            "batch_name" => "1-10",
            "status" => 1
        ]);

        PayoutBatch::updateOrCreate([
            "batch_name" => "11-20",
            "status" => 1
        ]);

        PayoutBatch::updateOrCreate([
            "batch_name" => "21-31",
            "status" => 1
        ]);
    }
}
