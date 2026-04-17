<?php

namespace Database\Seeders;

use App\Models\ContractSignatureDimension;
use App\Models\VendorContractTemplate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VendorContractTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templateSets = [
            "Normal Contract" => [
                ['page_type' => 'odd',  'x' => 35,  'y' => 232, 'width' => 40],
                ['page_type' => 'even', 'x' => 35,  'y' => 230, 'width' => 40],
            ],

            "Primark" => [
                ['page_type' => 'odd',  'x' => 135, 'y' => 237, 'width' => 40],
                ['page_type' => 'even', 'x' => 135, 'y' => 237, 'width' => 40],
            ],

            "Capitol" => [
                ['page_type' => 'odd',  'x' => 125, 'y' => 205, 'width' => 25],
                ['page_type' => 'even', 'x' => 125, 'y' => 205, 'width' => 25],
            ],

            // "Sample PDF" => [
            //     ['page_type' => 'odd',  'x' => 135, 'y' => 230, 'width' => 40],
            //     ['page_type' => 'even', 'x' => 135, 'y' => 230, 'width' => 40],
            // ]
        ];

        foreach ($templateSets as $templateName => $dimensions) {

            $template = VendorContractTemplate::updateOrCreate(
                [
                    "template_name" => $templateName,
                    "version" => 1,
                    "status"  => 1
                ]
            );

            foreach ($dimensions as $dim) {
                ContractSignatureDimension::updateOrCreate(
                    [
                        "contract_template_id" => $template->id,
                        "page_type" => $dim['page_type'],
                    ],
                    [
                        "x" => $dim['x'],
                        "y" => $dim['y'],
                        "width" => $dim['width'],
                    ]
                );
            }
        }
    }
}
