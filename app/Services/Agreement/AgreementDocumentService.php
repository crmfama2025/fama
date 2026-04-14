<?php

namespace App\Services\Agreement;

use App\Models\Contract;
use App\Repositories\Agreement\AgreementDocRepository;
use App\Repositories\Agreement\AgreementPaymentDetailRepository;
use App\Repositories\Agreement\AgreementPaymentRepository;
use App\Repositories\Agreement\AgreementRepository;
use App\Repositories\Agreement\AgreementTenantRepository;
use App\Repositories\Agreement\AgreementUnitRepository;
use App\Services\PdfCompressionService;
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
        // dd($documents);
        if (empty($documents)) {
            return;
        }
        $ct_type = $agreement->contract->contract_type_id;

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
            $this->validate($doc, $ct_type);
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
            // dd($path);

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
        $pdfservice = new PdfCompressionService();

        if (empty($documents)) {
            return;
        }
        // dd($documents);
        $code = $agreement->agreement_code;
        $project_code = $agreement->contract->project_code;
        $ct_type = $agreement->contract->contract_type_id;


        // $existingIds = array_filter(array_column($documents, 'id') ?? []);

        // // Delete documents that were removed in the form
        // $agreement->documents()
        //     ->whereNotIn('id', $existingIds)
        //     ->get()
        //     ->each(function ($doc) {
        //         Storage::disk('public')->delete($doc->original_document_path);
        //         $doc->delete();
        //     });
        DB::beginTransaction();
        try {
            foreach ($documents as $doc) {
                $this->validate($doc, $ct_type);
                // dd($doc);

                if (!empty($doc['id'])) {
                    if ($doc['document_type'] == 1 || $doc['document_type'] == 2) {
                        $existingDoc = $agreement->tenant->tenantDocuments()->find($doc['id']);
                    } else {
                        $existingDoc = $agreement->agreement_documents()->find($doc['id']);
                    }
                    // dd($existingDoc);
                    if ($existingDoc) {
                        // dd("test");
                        $existingDoc->document_number = $doc['document_number'] ?? $existingDoc->document_number;

                        if (!empty($doc['document_path']) && $doc['document_path'] instanceof UploadedFile) {
                            // dd($doc['document_path']);
                            Storage::disk('public')->delete($existingDoc->original_document_path);

                            // $path = $doc['document_path']->store('agreements/documents/' . $code . '/', 'public');

                            $filename = uniqid()  . '_' . $doc['document_path']->getClientOriginalName();
                            // $path = $doc['document_path']->storeAs("projects/{$project_code}/agreements/{$code}/documents", $filename, 'public');
                            if ($doc['document_path']->getClientOriginalExtension() == 'pdf') {

                                $path = $pdfservice->compress(
                                    $doc['document_path'],
                                    'projects/' . $project_code . '/agreements/' . $code . '/documents',
                                    $filename
                                );
                            } else {
                                $path = $doc['document_path']->storeAs("projects/{$project_code}/agreements/{$code}/documents", $filename, 'public');
                            }

                            $existingDoc->original_document_path = $path;
                            // dd($existingDoc->original_document_path);
                            $existingDoc->original_document_name = $doc['document_path']->getClientOriginalName();
                            $existingDoc->updated_by = $updatedBy;
                        }
                        $existingDoc->issued_date = parseDate($doc['issued_date'] ?? null);
                        $existingDoc->expiry_date = parseDate($doc['expiry_date'] ?? null);

                        $existingDoc->save();
                        // dd($existingDoc);
                        $this->updateAgreementFlags($agreement, $existingDoc->document_type);
                        $result[] = $existingDoc;
                    }
                    // dd("test");
                    continue;
                }
                // dd("test");
                if ($doc['document_type'] != 6 && $doc['document_type'] != 5) {
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
                // dd($doc);
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
                // $path = $doc['document_path']->storeAs("projects/{$project_code}/agreements/{$code}/documents", $filename, 'public');s
                // dd("test");
                // dd($doc);

                if ($doc['document_path']->getClientOriginalExtension() == 'pdf') {

                    $path = $pdfservice->compress(
                        $doc['document_path'],
                        'projects/' . $project_code . '/agreements/' . $code . '/documents',
                        $filename
                    );
                } else {
                    $path = $doc['document_path']->storeAs("projects/{$project_code}/agreements/{$code}/documents", $filename, 'public');
                }
                $doc_data = [
                    'agreement_id' => $agreement->id,
                    'document_type' => $doc['document_type'] ?? null,
                    'document_number' => $doc['document_number'] ?? null,
                    'original_document_path' => $path,
                    'original_document_name' => $doc['document_path']->getClientOriginalName(),
                    // 'updated_by' => $updatedBy,
                    'added_by' => $updatedBy,
                    'issued_date' => parseDate($doc['issued_date'] ?? null),
                    'expiry_date' => parseDate($doc['expiry_date'] ?? null),
                ];
                // dd($doc_data);
                if ($doc['document_type'] == 1 || $doc['document_type'] == 2) {
                    // dd("test");
                    $doc_data['tenant_id'] = $agreement->tenant->id;
                    $createdDoc = $agreement->tenant->tenantDocuments()->create($doc_data);
                    // dd($createdDoc);
                    $this->updateAgreementFlags($agreement, $createdDoc->document_type);
                    $result[] = $createdDoc;
                    continue;
                } else {
                    $createdDoc = $agreement->agreement_documents()->create($doc_data);
                    // dd($createdDoc);
                    $this->updateAgreementFlags($agreement, $createdDoc->document_type);
                    $result[] = $createdDoc;
                }
            }
            DB::commit(); // ✅ VERY IMPORTANT

            // return $createdDocs;
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    public function getDocuments($id)
    {
        return $this->agreementDocRepository->getDocuments($id);
    }

    // public function validate($data)
    // {
    //     $validator = Validator::make(
    //         $data,
    //         [
    //             'document_type' => 'nullable|string|max:255',
    //             'document_number' => 'nullable|string|max:255',
    //             'document_path' => [
    //                 'nullable',
    //                 function ($attribute, $value, $fail) {
    //                     if (!empty($value) && !$value instanceof \Illuminate\Http\UploadedFile) {
    //                         $fail('The document must be a valid uploaded file.');
    //                         return;
    //                     }

    //                     if ($value instanceof \Illuminate\Http\UploadedFile) {
    //                         $allowed = ['pdf', 'jpg', 'jpeg', 'png'];
    //                         $ext = strtolower($value->getClientOriginalExtension());

    //                         if (!in_array($ext, $allowed)) {
    //                             $fail('The document must be a file of type: pdf, jpg, jpeg, png.');
    //                         }
    //                     }
    //                 },
    //             ],

    //         ],
    //         [
    //             'document_number.required_with' => 'Document number is required when a file is uploaded.',
    //             'document_path.required' => 'Document file is required when a new document number is provided.',
    //         ]
    //     );


    //     if (empty($data['id']) && !empty($data['document_number'])) {
    //         $validator->sometimes('document_path', 'required', function () use ($data) {
    //             return !empty($data['document_number']);
    //         });
    //     }

    //     if ($validator->fails()) {
    //         throw new \Illuminate\Validation\ValidationException($validator);
    //     }
    // }
    public function validate($data, $ct_type)
    {
        // dd($data);
        $rules = [
            'document_type'   => 'nullable|string|max:255',
            'document_number' => 'nullable|string|max:255',
            'document_path'   => [
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
        ];


        if ((!empty($data['document_number']) && !empty($data['document_path']))) {

            switch ($data['document_type']) {

                case 2: // Emirates ID
                    $rules['document_number'] = [
                        'required',
                        'regex:/^\d{3}-\d{4}-\d{7}-\d{1}$/',
                    ];

                    // Only apply unique check if contract type is NOT 2
                    if ($ct_type != 2) {
                        $rules['document_number'][] = Rule::unique('agreement_documents', 'document_number')
                            ->ignore($data['id'] ?? null)
                            ->where(function ($query) {
                                return $query->where('document_type', 2);
                            });
                    }
                    break;
                case 1:
                    $rules['document_number'] = [
                        'required',
                        'regex:/^[A-Z0-9]{6,9}$/'
                    ];
                    break;
                case 4: // UID/UDB
                    $rules['document_number'] = [
                        'required',
                        'regex:/^\d{9,15}$/',
                    ];

                    // Only add unique check if contract type is NOT 2
                    if ($ct_type != 2) {
                        $rules['document_number'][] = Rule::unique('agreement_documents', 'document_number')
                            ->ignore($data['id'] ?? null)
                            ->where(function ($query) {
                                return $query->where('document_type', 4);
                            });
                    }
                    break;
                case 3: // Trade License
                    $rules['document_number'] = [
                        'required',
                        'regex:/^[A-Z0-9\/-]{5,20}$/', // 5–20 chars letters/numbers / or -
                    ];

                    // Unique only if contract type is NOT 2
                    if ($ct_type != 2) {
                        $rules['document_number'][] = Rule::unique('agreement_documents', 'document_number')
                            ->ignore($data['id'] ?? null)
                            ->where(function ($query) {
                                return $query->where('document_type', 3);
                            });
                    }
                    break;
            }
        }

        $messages = [
            'document_number.required' => 'Document number is required.',
            'document_number.regex'    => 'Invalid document number format.',
            'document_path.required'   => 'Document file is required when a document number is provided.',
        ];

        if (!empty($data['document_type']) && $data['document_type'] == 2) {
            $messages['document_number.unique'] = 'This Emirates ID number already exists in the system.';
            $messages['document_number.regex']  = 'Invalid Emirates ID format. It must be like 784-XXXX-XXXXXXX-X.';
        }
        if (!empty($data['document_type']) && $data['document_type'] == 1) {
            $messages['document_number.regex'] =
                'Invalid Passport Number. It must be 6–9 characters using only uppercase letters and numbers.';
        }
        if (!empty($data['document_type']) && $data['document_type'] == 4) {
            $messages['document_number.unique'] = 'This Visa UID number already exists in the system.';
            $messages['document_number.regex'] =
                'Invalid UID/UDB number format. It must be 9–15 digits.';
        }
        if (!empty($data['document_type']) && $data['document_type'] == 3) {
            $messages['document_number.unique'] = 'This trade license number already exists in the system.';
            $messages['document_number.regex']  = 'Trade License must be 5–20 characters.';
        }

        $validator = Validator::make($data, $rules, $messages);

        // 🔹 Require file only for new records
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
