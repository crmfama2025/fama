<?php

namespace App\Services\Agreement;

use App\Models\AgreementTenant;
use App\Models\Contract;
use App\Models\ProfitInterval;
use App\Models\TenantDocument;
use App\Models\TenantDocuments;
use App\Repositories\Agreement\AgreementDocRepository;
use App\Repositories\Agreement\AgreementPaymentDetailRepository;
use App\Repositories\Agreement\AgreementPaymentRepository;
use App\Repositories\Agreement\AgreementRepository;
use App\Repositories\Agreement\AgreementTenantRepository;
use App\Repositories\Agreement\AgreementUnitRepository;
use App\Services\NationalityService;
use App\Services\PaymentModeService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AgreementTenantService
{
    public function __construct(
        protected AgreementRepository $agreementRepository,
        protected AgreementDocRepository $agreementDocRepository,
        protected AgreementTenantRepository $agreementTenantRepository,
        protected NationalityService $nationalityService,
        protected PaymentModeService $paymentModeService,


    ) {}
    public function create($data)
    {
        $this->validate($data);
        return $this->agreementTenantRepository->create($data);
    }
    public function getById($id)
    {
        // dd($id);
        return $this->agreementTenantRepository->find($id);
    }
    private function validate(array $data, $id = null)
    {
        $validator = Validator::make($data, [
            'tenant_name' => 'required',
            'tenant_mobile' => ['required', 'regex:/^\+?[1-9]\d{9,14}$/'],
            'tenant_email' => 'required|email:rfc,dns',
            'nationality_id' =>  'required',
            'tenant_address' =>  'required',
            'contact_person' => 'required',
            'contact_number' => ['required', 'regex:/^\+?[1-9]\d{9,14}$/'],
            'contact_email' => 'required|email:rfc,dns',
        ], []);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
    public function update(array $data, $user_id = null)
    {
        // dd($data);
        $id = $data['id'];
        $this->validate($data, $id);
        $data['updated_by'] = $user_id ? $user_id : auth()->user()->id;
        return $this->agreementTenantRepository->update($id, $data);
    }
    public function gerFormData()
    {
        $nationalities = $this->nationalityService->getAll();
        $paymentmodes = $this->paymentModeService->getAll();
        $profitInterval = ProfitInterval::where('status', 2)->get();
        return compact('nationalities', 'paymentmodes', 'profitInterval');
    }
    public function createData($data)
    {
        // dd($data);
        $this->validateCreateB2B($data, null);
        return DB::transaction(function () use ($data) {

            // 1️⃣ Prepare tenant data
            $tenant_data = [
                'tenant_name'               => $data['tenant_name'],
                'tenant_mobile'             => $data['tenant_mobile'],
                'tenant_email'              => $data['tenant_email'],
                'nationality_id'            => $data['nationality_id'],
                'tenant_address'            => $data['tenant_address'],
                'tenant_street'             => $data['tenant_street'] ?? null,
                'tenant_city'               => $data['tenant_city'] ?? null,
                'contact_person'            => $data['contact_person'],
                'contact_number'            => $data['contact_number'] ?? null,
                'contact_email'             => $data['contact_email'] ?? null,
                'contact_person_department' => $data['contact_person_department'] ?? null,
                'payment_mode_id'           => $data['payment_mode_id'] ?? null,
                'payment_frequency_id'      => $data['payment_frequency_id'] ?? null,
                'security_cheque_status'    => $data['security_cheque'] ?? 0,
                'tenant_type'               => 1,
                'added_by'                  => auth()->user()->id,
                'tenant_code'               => $this->setProjectCode(),
                // 'no_of_owners'             => $data['no_of_owners'],
                'no_of_owners'             => $data['owners'] ? count($data['owners']) : 0,
            ];
            // dd($tenant_data);

            // 2️⃣ Save tenant
            $tenant = $this->create($tenant_data);

            $tenant_documents = [];
            // dd($data['documents'], $data['owners']);

            // 3️⃣ Main documents
            if (!empty($data['documents'])) {
                // dd($data['documents']);
                foreach ($data['documents'] as $docType => $doc) {
                    if (empty($doc['number']) && empty($doc['file'])) {
                        continue;
                    }

                    $filePath = null;
                    $fileName = null;

                    if (!empty($doc['file']) && $doc['file'] instanceof \Illuminate\Http\UploadedFile) {
                        $filePath = $doc['file']->store("tenants/{$tenant->tenant_code}", 'public');
                        $fileName = $doc['file']->getClientOriginalName();
                    }

                    $tenant_documents[] = [
                        'tenant_id'              => $tenant->id,
                        'document_type'          => $docType,
                        'document_number'        => $doc['number'] ?? null,
                        'issued_date'            => parseDate($doc['issued'] ?? null),
                        'expiry_date'            => parseDate($doc['expiry'] ?? null),
                        'original_document_path' => $filePath,
                        'original_document_name' => $fileName,
                        'added_by'               => auth()->user()->id,
                        'created_at'             => now(),
                        'updated_at'             => now(),
                    ];
                    // dd($tenant_documents);
                }
            }
            TenantDocument::insert($tenant_documents);

            // 4️⃣ Owner documents
            if (!empty($data['owners'])) {
                foreach ($data['owners'] as $ownerIndex => $ownerDocs) {
                    // dd($ownerIndex, $ownerDocs);
                    foreach ($ownerDocs as $docType => $doc) {
                        $docNumber = $doc['passport_number'] ?? $doc['emirates_id'] ?? null;
                        $uploadedFile = $doc['passport_file'] ?? $doc['emirates_file'] ?? null;

                        if (empty($docNumber) && empty($uploadedFile)) {
                            continue; // skip empty document
                        }
                        $filePath = null;
                        $fileName = null;

                        $uploadedFile = $doc['passport_file'] ?? $doc['emirates_file'] ?? null;
                        if ($uploadedFile instanceof \Illuminate\Http\UploadedFile) {
                            $filePath = $uploadedFile->store("tenants/{$tenant->tenant_code}", 'public');
                            $fileName = $uploadedFile->getClientOriginalName();
                        }

                        $owner_documents[] = [
                            'tenant_id'              => $tenant->id,
                            'owner_index'            => $ownerIndex,
                            'document_type'          => $docType, // 1=passport, 2=emirates_id
                            'document_number'        => $doc['passport_number'] ?? $doc['emirates_id'] ?? null,
                            'issued_date' => parseDate(
                                $doc['passport_issued'] ?? $doc['emirates_issued'] ?? null
                            ),

                            'expiry_date' => parseDate(
                                $doc['passport_expiry'] ?? $doc['emirates_expiry'] ?? null
                            ),
                            'original_document_path' => $filePath,
                            'original_document_name' => $fileName,
                            'added_by'               => auth()->user()->id,
                            'created_at'             => now(),
                            'updated_at'             => now(),
                        ];
                    }
                }
            }

            // 5️⃣ Insert all documents in batch
            // if (!empty($tenant_documents)) {
            //     dd("test", $tenant_documents);
            //     TenantDocument::insert($tenant_documents);
            // }

            if (!empty($owner_documents)) {
                // dd("test", $owner_documents);
                TenantDocument::insert($owner_documents);
            }

            return $tenant;
        });
    }
    public function setProjectCode($addval = 1)
    {
        // dd(123);
        $codeService = new \App\Services\CodeGeneratorService();
        $code = $codeService->generateNextCode('agreement_tenants', 'tenant_code', 'TEN', 5, $addval);
        // dd($code);
        return $code;
    }
    public function getDataTable(array $filters = [])
    {
        $query = $this->agreementTenantRepository->getQuery($filters);
        // $result = $query->get();
        // dd($result);

        $columns = [
            ['data' => 'DT_RowIndex', 'name' => 'id'],
            ['data' => 'tenant_name', 'name' => 'tenant_name'],
            ['data' => 'tenant_email', 'name' => 'tenant_email'],
            ['data' => 'tenant_mobile', 'name' => 'tenant_mobile'],
            ['data' => 'nationality_name', 'name' => 'nationality_id'],
            ['data' => 'tenant_address', 'name' => 'tenant_address'],
            ['data' => 'tenant_street', 'name' => 'tenant_street'],
            ['data' => 'tenant_city', 'name' => 'tenant_city'],
            ['data' => 'contact_person', 'name' => 'contact_person'],
            ['data' => 'contact_email', 'name' => 'contact_email'],
            ['data' => 'contact_number', 'name' => 'contact_number'],
            ['data' => 'contact_person_department', 'name' => 'contact_person_department'],
            ['data' => 'payment_mode', 'name' => 'payment_mode_id'],
            ['data' => 'payment_frequency', 'name' => 'payment_frequency_id'],
            ['data' => 'security_cheque_status', 'name' => 'security_cheque_status'],
            ['data' => 'tenant_type', 'name' => 'tenant_type'],
            ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false],
        ];

        return datatables()
            ->of($query)
            ->addIndexColumn()
            ->addColumn('tenant_name', fn($row) => $row->tenant_name ?? '-')
            ->addColumn('tenant_email', fn($row) => $row->tenant_email ?? '-')
            ->addColumn('tenant_mobile', fn($row) => $row->tenant_mobile ?? '-')
            ->addColumn('nationality_name', fn($row) => $row->nationality->nationality_name ?? '-')
            ->addColumn('tenant_address', fn($row) => $row->tenant_address ?? '-')
            ->addColumn('tenant_street', fn($row) => $row->tenant_street ?? '-')
            ->addColumn('tenant_city', fn($row) => $row->tenant_city ?? '-')
            ->addColumn('contact_person', fn($row) => $row->contact_person ?? '-')
            ->addColumn('contact_email', fn($row) => $row->contact_email ?? '-')
            ->addColumn('contact_number', fn($row) => $row->contact_number ?? '-')
            ->addColumn('contact_person_department', fn($row) => $row->contact_person_department ?? '-')
            ->addColumn('payment_mode', fn($row) => $row->paymentMode->payment_mode_name ?? '-')
            ->addColumn('payment_frequency', fn($row) => $row->paymentFrequency->profit_interval_name ?? '-')
            ->addColumn('security_cheque_status', fn($row) => $row->security_cheque_status ? 'Yes' : 'No')
            ->addColumn('tenant_type', fn($row) => $row->tenant_type == 1 ? 'B2B' : 'B2C')
            ->addColumn('action', function ($row) {
                $action = '<div class="d-flex flex-column flex-md-row">';

                // Edit (pencil icon)
                $action .= '<a href="' . route('tenant.edit', ['id' => $row->id]) . '"
                class="btn btn-info mb-1 mr-md-1" title="Edit">
                    <i class="fas fa-edit"></i>
                </a>';

                // View (eye icon)
                $action .= '<a href="' . route('tenant.show', $row->id) . '"
                    class="btn btn-warning mb-1 mr-md-1" title="View">
                    <i class="fas fa-eye"></i>
                </a>';

                // Delete (trash icon)
                // if (tenentAgreement($row->id) == 0) {
                $action .= '<button class="btn btn-danger mb-1" onclick="deleteConf(' . $row->id . ')" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>';
                // }


                $action .= '</div>';

                return $action ?: '-';
            })
            ->rawColumns(['action'])
            ->with(['columns' => $columns])
            ->toJson();
    }
    public function getDetails($id)
    {
        $tenant = AgreementTenant::with('tenantDocuments')->findOrFail($id);
        // dd($tenant->tenantDocuments);
        // $documents = TenantDocument::where('tenant_id', $id)->get();
        return $tenant;
    }
    public function updateData($tenantId, $data)
    {
        // dd($data);
        $this->validateCreateB2B($data, $tenantId);
        return DB::transaction(function () use ($tenantId, $data) {

            // 1️⃣ Get existing tenant
            $tenant = AgreementTenant::findOrFail($tenantId);

            // 2️⃣ Update tenant data
            $tenant_data = [
                'tenant_name'               => $data['tenant_name'],
                'tenant_mobile'             => $data['tenant_mobile'],
                'tenant_email'              => $data['tenant_email'],
                'nationality_id'            => $data['nationality_id'],
                'tenant_address'            => $data['tenant_address'],
                'tenant_street'             => $data['tenant_street'] ?? null,
                'tenant_city'               => $data['tenant_city'] ?? null,
                'contact_person'            => $data['contact_person'],
                'contact_number'            => $data['contact_number'] ?? null,
                'contact_email'             => $data['contact_email'] ?? null,
                'contact_person_department' => $data['contact_person_department'] ?? null,
                'payment_mode_id'           => $data['payment_mode_id'] ?? null,
                'payment_frequency_id'      => $data['payment_frequency_id'] ?? null,
                'security_cheque_status'    => $data['security_cheque'] ?? 0,
                'no_of_owners'             => $data['no_of_owners'],
                // 'no_of_owners'             => $data['owners'] ? count($data['owners']) : 0,

                'updated_by'               => auth()->user()->id,
            ];
            // dd($tenant_data);

            $tenant->update($tenant_data);

            // 3️⃣ Main documents (owner_index = null)
            if (!empty($data['documents'])) {
                // dd($data['documents']);
                foreach ($data['documents'] as $docType => $doc) {
                    $docNumber    = $doc['number'] ?? null;
                    $uploadedFile = $doc['file'] ??  null;

                    // ✅ Skip if nothing provided
                    if (empty($docNumber) && empty($uploadedFile)) {
                        continue;
                    }

                    // ✅ Skip if docNumber is null (file only, no number)
                    if (is_null($docNumber)) {
                        continue;
                    }

                    $docId = $doc['id'] ?? null;

                    $filePath = null;
                    $fileName = null;

                    $existingDoc = $docId ? TenantDocument::find($docId) : TenantDocument::where([
                        ['tenant_id', $tenant->id],
                        ['document_type', $docType],
                        ['owner_index', null],
                    ])->first();

                    if ($existingDoc) {
                        $filePath = $existingDoc->original_document_path;
                        $fileName = $existingDoc->original_document_name;
                    }

                    // Handle uploaded file
                    if (!empty($doc['file']) && $doc['file'] instanceof \Illuminate\Http\UploadedFile) {
                        $filePath = $doc['file']->store("tenants/{$tenant->tenant_code}", 'public');
                        $fileName = $doc['file']->getClientOriginalName();
                    }

                    $docData = [
                        'tenant_id'              => $tenant->id,
                        'owner_index'            => null,
                        'document_type'          => $docType,
                        'document_number'        => $doc['number'] ?? null,
                        'issued_date'            => parseDate($doc['issued']) ?? null,
                        'expiry_date'            => parseDate($doc['expiry']) ?? null,
                        'original_document_path' => $filePath,
                        'original_document_name' => $fileName,
                        'added_by'               => auth()->user()->id,
                        'updated_at'             => now(),
                    ];

                    if ($existingDoc) {
                        $existingDoc->update($docData);
                    } else {
                        $docData['created_at'] = now();
                        TenantDocument::create($docData);
                    }
                }
            }
            // dd($data['owners']);

            // 4️⃣ Owner documents (passport & Emirates ID)
            if (!empty($data['owners'])) {
                foreach ($data['owners'] as $ownerIndex => $ownerDocs) {
                    foreach ($ownerDocs as $docType => $doc) {
                        $docNumber = $doc['passport_number'] ?? $doc['emirates_id'] ?? null;
                        $uploadedFile = $doc['passport_file'] ?? $doc['emirates_file'] ?? null;

                        if (empty($docNumber) && empty($uploadedFile)) {
                            continue; // skip empty document
                        }

                        $docId = $doc['id'] ?? null;

                        $filePath = null;
                        $fileName = null;

                        $existingDoc = $docId ? TenantDocument::find($docId) : TenantDocument::where([
                            ['tenant_id', $tenant->id],
                            ['document_type', $docType],
                            ['owner_index', $ownerIndex],
                        ])->first();

                        $uploadedFile = $doc['passport_file'] ?? $doc['emirates_file'] ?? null;
                        if ($uploadedFile instanceof \Illuminate\Http\UploadedFile) {
                            $filePath = $uploadedFile->store("tenants/{$tenant->tenant_code}", 'public');
                            $fileName = $uploadedFile->getClientOriginalName();
                        } elseif ($existingDoc) {
                            $filePath = $existingDoc->original_document_path;
                            $fileName = $existingDoc->original_document_name;
                        }

                        $docData = [
                            'tenant_id'              => $tenant->id,
                            'owner_index'            => $ownerIndex,
                            'document_type'          => $docType,
                            'document_number'        => $doc['passport_number'] ?? $doc['emirates_id'] ?? null,
                            'issued_date' => parseDate(
                                $doc['passport_issued'] ?? $doc['emirates_issued'] ?? null
                            ),

                            'expiry_date' => parseDate(
                                $doc['passport_expiry'] ?? $doc['emirates_expiry'] ?? null
                            ),
                            'original_document_path' => $filePath,
                            'original_document_name' => $fileName,
                            'added_by'               => auth()->user()->id,
                            'updated_at'             => now(),
                        ];

                        if ($existingDoc) {
                            $existingDoc->update($docData);
                        } else {
                            $docData['created_at'] = now();
                            TenantDocument::create($docData);
                        }
                    }
                }
            }

            return $tenant;
        });
    }
    public function removeOwnerDocuments($documentIds, $tenantId)
    {
        return DB::transaction(function () use ($documentIds, $tenantId) {

            //  Delete owner documents
            TenantDocument::where('tenant_id', $tenantId)
                ->whereIn('id', $documentIds)
                ->delete();

            //  Decrease owner count safely (not below 0)
            $tenant = $this->getById($tenantId);

            if ($tenant->no_of_owners > 0) {
                $tenant->update([
                    'no_of_owners' => $tenant->no_of_owners - 1,
                ]);
            }

            return true;
        });
    }
    public function delete($id)
    {
        // dd($id);

        $tenant = $this->getById($id);
        // dd($tenant);
        return $tenant->delete();
    }
    public function validateCreateB2B(array $data, $tenantId = null): void
    {
        $ownerDocIds = [];
        if (!empty($data['owners'])) {
            foreach ($data['owners'] as $ownerDocs) {
                foreach ($ownerDocs as $doc) {
                    $hasNumber = !empty($doc['passport_number']) || !empty($doc['emirates_id']);
                    if (!empty($doc['id']) && $hasNumber) {
                        $ownerDocIds[] = (int) $doc['id'];
                    }
                }
            }
        }

        // ✅ Trade license doc ID
        $tradeLicenseId = (!empty($data['documents'][3]['id']) && !empty($data['documents'][3]['number']))
            ? (int) $data['documents'][3]['id']
            : null;
        $rules = [
            // Tenant core fields
            'tenant_name'               => 'required|string|max:255',
            'tenant_mobile'             => ['required', 'regex:/^\+?[1-9]\d{9,14}$/'],
            'tenant_email'              => 'required|email:rfc,dns',
            'nationality_id'            => 'required|integer|exists:nationalities,id',
            'tenant_address'            => 'required|string|max:500',
            'tenant_street'             => 'nullable|string|max:255',
            'tenant_city'               => 'nullable|string|max:255',
            'contact_person'            => 'required|string|max:255',
            'contact_number'            => ['required', 'regex:/^\+?[1-9]\d{9,14}$/'],
            'contact_email'             => 'required|email:rfc,dns',
            'contact_person_department' => 'nullable|string|max:255',
            'payment_mode_id'           => 'nullable|integer|exists:payment_modes,id',
            'payment_frequency_id'      => 'nullable|integer|exists:profit_intervals,id',
            'security_cheque'           => 'nullable|boolean',
            // 'no_of_owners'              => 'required|integer|min:1',

            // Main documents (optional but validated if present)
            'documents'                         => 'nullable|array',
            // 'documents.*.number'                => 'nullable|string|max:100',
            'documents.3.number'     => [ // 3 = trade license document type
                'nullable',
                'string',
                'max:100',
                Rule::unique('tenant_documents', 'document_number')
                    ->where('document_type', 3)
                    ->ignore($tradeLicenseId), // pass $id for update, null for create
            ],
            'documents.*.file'                  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'documents.*.issued'                => 'nullable|date',
            'documents.*.expiry'                => 'nullable|date|after_or_equal:documents.*.issued',

            // Owner documents
            'owners'                                    => 'nullable|array',
            'owners.*'                                  => 'array',
            // 'owners.*.*.passport_number'             => ['nullable', 'string', 'regex:/^[A-Z0-9]{6,9}$/'],
            'owners.*.*.passport_number'     => [
                'nullable',
                'string',
                'regex:/^[A-Z0-9]{6,9}$/',
                Rule::unique('tenant_documents', 'document_number')
                    ->where('document_type', 1)
                    ->whereNotIn('id', $ownerDocIds ?: [0]),
            ],
            'owners.*.*.passport_file'                  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'owners.*.*.passport_issued'                => 'nullable|date',
            'owners.*.*.passport_expiry'                => 'nullable|date|after_or_equal:owners.*.*.passport_issued',
            // 'owners.*.*.emirates_id'                 => ['nullable', 'string', 'regex:/^\d{3}-\d{4}-\d{7}-\d{1}$/'],
            'owners.*.*.emirates_id' => [
                'nullable',
                'string',
                'regex:/^\d{3}-\d{4}-\d{7}-\d{1}$/',
                Rule::unique('tenant_documents', 'document_number')
                    ->where('document_type', 2)
                    ->whereNotIn('id', $ownerDocIds ?: [0]),
            ],
            'owners.*.*.emirates_file'                  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'owners.*.*.emirates_issued'                => 'nullable|date',
            'owners.*.*.emirates_expiry'                => 'nullable|date|after_or_equal:owners.*.*.emirates_issued',
        ];

        $messages = [
            'tenant_mobile.regex'           => 'Tenant mobile must be a valid international phone number.',
            'contact_number.regex'          => 'Contact number must be a valid international phone number.',
            // 'no_of_owners.min'              => 'At least one owner is required.',
            'documents.*.file.mimes'        => 'Document files must be JPG, PNG, or PDF.',
            'documents.*.file.max'          => 'Document files must not exceed 5MB.',
            'documents.*.expiry.after_or_equal' => 'Document expiry must be on or after the issued date.',
            'owners.*.*.passport_number.regex'  => 'Passport must be 6–9 characters (letters & numbers only).',
            'owners.*.*.emirates_id.regex'      => 'Emirates ID must be in format: 784-XXXX-XXXXXXX-X.',
            'owners.*.*.passport_file.mimes'    => 'Passport file must be JPG, PNG, or PDF.',
            'owners.*.*.emirates_file.mimes'    => 'Emirates ID file must be JPG, PNG, or PDF.',
            'owners.*.*.passport_expiry.after_or_equal' => 'Passport expiry must be on or after the issued date.',
            'owners.*.*.emirates_expiry.after_or_equal' => 'Emirates ID expiry must be on or after the issued date.',
            'documents.3.number.unique' => 'This trade license number is already associated with another tenant.',
            'owners.*.*.passport_number.unique'         => 'This passport number is already registered.',
            'owners.*.*.emirates_id.unique'             => 'This Emirates ID is already registered.',
        ];

        $validator = Validator::make($data, $rules, $messages);

        // // Cross-field: each owner must have at least a passport OR emirates ID
        // $validator->after(function ($validator) use ($data) {
        //     if (!empty($data['owners'])) {
        //         foreach ($data['owners'] as $ownerIndex => $ownerDocs) {
        //             $hasPassport  = !empty($ownerDocs['passport']['passport_number'] ?? null);
        //             $hasEmiratesId = !empty($ownerDocs['emirates_id']['emirates_id'] ?? null);

        //             if (!$hasPassport && !$hasEmiratesId) {
        //                 $validator->errors()->add(
        //                     "owners.{$ownerIndex}",
        //                     "Owner " . ($ownerIndex + 1) . " must have at least a passport number or Emirates ID."
        //                 );
        //             }
        //         }
        //     }
        // });

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
