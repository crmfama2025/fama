<?php

namespace App\Http\Controllers;

use App\Exports\AgreementExport;
use App\Models\Agreement;
use App\Models\Bank;
use App\Models\ContractType;
use App\Models\Emirate;
use App\Models\Installment;
use App\Models\PaymentMode;
use App\Models\TenantIdentity;
use App\Models\UnitType;
use App\Repositories\Agreement\AgreementRepository;
use App\Services\Agreement\AgreementDocumentService;
use App\Services\Agreement\AgreementService;
use App\Services\Agreement\AgreementUnitService;
use App\Services\Agreement\InvoiceService;
use App\Services\BankService;
use App\Services\CompanyService;
use App\Services\Contracts\ContractService;
use App\Services\InstallmentService;
use App\Services\NationalityService;
use App\Services\PaymentModeService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use SebastianBergmann\CodeCoverage\Report\Xml\Unit;

class AgreementController extends Controller
{
    //
    public function __construct(
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
        protected AgreementRepository $agreementRepository
    ) {}
    public function index()
    {
        $title = 'Agreemants';
        $paymentmodes = $this->paymentModeService->getAll();
        $banks = $this->bankService->getAll();
        return view("admin.projects.agreement.agreement", compact("title", 'paymentmodes', 'banks'));
    }
    public function create()
    {
        $companies = $this->companyService->getAll();
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


        // dd($contractTypes);

        // dd($contracts);
        return view('admin.projects.agreement.create-agreement', compact('companies', 'contracts', 'installments', 'unitTypes', 'tenantIdentities', 'paymentmodes', 'banks', 'nationalities', 'contractTypes', 'emirates'));
    }
    public function store(Request $request)
    {
        try {
            $agreement = $this->agreementService->createOrRestore($request->all());
            return response()->json(['success' => true, 'data' => $agreement, 'message' => 'Agreeament created successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'error'   => $e], 500);
        }
    }
    public function show(Agreement $agreement)
    {
        $agreement = $this->agreementService->getDetails($agreement->id);
        // dd($agreement);
        // dd($agreement->agreement_payment_details);
        // dd($agreement->agreement_units);
        // dd($agreement->agreement_payment->agreementPaymentDetails);
        return view('admin.projects.agreement.agreement-view', compact('agreement'));
    }
    public function getAgreements(Request $request)
    {
        // dd("test");
        if ($request->ajax()) {
            $filters = [
                'company_id' => auth()->user()->company_id,
                'search' => $request->search['value'] ?? null,
                'status' => $request->status ?? 'all',
            ];
            return $this->agreementService->getDataTable($filters);
        }
    }
    public function exportAgreement(Agreement $agreement)
    {
        $search = request('search');
        $filters = auth()->user()->company_id ? [
            'company_id' => auth()->user()->company_id,
        ] : null;
        return Excel::download(new AgreementExport($search, $filters), 'agreements.xlsx');
    }
    public function edit(Agreement $agreement)
    {
        $agreement = $this->agreementService->getById($agreement->id);
        // dd($agreement);
        $agreement->contract->contract_unit->business_type_text = $agreement->contract->contract_unit->business_type();
        // dd($agreement->contract->contract_unit->business_type_text);
        // dd($agreement);
        $companies = $this->companyService->getAll();
        $contracts = $this->contractService->getAllwithUnits();
        $fullContracts = $this->contractService->fullContracts();
        $installments = $this->installmentService->getAll();
        $tenantIdentities = TenantIdentity::where('show_status', true)->get();
        $unitTypes = UnitType::all();
        $paymentmodes = $this->paymentModeService->getAll();
        $banks = $this->bankService->getAll();
        $nationalities = $this->nationalityService->getAll();
        $contractTypes = ContractType::all();
        $unitTypeList = $agreement->getVacantunitTypes();
        $vacantData = $agreement->getVacantUnits();
        $tenant = $agreement->tenant;
        $emirates = Emirate::all();
        // dd($unitTypeList);

        return view('admin.projects.agreement.create-agreement', compact(
            'agreement',
            'companies',
            'contracts',
            'installments',
            'unitTypes',
            'tenantIdentities',
            'paymentmodes',
            'banks',
            'nationalities',
            'contractTypes',
            'fullContracts',
            'unitTypeList',
            'vacantData',
            'tenant',
            'emirates'
        ));
    }
    public function update(Request $request, $id)
    {
        try {
            $agreement = $this->agreementService->update($id, $request->all());

            return response()->json(['success' => true, 'data' => $agreement, 'message' => 'Agreemant updated successfully'], 200);
        } catch (\Exception $e) {

            return response()->json(['success' => false, 'message' => $e->getMessage(), 'error'   => $e], 500);
        }
    }

    public function print_view($id)
    {
        $agreement = $this->agreementService->getDetails($id);
        $page = 1;
        return view('admin.projects.agreement.printview-agreement', compact('agreement', 'page'));
    }


    public function print($id)
    {
        $agreement = $this->agreementService->getDetails($id);
        $page = 0;
        $pdf = Pdf::loadView('admin.projects.agreement.pdf-agreement', compact('agreement', 'page'))
            ->setPaper([0, 0, 930, 1250]);
        return $pdf->stream('agreement-' . $agreement->id . '.pdf');
    }
    public function agreementDocuments($id)
    {
        $documents = $this->agreementDocumentService->getDocuments($id);
        $tenantIdentities = TenantIdentity::get();
        $agreementId = $id;
        $agreement = $this->agreementService->getDetails($id);
        return view('admin.projects.agreement.agreement_documents', compact('documents', 'tenantIdentities', 'agreementId', 'agreement'));
    }
    public function documentUpload(Request $request, $id)
    {
        $agreement = $this->agreementService->getById($request->agreement_id);
        $data['documents'] = $request->documents;
        $data['added_by'] = auth()->user()->id;
        try {
            $documents =  $this->agreementDocumentService->update(
                $agreement,
                $data['documents'] ?? [],
                $data['added_by']
            );

            return response()->json(['success' => true, 'data' =>  $documents, 'message' => 'Documents added successfully'], 200);
        } catch (\Exception $e) {

            return response()->json(['success' => false, 'message' => $e->getMessage(), 'error'   => $e], 500);
        }
    }
    public function destroy(Agreement $agreement)
    {
        $this->agreementService->delete($agreement->id);
        return response()->json(['success' => true, 'message' => 'Agreement deleted successfully']);
    }
    public function terminate(Request $request)
    {
        // dd($request->all());
        try {
            $agreement = $this->agreementService->terminate($request->all());
            return response()->json(['success' => true, 'data' => $agreement, 'message' => 'Agreeament terminated successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'error'   => $e], 500);
        }
    }
    public function invoice_upload(Request $request)
    {
        // dd($request->all());
        try {
            $upload = $this->invoiceService->upload_invoice($request->all());
            return response()->json(['success' => true, 'data' => $upload, 'message' => 'Invoice Uploaded successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'error'   => $e], 500);
        }
    }
    public function delete_unit(Request $request, $unitId)
    {
        // dd($request->all());
        $contract_id = $request->contract_id;
        // dd($contract_id);
        try {
            $response = $this->agreementUnitService->deleteUnit($unitId, $contract_id);

            return response()->json([
                'success' => $response['success'],
                'vacant_units' => $response['vacant_units'] ?? 0,
                'message' => 'Agreement Unit deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'error'   => $e], 500);
        }
    }

    public function getAgreementsExpiring()
    {
        $title = 'Agreement Pendings';

        return view("admin.projects.agreement.agreement-expiring-list", compact("title"));
    }
    public function getAgreementsExpiringTable(Request $request)
    {
        if ($request->ajax()) {
            $filters = [
                'company_id' => auth()->user()->company_id,
                'search' => $request->search['value'] ?? null,
                'end_date_from' => $request->end_date_from ?? null,
                'end_date_to' => $request->end_date_to ?? null,
            ];
            return $this->agreementService->getExpired($filters);
        }
    }
    public function renewAgreement($agreement_id)
    {
        // dd($agreement_id);
        $companies = $this->companyService->getAll();
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
        $agreement = $this->agreementService->getById($agreement_id);
        $tenant = $agreement->tenant;
        $company_id = $agreement->company_id;
        $contract = $this->contractService->getById($agreement->contract_id);
        // dd($contract);
        $renewalContractId = $contract->children[0]->id;
        $emirates = Emirate::all();
        // dd($renewalContractId);
        // $renewalContractId = 51;
        // dd($tenant);
        return view('admin.projects.agreement.create-agreement', compact('companies', 'contracts', 'installments', 'unitTypes', 'tenantIdentities', 'paymentmodes', 'banks', 'nationalities', 'contractTypes', 'tenant', 'company_id', 'renewalContractId', 'emirates'));
    }
    public function rentBifurcationStore(Request $request)
    {
        // dd($request->all());
        try {
            $bifurcation = $this->agreementService->rentBifurcationStore($request->all());

            return response()->json([
                'success' => $bifurcation['status'],
                'data' => $bifurcation,
                'message' => $bifurcation['message']
            ], $bifurcation['status'] ? 201 : 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error'   => $e->getTraceAsString()
            ], 500);
        }
    }
}
