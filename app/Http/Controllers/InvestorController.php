<?php

namespace App\Http\Controllers;

use App\Exports\InvestorExport;
use App\Models\DocumentType;
use App\Models\Investment;
use App\Models\Investor;
use App\Models\InvestorRelation;
use App\Models\PaymentMode;
use App\Models\PayoutBatch;
use App\Repositories\Investment\InvestorLedgerRepository;
use App\Services\Investment\InvestorBankService;
use App\Services\Investment\InvestorDocumentService;
use App\Services\Investment\InvestorService;
use App\Services\NationalityService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class InvestorController extends Controller
{
    public function __construct(
        protected InvestorService $investorService,
        protected InvestorBankService $investorBankSer,
        protected NationalityService $nationalityService,
        protected InvestorDocumentService $investorDocSer,
        protected InvestorLedgerRepository $investorLedgerRepo
    ) {}

    public function index()
    {
        $title = 'Investor';
        return view("admin.investment.investor", compact("title"));
    }

    public function create()
    {
        $title = 'Add Investor';
        $payoutbatches = PayoutBatch::where('status', 1)->get();
        $documentTypes = DocumentType::where('status', 2)->get();
        $nationalities = $this->nationalityService->getAll();
        $paymentModes = PaymentMode::all();
        $investorsLists = $this->investorService->getAllActive();
        $investor = null;
        $relations  = InvestorRelation::all();


        return view("admin.investment.investor-create", compact(
            "title",
            "payoutbatches",
            "nationalities",
            "documentTypes",
            "paymentModes",
            "investorsLists",
            "investor",
            "relations"
        ));
    }

    public function edit($id)
    {
        $title = 'Edit Investor';
        $payoutbatches = PayoutBatch::where('status', 1)->get();
        $documentTypes = DocumentType::where('status', 2)->get();
        $nationalities = $this->nationalityService->getAll();
        $paymentModes = PaymentMode::all();
        $investor = $this->investorService->getById($id);
        $investorsLists = $this->investorService->getAllActive();
        $relations  = InvestorRelation::all();


        return view("admin.investment.investor-create", compact(
            "title",
            "payoutbatches",
            "nationalities",
            "documentTypes",
            "paymentModes",
            "investorsLists",
            "investor",
            "relations"
        ));
    }

    public function store(Request $request)
    {
        try {
            if (!empty($request->id)) {
                $investor = $this->investorService->update($request->id, $request->all());
                return response()->json(['success' => true, 'data' => $investor, 'message' => 'Investor updated successfully'], 200);
            } else {
                $investor = $this->investorService->create($request->all());

                return response()->json(['success' => true, 'data' => $investor, 'message' => 'Investor created successfully'], 201);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'error'   => $e], 500);
        }
    }

    public function getInvestors(Request $request)
    {
        if ($request->ajax()) {
            $filters = [
                'search' => $request->search['value'] ?? null
            ];
            return $this->investorService->getDataTable($filters);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $investor = $this->investorService->update($id, $request->all());

            return response()->json(['success' => true, 'data' => $investor, 'message' => 'Investor updated successfully'], 200);
        } catch (\Exception $e) {

            return response()->json(['success' => false, 'message' => $e->getMessage(), 'error'   => $e], 500);
        }
    }

    public function show($id)
    {
        $title = 'Investor Details';

        $investorBanks = $this->investorBankSer->getByInvestor(['investor_id' => $id]);
        $investorDocuments = $this->investorDocSer->getByInvestor(['investor_id' => $id]);
        $investor = $this->investorService->getById($id);

        return view("admin.investment.view-investor", compact("title", "investorBanks", "investor", "investorDocuments"));
    }

    public function addorUpdateInvestorBank(Request $request)
    {
        try {

            if ($request->investor_bank_id) {
                $investor = $this->investorBankSer->update($request->investor_bank_id, $request->all());
                $msg = 'Investor bank updated successfully';
            } else {
                $investor = $this->investorBankSer->create($request->all(), $request->investor_id);
                $msg = 'Investor bank created successfully';
            }

            return response()->json(['success' => true, 'data' => $investor, 'message' => $msg], 200);
        } catch (\Exception $e) {

            return response()->json(['success' => false, 'message' => $e->getMessage(), 'error'   => $e], 500);
        }
    }

    public function getInvestorBankDetails($id)
    {
        $InvBank = $this->investorBankSer->getById($id);
        return response()->json($InvBank);
    }

    public function exportInvestors()
    {
        $search = request('search');

        return Excel::download(
            new InvestorExport($search),
            'investors.xlsx'
        );
    }

    public function destroy(Investor $investor)
    {
        $this->investorService->delete($investor->id);
        return response()->json(['success' => true, 'message' => 'Investor deleted successfully']);
    }
    public function partialWithdrawal($id)
    {
        $title = 'Partial Withdrawal';
        $companies = $this->investorService->getInvestedCompanies($id);
        // dd($companies);
        $investor = $this->investorService->getById($id);

        return view("admin.investment.partial-withdrawal", compact("title", "investor", "companies"));
    }
    // public function partialWithdrawal($id)
    // {
    //     $title = 'Partial Withdrawal';
    //     $companies = $this->investorService->getInvestedCompanies($id);
    //     // dd($companies);
    //     $investor = $this->investorService->getById($id);

    //     return view("admin.investment.partial_withdrawal", compact("title", "investor", "companies"));
    // }
    // public function getInvestorLedger(Request $request, $investorId)
    // {
    //     // dd("test");
    //     if ($request->ajax()) {
    //         $filters = [
    //             'search' => $request->search['value'] ?? null,
    //             'investor_id' => $investorId,
    //             'company_id' => $request->company_id ?? null,
    //         ];
    //         return $this->investorService->getInvestorLedger($filters);
    //     }
    // }
    public function getCompanyInvestments($investorId, $companyId, Request $request)
    {
        // $investments = $this->investorService->getCompanyInvestments($investorId, $companyId);
        // // dd($investments);

        // return response()->json($investments);
        $editLedgerId = $request->query('edit_ledger_id');

        $investments = $this->investorService->getCompanyInvestments($investorId, $companyId, $editLedgerId);

        return response()->json($investments);
    }
    public function partialWithdrawalSubmit(Request $request, $id)
    {
        // dd($request->all());
        try {
            $withdrawal = $this->investorService->partialWithdrawal($id, $request->all());

            return response()->json(['success' => true, 'data' => $withdrawal, 'message' => 'Partial Withdrawal submitted successfully'], 200);
        } catch (\Exception $e) {

            return response()->json(['success' => false, 'message' => $e->getMessage(), 'error'   => $e], 500);
        }
    }
    public function partialWithdrawallist()
    {
        // dd("test");
        $title = "Partial Withdrawal List";
        // dd($title);
        return view("admin.investment.partial-withdrawals-list", compact("title"));
    }
    public function getPartialWithdrawals(Request $request)
    {
        // dd("test");
        if ($request->ajax()) {
            $filters = [
                'investor_id' => $request->investorid,
                'company_id' => auth()->user()->company_id,
                'investment_id' => $request->investment_id,
                'search' => $request->search['value'] ?? null
            ];

            return $this->investorService->getPartialWithdrawals($filters);
        }
    }
    public function editPartialWithdrawal($id)
    {
        $data = $this->investorService->getPartialWithdrawalForEdit($id);

        $companies = $this->investorService->getInvestedCompanies($data['investor']['id']);
        // dd($companies);
        // dd($data);

        if (!$data) {
            abort(404);
        }

        return view('admin.investment.partial-withdrawal-edit', compact('data', 'companies'));
    }
    public function updatePartialWithdrawal($id, Request $request)
    {
        try {
            $this->investorService->updatePartialWithdrawal($id, $request->all());
            return response()->json(['success' => true, 'message' => 'Partial withdrawal updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
