<?php

namespace App\Http\Controllers\reports;


use App\Exports\GenericExport;
use App\Http\Controllers\Controller;
use App\Models\PayoutBatch;
use App\Repositories\Reports\InvestmentReportRepository;
use App\Services\BankService;
use App\Services\CompanyService;
use App\Services\Investment\InvestorService;
use App\Services\PaymentModeService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\Reports\InvestmentReportService;

class InvestmentReportController extends Controller
{

    //
    public function __construct(
        protected InvestmentReportService $investmentReportService,
        protected InvestmentReportRepository $investmentReportRepository,
        protected BankService $bankService,
        protected InvestorService $investorService,
        protected PaymentModeService $paymentModeService,
        protected CompanyService $companyService,

    ) {}

    public function index()
    {
        $title = "Investment Report";
        $companies = getInvsetmentCompanies();
        $investors = getInvestorsHaveInvestment();
        return view('admin.reports.investment.investment_report', compact('title', 'companies', 'investors'));
    }
    public function getInvestmentDatatable(Request $request)
    {
        // dd("test");
        // dd($request->all());

        if ($request->ajax()) {
            $filters = [
                'date_from'   => $request->date_from ?? null,
                'date_to'     => $request->date_to ?? null,
                'investor_id' => $request->investor_id,
                'company_id' => $request->company_id,
                'investment_term_type' => $request->investment_term_type ?? null,
                'investment_status'    => $request->investment_status ?? null,
                'search' => $request->search['value'] ?? null
            ];

            return $this->investmentReportService->getInvestmentDataTable($filters);
        }
    }
    public function payoutIndex()
    {
        $title = "Investor Payouts";
        $banks = $this->bankService->getAll();
        $companies = $this->companyService->getAll('finance', 'payout');
        $paymentmodes = $this->paymentModeService->getAll()->where('id', '!=', 4);
        $payoutbatches = PayoutBatch::where('status', 1)->get();
        $investors = $this->investorService->getAllActive();
        return view("admin.reports.investment.payout_report", compact("title", "paymentmodes", "banks", "payoutbatches", "investors", "companies"));
    }
    public function getPayoutDatatable(Request $request)
    {
        if ($request->ajax()) {
            $filterData = [];
            if ($request->month || $request->batch_id || $request->investor_id || $request->investment_id) {
                $filterData = array(
                    'month' => $request->month,
                    'batch_id' => $request->batch_id,
                    'investor_id' => $request->investor_id,
                    'investment_id' => $request->investment_id
                );
            }


            $filters = [
                'search' => $request->search['value'] ?? null,
                'filter' => $filterData,
            ];

            return $this->investmentReportService->getPendingList($filters);
        }
    }
    public function exportInvestments(Request $request)
    {
        $filters = [
            'date_from'            => $request->date_from ?? null,
            'date_to'              => $request->date_to ?? null,
            'investor_id'          => $request->investor_id ?? null,
            'company_id'           => $request->company_id ?? null,
            'investment_term_type' => $request->investment_term_type ?? null,
            'investment_status'    => $request->investment_status ?? null,
            'search'               => $request->search['value'] ?? null,
        ];

        $data = $this->investmentReportService->getInvestmentExportData($filters);

        return Excel::download(
            new GenericExport(
                $data,
                $this->investmentReportService->investmentExportHeadings()
            ),
            'investments.xlsx'
        );
    }
    public function exportPending(Request $request)
    {
        $filterData = [];

        if (
            $request->month ||
            $request->batch_id ||
            $request->investor_id ||
            $request->investment_id
        ) {
            $filterData = [
                'month'         => $request->month,
                'batch_id'      => $request->batch_id,
                'investor_id'   => $request->investor_id,
                'investment_id' => $request->investment_id,
            ];
        }

        $filters = [
            'search' => $request->search['value'] ?? null,
            'filter' => $filterData,
        ];

        $data = $this->investmentReportService->getPendingExportData($filters);

        return Excel::download(
            new GenericExport(
                $data,
                $this->investmentReportService->pendingExportHeadings()
            ),
            'pending-payouts.xlsx'
        );
    }
}
