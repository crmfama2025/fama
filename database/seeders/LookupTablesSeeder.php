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
        // Countries
        DB::table('property_size_units')->insert([
            ['unit_name' => 'Sq.ft', 'created_at' => now()],
            ['unit_name' => 'Sq.mt', 'created_at' => now()],
        ]);

        DB::table('contract_types')->insert([
            ['contract_type' => 'Direct Fama', 'created_at' => now(), 'shortcode' => 'DF'],
            ['contract_type' => 'Fama Faateh', 'created_at' => now(), 'shortcode' => 'FF'],
        ]);

        DB::table('unit_types')->insert([
            ['unit_type' => 'Studio', 'created_at' => now()],
            ['unit_type' => '1BHK', 'created_at' => now()],
            ['unit_type' => '2BHK', 'created_at' => now()],
            ['unit_type' => '3BHK', 'created_at' => now()],
            ['unit_type' => '4BHK', 'created_at' => now()],
            ['unit_type' => '5BHK', 'created_at' => now()],
            ['unit_type' => '6BHK', 'created_at' => now()],
            ['unit_type' => 'Pent house', 'created_at' => now()],
        ]);

        DB::table('unit_statuses')->insert([
            ['unit_status' => 'Furnished', 'created_at' => now()],
            ['unit_status' => 'Un Furnished', 'created_at' => now()],
        ]);

        DB::table('unit_size_units')->insert([
            ['unit_size_unit' => 'sq. ft', 'created_at' => now()],
            ['unit_size_unit' => 'sq. m', 'created_at' => now()],
        ]);
    }
}
