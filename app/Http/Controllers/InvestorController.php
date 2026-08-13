<?php

namespace App\Http\Controllers;

use App\Exports\InvestorExport;
use App\Exports\WithdrawalExport;
use App\Models\DocumentType;
use App\Models\Investment;
use App\Models\Investor;
use App\Models\InvestorDocument;
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
use Barryvdh\DomPDF\Facade\Pdf;

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
        $investorDocuments = null;
        $relations  = InvestorRelation::all();


        return view("admin.investment.investor-create", compact(
            "title",
            "payoutbatches",
            "nationalities",
            "documentTypes",
            "paymentModes",
            "investorsLists",
            "investor",
            "relations",
            "investorDocuments"
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
        $investorDocuments = InvestorDocument::where('investor_id', $id)->get();
        // dd($investorDocuments);


        return view("admin.investment.investor-create", compact(
            "title",
            "payoutbatches",
            "nationalities",
            "documentTypes",
            "paymentModes",
            "investorsLists",
            "investor",
            "relations",
            "investorDocuments"
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
        // dd($request->all());
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
        $title = 'Withdrawal/Settlement';
        $companies = $this->investorService->getInvestedCompanies($id);
        // dd($companies);
        $investor = $this->investorService->getById($id);

        return view("admin.investment.partial-withdrawal", compact("title", "investor", "companies"));
    }

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
        $title = "Withdrawal/Settlement List";
        // dd($title);
        return view("admin.investment.partial-withdrawals-list", compact("title"));
    }
    public function getPartialWithdrawals(Request $request)
    {
        // dd($request->all());
        // dd("test");
        if ($request->ajax()) {
            $filters = [
                'investor_id' => $request->investorid,
                'company_id' => auth()->user()->company_id,
                'search' => $request->input('search.value') ?? null,
                'status' => $request->status ?? 'all',
            ];
            // dd($filters);

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
            return response()->json(['success' => true, 'message' => 'Withdrawal updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
    public function approvePartialWithdrawal($id, Request $request)
    {
        try {
            $this->investorService->approvePartialWithdrawal($id, $request->all());
            return response()->json(['success' => true, 'message' => 'Withdrawal approved successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
    public function exportPartialWithdrawals()
    {
        $filters = [
            'search' => request('search'),
            'status' => request('status'),
            'investor_id' => request('investor_id'),
        ];

        return Excel::download(
            new WithdrawalExport(null, $filters),
            'withdrawals.xlsx'
        );
    }
    public function investmentAnnexture($id)
    {

        $title = 'Investment Annexture';
        $investor = $this->investorService->getById($id);
        $investments = $this->investorService->getCompanyTotalInvestments($id);
        return view("admin.investment.inv_agreement.investment_annexture", compact("title", "investor", "investments"));
    }

    public function downloadInvestmentAnnexure($investorId)
    {
        $investor = $this->investorService->getById($investorId);
        $investments = $this->investorService->getCompanyTotalInvestments($investorId);

        $pdf = Pdf::loadView('admin.investment.inv_agreement.investment_annexture', compact('investor', 'investments'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Investment-Annexure-' . $investor->investor_name . '.pdf');
    }

    public function deleteTermination($ledgerId)
    {

        try {

            $this->investorService->deleteTermination($ledgerId);

            return response()->json([
                'success' => true,
                'message' => 'Termination deleted successfully.'
            ]);
        } catch (\Throwable $e) {

            \Log::error('Delete termination failed', [
                'ledger_id' => $ledgerId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to delete termination.'
            ], 500);
        }
    }
}
