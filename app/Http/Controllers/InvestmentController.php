<?php

namespace App\Http\Controllers;

use App\Exports\InvestmentExport;
use App\Models\Investment;
use App\Models\InvestmentReceivedPayment;
use App\Repositories\Investment\InvestmentRepository;
use App\Services\Investment\InvestmentContractDocumentService;
use App\Services\Investment\InvestmentService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class InvestmentController extends Controller
{
    //
    public function __construct(
        protected InvestmentService $investmentService,
        protected InvestmentRepository $investmentRepository,
        protected InvestmentContractDocumentService $investmentContractService,
    ) {}

    public function index()
    {
        $title = 'Investments';
        return view("admin.investment.investment.investment", compact("title"));
    }

    public function create(Request $request)
    {
        $title = 'Create Investment';
        $data = $this->investmentService->getFormData();
        $reinvestment = $request->query('reinvestment') ?? 0;
        $parent_investment_id = $request->query('parent_id') ?? null;
        $parent = array(
            'investor_id' => $request->query('investor_id'),
            'amount' => $request->query('amount'),
            'date' => $request->query('date'),
        );
        $paymentsCount = 0;

        // dd($data);
        return view("admin.investment.investment.create-investment-edit", compact("title", "data", 'reinvestment', 'parent_investment_id', 'paymentsCount', 'parent'));
    }

    public function store(Request $request)
    {
        // dd($request);
        try {
            $investment = $this->investmentService->createOrRestore($request->all());
            return response()->json(['success' => true, 'data' => $investment, 'message' => 'Investment created successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'error'   => $e], 500);
        }
    }

    public function getInvestments(Request $request)
    {
        // dd("test");
        // dd($request->all());

        if ($request->ajax()) {
            $filters = [
                'investor_id' => $request->investorid,
                'company_id' => auth()->user()->company_id,
                'search' => $request->search['value'] ?? null
            ];

            return $this->investmentService->getDataTable($filters);
        }
    }

    public function addpendingInvestment(Request $request)
    {
        try {
            $payment = $this->investmentService->submitPending($request->all());
            return response()->json(['success' => true, 'data' => $payment, 'message' => 'Investment submitted successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'error'   => $e], 422);
        }
    }

    public function edit($id)
    {
        $title = 'Edit Investment';
        $investment = $this->investmentRepository->getWithDetails($id);
        $data = $this->investmentService->getFormData();
        $reinvestment = 0;
        $parent_investment_id = null;
        $paymentsCount = InvestmentReceivedPayment::where('investment_id', $id)->count();

        return view("admin.investment.investment.create-investment-edit", compact("title", "data", "investment", 'reinvestment', 'parent_investment_id', 'paymentsCount'));
    }
    public function update(Request $request, $id)
    {
        // dd($request->all());
        try {
            $investment = $this->investmentService->update($id, $request->all());

            return response()->json(['success' => true, 'data' => $investment, 'message' => 'Investment updated successfully'], 200);
        } catch (\Exception $e) {

            return response()->json(['success' => false, 'message' => $e->getMessage(), 'error'   => $e], 500);
        }
    }
    public function exportInvestment()
    {
        // $filters = [
        //     'investor_id' => $request->investorid,
        //     'company_id' => auth()->user()->company_id,
        // ];
        $search = request('search') ?? null;

        return Excel::download(new InvestmentExport($search), 'investments.xlsx');
    }
    public function show(Investment $investment)
    {
        $investment = $this->investmentService->getDetails($investment->id);
        // dd($investment);
        return view('admin.investment.investment.view-investment', compact('investment'));
    }
    public function destroy(Investment $investment)
    {
        $this->investmentService->delete($investment->id);
        return response()->json(['success' => true, 'message' => 'Investment deleted successfully']);
    }
    public function updatePendingInvestment(Request $request)
    {
        try {
            $payment = $this->investmentService->updatePending($request->all());
            return response()->json(['success' => true, 'data' => $payment, 'message' => 'Pending Investment updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'error'   => $e], 422);
        }
    }
    public function terminateRequestSubmit(Request $request)
    {
        // dd($request);
        try {
            $termination = $this->investmentService->terminateRequest($request->all());
            return response()->json(['success' => true, 'data' => $termination, 'message' => 'Termination request submittedsuccessfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'error'   => $e], 422);
        }
    }
    public function documents($id)
    {
        $title = "Documents";
        $formData = $this->investmentService->documentsFormData();
        $investment = $this->investmentService->getDetails($id);
        // dd($formData);
        return view('admin.investment.investment.investment-documents', compact('title', 'formData', 'investment'));
    }
    public function contractsList($id)
    {
        $title = "Contracts List";
        // dd("test");
        // $formData = $this->investmentService->documentsFormData();
        $investment = $this->investmentService->getDetails($id);
        // dd($formData);
        return view('admin.investment.investment.documents_list', compact('title', 'investment'));
    }
    public function getContracts(Request $request)
    {
        // dd("test");
        // dd($request->all());

        if ($request->ajax()) {
            $filters = [
                'investor_id' => $request->investorid,
                'company_id' => auth()->user()->company_id,
                'search' => $request->search['value'] ?? null
            ];

            return $this->investmentContractService->getDataTable($filters);
        }
    }
}
