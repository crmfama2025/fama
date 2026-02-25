<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $documentTypes = [
            [
                // 'document_type' => 1,
                'label_name' => 'Vendor Contract',
                'field_type' => 'file',
                'field_name' => 'vendor_contract',
                'status_change_value' => 'is_vendor_contract_uploaded',
                'accept_types' => '.pdf,image/*',
                'status' => 1,  //contract
            ],
            [
                // 'document_type' => 2,
                'label_name' => 'Upload Cheque copy',
                'field_type' => 'file',
                'field_name' => 'cheque_copy',
                'status_change_value' => 'is_cheque_copy_uploaded',
                'accept_types' => '.pdf,image/*',
                'status' => 1, //contract
            ],
            [
                // 'document_type' => 3,
                'label_name' => 'Acknowledgement',
                'field_type' => 'file',
                'field_name' => 'acknoledgement',
                'status_change_value' => 'is_aknowledgement_uploaded',
                'accept_types' => '.pdf,image/*',
                'status' => 1, //contract
            ],

            [
                // 'document_type' => 3,
                'label_name' => 'Emirates ID / Other ID',
                'field_type' => 'file',
                'field_name' => 'id_file',
                'status_change_value' => 'is_id_uploaded',
                'accept_types' => '.pdf,image/*',
                'status' => 2, //Investment
            ],
            [
                // 'document_type' => 3,
                'label_name' => 'Passport',
                'field_type' => 'file',
                'field_name' => 'passport_copy',
                'status_change_value' => 'is_passport_uploaded',
                'accept_types' => '.pdf,image/*',
                'status' => 2, //Investment
            ],
            [
                // 'document_type' => 3,
                'label_name' => 'Supporting Document',
                'field_type' => 'file',
                'field_name' => 'supporting_doc',
                'status_change_value' => 'is_supp_doc_uploaded',
                'accept_types' => '.pdf,image/*',
                'status' => 2, //Investment
            ],
            [
                // 'document_type' => 3,
                'label_name' => 'Referal Commission Contract',
                'field_type' => 'file',
                'field_name' => 'referal_comm_contract',
                'status_change_value' => 'is_ref_com_cont_uploaded',
                'accept_types' => '.pdf,image/*',
                'status' => 2, //Investment
            ],
        ];

        foreach ($documentTypes as $type) {
            DocumentType::updateOrCreate($type);
        }
    }
}
