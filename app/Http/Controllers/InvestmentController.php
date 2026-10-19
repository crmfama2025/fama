<?php

namespace App\Http\Controllers;

use App\Exports\InvestmentExport;
use App\Models\Investment;
use App\Models\Investor;
use App\Models\Company;
use App\Models\InvestmentProfitRecord;
use App\Models\InvestmentProfitRecordRenewalLog;
use App\Models\InvestmentReceivedPayment;
use App\Repositories\Investment\InvestmentRepository;
use App\Services\Investment\InvestmentContractDocumentService;
use App\Services\Investment\InvestmentService;
use App\Services\Investment\InvestorService;
use App\Services\Investment\InvestorLedgerService;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class InvestmentController extends Controller
{
    //
    public function __construct(
        protected InvestmentService $investmentService,
        protected InvestmentRepository $investmentRepository,
        protected InvestmentContractDocumentService $investmentContractService,
        protected InvestorLedgerService $investorLedgerService,
        protected InvestorService $investorService,
    ) {}

    public function index()
    {
         $title = 'Investments';
        // return view("admin.investment.investment.investment", compact("title"));
         $permittedCompanyIds = getUserPermittedCompanyIds(
            auth()->id(),
            'investment'
        );

        $visibleInvestments = Investment::query()
            ->whereHas('company', function ($q) use ($permittedCompanyIds) {
                $q->whereIn('company_id', $permittedCompanyIds);
            });

        $investorIds = (clone $visibleInvestments)
            ->select('investor_id')
            ->distinct()
            ->pluck('investor_id');

        $companyIds = (clone $visibleInvestments)
            ->select('company_id')
            ->distinct()
            ->pluck('company_id');

        $investors = Investor::whereIn('id', $investorIds)
            ->orderBy('investor_name')
            ->get(['id', 'investor_name', 'investor_code']);

        $companies = Company::whereIn('id', $companyIds)
            ->orderBy('company_name')
            ->get(['id', 'company_name']);

        return view(
            'admin.investment.investment.investment',
            compact('title', 'investors', 'companies')
        );
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

        $mode = 'create';
        $paymentsCount = 0;
        $profitRecords = collect();

        // dd($data);
        return view("admin.investment.investment.create-investment-edit", compact("title", "data", 'reinvestment', 'parent_investment_id', 'paymentsCount', 'parent', 'profitRecords', 'mode'));
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

        if ($request->ajax()) {
            $filters = [
                'investor_id' => $request->input('investor_id'),
                'company_id' => $request->input('company_id'),
                'investment_term_type' => $request->input('investment_term_type'),
                'search' => $request->input('search.value'),
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



    public function edit(Request $request, $id)
    {
        $mode = $request->routeIs('investments.renew') ? 'renew' : 'edit';

        $title = 'Edit Investment';
        $investment = $this->investmentRepository->getWithDetails($id);
        $data = $this->investmentService->getFormData();
        $reinvestment = 0;
        $parent_investment_id = null;
        $paymentsCount = InvestmentReceivedPayment::where('investment_id', $id)->count();
        $profitRecords = $this->investmentService->getInvestmentProfitRecords($investment, $mode);

        // $isRenewal = $mode === 'renew';

        // dd($profitRecords);

        return view("admin.investment.investment.create-investment-edit", compact("title", "data", "investment", 'reinvestment', 'parent_investment_id', 'paymentsCount', 'profitRecords', 'mode'));
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
    // public function exportInvestment()
    // {
    //     // $filters = [
    //     //     'investor_id' => $request->investorid,
    //     //     'company_id' => auth()->user()->company_id,
    //     // ];
    //     $search = request('search') ?? null;

    //     return Excel::download(new InvestmentExport($search), 'investments.xlsx');
    // }
    public function exportInvestment(Request $request)
    {
        $filters = [
            'investor_id' => $request->input('investor_id'),
            'company_id' => $request->input('company_id'),
            'investment_term_type' => $request->input('investment_term_type'),
        ];

        $search = $request->input('search');

        return Excel::download(
            new InvestmentExport($search, $filters),
            'Investments.xlsx'
        );
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
    // public function documents($id)
    // {
    //     $title = "Documents";
    //     $formData = $this->investmentService->documentsFormData();
    //     $investment = $this->investmentService->getDetails($id);
    //     // dd($formData);
    //     return view('admin.investment.investment.investment-documents', compact('title', 'formData', 'investment'));
    // }
    public function contractsList($id)
    {
        $title = "Contracts List";

        $investment = $this->investmentService->getDetails($id);
        return view('admin.investment.investment.documents_list', compact('title', 'investment'));
    }
    public function getContracts(Request $request)
    {
        // dd("test");
        // dd($request->all());

        if ($request->ajax()) {
            $filters = [
                'investor_id' => $request->investorid,
                // 'company_id' => auth()->user()->company_id,
                'investment_id' => $request->investment_id,
                'search' => $request->search['value'] ?? null,
                'status' => $request->status ?? 'all',
            ];

            return $this->investmentContractService->getDataTable($filters);
        }
    }
    public function ledgerList($id)
    {
        $title = "Ledger List";
        $investment = $this->investmentService->getDetails($id);
        // dd($investment);
        return view('admin.investment.investment.ledger', compact('title', 'investment'));
    }
    public function getLedgerData(Request $request)
    {
        // dd("test");
        // dd($request->all());

        if ($request->ajax()) {
            $filters = [
                'investor_id' => $request->investorid,
                'company_id' => auth()->user()->company_id,
                'investment_id' => $request->investment_id,
                'search' => $request->search['value'] ?? null
            ];

            return $this->investorLedgerService->getDataTable($filters);
        }
    }
    // InvestmentController.php
    public function destroyProfitRecord($id)
    {
        // dd($id);
        $record = InvestmentProfitRecord::findOrFail($id);
        $record->delete();

        return response()->json([
            'message' => 'Profit record deleted successfully.'
        ]);
    }

    public function shortTermTermination(Request $request)
    {
        try {
            $terminate = $this->investmentService->shortTermTermination($request->all());

            return response()->json(['success' => true, 'data' =>  $terminate, 'message' => 'Investment terminated successfully'], 200);
        } catch (\Exception $e) {

            return response()->json(['success' => false, 'message' => $e->getMessage(), 'error'   => $e], 500);
        }
    }
    // profitschedule updates
    public function editProfitSchedule(int $investmentId)
    {
        $title = 'Edit Profit Recpords';
        $investment = Investment::query()
            ->activeLongTerm()
            ->findOrFail($investmentId);

        $profitRecords = $investment->profitRecords()
            ->editable()
            ->orderBy('profit_release_month')
            ->get();

        return view('admin.investment.profit-schedule-edit', [
            'title' => $title,
            'investment' => $investment,
            'profitRecords' => $profitRecords,
        ]);
    }

    public function updateProfitSchedule(Request $request, int $investmentId)
    {
        $investment = Investment::query()
            ->activeLongTerm()
            ->findOrFail($investmentId);

        $maturityDate = Carbon::parse($investment->maturity_date)->toDateString();

        $validated = $request->validate([
            'profit_records' => ['required', 'array', 'min:1',],
            'profit_records.*.id' => ['required', 'integer', 'distinct',],
            'profit_records.*.profit_release_month' => ['required', 'date_format:Y-m-d', 'after_or_equal:today', "before_or_equal:{$maturityDate}", 'distinct',],
            'profit_records.*.profit_amount' => ['required', 'numeric', 'min:0',],
        ]);

        $this->investmentService->updateProfitSchedule($investmentId, $validated['profit_records']);

        return redirect()
            ->route('investment.show', $investment->id)
            ->with(
                'success',
                'Profit schedule updated successfully.'
            );
    }

    public function investmentRenewal(Request $request)
    {
        $title = 'Investment Renewal';

        return view("admin.investment.investment.investment-renewal-list", compact("title"));
    }

    public function getRenewalList(Request $request)
    {
        if ($request->ajax()) {
            $filters = [
                // 'company_id' => auth()->user()->company_id,
                'search' => $request->search['value'] ?? null
            ];
            return $this->investmentService->getRenewalDataTable($filters);
        }
    }
    public function uploadContracts(request $request, $id)
    {
        // dd($id);
        try {
            $upload = $this->investmentService->uploadContracts($request->all(), $id);

            return response()->json(['success' => true, 'data' =>  $upload, 'message' => 'Documents Uploaded successfully'], 200);
        } catch (\Exception $e) {

            return response()->json(['success' => false, 'message' => $e->getMessage(), 'error'   => $e], 500);
        }
    }
    public function deleteDocument($id)
    {
        try {
            $upload = $this->investmentService->deleteContracts($id);

            return response()->json(['success' => true, 'data' =>  $upload, 'message' => 'Documents Deleted successfully'], 200);
        } catch (\Exception $e) {

            return response()->json(['success' => false, 'message' => $e->getMessage(), 'error'   => $e], 500);
        }
    }
    public function getInvestmentBreakdown(Request $request)
    {
        $validated = $request->validate([
            'investor_id' => ['required', 'integer', 'min:1'],
        ]);

        $investments = $this->investorService
            ->getCompanyTotalInvestments(
                $validated['investor_id'],
                null
            );

        return response()->json($investments);
    }
}
