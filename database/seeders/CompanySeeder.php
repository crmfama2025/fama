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


        Company::updateOrCreate(
            [
                'company_code' => 'CMP00001',
                'company_name' => 'Fama Real Estate'
            ],
            [

                'company_short_code' => 'FAMA',
                'added_by' => 1,
                'industry_id' => 1,
                'trade_license_number' => '1027191',
                'phone' => '97142830752',
                'email' => 'contracts@famagrp.ae',
                'website' => 'https://famagrp.ae/'

            ]
        );

        Company::updateOrCreate([
            'company_code' => 'CMP00002',
            'company_name' => 'Floors and Doors'
        ], [

            'company_short_code' => 'F&D',
            'added_by' => 1,
            'industry_id' => 1,
            'trade_license_number' => '1275734',
            'registration_no' => '2159446',
            'phone' => '97142622177',
            'email' => 'contracts@famagrp.ae',
            'website' => 'https://famagrp.ae/'
        ]);

        Company::updateOrCreate(
            [
                'company_code' => 'CMP00003',
                'company_name' => 'Lock and Key'
            ],
            [

                'company_short_code' => 'L&K',
                'added_by' => 1,
                'industry_id' => 1,
            ]
        );


        Company::updateOrCreate(
            [
                'company_code' => 'CMP00004',
                'company_name' => 'Walls and Bricks'
            ],
            [

                'company_short_code' => 'WB',
                'added_by' => 1,
                'industry_id' => 1,
                'trade_license_number' => '1245154',
                'registration_no' => '2102464',
                'phone' => '97142830752',
                'website' => 'https://wallsandbricks.com/',
                'email' => 'contracts@famagrp.ae'
            ]
        );

        Company::updateOrCreate(
            [
                'company_code' => 'CMP00005',
                'company_name' => 'Fama Investment'
            ],
            [

                'company_short_code' => 'FAMA Investment',
                'added_by' => 1,
                'industry_id' => 2,
                'trade_license_number' => '1465937',
                'registration_no' => '2520181',
                'phone' => '97142830752',
                'website' => 'https://famainvestment.ae/',
                'email' => 'contracts@famagrp.ae'
            ]
        );

        Company::updateOrCreate(
            [
                'company_code' => 'CMP00006',
                'company_name' => 'RFB'
            ],
            [

                'company_short_code' => 'RFB',
                'added_by' => 1,
                'industry_id' => 1,
            ]
        );
    }
}
