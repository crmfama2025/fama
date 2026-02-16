<?php

namespace App\Services\Agreement;

use App\Models\Contract;
use App\Repositories\Agreement\AgreementDocRepository;
use App\Repositories\Agreement\AgreementPaymentDetailRepository;
use App\Repositories\Agreement\AgreementPaymentRepository;
use App\Repositories\Agreement\AgreementRepository;
use App\Repositories\Agreement\AgreementTenantRepository;
use App\Repositories\Agreement\AgreementUnitRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AgreementDocumentService
{
    public function __construct(
        protected AgreementRepository $agreementRepository,
        protected AgreementDocRepository $agreementDocRepository,
        protected AgreementTenantRepository $agreementTenantRepository,


    ) {}
    public function storeDocuments($agreement, array $documents, $addedBy)
    {
        // dd($agreement);
        if (empty($documents)) {
            return;
        }

        foreach ($documents as $doc) {
            // $validator = Validator::make($doc, [
            //     'document_type' => 'nullable|string|max:255',
            //     'document_number' => 'required_with:document_path|nullable|string|max:255',
            //     'document_path' => [
            //         'required_with:document_number',
            //         function ($attribute, $value, $fail) {
            //             if (!empty($value) && !$value instanceof \Illuminate\Http\UploadedFile) {
            //                 $fail('The document must be a valid uploaded file.');
            //             }
            //             if ($value instanceof \Illuminate\Http\UploadedFile) {
            //                 $allowed = ['pdf', 'jpg', 'jpeg', 'png'];

            //                 if (!in_array(strtolower($value->getClientOriginalExtension()), $allowed)) {
            //                     $fail('The document must be a file of type: pdf, jpg, jpeg, png.');
            //                 }
            //             }
            //         },
            //     ],
            // ], [
            //     'document_number.required_with' => 'Document number is required when a file is uploaded.',
            //     'document_path.required_with' => 'Document file is required when a document number is provided.',
            // ]);

            // if ($validator->fails()) {
            //     throw new ValidationException($validator);
            // }
            $this->validate($doc);
            if (
                empty($doc['document_number']) ||
                empty($doc['document_path']) ||
                !($doc['document_path'] instanceof UploadedFile)
            ) {
                continue;
            }

            $code = $agreement->agreement_code;

            // $path = $doc['document_path']->store('agreements/documents/' . $code . '/', 'public');

            $filename = uniqid() .  '_' . $doc['document_path']->getClientOriginalName();
            $path = $doc['document_path']->storeAs('agreements/documents/' . $code . '/', $filename, 'public');

            $doc_data = [
                'agreement_id' => $agreement->id,
                'document_type' => $doc['document_type'] ?? null,
                'document_number' => $doc['document_number'] ?? null,
                'original_document_path' => $path,
                'original_document_name' => $doc['document_path']->getClientOriginalName(),
                'added_by' => $addedBy,
                'issued_date' => parseDate($doc['issued_date']) ?? null,
                'expiry_date' => parseDate($doc['expiry_date']) ?? null,
            ];

            $createdDoc = $this->agreementDocRepository->create($doc_data);


            $this->updateAgreementFlags($agreement, $createdDoc->document_type);
        }
    }

    /**
     * Update agreement document upload status flags.
     */
    private function updateAgreementFlags($agreement, $type)
    {
        $flagMap = [
            1 => 'is_passport_uploaded',
            2 => 'is_emirates_id_uploaded',
            3 => 'is_trade_license_uploaded',
            4 => 'is_visa_uploaded',
            5 => 'is_signed_agreement_uploaded'
        ];

        if (isset($flagMap[$type])) {
            $agreement->{$flagMap[$type]} = 1;
            $agreement->save();
        }
    }
    public function update($agreement, array $documents, $updatedBy)
    {
        // dd($documents);
        if (empty($documents)) {
            return;
        }
        $code = $agreement->agreement_code;
        $project_code = $agreement->contract->project_code;


        // $existingIds = array_filter(array_column($documents, 'id') ?? []);

        // // Delete documents that were removed in the form
        // $agreement->documents()
        //     ->whereNotIn('id', $existingIds)
        //     ->get()
        //     ->each(function ($doc) {
        //         Storage::disk('public')->delete($doc->original_document_path);
        //         $doc->delete();
        //     });

        foreach ($documents as $doc) {
            $this->validate($doc);

            if (!empty($doc['id'])) {
                $existingDoc = $agreement->agreement_documents()->find($doc['id']);
                // dd($existingDoc);
                if ($existingDoc) {
                    $existingDoc->document_number = $doc['document_number'] ?? $existingDoc->document_number;

                    if (!empty($doc['document_path']) && $doc['document_path'] instanceof UploadedFile) {
                        Storage::disk('public')->delete($existingDoc->original_document_path);

                        // $path = $doc['document_path']->store('agreements/documents/' . $code . '/', 'public');

                        $filename = uniqid()  . '_' . $doc['document_path']->getClientOriginalName();
                        $path = $doc['document_path']->storeAs("projects/{$project_code}/agreements/{$code}/documents", $filename, 'public');

                        $existingDoc->original_document_path = $path;
                        $existingDoc->original_document_name = $doc['document_path']->getClientOriginalName();
                        $existingDoc->updated_by = $updatedBy;
                    }
                    $existingDoc->issued_date = parseDate($doc['issued_date']) ?? null;
                    $existingDoc->expiry_date = parseDate($doc['expiry_date']) ?? null;

                    $existingDoc->save();
                    $this->updateAgreementFlags($agreement, $existingDoc->document_type);
                }
                continue;
            }
            // dd("test");
            if ($doc['document_type'] != 6) {
                if (
                    empty($doc['document_number']) ||
                    empty($doc['document_path']) ||
                    !($doc['document_path'] instanceof UploadedFile)
                ) {
                    continue;
                }
            } else {
                if (
                    empty($doc['document_path']) ||
                    !($doc['document_path'] instanceof UploadedFile)
                ) {
                    continue;
                    // $errors["documents.document_path"] = 'Document file is required.';
                }
                $doc['document_number'] = 0;
            }
            // if (!empty($errors)) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Please fix the errors below.',
            //         'errors' => $errors,
            //     ], 422);
            // }



            // dd("test");
            // dd($doc['document_path']);



            // $path = $doc['document_path']->store('agreements/documents/' . $code . '/', 'public');

            $filename = uniqid() .  '_' . $doc['document_path']->getClientOriginalName();
            $path = $doc['document_path']->storeAs("projects/{$project_code}/agreements/{$code}/documents", $filename, 'public');

            $doc_data = [
                'agreement_id' => $agreement->id,
                'document_type' => $doc['document_type'] ?? null,
                'document_number' => $doc['document_number'] ?? null,
                'original_document_path' => $path,
                'original_document_name' => $doc['document_path']->getClientOriginalName(),
                // 'updated_by' => $updatedBy,
                'added_by' => $updatedBy,
                'issued_date' => parseDate($doc['issued_date']) ?? null,
                'expiry_date' => parseDate($doc['expiry_date']) ?? null,
            ];
            // dd($doc_data);

            $createdDoc = $this->agreementDocRepository->create($doc_data);
            $this->updateAgreementFlags($agreement, $createdDoc->document_type);
        }
    }
    public function getDocuments($id)
    {
        return $this->agreementDocRepository->getDocuments($id);
    }

    public function validate($data)
    {
        $validator = Validator::make($data, [
            'document_type' => 'nullable|string|max:255',
            'document_number' => 'nullable|string|max:255',
            'document_path' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if (!empty($value) && !$value instanceof \Illuminate\Http\UploadedFile) {
                        $fail('The document must be a valid uploaded file.');
                        return;
                    }

                    if ($value instanceof \Illuminate\Http\UploadedFile) {
                        $allowed = ['pdf', 'jpg', 'jpeg', 'png'];
                        $ext = strtolower($value->getClientOriginalExtension());

                        if (!in_array($ext, $allowed)) {
                            $fail('The document must be a file of type: pdf, jpg, jpeg, png.');
                        }
                    }
                },
            ],
        ], [
            'document_number.required_with' => 'Document number is required when a file is uploaded.',
            'document_path.required' => 'Document file is required when a new document number is provided.',
        ]);


        if (empty($data['id']) && !empty($data['document_number'])) {
            $validator->sometimes('document_path', 'required', function () use ($data) {
                return !empty($data['document_number']);
            });
        }

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }
    }
}
