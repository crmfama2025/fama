<?php

namespace App\Http\Controllers;

use App\Models\SalesTenantAgreement;
use App\Services\Sales\TenantRegistrationService;
use Illuminate\Http\Request;

class TenantregistrationController extends Controller
{
    //
    public function __construct(

        protected TenantRegistrationService $tenantRegistrationService,

    ) {}
    public function index()
    {
        $title = 'Tenant Registration';
        return view('admin.sales.tenant-registration', compact('title'));
    }
    public function create()
    {
        $title = 'Tenant Registration';
        $formData = $this->tenantRegistrationService->getTenantRegistrationFormData();
        // dd($formData);
        return view('admin.sales.tenant-registration-create', compact('title', 'formData'));
    }
    public function store(Request $request)
    {
        // dd($request->all());
        try {
            $agreement = $this->tenantRegistrationService->createOrRestore($request);
            return response()->json(['success' => true, 'data' => $agreement, 'message' => 'Agreeament created successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'error'   => $e], 500);
        }
    }
    public function getList(Request $request)
    {
        // dd($request);
        try {
            $filters = [
                'search' => $request->search['value'] ?? null,
                'status' => $request->status ?? 'all',
            ];
            return $this->tenantRegistrationService->getDataTable($filters);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $agreement = $this->tenantRegistrationService->getDetails($id);

        $tenant = $agreement->tenant;

        // ── Owner docs (B2B only) ──
        $ownerDocs = $agreement->business_type == 1
            ? $tenant->tenantDocuments()->whereNotNull('owner_index')->get()
            : collect();

        return view('admin.sales.tenant-registration-view', compact('agreement', 'tenant', 'ownerDocs'));
    }
    public function edit($id)
    {
        $title = 'Edit Tenant Registration';

        $agreement = $this->tenantRegistrationService->getAgeement($id);

        $tenant       = $agreement->tenant;
        $formData     = $this->tenantRegistrationService->getTenantRegistrationFormData();
        $tradeLicense = $tenant->tenantDocuments->firstWhere('document_type', 3);
        $ownerDocs    = $tenant->tenantDocuments->where('document_type', '!=', 3)->groupBy('owner_index');
        // dd($ownerDocs);
        // dd($tradeLicense);
        $existingUnits = $this->tenantRegistrationService->getExistingUnits($agreement);

        $existingOwnerDocsJson = $this->tenantRegistrationService->getExistingOwnerDocsJson($ownerDocs);

        // dd($existingOwnerDocsJson, $ownerDocs);
        $existingB2CDocs = null;
        if ($agreement->business_type == 2) {
            $existingB2CDocs = $this->tenantRegistrationService->getExistingB2CDocs($tenant);
        }
        // dd($existingB2CDocs);
        // dd($agreement);
        // dd($existingOwnerDocsJson);
        // dd($tradeLicense);

        return view(
            'admin.sales.tenant-registration-create',
            compact('title', 'formData', 'agreement', 'tenant', 'tradeLicense', 'ownerDocs', 'existingUnits', 'existingOwnerDocsJson', 'existingB2CDocs')
        );
    }
    public function update(Request $request, $id)
    {
        // dd($request);
        try {
            $agreement = $this->tenantRegistrationService->update($id, $request);

            return response()->json(['success' => true, 'data' => $agreement, 'message' => 'Agreemant updated successfully'], 200);
        } catch (\Exception $e) {

            return response()->json(['success' => false, 'message' => $e->getMessage(), 'error'   => $e], 500);
        }
    }
    public function documents() {}
    public function approve(Request $request, $id)
    {
        try {
            $agreement = $this->tenantRegistrationService->approve($id, $request);

            return redirect()->back()
                ->with('success', 'Agreement approved successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }
    public function reject(Request $request, $id)
    {
        try {
            $agreement = $this->tenantRegistrationService->reject($id, $request);

            return redirect()->back()
                ->with('success', 'Agreement rejected successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }
    public function deleteAgreementUnit(SalesTenantAgreement $agreement, $unitId)
    {
        try {
            $this->tenantRegistrationService->deleteAgreementUnit($agreement->id, $unitId);

            return response()->json([
                'success' => true,
                'message' => 'Unit removed from agreement.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }
    public function deleteAgreementDocumentB2c($agreementId, $docId)
    {
        try {
            $this->tenantRegistrationService->deleteAgreementDocumentB2c($agreementId, $docId);
            return response()->json(['success' => true, 'message' => 'Document deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }
}
