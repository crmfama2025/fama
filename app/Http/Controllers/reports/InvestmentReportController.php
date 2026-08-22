<?php

namespace App\Http\Controllers\reports;


use App\Exports\GenericExport;
use App\Http\Controllers\Controller;
use App\Models\PayoutBatch;
use App\Models\ProfitInterval;
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
        $payoutbatches = PayoutBatch::where('status', 1)->get();
        $profitInterval = ProfitInterval::where('status', 1)->get();
        $tenures = getInvestmentTenures();
        $profitPerc = getInvestmentProfitPerc();
        // dd($tenures, $profitPerc);
        return view('admin.reports.investment.investment_report', compact('title', 'companies', 'investors', 'payoutbatches', 'profitInterval', 'tenures', 'profitPerc', 'tenures', 'profitPerc'));
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
                'search' => $request->search['value'] ?? null,
                'maturitydate_from' => $request->input('maturitydate_from'),
                'maturitydate_to' => $request->input('maturitydate_to'),
                'profit_interval_id' => $request->input('profit_interval_id'),
                'payout_batch_id' => $request->input('payout_batch_id'),
                'investment_tenure' => $request->input('investment_tenure'),
                'profit_perc' => $request->input('profit_perc'),

            ];
            // dd($filters);

            return $this->investmentReportService->getInvestmentDataTable($filters);
        }
    }
    public function payoutIndex()
    {
        $title = "Investor Payouts";
        $banks = $this->bankService->getAll();
        $paymentmodes = $this->paymentModeService->getAll()->where('id', '!=', 4);
        $payoutbatches = PayoutBatch::where('status', 1)->get();
        $companies = getInvsetmentCompanies();
        $investors = getInvestorsHaveInvestment();
        return view("admin.reports.investment.payout_report", compact("title", "paymentmodes", "banks", "payoutbatches", "investors", "companies"));
    }
    public function getPayoutDatatable(Request $request)
    {
        $filters = [
            'search' => $request->input('search.value'),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
            'company_id' => $request->input('company_id'),
            'investor_id' => $request->input('investor_id'),
            'investment_term_type' => $request->input('investment_term_type'),
            'investment_status' => $request->input('investment_status'),
            'month' => $request->input('month'),
            'payout_batch_id' => $request->input('payout_batch_id'),
            'is_processed' => $request->input('is_processed'),

        ];


        return $this->investmentReportService
            ->getPendingList($filters);
    }
    public function exportInvestments(Request $request)
    {
        // dd($request);
        $filters = [
            'search' => $request->input('search'),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
            'company_id' => $request->input('company_id'),
            'investor_id' => $request->input('investor_id'),
            'investment_term_type' => $request->input('investment_term_type'),
            'investment_status' => $request->input('investment_status'),
            'maturitydate_from' => $request->input('maturitydate_from'),
            'maturitydate_to' => $request->input('maturitydate_to'),
            'profit_interval_id' => $request->input('profit_interval_id'),
            'payout_batch_id' => $request->input('payput_batch_id'),
            'investment_tenure' => $request->input('investment_tenure'),
            'profit_perc' => $request->input('profit_perc'),
        ];
        // dd($filters);
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
        $filters = [
            'search'              => $request->input('search'),
            'date_from'           => $request->input('date_from'),
            'date_to'             => $request->input('date_to'),
            'investor_id'         => $request->input('investor_id'),
            'company_id'          => $request->input('company_id'),
            'month'               => $request->input('month'),
            'investment_term_type' => $request->input('investment_term_type'),
            'investment_status'   => $request->input('investment_status'),
            'payout_batch_id' => $request->input('payout_batch_id'),
            'is_processed' => $request->input('is_processed'),
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
