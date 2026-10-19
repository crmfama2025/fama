<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReceiptAllocationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $allocations = [
            ['id' => 1, 'receipt_allocation_name' => 'FAATEH REAL ESTATE', 'created_at' => now()],
            ['id' => 2, 'receipt_allocation_name' => 'FAATEH PROJECT MANAGEMENT', 'created_at' => now()],
            ['id' => 3, 'receipt_allocation_name' => 'CZAR TECHNICAL SERVICES', 'created_at' => now()],
            ['id' => 4, 'receipt_allocation_name' => 'CZAR ELITE REAL ESTATE', 'created_at' => now()],
            ['id' => 5, 'receipt_allocation_name' => 'CASH DEPOSIT', 'created_at' => now()],
            ['id' => 6, 'receipt_allocation_name' => 'OTHER B2B COMPANY', 'created_at' => now()],
        ];

        foreach ($allocations as $allocation) {
            DB::table('receipt_allocations')->updateOrInsert(
                ['receipt_allocation_name' => $allocation['receipt_allocation_name']],
                [
                    'id' => $allocation['id'],
                    'created_at' => $allocation['created_at'],
                ]
            );
        }
    }
}
