<?php

namespace App\Http\Controllers;

use App\Exports\TenantExport;
use App\Models\AgreementTenant;
use App\Services\Agreement\AgreementDocumentService;
use App\Services\Agreement\AgreementTenantService;
use App\Services\NationalityService;
use App\Services\PaymentModeService;
use App\Services\TenantChequeService;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TenantController extends Controller
{
    //
    public function __construct(

        protected AgreementTenantService $tenantService,
        protected PaymentModeService $paymentModeService,

    ) {}
    public function index()
    {
        $title = "Tenants";
        return view("admin.master.tenants.tenant", compact('title'));
    }
    public function create()
    {
        $title = "Tenants";
        $formData = $this->tenantService->gerFormData();
        // dd($formData);
        return view("admin.master.tenants.create", compact('title', 'formData'));
    }
    public function store(Request $request)
    {
        $data = $request->all();
        // dd($data);
        $this->tenantService->createdata($data);
        return redirect()->route('tenant.index')->with('success', 'Tenant created successfully.');
    }
    public function list(Request $request)
    {
        if ($request->ajax()) {
            $filters = [
                'company_id' => auth()->user()->company_id,
                'search' => $request->search['value'] ?? null,
                'status' => $request->status ?? 'all',
            ];
            return $this->tenantService->getDataTable($filters);
        }
    }
    public function edit($id)
    {
        $tenant = $this->tenantService->getDetails($id);

        // Prepare owners data by document type (skip Trade License type 3)
        $owners = [];
        // dd($tenant->tenantDocuments);

        foreach ($tenant->tenantDocuments as $doc) {
            if ($doc->document_type == 3) continue; // skip trade license

            // Assume $doc->owner_id or some owner reference exists, otherwise just index
            $ownerIndex = $doc->owner_index ?? 1; // fallback to 1 if not stored

            $owners[$ownerIndex][$doc->document_type] = [
                'number' => $doc->document_number,
                'file'   => $doc->document_path ? asset('storage/' . $doc->document_path) : null,
                'issued' => $doc->issued_date,
                'expiry' => $doc->expiry_date,
                'type'  => $doc->document_type,
                'id'     => $doc->id,
            ];
        }
        // dd($owners);

        $title = "Tenants";
        $formData = $this->tenantService->gerFormData();
        return view('admin.master.tenants.create', compact('title', 'tenant', 'formData', 'owners'));
    }
    public function update(Request $request, $id)
    {
        $data = $request->all();
        // dd($data);
        $this->tenantService->updateData($id, $data);
        return redirect()->route('tenant.index')->with('success', 'Tenant updated successfully.');
    }
    public function removeOwnerDocuments(Request $request)
    {
        $documentIds = $request->input('document_ids', []);
        $tenantId = $request->input('tenant_id');
        if (empty($documentIds)) {
            return response()->json(['message' => 'No document IDs provided.'], 400);
        }

        try {
            $this->tenantService->removeOwnerDocuments($documentIds, $tenantId);
            return response()->json(['message' => 'Owner documents removed successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error removing documents: ' . $e->getMessage()], 500);
        }
    }
    public function show(AgreementTenant $tenant)
    {
        $tenant = $this->tenantService->getDetails($tenant->id);
        return view('admin.master.tenants.view', compact('tenant'));
    }
    public function destroy(AgreementTenant $tenant)
    {
        try {
            $this->tenantService->delete($tenant->id);
            return response()->json(['success' => true, 'message' => 'Tenant deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting tenant: ' . $e->getMessage()]);
        }
    }
    public function export()
    {
        $search = request('search');

        return Excel::download(new TenantExport($search), 'tenants.xlsx');
    }
}
