<?php

namespace App\Services\Sales;


use App\Imports\AreaImport;
use App\Models\ContractUnitDetail;
use App\Models\SalesTenantSubunitRent;
use App\Models\SalesTenantUnit;
use App\Models\TenantDocument;
use App\Repositories\AreaRepository;
use App\Repositories\Sales\TenantRegistrationRepository;
use App\Services\Agreement\AgreementTenantService;
use App\Services\NationalityService;
use App\Services\PropertyService;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class TenantRegistrationService
{
    public function __construct(
        protected AreaRepository $areaRepository,
        protected PropertyService $propertyService,
        protected AgreementTenantService $agreementTenantService,
        protected NationalityService $nationalityService,
        protected TenantRegistrationRepository $tenantRegistrationRepository,
        protected AgreementTenantService $tenantService
    ) {}
    public function getTenantRegistrationFormData()
    {
        $properties = $this->propertyService->getProperties();
        $nationalities = $this->nationalityService->getAll();

        $propertyUnitMap = $this->buildPropertyUnitMap($properties);
        // dd($propertyUnitMap);
        // $existingCustomers = $this->agreementTenantService->getAgreementTenantsB2b();
        $existingCustomers = $this->agreementTenantService->getAgreementTenantsB2b()->map(fn($c) => [
            'id'           => $c->id,
            'code'         => $c->tenant_code ?? '—',
            'name'         => $c->tenant_name,
            'tradeLicense' => optional($c->tenantDocuments->where('document_type', 'trade_license')->first())->document_number ?? '—',
            'type'         => 'B2B',
            'docs' => $c->tenantDocuments->map(function ($doc) {
                return [
                    'type' => $doc->document_type,
                    'name' => $doc->TenantIdentity->identity_type,
                    // 'name' => ucfirst(str_replace('_', ' ', $doc->document_type)),
                    'url'  => asset('storage/' . $doc->original_document_path),
                ];
            })->values()->toArray(),
        ]);
        // dd($existingCustomers);


        return compact('properties', 'propertyUnitMap', 'existingCustomers', 'nationalities');
    }

    // private function buildPropertyUnitMap($properties): array
    // {
    //     $map = [];

    //     foreach ($properties as $property) {
    //         $b2cFloorsMap = [];
    //         $b2bFloorsMap = [];
    //         $floorsMap = [];
    //         // Debug property 11 specifically
    //         // if ($property->id === 11) {
    //         //     dd([
    //         //         'property_id'     => $property->id,
    //         //         'contracts_count' => $property->contracts->count(),
    //         //         'contracts'       => $property->contracts->map(fn($c) => [
    //         //             'contract_id'          => $c->id,
    //         //             'has_contract_unit'    => (bool) $c->contract_unit,
    //         //             'contract_unit_id'     => $c->contract_unit?->id ?? null,
    //         //             'has_details'          => $c->contract_unit ? (bool) $c->contract_unit->contractUnitDetails : false,
    //         //             'details_count'        => $c->contract_unit?->contractUnitDetails?->count() ?? 0,
    //         //         ]),
    //         //     ]);
    //         // }

    //         foreach ($property->contracts as $contract) {

    //             // Guard: contract_unit may be null or bool on some contracts
    //             $contractUnit = $contract->contract_unit;
    //             if (!$contractUnit) {
    //                 continue;
    //             }
    //             // foreach ($contractUnits as $contractUnit) {

    //             // Guard: contractUnitDetails may also be null
    //             $details = $contractUnit->contractUnitDetails;
    //             // dd($details);
    //             if (!$details || !is_iterable($details)) {
    //                 continue;
    //             }
    //             // Determine business type: 1 = B2B, 2 = B2C
    //             $isB2C = $contractUnit->business_type == 2;

    //             if ($isB2C) {
    //                 $floorsMap = &$b2cFloorsMap;
    //             } else {
    //                 $floorsMap = &$b2bFloorsMap;
    //             }


    //             foreach ($details as $detail) {

    //                 $floor      = (string) ($detail->floor_no ?? '');
    //                 $unitTypeId = (int)    ($detail->unit_type_id ?? 0);
    //                 $unitType   = (string) ($detail->unit_type->unit_type ?? 'Unknown');
    //                 $unitNo     = (string) ($detail->unit_number ?? '');
    //                 $unitId     = (int)    ($detail->id ?? 0);

    //                 if (!$floor || !$unitNo || !$unitId || !$unitTypeId) {
    //                     continue;
    //                 }

    //                 // Subunits
    //                 $subUnitRelation = $detail->contractSubUnitDetails;
    //                 $subunits = ($subUnitRelation && is_iterable($subUnitRelation))
    //                     ? collect($subUnitRelation)->map(fn($sub) => [
    //                         'id'    => $sub->id,
    //                         'label' => $sub->subunit_no,
    //                     ])->values()->toArray()
    //                     : [];

    //                 // Init bucket using unitTypeId as key
    //                 if (!isset($floorsMap[$floor][$unitTypeId])) {
    //                     $floorsMap[$floor][$unitTypeId] = [
    //                         'label' => $unitType,
    //                         'units' => [],
    //                     ];
    //                 }

    //                 // Deduplicate by unit_number
    //                 $alreadyAdded = collect($floorsMap[$floor][$unitTypeId]['units'])
    //                     ->contains('unit_number', $unitNo);

    //                 if (!$alreadyAdded) {
    //                     $floorsMap[$floor][$unitTypeId]['units'][] = [
    //                         'id'          => $unitId,
    //                         'unit_number' => $unitNo,
    //                         'subunits'    => $subunits,
    //                     ];
    //                 }
    //             }
    //         }

    //         $map[$property->id] = [
    //             'b2c' => $b2cFloorsMap,
    //             'b2b' => $b2bFloorsMap,
    //         ];
    //     }
    //     // dd($map);

    //     return $map;
    // }
    // public function createOrRestore($data)
    // {
    //     // dd($data);
    //     if ($data->business_type == 2) {
    //         $tenantData = [
    //             'tenant_name' => $data->tenant_name,
    //             'tenant_mobile' => $data->tenant_mobile,
    //             'tenant_email' => $data->tenant_email,
    //             'nationality_id' => $data->nationality_id,
    //             'tenant_address' => $data->tenant_address,
    //             'tenant_street' => $data->tenant_street,
    //             'tenant_city' => $data->tenant_city,
    //             'contact_person' => $data->contact_person,
    //             'contact_person_department' => $data->contact_person_department ?? null,
    //             'office_landline' => $data->office_landline,
    //             'contact_number' => $data->contact_number,
    //             'contact_email' => $data->contact_email,
    //             'tenant_type' => $data->business_type,
    //             'added_by' => auth()->user()->id,
    //         ];
    //         $tenant = $this->tenantService->create($tenantData);
    //     }


    //     $agreement_data = [
    //         'property_id' => $data->property_id,
    //         'business_type' => $data->business_type,
    //         'start_date' => parseDate($data->start_date),
    //         'end_date' => parseDate($data->end_date ?? null),
    //         'added_by' => auth()->user()->id,
    //     ];
    //     $salesAgreement = $this->tenantRegistrationRepository->create($agreement_data);
    //     $agreement_unit_data = [
    //         'sales_tenant_agreement_id' => $salesAgreement->id,
    //         'floor_number' => $data->floor_number,
    //         'unit_type-id' => $data->unit_type_id,
    //         'contract_unit_details_id' => $data->contract_unit_details_id,
    //         'contract_subunit_details_id' => $data->contract_subunit_details_id,
    //         'annual_rent' => $data->rent_per_annum,
    //         'monthly_rent' => $data->rent_per_montrh,
    //         'added_by' => auth()->user()->id,
    //     ];
    //     // $salesUnit = $this->tenantRegistrationRepository->createUnit($agreement_unit_data);
    // }

    // public function createOrRestore($data)
    // {
    //     // dd($data);
    //     return DB::transaction(function () use ($data) {

    //         $userId = auth()->user()->id;
    //         $tenant = null;
    //         $salesUnit = null;

    //         // -----------------------------
    //         // 1. Create Tenant (B2B only)
    //         // -----------------------------
    //         if ($data->business_type == 2) {
    //             // dd("test");

    //             $tenantData = [
    //                 'tenant_name' => $data->tenant_name,
    //                 'tenant_mobile' => $data->tenant_mobile,
    //                 'tenant_email' => $data->tenant_email,
    //                 'nationality_id' => $data->nationality_id,
    //                 'tenant_address' => $data->tenant_address,
    //                 'tenant_street' => $data->tenant_street,
    //                 'tenant_city' => $data->tenant_city,
    //                 'contact_person' => $data->contact_person,
    //                 'contact_person_department' => $data->contact_person_department ?? null,
    //                 'office_landline' => $data->office_landline,
    //                 'contact_number' => $data->contact_number,
    //                 'contact_email' => $data->contact_email,
    //                 'tenant_type' => $data->business_type,
    //                 'added_by' => $userId,
    //                 'tenant_source' => 2
    //             ];

    //             $tenant = $this->tenantService->create($tenantData);
    //             if (!empty($data->docsB2C) && $tenant) {

    //                 foreach ($data->docsB2C as $doc) {
    //                     // dd($doc);

    //                     if (!empty($doc['file'])) {

    //                         $path = $doc['file']->store("tenants/{$tenant->tenant_code}", 'public');

    //                         $docData = [
    //                             'tenant_id' => $tenant->id,
    //                             'document_type' => $doc['type'] ?? null,
    //                             'document_number' => $doc['number'] ?? null,
    //                             'original_document_path' => $path,
    //                             'original_document_name' => $doc['file']->getClientOriginalName(),
    //                             'issued_date' => parseDate($doc['issued_date'] ?? null),
    //                             'expiry_date' => parseDate($doc['expiry_date'] ?? null),
    //                             'added_by' => $userId,
    //                         ];

    //                         $tenant->tenantDocuments()->create($docData);
    //                     }
    //                 }
    //             }
    //         }
    //         // dd($tenant);

    //         // -----------------------------
    //         // 2. Create Sales Agreement
    //         // -----------------------------
    //         $agreementData = [
    //             'property_id' => $data->property_id,
    //             'area_id' => $data->area_id,
    //             'locality_id' => $data->locality_id,
    //             'business_type' => $data->business_type,
    //             'start_date' => parseDate($data->start_date),
    //             'end_date' => parseDate($data->end_date ?? null),
    //             'added_by' => $userId,
    //             'sales_agreement_code' => $this->setProjectCode(),
    //             'tenant_id' => $tenant->id
    //         ];

    //         $salesAgreement = $this->tenantRegistrationRepository->createSalesAgreement($agreementData);

    //         // -----------------------------
    //         // 3. Create Agreement Unit
    //         // -----------------------------
    //         $contractUnit = ContractUnitDetail::find($data->contract_unit_details_id);
    //         $agreementUnitData = [
    //             'sales_tenant_agreement_id' => $salesAgreement->id,
    //             'floor_number' => $data->floor_number,
    //             'unit_type_id' => $data->unit_type_id,
    //             'contract_unit_details_id' => $data->contract_unit_details_id,
    //             'contract_subunit_details_id' => $data->contract_subunit_details_id,
    //             'annual_rent' => $data->rent_per_annum,
    //             'monthly_rent' => $data->rent_per_month,
    //             'added_by' => $userId,
    //             'contract_id' => $contractUnit->contract_id
    //         ];

    //         $salesUnit = $this->tenantRegistrationRepository->createUnit($agreementUnitData);

    //         // -----------------------------
    //         // 4. Attach Documents (if any)
    //         // -----------------------------


    //         return [
    //             'tenant' => $tenant,
    //             'agreement' => $salesAgreement,
    //             'unit' => $salesUnit,
    //         ];
    //     });
    // }
    public function setProjectCode($addval = 1)
    {
        // dd(123);
        $codeService = new \App\Services\CodeGeneratorService();
        $code = $codeService->generateNextCode('sales_tenant_agreements', 'sales_agreement_code', 'STA', 5, $addval);
        // dd($code);
        return $code;
    }
    public function getDataTable(array $filters = [])
    {
        $query = $this->tenantRegistrationRepository->getQuery($filters);

        $columns = [
            ['data' => 'DT_RowIndex',          'name' => 'id'],
            ['data' => 'action',               'name' => 'action',          'orderable' => false, 'searchable' => false],
            ['data' => 'sales_agreement_code', 'name' => 'sales_agreement_code'],
            ['data' => 'tenant_details',       'name' => 'tenant_details',   'orderable' => false, 'searchable' => false],
            ['data' => 'business_type',        'name' => 'business_type'],
            ['data' => 'property_details',     'name' => 'property_details', 'orderable' => false, 'searchable' => false],
            ['data' => 'rent_details',         'name' => 'rent_details',     'orderable' => false, 'searchable' => false],
            ['data' => 'start_date',           'name' => 'start_date'],
            ['data' => 'end_date',             'name' => 'end_date'],
        ];

        return datatables()
            ->of($query)
            ->addIndexColumn()

            // Agreement Code
            ->addColumn(
                'sales_agreement_code',
                fn($row) =>
                "<span class='font-weight-bold text-primary'>{$row->sales_agreement_code}</span>"
            )

            // Tenant Details
            ->addColumn('tenant_details', function ($row) {
                $name  = $row->tenant->tenant_name   ?? '-';
                $email = $row->tenant->tenant_email  ?? '-';
                $phone = $row->tenant->tenant_mobile ?? '-';

                return "<strong class='text-capitalize'>{$name}</strong>
                    <p class='mb-0 text-primary small'>{$email}</p>
                    <p class='mb-0 text-muted small'>
                        <i class='fa fa-phone-alt text-danger'></i>
                        <span class='font-weight-bold'>{$phone}</span>
                    </p>";
            })

            // Business Type
            ->addColumn('business_type', function ($row) {
                if ($row->business_type == 1) {
                    return "<span class='badge badge-info'>B2B</span>";
                } elseif ($row->business_type == 2) {
                    return "<span class='badge badge-success'>B2C</span>";
                }
                return '-';
            })

            // Property / Unit / SubUnit
            ->addColumn('property_details', function ($row) {
                $property = $row->property->property_name ?? '-';

                $units = $row->agreementUnits
                    ->map(fn($au) => optional($au->contractUnitDetail)->unit_number)
                    ->filter()
                    ->implode(', ');

                $subunit = optional(
                    $row->agreementUnits->first()?->contractSubunitDetail
                )->subunit_no;
                $subunit = $subunit ? "SubUnit - {$subunit}" : null;

                $html = "<span class='font-weight-bold'>{$property}</span>";
                if ($units)   $html .= "<br><small class='text-muted'>Unit(s): {$units}</small>";
                if ($subunit) $html .= "<br><small class='text-muted'>{$subunit}</small>";

                return $html;
            })

            // Rent Details
            ->addColumn('rent_details', function ($row) {
                $unit = $row->agreementUnits->first();
                $annual  = $unit->annual_rent  ?? '-';
                $monthly = $unit->monthly_rent ?? '-';

                return "<span class='font-weight-bold'>Annual: {$annual}</span>
                    <br><small class='text-muted'>Monthly: {$monthly}</small>";
            })

            ->addColumn('start_date', fn($row) => getFormattedDate($row->start_date))
            ->addColumn('end_date',   fn($row) => getFormattedDate($row->end_date))

            // Actions
            ->addColumn('action', function ($row) {
                $viewUrl = route('tenant-registration.show', $row->id);
                $editUrl = route('tenant-registration.edit', $row->id);
                $docUrl  = route('tenant-registration.documents', $row->id);
                $approveUrl = route('tenant-registration.approve', $row->id);
                $makeAgreement = route('tenant-registration.make-agreement', $row->id);
                $action  = '';

                if (auth()->user()->hasAnyPermission(['tenant-registration.view'])) {
                    $action .= '<a href="' . $viewUrl . '" class="btn btn-primary btn-sm mr-1" title="View">
                                <i class="fas fa-eye"></i></a>';
                }
                if (auth()->user()->hasAnyPermission(['tenant-registration.edit'])) {
                    $action .= '<a href="' . $editUrl . '" class="btn btn-info btn-sm mr-1" title="Edit">
                                <i class="fas fa-pencil-alt"></i></a>';
                }
                if (auth()->user()->hasAnyPermission(['tenant-registration.approve'])) {
                    $action .= '<button
                                    class="btn btn-success btn-sm mr-1 open-approval-modal"
                                    data-url="' . $approveUrl . '"
                                    title="Approve">
                                    <i class="fas fa-clipboard-check"></i>
                                </button>';
                }
                if (auth()->user()->hasAnyPermission(['tenant-registration.delete'])) {
                    $action .= '<a class="btn btn-danger btn-sm mr-1" onclick="deleteConf(' . $row->id . ')" title="Delete">
                                <i class="fas fa-trash"></i></a>';
                }
                // if (auth()->user()->hasAnyPermission(['tenant-registration.make-agreement'])) {
                //     $action .= '<button
                //                     class="btn bg-gradient-gray btn-sm mr-1 open-approval-modal"
                //                     data-url="' . $makeAgreement . '"
                //                     title="Approve">
                //                     <i class="fas fa-handshake"></i>
                //                 </button>';
                // }

                return $action ?: '-';
            })

            ->rawColumns([
                'sales_agreement_code',
                'tenant_details',
                'business_type',
                'property_details',
                'rent_details',
                'action',
            ])
            ->with(['columns' => $columns])
            ->toJson();
    }
    public function createOrRestore($data)
    {
        // dd($data);
        return DB::transaction(function () use ($data) {

            $userId = auth()->user()->id;
            $tenant = null;

            $isB2B = $data->business_type == 1;
            $isB2C = $data->business_type == 2;

            // ══════════════════════════════════════════════════════
            // 1. Resolve / Create Tenant
            // ══════════════════════════════════════════════════════
            if ($isB2B) {

                // ── A) Existing customer selected ──
                if (!empty($data->existing_customer_id)) {
                    $tenant = $this->tenantService->getById($data->existing_customer_id);

                    // ── B) New B2B company ──
                } else {
                    $tenantData = [
                        'tenant_name'                => $data->tenant_name,
                        'tenant_mobile'              => $data->tenant_mobile,
                        'tenant_email'               => $data->tenant_email,
                        'nationality_id'             => $data->nationality_id,
                        'tenant_address'             => $data->tenant_address,
                        'tenant_street'              => $data->tenant_street,
                        'tenant_city'                => $data->tenant_city,
                        'contact_person'             => $data->contact_person,
                        'contact_person_department'  => $data->contact_person_department ?? null,
                        'office_landline'            => $data->office_landline,
                        'contact_number'             => $data->contact_number,
                        'contact_email'              => $data->contact_email,
                        'tenant_type'                => $data->business_type,
                        'added_by'                   => $userId,
                        'tenant_source'              => 2,
                        'no_of_owners'               => $data->no_of_owners
                    ];
                    $tenant = $this->tenantService->create($tenantData);

                    // ── Owner documents (Emirates ID + Passport per owner) ──
                    if ($tenant && !empty($data->owners)) {
                        foreach ($data->owners as $ownerIndex => $docTypes) {
                            // $docTypes = [ 1 => [passport fields], 2 => [emirates fields] ]
                            foreach ($docTypes as $docTypeId => $docData) {

                                $fileKey = match ((int)$docTypeId) {
                                    1 => 'passport_file',
                                    2 => 'emirates_file',
                                    default => null,
                                };
                                $numberKey = match ((int)$docTypeId) {
                                    1 => 'passport_number',
                                    2 => 'emirates_id',
                                    default => null,
                                };

                                if (!$fileKey || empty($docData[$fileKey])) continue;

                                $path = $docData[$fileKey]->store(
                                    "tenants/{$tenant->tenant_code}/owner_{$ownerIndex}",
                                    'public'
                                );
                                $doc_data = [
                                    'tenant_id'             => $tenant->id,
                                    'owner_index' => $ownerIndex,
                                    'document_type'         => $docTypeId,
                                    'document_number'       => $docData[$numberKey] ?? null,
                                    'original_document_path' => $path,
                                    'original_document_name' => $docData[$fileKey]->getClientOriginalName(),
                                    'issued_date'           => parseDate($docData[str_replace('_file', '_issued', $fileKey)] ?? null),
                                    'expiry_date'           => parseDate($docData[str_replace('_file', '_expiry', $fileKey)] ?? null),
                                    'added_by'              => $userId,
                                ];
                                $this->validateTenantDocs($doc_data);


                                $tenant->tenantDocuments()->create($doc_data);
                            }
                        }
                    }

                    // ── Trade License ──
                    if ($tenant && !empty($data->file('tl_file'))) {
                        $tlFile = $data->file('tl_file');
                        $path   = $tlFile->store("tenants/{$tenant->tenant_code}/trade_license", 'public');
                        $doc_data_tl = [
                            'tenant_id'              => $tenant->id,
                            'document_type'          => 3, // trade_license type ID
                            'document_number'        => $data->tl_number ?? null,
                            'original_document_path' => $path,
                            'original_document_name' => $tlFile->getClientOriginalName(),
                            'issued_date'            => parseDate($data->tl_issued ?? null),
                            'expiry_date'            => parseDate($data->tl_expiry ?? null),
                            'added_by'               => $userId,
                        ];
                        $this->validateTenantDocs($doc_data_tl);
                        $tenant->tenantDocuments()->create($doc_data_tl);
                    }
                }
            } elseif ($isB2C) {

                // ── New B2C individual ──
                $tenantData = [
                    'tenant_name'    => $data->tenant_name,
                    'tenant_mobile'  => $data->tenant_mobile,
                    'tenant_email'   => $data->tenant_email,
                    'nationality_id' => $data->nationality_id,
                    'tenant_address' => $data->tenant_address,
                    'tenant_street'  => $data->tenant_street,
                    'tenant_city'    => $data->tenant_city,
                    'contact_person' => $data->contact_person,
                    'contact_number' => $data->contact_number,
                    'contact_email'  => $data->contact_email,
                    'tenant_type'    => $data->business_type,
                    'added_by'       => $userId,
                    'tenant_source'  => 2,
                ];
                $tenant = $this->tenantService->create($tenantData);

                // ── B2C documents (Emirates ID / Passport) ──
                if ($tenant && !empty($data->docsB2C)) {
                    foreach ($data->docsB2C as $doc) {
                        if (empty($doc['file'])) continue;

                        $path = $doc['file']->store(
                            "tenants/{$tenant->tenant_code}",
                            'public'
                        );
                        $doc_data = [
                            'tenant_id'              => $tenant->id,
                            'document_type'          => $doc['type'] ?? null,
                            'document_number'        => $doc['number'] ?? null,
                            'original_document_path' => $path,
                            'original_document_name' => $doc['file']->getClientOriginalName(),
                            'issued_date'            => parseDate($doc['issued_date'] ?? null),
                            'expiry_date'            => parseDate($doc['expiry_date'] ?? null),
                            'added_by'               => $userId,
                        ];
                        $this->validateTenantDocs($doc_data);

                        $tenant->tenantDocuments()->create($doc_data);
                    }
                }
            }

            // ══════════════════════════════════════════════════════
            // 2. Create Sales Agreement
            // ══════════════════════════════════════════════════════
            $agreementData = [
                'property_id'          => $data->property_id,
                'area_id'              => $data->area_id,
                'locality_id'          => $data->locality_id,
                'business_type'        => $data->business_type,
                'start_date'           => parseDate($data->start_date),
                'end_date'             => parseDate($data->end_date ?? null),
                'added_by'             => $userId,
                'sales_agreement_code' => $this->setProjectCode(),
                'tenant_id'            => $tenant->id,
            ];

            $salesAgreement = $this->tenantRegistrationRepository->createSalesAgreement($agreementData);

            // ══════════════════════════════════════════════════════
            // 3. Create Agreement Unit(s)
            // ══════════════════════════════════════════════════════
            $units = [];

            if ($isB2C) {
                // Single unit selection from dropdowns
                $contractUnit = ContractUnitDetail::find($data->contract_unit_details_id);

                $units[] = $this->tenantRegistrationRepository->createUnit([
                    'sales_tenant_agreement_id'   => $salesAgreement->id,
                    'floor_number'                => $data->floor_number,
                    'unit_type_id'                => $data->unit_type_id,
                    'contract_unit_details_id'    => $data->contract_unit_details_id,
                    'contract_subunit_details_id' => $data->contract_subunit_details_id ?? null,
                    'annual_rent'                 => $data->rent_per_annum,
                    'monthly_rent'                => $data->rent_per_month,
                    'added_by'                    => $userId,
                    'contract_id'                 => $contractUnit->contract_id,
                ]);
            } elseif ($isB2B) {
                $unitRents    = $data->input('unit_rent', []);
                $subunitRents = $data->input('subunit_rent', []);

                foreach ($unitRents as $key => $rents) {
                    [$floor, $typeId, $unitId] = explode('_', $key);

                    if (empty($rents['annual']) && empty($rents['monthly'])) continue;

                    $contractUnit = ContractUnitDetail::find((int)$unitId);
                    if (!$contractUnit) continue;

                    // ── Collect subunit IDs for this unit first ──
                    $subunitIds = [];
                    foreach ($subunitRents as $subKey => $subRents) {
                        if (!str_starts_with($subKey, "{$floor}_{$typeId}_{$unitId}_")) continue;
                        [,,, $subId] = explode('_', $subKey);
                        $subunitIds[] = (int)$subId;
                    }

                    // ── Save to sales_tenant_units with subunit_ids ──
                    $unit = $this->tenantRegistrationRepository->createUnit([
                        'sales_tenant_agreement_id'   => $salesAgreement->id,
                        'floor_number'                => $floor,
                        'unit_type_id'                => (int)$typeId,
                        'contract_unit_details_id'    => (int)$unitId,
                        'contract_subunit_details_id' => null,
                        'subunit_ids'                 => !empty($subunitIds) ? json_encode($subunitIds) : null,
                        'annual_rent'                 => $rents['annual'] ?? null,
                        'monthly_rent'                => $rents['monthly'] ?? null,
                        'added_by'                    => $userId,
                        'contract_id'                 => $contractUnit->contract_id,
                    ]);
                    $units[] = $unit;

                    // ── Save each subunit to sales_tenant_subunit_rents ──
                    foreach ($subunitRents as $subKey => $subRents) {
                        if (!str_starts_with($subKey, "{$floor}_{$typeId}_{$unitId}_")) continue;

                        [,,, $subId] = explode('_', $subKey);

                        \App\Models\SalesTenantSubunitRent::create([
                            'sales_tenant_agreement_id'   => $salesAgreement->id,
                            'sales_tenant_unit_id'        => $unit->id,
                            'contract_subunit_details_id' => (int)$subId,
                            'rent_per_month'              => $subRents['monthly'] ?? null,
                            'added_by'                    => $userId,
                        ]);
                    }
                }
            }

            return [
                'tenant'    => $tenant,
                'agreement' => $salesAgreement,
                'units'     => $units,
            ];
        });
    }
    // private function buildPropertyUnitMap($properties): array
    // {
    //     $occupiedUnitIds = \App\Models\SalesTenantUnit::whereNotNull('contract_unit_details_id')
    //         ->whereNull('contract_subunit_details_id')
    //         ->whereHas('salesAgreement', fn($q) => $q->where('is_approved', '!=', 2))
    //         ->pluck('contract_unit_details_id')
    //         ->toArray();

    //     $occupiedSubunitIds = \App\Models\SalesTenantUnit::whereNotNull('contract_subunit_details_id')
    //         ->whereHas('salesAgreement', fn($q) => $q->where('is_approved', '!=', 2))
    //         ->pluck('contract_subunit_details_id')
    //         ->toArray();

    //     $map = [];

    //     foreach ($properties as $property) {
    //         $b2cFloorsMap = [];
    //         $b2bFloorsMap = [];

    //         foreach ($property->contracts as $contract) {

    //             $contractUnit = $contract->contract_unit;
    //             if (!$contractUnit) continue;

    //             $details = $contractUnit->contractUnitDetails;
    //             if (!$details || !is_iterable($details)) continue;

    //             $isB2C   = $contractUnit->business_type == 2;
    //             if ($isB2C) {
    //                 $floorsMap = &$b2cFloorsMap;
    //             } else {
    //                 $floorsMap = &$b2bFloorsMap;
    //             }

    //             foreach ($details as $detail) {

    //                 $floor      = (string) ($detail->floor_no ?? '');
    //                 $unitTypeId = (int)    ($detail->unit_type_id ?? 0);
    //                 $unitType   = (string) ($detail->unit_type->unit_type ?? 'Unknown');
    //                 $unitNo     = (string) ($detail->unit_number ?? '');
    //                 $unitId     = (int)    ($detail->id ?? 0);

    //                 if (!$floor || !$unitNo || !$unitId || !$unitTypeId) continue;

    //                 $subUnitRelation = $detail->contractSubUnitDetails;
    //                 $hasSubunits     = $subUnitRelation
    //                     && is_iterable($subUnitRelation)
    //                     && collect($subUnitRelation)->isNotEmpty();

    //                 if (!$hasSubunits) {
    //                     // ── No subunits: skip if unit is occupied ──
    //                     if (in_array($unitId, $occupiedUnitIds)) continue;

    //                     $subunits = [];
    //                 } else {
    //                     // ── Has subunits: filter occupied ones out ──
    //                     $subunits = collect($subUnitRelation)
    //                         ->filter(fn($sub) => !in_array($sub->id, $occupiedSubunitIds))
    //                         ->map(fn($sub) => [
    //                             'id'    => $sub->id,
    //                             'label' => $sub->subunit_no,
    //                         ])
    //                         ->values()
    //                         ->toArray();

    //                     // ── All subunits occupied: skip unit entirely ──
    //                     if (empty($subunits)) continue;
    //                 }

    //                 // ── Init type group if not exists ──
    //                 if (!isset($floorsMap[$floor][$unitTypeId])) {
    //                     $floorsMap[$floor][$unitTypeId] = [
    //                         'label' => $unitType,
    //                         'units' => [],
    //                     ];
    //                 }

    //                 // ── Check if unit already added ──
    //                 $existingIndex = null;
    //                 foreach ($floorsMap[$floor][$unitTypeId]['units'] as $idx => $u) {
    //                     if ($u['id'] === $unitId) {
    //                         $existingIndex = $idx;
    //                         break;
    //                     }
    //                 }

    //                 if ($existingIndex === null) {
    //                     // ── Not yet added: insert ──
    //                     $floorsMap[$floor][$unitTypeId]['units'][] = [
    //                         'id'          => $unitId,
    //                         'unit_number' => $unitNo,
    //                         'subunits'    => $subunits,
    //                     ];
    //                 } else {
    //                     // ── Already added: UPDATE subunits in case availability changed ──
    //                     $floorsMap[$floor][$unitTypeId]['units'][$existingIndex]['subunits'] = $subunits;

    //                     // ── If subunits became empty after update: remove the unit ──
    //                     if ($hasSubunits && empty($subunits)) {
    //                         array_splice($floorsMap[$floor][$unitTypeId]['units'], $existingIndex, 1);

    //                         // ── If no units left in this type group: remove type group ──
    //                         if (empty($floorsMap[$floor][$unitTypeId]['units'])) {
    //                             unset($floorsMap[$floor][$unitTypeId]);

    //                             // ── If no type groups left on floor: remove floor ──
    //                             if (empty($floorsMap[$floor])) {
    //                                 unset($floorsMap[$floor]);
    //                             }
    //                         }
    //                     }
    //                 }
    //             }

    //             unset($floorsMap);
    //         }

    //         $map[$property->id] = [
    //             'b2c' => $b2cFloorsMap,
    //             'b2b' => $b2bFloorsMap,
    //         ];
    //     }

    //     return $map;
    // }

    private function buildPropertyUnitMap($properties): array
    {
        // ── B2C: fully occupied units ──
        $occupiedUnitIds = \App\Models\SalesTenantUnit::whereNotNull('contract_unit_details_id')
            ->whereNull('contract_subunit_details_id')
            ->whereNull('subunit_ids')
            ->whereHas('salesAgreement', fn($q) => $q->where('is_approved', '!=', 2))
            ->pluck('contract_unit_details_id')
            ->toArray();

        // ── B2C: occupied subunits ──
        // $occupiedSubunitIds = \App\Models\SalesTenantUnit::whereNotNull('contract_subunit_details_id')
        //     ->whereHas('salesAgreement', fn($q) => $q->where('is_approved', '!=', 2))
        //     ->pluck('contract_subunit_details_id')
        //     ->toArray();
        $occupiedSubunitIds = SalesTenantUnit::whereNotNull('contract_subunit_details_id')
            ->whereHas('salesAgreement', fn($q) => $q->where('is_approved', '!=', 2))
            ->pluck('contract_subunit_details_id')
            ->toArray();

        // ── B2B: unit_id => [occupied subunit ids] ──
        $b2bOccupiedSubunitsByUnit = \App\Models\SalesTenantUnit::whereNotNull('subunit_ids')
            ->whereHas('salesAgreement', fn($q) => $q->where('is_approved', '!=', 2))
            ->get(['contract_unit_details_id', 'subunit_ids'])
            ->groupBy('contract_unit_details_id')
            ->map(fn($rows) => $rows->flatMap(fn($r) => json_decode($r->subunit_ids, true) ?? [])->toArray());

        $map = [];

        foreach ($properties as $property) {
            $b2cFloorsMap = [];
            $b2bFloorsMap = [];

            foreach ($property->contracts as $contract) {

                $contractUnit = $contract->contract_unit;
                if (!$contractUnit) continue;

                $details = $contractUnit->contractUnitDetails;
                if (!$details || !is_iterable($details)) continue;

                $isB2C = $contractUnit->business_type == 2;
                if ($isB2C) {
                    $floorsMap = &$b2cFloorsMap;
                } else {
                    $floorsMap = &$b2bFloorsMap;
                }

                foreach ($details as $detail) {

                    $floor      = (string) ($detail->floor_no ?? '');
                    $unitTypeId = (int)    ($detail->unit_type_id ?? 0);
                    $unitType   = (string) ($detail->unit_type->unit_type ?? 'Unknown');
                    $unitNo     = (string) ($detail->unit_number ?? '');
                    $unitId     = (int)    ($detail->id ?? 0);

                    if (!$floor || !$unitNo || !$unitId || !$unitTypeId) continue;

                    $subUnitRelation = $detail->contractSubUnitDetails;
                    $hasSubunits     = $subUnitRelation
                        && is_iterable($subUnitRelation)
                        && collect($subUnitRelation)->isNotEmpty();

                    if (!$hasSubunits) {
                        // ── No subunits: use existing B2C occupied check ──
                        if (in_array($unitId, $occupiedUnitIds)) continue;
                        $subunits = [];
                    } else {
                        if ($isB2C) {
                            // ── B2C subunits: use contract_subunit_details_id ──
                            $subunits = collect($subUnitRelation)
                                ->filter(fn($sub) => !in_array($sub->id, $occupiedSubunitIds))
                                ->map(fn($sub) => ['id' => $sub->id, 'label' => $sub->subunit_no])
                                ->values()
                                ->toArray();
                        } else {
                            // ── B2B subunits: use subunit_ids JSON column ──
                            $occupiedSubs = $b2bOccupiedSubunitsByUnit->get($unitId, []);
                            $subunits = collect($subUnitRelation)
                                ->filter(fn($sub) => !in_array($sub->id, $occupiedSubs))
                                ->map(fn($sub) => ['id' => $sub->id, 'label' => $sub->subunit_no])
                                ->values()
                                ->toArray();
                        }

                        // ── All subunits occupied: skip unit entirely ──
                        if (empty($subunits)) continue;
                    }

                    // ── Init type group if not exists ──
                    if (!isset($floorsMap[$floor][$unitTypeId])) {
                        $floorsMap[$floor][$unitTypeId] = [
                            'label' => $unitType,
                            'units' => [],
                        ];
                    }

                    // ── Check if unit already added ──
                    $existingIndex = null;
                    foreach ($floorsMap[$floor][$unitTypeId]['units'] as $idx => $u) {
                        if ($u['id'] === $unitId) {
                            $existingIndex = $idx;
                            break;
                        }
                    }

                    if ($existingIndex === null) {
                        $floorsMap[$floor][$unitTypeId]['units'][] = [
                            'id'          => $unitId,
                            'unit_number' => $unitNo,
                            'subunits'    => $subunits,
                        ];
                    } else {
                        $floorsMap[$floor][$unitTypeId]['units'][$existingIndex]['subunits'] = $subunits;

                        if ($hasSubunits && empty($subunits)) {
                            array_splice($floorsMap[$floor][$unitTypeId]['units'], $existingIndex, 1);

                            if (empty($floorsMap[$floor][$unitTypeId]['units'])) {
                                unset($floorsMap[$floor][$unitTypeId]);

                                if (empty($floorsMap[$floor])) {
                                    unset($floorsMap[$floor]);
                                }
                            }
                        }
                    }
                }

                unset($floorsMap);
            }

            $map[$property->id] = [
                'b2c' => $b2cFloorsMap,
                'b2b' => $b2bFloorsMap,
            ];
        }

        return $map;
    }

    public function getDetails($id)
    {
        $agreement = \App\Models\SalesTenantAgreement::with([
            'tenant.nationality',
            'tenant.tenantDocuments.TenantIdentity',
            'property',
            'area',
            'locality',
            'addedBy',
            'agreementUnits.contractUnitDetail.contractSubUnitDetails',
            'agreementUnits.unitType',
            'agreementUnits.salesTenantSubunitRents.contractSubUnitDetail', // ← add this
        ])->findOrFail($id);
        return $agreement;
    }
    public function update($agreementId, $data)
    {
        // dd($data);
        return DB::transaction(function () use ($data, $agreementId) {

            $userId = auth()->user()->id;
            $isB2B  = $data->business_type == 1;
            $isB2C  = $data->business_type == 2;

            $agreement = \App\Models\SalesTenantAgreement::findOrFail($agreementId);
            $tenant    = $agreement->tenant;

            // ══════════════════════════════════════════════════════
            // 1. Update Tenant
            // ══════════════════════════════════════════════════════
            if ($isB2B) {

                if (!empty($data->existing_customer_id)) {
                    // Existing customer — just reassign
                    $tenant = $this->tenantService->getById($data->existing_customer_id);
                    $agreement->tenant_id = $tenant->id;
                } else {
                    // Update B2B tenant fields
                    $this->tenantService->update([
                        'id' => $data->agreement_tenant_id,
                        'tenant_name'               => $data->tenant_name,
                        'tenant_mobile'             => $data->tenant_mobile,
                        'tenant_email'              => $data->tenant_email,
                        'nationality_id'            => $data->nationality_id,
                        'tenant_address'            => $data->tenant_address,
                        'tenant_street'             => $data->tenant_street,
                        'tenant_city'               => $data->tenant_city,
                        'contact_person'            => $data->contact_person,
                        'contact_person_department' => $data->contact_person_department ?? null,
                        'office_landline'           => $data->office_landline ?? null,
                        'contact_number'            => $data->contact_number,
                        'contact_email'             => $data->contact_email,
                        'no_of_owners'              => $data->no_of_owners,
                        'updated_by'                => $userId,
                    ]);

                    // ── Owner documents — update existing, skip if no new file ──
                    if (!empty($data->owners)) {
                        foreach ($data->owners as $ownerIndex => $docTypes) {
                            foreach ($docTypes as $docTypeId => $docData) {

                                $fileKey = match ((int)$docTypeId) {
                                    1 => 'passport_file',
                                    2 => 'emirates_file',
                                    default => null,
                                };
                                $numberKey = match ((int)$docTypeId) {
                                    1 => 'passport_number',
                                    2 => 'emirates_id',
                                    default => null,
                                };

                                if (!$fileKey) continue;

                                $docId = $docData['id'] ?? null;

                                $updatePayload = [
                                    'document_number' => $docData[$numberKey] ?? null,
                                    'issued_date'     => parseDate($docData[str_replace('_file', '_issued', $fileKey)] ?? null),
                                    'expiry_date'     => parseDate($docData[str_replace('_file', '_expiry', $fileKey)] ?? null),
                                    'updated_by'      => $userId,
                                ];

                                // Only update file if a new one was uploaded
                                if (!empty($docData[$fileKey]) && $docData[$fileKey] instanceof \Illuminate\Http\UploadedFile) {
                                    $path = $docData[$fileKey]->store(
                                        "tenants/{$tenant->tenant_code}/owner_{$ownerIndex}",
                                        'public'
                                    );
                                    $updatePayload['original_document_path'] = $path;
                                    $updatePayload['original_document_name'] = $docData[$fileKey]->getClientOriginalName();
                                }

                                if ($docId) {
                                    // ✅ Update by id directly — no query needed
                                    $existingDoc = $tenant->tenantDocuments()->find($docId);
                                    if ($existingDoc) {
                                        $existingDoc->update($updatePayload);
                                    }
                                } else {
                                    // ✅ New doc — must have file
                                    if (!empty($docData[$fileKey]) && $docData[$fileKey] instanceof \Illuminate\Http\UploadedFile) {
                                        $path = $docData[$fileKey]->store(
                                            "tenants/{$tenant->tenant_code}/owner_{$ownerIndex}",
                                            'public'
                                        );
                                        $newDocPayload = array_merge($updatePayload, [
                                            'tenant_id'              => $tenant->id,
                                            'owner_index'            => $ownerIndex,
                                            'document_type'          => $docTypeId,
                                            'original_document_path' => $path,
                                            'original_document_name' => $docData[$fileKey]->getClientOriginalName(),
                                            'added_by'               => $userId,
                                        ]);
                                        $this->validateTenantDocs($newDocPayload);

                                        $tenant->tenantDocuments()->create($newDocPayload);
                                    }
                                }
                            }
                        }
                    }
                    // Trade License document
                    $tlId = $data->tl_id ?? null;

                    $tlPayload = [
                        'document_number' => $data->tl_number ?? null,
                        'issued_date'     => parseDate($data->tl_issued ?? null),
                        'expiry_date'     => parseDate($data->tl_expiry ?? null),
                        'updated_by'      => $userId,
                    ];

                    // Only update file if a new one was uploaded
                    if (!empty($data->tl_file) && $data->tl_file instanceof \Illuminate\Http\UploadedFile) {
                        $path = $data->tl_file->store("tenants/{$tenant->tenant_code}", 'public');
                        $tlPayload['original_document_path'] = $path;
                        $tlPayload['original_document_name'] = $data->tl_file->getClientOriginalName();
                    }

                    if ($tlId) {
                        // ✅ Update existing by id
                        $existingTl = $tenant->tenantDocuments()->find($tlId);
                        if ($existingTl) {
                            $this->validateTenantDocs(array_merge($tlPayload, [
                                'document_type' => 3,
                            ]), $tlId);
                            $existingTl->update($tlPayload);
                        }
                    } else {
                        // ✅ Create new — must have file
                        if (!empty($data->tl_file) && $data->tl_file instanceof \Illuminate\Http\UploadedFile) {
                            $newTlPayload = array_merge($tlPayload, [
                                'tenant_id'     => $tenant->id,
                                'document_type' => 3,
                                'added_by'      => $userId,
                            ]);

                            $this->validateTenantDocs($newTlPayload);
                            $tenant->tenantDocuments()->create(array_merge($newTlPayload));
                        }
                    }
                }
            } elseif ($isB2C) {

                $this->tenantService->update([
                    'id' => $data->agreement_tenant_id,
                    'tenant_name'    => $data->tenant_name,
                    'tenant_mobile'  => $data->tenant_mobile,
                    'tenant_email'   => $data->tenant_email,
                    'nationality_id' => $data->nationality_id,
                    'tenant_address' => $data->tenant_address,
                    'tenant_street'  => $data->tenant_street,
                    'tenant_city'    => $data->tenant_city,
                    'contact_person' => $data->contact_person,
                    'contact_number' => $data->contact_number,
                    'contact_email'  => $data->contact_email,
                    'updated_by'     => $userId,
                ]);

                // ── B2C documents — update existing or create new ──
                if (!empty($data->docsB2C)) {
                    foreach ($data->docsB2C as $doc) {
                        $docId = $doc['id'] ?? null;

                        // ── If existing doc (has id) ──
                        if ($docId) {
                            $existingDoc = $tenant->tenantDocuments()->find($docId);
                            if (!$existingDoc) continue;

                            $updateData = [
                                'document_type'   => $doc['type']   ?? $existingDoc->document_type,
                                'document_number' => $doc['number']  ?? $existingDoc->document_number,
                                'issued_date'     => parseDate($doc['issued_date'] ?? null) ?? $existingDoc->issued_date,
                                'expiry_date'     => parseDate($doc['expiry_date'] ?? null) ?? $existingDoc->expiry_date,
                                'updated_by'      => $userId,
                            ];

                            // Only update file if a new one was uploaded
                            if (!empty($doc['file'])) {
                                // Delete old file
                                if ($existingDoc->original_document_path) {
                                    Storage::disk('public')->delete($existingDoc->original_document_path);
                                }

                                $path = $doc['file']->store("tenants/{$tenant->tenant_code}", 'public');
                                $updateData['original_document_path'] = $path;
                                $updateData['original_document_name'] = $doc['file']->getClientOriginalName();
                            }
                            $this->validateTenantDocs($updateData, $docId);

                            $existingDoc->update($updateData);
                        } else {
                            // ── New doc (no id) — must have file ──
                            if (empty($doc['file'])) continue;

                            $path = $doc['file']->store("tenants/{$tenant->tenant_code}", 'public');
                            $doc_data = [
                                'tenant_id'              => $tenant->id,
                                'document_type'          => $doc['type']   ?? null,
                                'document_number'        => $doc['number']  ?? null,
                                'original_document_path' => $path,
                                'original_document_name' => $doc['file']->getClientOriginalName(),
                                'issued_date'            => parseDate($doc['issued_date'] ?? null),
                                'expiry_date'            => parseDate($doc['expiry_date'] ?? null),
                                'added_by'               => $userId,
                            ];
                            $this->validateTenantDocs($doc_data);
                            $tenant->tenantDocuments()->create();
                        }
                    }
                }
            }

            // ══════════════════════════════════════════════════════
            // 2. Update Agreement
            // ══════════════════════════════════════════════════════
            $agreement->update([
                'property_id'   => $data->property_id,
                'area_id'       => $data->area_id,
                'locality_id'   => $data->locality_id,
                'business_type' => $data->business_type,
                'start_date'    => parseDate($data->start_date),
                'end_date'      => parseDate($data->end_date ?? null),
                'tenant_id'     => $data->agreement_tenant_id,
                'updated_by'    => $userId,
            ]);

            // ══════════════════════════════════════════════════════
            // 3. Replace Agreement Units
            // ══════════════════════════════════════════════════════

            // Delete old units and subunit rents
            // $oldUnitIds = $agreement->agreementUnits()->pluck('id');
            // \App\Models\SalesTenantSubunitRent::whereIn('sales_tenant_unit_id', $oldUnitIds)->delete();
            // $agreement->agreementUnits()->delete();

            // Recreate units (same logic as create)
            if ($isB2C) {
                $contractUnit = ContractUnitDetail::find($data->contract_unit_details_id);

                $this->tenantRegistrationRepository->updateUnit([
                    'sales_tenant_agreement_id'   => $agreement->id,
                    'floor_number'                => $data->floor_number,
                    'unit_type_id'                => $data->unit_type_id,
                    'contract_unit_details_id'    => $data->contract_unit_details_id,
                    'contract_subunit_details_id' => $data->contract_subunit_details_id ?? null,
                    'annual_rent'                 => $data->rent_per_annum,
                    'monthly_rent'                => $data->rent_per_month,
                    'added_by'                    => $userId,
                    'contract_id'                 => $contractUnit->contract_id,
                    'id' => $data->agreement_unit_id
                ]);
            } elseif ($isB2B) {
                // dd($data->unit_rent);
                $unitRents    = $data->input('unit_rent', []);
                $subunitRents = $data->input('subunit_rent', []);

                foreach ($unitRents as $key => $rents) {
                    [$floor, $typeId, $unitId] = explode('_', $key);

                    // Skip units with no rent entered
                    if (empty($rents['annual']) && empty($rents['monthly'])) continue;

                    $contractUnit = ContractUnitDetail::find((int)$unitId);
                    if (!$contractUnit) continue;

                    $subunitIds = [];
                    foreach ($subunitRents as $subKey => $subRents) {
                        if (!str_starts_with($subKey, "{$floor}_{$typeId}_{$unitId}_")) continue;
                        [,,, $subId] = explode('_', $subKey);
                        $subunitIds[] = (int)$subId;
                    }

                    $unitPayload = [
                        'sales_tenant_agreement_id'   => $agreement->id,
                        'floor_number'                => $floor,
                        'unit_type_id'                => (int)$typeId,
                        'contract_unit_details_id'    => (int)$unitId,
                        'contract_subunit_details_id' => null,
                        'subunit_ids'                 => !empty($subunitIds) ? json_encode($subunitIds) : null,
                        'annual_rent'                 => $rents['annual'] ?? null,
                        'monthly_rent'                => $rents['monthly'] ?? null,
                        'added_by'                    => $userId,
                        'contract_id'                 => $contractUnit->contract_id,
                    ];

                    // ── Update existing unit or create new ──
                    $existingUnitId = !empty($rents['id']) ? (int)$rents['id'] : null;

                    if ($existingUnitId) {
                        // dd($existingUnitId);
                        $unit = SalesTenantUnit::find($existingUnitId);
                        if ($unit && $unit->sales_tenant_agreement_id == $agreement->id) {
                            // dd($unit);
                            $unit->update($unitPayload);
                        } else {
                            $unit = $this->tenantRegistrationRepository->createUnit($unitPayload);
                        }
                    } else {
                        $unit = $this->tenantRegistrationRepository->createUnit($unitPayload);
                    }

                    // ── Handle subunit rents ──
                    foreach ($subunitRents as $subKey => $subRents) {
                        if (!str_starts_with($subKey, "{$floor}_{$typeId}_{$unitId}_")) continue;
                        if (empty($subRents['monthly'])) continue;

                        [,,, $subId] = explode('_', $subKey);

                        $subPayload = [
                            'sales_tenant_agreement_id'   => $agreement->id,
                            'sales_tenant_unit_id'        => $unit->id,
                            'contract_subunit_details_id' => (int)$subId,
                            'rent_per_month'              => $subRents['monthly'],
                            'added_by'                    => $userId,
                        ];

                        // ── Update existing subunit rent or create new ──
                        $existingSubId = !empty($subRents['id']) ? (int)$subRents['id'] : null;

                        if ($existingSubId) {
                            $sub = SalesTenantSubunitRent::find($existingSubId);
                            if ($sub && $sub->sales_tenant_agreement_id == $agreement->id) {
                                $sub->update($subPayload);
                            } else {
                                SalesTenantSubunitRent::create($subPayload);
                            }
                        } else {
                            SalesTenantSubunitRent::create($subPayload);
                        }
                    }
                }
            }

            return [
                'tenant'    => $tenant,
                'agreement' => $agreement,
            ];
        });
    }
    public function deleteAgreementUnit($agreementId, $unitId)
    {
        $unit = SalesTenantUnit::where('id', $unitId)
            ->where('sales_tenant_agreement_id', $agreementId)
            ->firstOrFail();
        // dd($unit);

        $unit->delete();
    }
    public function deleteAgreementDocumentB2c($agreementId, $docId)
    {
        $doc = TenantDocument::where('id', $docId)
            ->where('sales_tenant_agreement_id', $agreementId)
            ->firstOrFail();
        $doc->delete();
        return true;
    }
    public function getAgeement($id)
    {
        $agreement = $this->tenantRegistrationRepository->getAgreement($id);
        return $agreement;
    }
    public function getExistingUnits($agreement)
    {
        $existingUnits = $agreement->agreementUnits->map(fn($u) => [
            'agreement_unit_id' => $u->id,
            'key'           => "{$u->floor_number}_{$u->unit_type_id}_{$u->contract_unit_details_id}",
            'floor'         => $u->floor_number,
            'unit_type_id'  => $u->unit_type_id,
            'unit_type'     => $u->unitType->unit_type ?? '',
            'unit_id'       => $u->contract_unit_details_id,
            'unit_number'   => $u->contractUnitDetail->unit_number ?? '',

            'annual'        => $u->annual_rent,
            'monthly'       => $u->monthly_rent,
            'subunit_id'    => $u->contract_subunit_details_id ?? '',
            'subunit_number' => $u->contractSubunitDetail->subunit_no ?? '',

            'subunit_ids'   => json_decode($u->subunit_ids ?? '[]'),
            'subunit_rents' => $u->salesTenantSubunitRents->map(fn($s) => [
                'id'      => $s->contract_subunit_details_id,
                'label'   => $s->contractSubunitDetail->subunit_no ?? '',
                'monthly' => $s->rent_per_month,
                'subunit_rent_id' => $s->id
            ])->toArray(),

        ])->toArray();
        return $existingUnits;
    }
    public function getExistingOwnerDocsJson($ownerDocs)
    {
        $existingOwnerDocsJson = $ownerDocs->map(function ($docs, $ownerIdx) {
            return $docs->map(function ($doc) use ($ownerIdx) {
                return [
                    'doc_id' => $doc->id,
                    'owner_index'     => $ownerIdx,
                    'document_type'   => $doc->document_type,
                    'document_number' => $doc->document_number,
                    'issued_date'     => $doc->issued_date
                        ? \Carbon\Carbon::parse($doc->issued_date)->format('d-m-Y')
                        : '',
                    'expiry_date'     => $doc->expiry_date
                        ? \Carbon\Carbon::parse($doc->expiry_date)->format('d-m-Y')
                        : '',
                    'view_url'        => $doc->original_document_path
                        ? asset('storage/' . $doc->original_document_path)
                        : null,
                ];
            })->values();
        });
        return $existingOwnerDocsJson;
    }
    public function getExistingB2CDocs($tenant)
    {
        $existingB2CDocs = $tenant->tenantDocuments->map(fn($doc) => [
            'id' => $doc->id,
            'type'        => $doc->document_type,
            'number'      => $doc->document_number,
            'issued_date' => $doc->issued_date ? \Carbon\Carbon::parse($doc->issued_date)->format('d-m-Y') : '',
            'expiry_date' => $doc->expiry_date ? \Carbon\Carbon::parse($doc->expiry_date)->format('d-m-Y') : '',
            'view_url'    => $doc->original_document_path ? asset('storage/' . $doc->original_document_path) : null,
        ])->values()->toArray();
        return $existingB2CDocs;
    }
    private function validateTenantDocs(array $doc_data, $docId = null)
    {
        $rules = [
            'document_number' => 'nullable|string',
            'issued_date'     => 'nullable|date',
            'expiry_date'     => 'nullable|date',
        ];

        // ── original_document_path only required on create ──
        if (!$docId) {
            $rules['original_document_path'] = 'required';
        }

        $docType = (int)($doc_data['document_type'] ?? 0);

        // ── Document type labels ──
        $docLabels = [
            1 => 'Passport number',
            2 => 'Emirates ID',
            3 => 'Trade License number',
        ];

        $docLabel = $docLabels[$docType] ?? 'Document number';

        // ── Unique rule for all document types ──
        if (in_array($docType, [1, 2, 3]) && !empty($doc_data['document_number'])) {

            $uniqueRule = \Illuminate\Validation\Rule::unique('tenant_documents', 'document_number')
                ->where('document_type', $docType); // ← dynamic, no need for 3 blocks

            if ($docId) {
                $uniqueRule->ignore($docId);
            }

            $rules['document_number'] = ['nullable', 'string', $uniqueRule];
        }

        // ── Custom messages ──
        $messages = [
            'issued_date.date'                => 'Issued date must be a valid date.',
            'expiry_date.date'                => 'Expiry date must be a valid date.',
            'original_document_path.required' => 'Please upload a document file.',
            'document_number.required'        => "{$docLabel} is required.",
            'document_number.string'          => "{$docLabel} must be a valid string.",
            'document_number.unique'          => "{$docLabel} (:input) is already registered to another tenant.",
        ];

        $validator = \Illuminate\Support\Facades\Validator::make($doc_data, $rules, $messages);

        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }
    }
    public function approve($id, $data)
    {
        // dd($data);
        $approve['approved_by'] = auth()->id();
        $approve['approved_date'] = now();
        $approve['approved_comments'] = $data->approved_comments;
        $approve['is_approved'] = 1;
        // dd($data);
        $update = $this->tenantRegistrationRepository->approveOrReject($id, $approve);
        return $update;
    }
    public function reject($id, $data)
    {
        // dd($data);
        $approve['approved_by'] = auth()->id();
        $approve['approved_date'] = now();
        $approve['rejection_reason'] = $data->approved_comments;
        $approve['is_approved'] = 2;
        // dd($data);
        $update = $this->tenantRegistrationRepository->approveOrReject($id, $approve);
        return $update;
    }
}
