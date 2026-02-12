<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        Company::updateOrCreate([
            'company_code' => 'CMP00001',
            'company_name' => 'Fama Real Estate',
            'company_short_code' => 'FAMA',
            'added_by' => 1,
            'industry_id' => 1,
        ]);

        Company::updateOrCreate([
            'company_code' => 'CMP00002',
            'company_name' => 'Floors and Doors',
            'company_short_code' => 'F&D',
            'added_by' => 1,
            'industry_id' => 1,
        ]);

        Company::updateOrCreate([
            'company_code' => 'CMP00003',
            'company_name' => 'Lock and Key',
            'company_short_code' => 'L&K',
            'added_by' => 1,
            'industry_id' => 1,
        ]);


        Company::updateOrCreate([
            'company_code' => 'CMP00004',
            'company_name' => 'Walls and Bricks',
            'company_short_code' => 'WB',
            'added_by' => 1,
            'industry_id' => 1,
        ]);

        Company::updateOrCreate([
            'company_code' => 'CMP00005',
            'company_name' => 'Fama Investment',
            'company_short_code' => 'FAMA Investment',
            'added_by' => 1,
            'industry_id' => 2,
        ]);

        Company::updateOrCreate([
            'company_code' => 'CMP00006',
            'company_name' => 'RFB',
            'company_short_code' => 'RFB',
            'added_by' => 1,
            'industry_id' => 1,
        ]);
    }
}
