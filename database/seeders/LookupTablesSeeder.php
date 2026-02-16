<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LookupTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        // Property Size Units
        DB::table('property_size_units')->upsert(
            [
                ['unit_name' => 'Sq.ft', 'created_at' => $now, 'updated_at' => $now],
                ['unit_name' => 'Sq.mt', 'created_at' => $now, 'updated_at' => $now],
            ],
            ['unit_name'],
            ['updated_at']
        );

        // Contract Types
        DB::table('contract_types')->upsert(
            [
                [
                    'contract_type' => 'Direct',
                    'shortcode'     => 'Direct',
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ],
                [
                    'contract_type' => 'Fama Faateh',
                    'shortcode'     => 'FF',
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ],
            ],
            ['contract_type'],
            ['shortcode', 'updated_at']
        );

        // Unit Types
        DB::table('unit_types')->upsert(
            [
                ['unit_type' => 'Studio', 'created_at' => $now, 'updated_at' => $now],
                ['unit_type' => '1BHK', 'created_at' => $now, 'updated_at' => $now],
                ['unit_type' => '2BHK', 'created_at' => $now, 'updated_at' => $now],
                ['unit_type' => '3BHK', 'created_at' => $now, 'updated_at' => $now],
                ['unit_type' => '4BHK', 'created_at' => $now, 'updated_at' => $now],
                ['unit_type' => '5BHK', 'created_at' => $now, 'updated_at' => $now],
                ['unit_type' => '6BHK', 'created_at' => $now, 'updated_at' => $now],
                ['unit_type' => 'Pent house', 'created_at' => $now, 'updated_at' => $now],
            ],
            ['unit_type'],
            ['updated_at']
        );

        // Unit Statuses
        DB::table('unit_statuses')->upsert(
            [
                ['unit_status' => 'Furnished', 'created_at' => $now, 'updated_at' => $now],
                ['unit_status' => 'Un Furnished', 'created_at' => $now, 'updated_at' => $now],
            ],
            ['unit_status'],
            ['updated_at']
        );

        // Unit Size Units
        DB::table('unit_size_units')->upsert(
            [
                ['unit_size_unit' => 'sq. ft', 'created_at' => $now, 'updated_at' => $now],
                ['unit_size_unit' => 'sq. m', 'created_at' => $now, 'updated_at' => $now],
            ],
            ['unit_size_unit'],
            ['updated_at']
        );
    }
}
