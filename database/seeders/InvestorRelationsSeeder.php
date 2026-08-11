<?php

namespace Database\Seeders;

use App\Models\InvestorRelation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InvestorRelationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $relations = array(
            'Mr Moneer Son',
            'Not Related Party',
            'Mr Fadi Father',
            'Mr Moneer Wife',
            'Mr Fadi Brother',
            'Mr Moneer Brother',
            'Mr Moneer Sister',
            'Mr Fadi Mother',
            'Mr Moneer Jordan Family',
            'Mr Mohammed & Yousef Mother',
            'Mr Mohammed & Yousef Father',
            'Share Holder (Mr Mohammed)',
            'Mr Mohammed & Yousef Uncle',
            'Mr Mohammed & Yousef Cousin',
            'Mr Fadi Wife',
            'Mr Fadi Son',
            'Share Holder (Mr Yousef)',
            'Mr Fadi',
            'Father in Law',
            "Ms.Samah's Brother's Wife"
        );

        foreach ($relations as $key => $relation) {
            InvestorRelation::updateOrCreate([
                "relation_name" => $relation,
                'status' => 1
            ]);
        }
    }
}
