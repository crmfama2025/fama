<?php

namespace App\Http\Controllers;

use App\Exports\InvestmentContractsExport;
use App\Services\Investment\InvestmentContractDocumentService;
use App\Services\Investment\InvestmentService;
use Illuminate\Http\Request;
use App\Services\CompanyService;
use Maatwebsite\Excel\Facades\Excel;

class InvestmentContractsController extends Controller
{
    //
    public function __construct(
        protected InvestmentContractDocumentService $investmentContractService,
        protected InvestmentService $investmentService,
        protected CompanyService $companyService,
    ) {}

    public function index()
    {
        $title = "Contracts List";
        $companies = $this->companyService->getAll();
        return view("admin.investment.contracts", compact("title", "companies"));
    }
    public function getContracts(Request $request)
    {


        if ($request->ajax()) {
            $filters = [
                'investor_id' => $request->investorid,
                'company_id' => auth()->user()->company_id,
                'search' => $request->search['value'] ?? null,
                'status' => $request->status ?? 'all',
            ];

            return $this->investmentContractService->getDataTable($filters);
        }
    }
    public function updateContract(Request $request, $id)
    {
        // dd($request->all());
        $data = $request->all();
        try {
            $investment = $this->investmentContractService->updateDocument($data, $id);
            return response()->json(['success' => true, 'data' => $investment, 'message' => 'Document Generated successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'error'   => $e], 500);
        }
    }
    public function document($id)
    {
        $title = "Documents";
        $formData = $this->investmentContractService->documentsFormData();
        $document = $this->investmentContractService->getDetails($id);
        // dd($formData);
        return view('admin.investment.investment.investment-documents', compact('title', 'formData', 'document'));
    }
    public function documentView($id)
    {
        // $formData = $this->investmentContractService->documentsFormData();
        $document = $this->investmentContractService->getDetails($id);
        $title =  $document->agreementType?->investor_agreement_type;
        // dd($title);

        // dd($document);
        return view('admin.investment.investment.document_view', compact('title',  'document'));
    }
    public function export()
    {
        // $filters = [
        //     'investor_id' => $request->investorid,
        //     'company_id' => auth()->user()->company_id,
        // ];
        $search = request('search') ?? null;

        return Excel::download(new InvestmentContractsExport($search), 'investment_contracts.xlsx');
    }
}
