<?php

namespace App\Http\Controllers;

use App\Exports\InvestmentContractsExport;
use App\Models\Investment;
use App\Models\InvestmentContractDocuments;
use App\Services\Investment\InvestmentContractDocumentService;
use App\Services\Investment\InvestmentService;
use Illuminate\Http\Request;
use App\Services\CompanyService;
use App\Services\Investment\InvestorAgreementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class InvestmentContractsController extends Controller
{
    //
    public function __construct(
        protected InvestmentContractDocumentService $investmentContractService,
        protected InvestmentService $investmentService,
        protected CompanyService $companyService,
        protected InvestorAgreementService $InvestorAgreementService,
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
                // 'company_id' => auth()->user()->company_id,
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

    public function viewSignedPdf(InvestmentContractDocuments $contract)
    {
        abort_if(
            blank($contract->signed_pdf_path),
            404,
            'Signed PDF has not been generated.'
        );

        $disk = Storage::disk('public');
        $pdfPath = $contract->signed_pdf_path;

        abort_unless(
            $disk->exists($pdfPath),
            404,
            'Signed PDF file was not found.'
        );

        return response()->file(
            $disk->path($pdfPath),
            [
                'Content-Type' => 'application/pdf',

                /*
                * "inline" tells the browser to display the PDF
                * instead of immediately downloading it.
                */
                'Content-Disposition' =>
                'inline; filename="' .
                    basename($pdfPath) .
                    '"',
            ]
        );
    }

    public function novationInvestments(int $investorId): JsonResponse
    {
        $investments = Investment::query()
            ->activeLongTerm()
            ->where('investor_id', $investorId)
            ->with('company:id,company_name')
            ->orderByDesc('id')
            ->get(['id', 'investor_id', 'company_id', 'investment_date', 'investment_amount', 'investment_code'])
            ->map(function ($investment) {
                return [
                    'id' => $investment->id,
                    'company_id' => $investment->company_id,
                    'investment_code' => $investment->investment_code,
                    'investment_date' => $investment->investment_date,
                    'investment_amount' => $investment->investment_amount,
                    'company_name' => optional($investment->company)->company_name,
                ];
            });

        return response()->json([
            'status' => true,
            'data' => $investments,
        ]);
    }

    public function applyNovation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'investor_id' => ['required', 'integer', 'exists:investors,id'],
            'investment_ids' => ['required', 'array', 'min:1'],
            'investment_ids.*' => ['required', 'integer', 'distinct', 'exists:investments,id'],
        ]);

        $this->InvestorAgreementService->novationOfSelectedInvestorInvestments(
            (int) $validated['investor_id'],
            $validated['investment_ids']
        );

        return response()->json([
            'status' => true,
            'message' => 'Novation applied to the selected investments.',
        ]);
    }
}
