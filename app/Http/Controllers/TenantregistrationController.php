<?php

namespace App\Http\Controllers;

use App\Exports\SalesTenantExport;
use App\Models\ContractType;
use App\Models\Emirate;
use App\Models\SalesTenantAgreement;
use App\Models\TenantIdentity;
use App\Models\UnitType;
use App\Repositories\Agreement\AgreementRepository;
use App\Services\Agreement\AgreementDocumentService;
use App\Services\Agreement\AgreementService;
use App\Services\Agreement\AgreementTenantService;
use App\Services\Agreement\AgreementUnitService;
use App\Services\Agreement\InvoiceService;
use App\Services\BankService;
use App\Services\CompanyService;
use App\Services\Contracts\ContractService;
use App\Services\InstallmentService;
use App\Services\NationalityService;
use App\Services\PaymentModeService;
use App\Services\Sales\TenantRegistrationService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TenantregistrationController extends Controller
{
    //
    public function __construct(

        protected TenantRegistrationService $tenantRegistrationService,
        protected ContractService $contractService,
        protected CompanyService $companyService,
        protected InstallmentService $installmentService,
        protected NationalityService $nationalityService,
        protected PaymentModeService $paymentModeService,
        protected BankService $bankService,
        protected AgreementService $agreementService,
        protected AgreementDocumentService $agreementDocumentService,
        protected AgreementUnitService $agreementUnitService,
        protected InvoiceService $invoiceService,
        protected AgreementRepository $agreementRepository,
        protected AgreementTenantService $tenantService

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
        // dd($tenant);

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
    public function export()
    {
        $search = request('search');

        return Excel::download(new SalesTenantExport($search), 'TenantDetails.xlsx');
    }
    public function destroy(SalesTenantAgreement $tenant_registration)
    {
        // dd($agreement);
        $this->tenantRegistrationService->delete($tenant_registration->id);
        return response()->json(['success' => true, 'message' => 'Agreement deleted successfully']);
    }
    public function sendForApproval($id)
    {
        $this->tenantRegistrationService->sendForApproval($id);

        return response()->json([
            'success' => true,
            'message' => 'Sent for approval successfully'
        ]);
    }
    public function makeAgreement($id)
    {
        // dd($id);
        $salesagreement = $this->tenantRegistrationService->getAgeement($id);
        // dd($salesagreement);
        if ($salesagreement->business_type == 2) {
            if ($salesagreement->agreementUnits->isNotEmpty()) {
                // Get the contract of the first unit
                $salesContract = $salesagreement->agreementUnits->first()->contract;
                $salesCompany = $salesContract->company()->first();
                // $salesUnit = $salesagreement->agreementUnits->first();
                $salesUnit = $salesagreement->agreementUnits;
                // dd($salesCompany);
            }
            $salesTenant = $salesagreement->tenant;
            // dd($salesTenant);
        }

        $salesData = 1;
        $companies = $this->companyService->getAll('agreement');
        $tenants = $this->tenantService->getTenantsForAgreement();
        // dd($tenants);
        // $contracts = $this->contractService->getAllwithUnits();
        $contracts = $this->contractService->getAllwithUnits()->map(function ($contract) {
            $contract->contract_unit->business_type_text = $contract->contract_unit->business_type();
            return $contract;
        });
        // dd($contracts);
        $installments = $this->installmentService->getAll();
        $tenantIdentities = TenantIdentity::where('show_status', true)->get();
        $unitTypes = UnitType::all();
        $paymentmodes = $this->paymentModeService->getAll();
        $installments = $this->installmentService->getAll();
        $banks = $this->bankService->getAll();
        $nationalities = $this->nationalityService->getAll();
        $contractTypes = ContractType::all();
        $emirates = Emirate::all();
        $parent_agreement_id = null;
        $renew = 0;
        return view('admin.projects.agreement.create-agreement', compact('salesTenant', 'salesCompany', 'companies', 'contracts', 'installments', 'salesagreement', 'salesContract', 'salesData', 'unitTypes', 'tenantIdentities', 'paymentmodes', 'banks', 'nationalities', 'contractTypes', 'emirates', 'tenants', 'parent_agreement_id', 'renew', 'salesUnit'));
    }
    public function makeAgreementB2B($id)
    {
        // dd($id);
        $salesagreement = $this->tenantRegistrationService->getAgeement($id);
        $contractId = request('contract_id');
        // dd($salesagreement);
        if ($salesagreement->business_type == 1) {
            if ($salesagreement->agreementUnits->isNotEmpty()) {
                // Get the contract of the first unit
                $salesContract = $salesagreement->agreementUnits->first()->contract;
                $salesCompany = $salesContract->company()->first();
                $salesUnit = $salesagreement->agreementUnits;
                // dd($salesCompany);
            }
            $salesTenant = $salesagreement->tenant;
            // dd($salesTenant);
        }

        $salesData = 1;
        $companies = $this->companyService->getAll('agreement');
        $tenants = $this->tenantService->getTenantsForAgreement();
        // dd($tenants);
        // $contracts = $this->contractService->getAllwithUnits();
        $contracts = $this->contractService->getAllwithUnits()->map(function ($contract) {
            $contract->contract_unit->business_type_text = $contract->contract_unit->business_type();
            return $contract;
        });
        // dd($contracts);
        $installments = $this->installmentService->getAll();
        $tenantIdentities = TenantIdentity::where('show_status', true)->get();
        $unitTypes = UnitType::all();
        $paymentmodes = $this->paymentModeService->getAll();
        $installments = $this->installmentService->getAll();
        $banks = $this->bankService->getAll();
        $nationalities = $this->nationalityService->getAll();
        $contractTypes = ContractType::all();
        $emirates = Emirate::all();
        $parent_agreement_id = null;
        $renew = 0;
        return view('admin.projects.agreement.create-agreement', compact('salesTenant', 'salesCompany', 'companies', 'contracts', 'installments', 'salesagreement', 'salesContract', 'salesData', 'unitTypes', 'tenantIdentities', 'paymentmodes', 'banks', 'nationalities', 'contractTypes', 'emirates', 'tenants', 'parent_agreement_id', 'renew', 'salesUnit'));
    }
}
