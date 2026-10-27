<?php

namespace App\Http\Controllers;

use App\Exports\DistributePendingExport;
use App\Exports\DistributeReportExport;
use App\Models\InvestorPaymentDistribution;
use App\Models\PayoutBatch;
use App\Services\BankService;
use App\Services\CompanyService;
use App\Models\Company;
use App\Services\Investment\InvestorPaymentDistributionService;
use App\Services\Investment\InvestorService;
use App\Services\PaymentModeService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class InvestorPaymentDistributionController extends Controller
{

    public function __construct(
        protected InvestorPaymentDistributionService $investorDistrService,
        protected PaymentModeService $paymentModeService,
        protected BankService $bankService,
        protected InvestorService $investorService,
        protected CompanyService $companyService,
    ) {}

    public function index()
    {
        $title = 'Investor Payout';

        $banks = $this->bankService->getAll();
        $companies = $this->companyService->getAll('finance', 'payout');
        $investedCompanies = Company::whereHas('investments')
                    ->select('id', 'company_name')
                    ->orderBy('company_name')
                    ->get();
        $paymentmodes = $this->paymentModeService->getAll()->where('id', '!=', 4);
        $payoutbatches = PayoutBatch::where('status', 1)->get();
        $investors = $this->investorService->getAllActive();

        return view("admin.investment.investor-payment-distribution", compact("title", "paymentmodes", "banks", "payoutbatches", "investors", "companies", "investedCompanies"));
    }

    // public function getPayouts(Request $request)
    // {

    //     if ($request->ajax()) {
    //         $filterData = [];
    //         if ($request->month || $request->batch_id || $request->investor_id || $request->investment_id || $request->company_id) {
    //             $filterData = array(
    //                 'month' => $request->month,
    //                 'batch_id' => $request->batch_id,
    //                 'investor_id' => $request->investor_id,
    //                 'investment_id' => $request->investment_id,
    //                 'company_id' => $request->company_id
    //             );
    //         }


    //         $filters = [
    //             'search' => $request->search['value'] ?? null,
    //             'filter' => $filterData,
    //         ];

    //         return $this->investorDistrService->getPendingList($filters);
    //     }
    // }
    
    public function getPayouts(Request $request)
    {
        if (!$request->ajax()) {
            abort(404);
        }

        $filterData = array_filter([
            'month'        => $request->input('month'),
            'batch_id'     => $request->input('batch_id'),
            'investor_id'  => $request->input('investor_id'),
            'investment_id' => $request->input('investment_id'),
            'company_id'   => $request->input('company_id'),
        ], function ($value) {
            return $value !== null && $value !== '';
        });

        $filters = [
            'search' => $request->input('search.value'),
            'filter' => $filterData,
        ];

        return $this->investorDistrService->getPendingList($filters);
    }
    public function getSelectAllPayouts(Request $request)
    {
        if (!$request->ajax()) {
            abort(404);
        }

        $filterData = array_filter([
            'month'       => $request->input('month'),
            'batch_id'    => $request->input('batch_id'),
            'investor_id' => $request->input('investor_id'),
            'company_id'  => $request->input('company_id'),
        ], fn ($v) => $v !== null && $v !== '');

        return response()->json(
            $this->investorDistrService->getPendingSelection(['filter' => $filterData])
        );
    }


    public function savePayouts(Request $request)
    {
        try {
            $paidDet = $this->investorDistrService->savePayout($request->all());

            return response()->json(['success' => true, 'data' => $paidDet, 'message' => 'Pay outs saved successfully'], 200);
        } catch (\Exception $e) {

            return response()->json(['success' => false, 'message' => $e->getMessage(), 'error'   => $e], 500);
        }
    }

    public function distributedReport()
    {
        $title = 'Investor Payout Report';

        $banks = $this->bankService->getAll();
        $paymentmodes = $this->paymentModeService->getAll()->where('id', '!=', 4);
        $payoutbatches = PayoutBatch::where('status', 1)->get();
        $investors = $this->investorService->getAllActive();

        return view("admin.investment.investor-payout-distribution-report", compact("title", "paymentmodes", "banks", "payoutbatches", "investors"));
    }

    public function getDistributedList(Request $request)
    {

        if ($request->ajax()) {
            $filterData = [];
            if ($request->date_From || $request->date_To) {
                $filterData = array(
                    'date_From' => dateFormatChange($request->date_From, 'Y-m-d'),
                    'date_To' => dateFormatChange($request->date_To, 'Y-m-d'),
                    'investment_id' => $request->investment_id
                );
            }


            $filters = [
                'search' => $request->search['value'] ?? null,
                'filter' => $filterData,
            ];

            return $this->investorDistrService->getDistributedList($filters);
        }
    }

    // public function exportPayoutPending()
    // {
    //     $filters = [];

    //     if (request('month') || request('batch_id') || request('investor_id') || request('investment_id')) {
    //         $filters = array(
    //             'month' => request('month'),
    //             'batch_id' => request('batch_id'),
    //             'investor_id' => request('investor_id'),
    //             'investment_id' => request('investment_id')
    //         );
    //     }

    //     $search = request('search') ?? null;

    //     return Excel::download(new DistributePendingExport($search, $filters), 'payout-pending-report.xlsx');
    // }
    
    // public function exportPayoutPending()
    // {
    //     $filters = array_filter([
    //         'month'         => request('month'),
    //         'batch_id'      => request('batch_id'),
    //         'investor_id'   => request('investor_id'),
    //         'investment_id' => request('investment_id'),
    //         'company_id'    => request('company_id'),
    //     ], function ($value) {
    //         return $value !== null && $value !== '';
    //     });

    //     $searchInput = request('search');

    //     $search = is_array($searchInput)
    //         ? ($searchInput['value'] ?? '')
    //         : $searchInput;

    //     return Excel::download(new DistributePendingExport($search, $filters),'payout-pending-report.xlsx');
    // }
    public function exportPayoutPending()
    {
        $filters = array_filter([
            'month'         => request('month'),
            'batch_id'      => request('batch_id'),
            'investor_id'   => request('investor_id'),
            'investment_id' => request('investment_id'),
            'company_id'    => request('company_id'),
        ], fn ($value) => $value !== null && $value !== '');

        $searchInput = request('search');

        $search = is_array($searchInput)
            ? ($searchInput['value'] ?? '')
            : $searchInput;

        // Current DataTable sort (order[0][column], order[0][dir])
        $order = request('order.0', []);

        return Excel::download(
            new DistributePendingExport($search, $filters, [
                'column' => $order['column'] ?? 4,
                'dir'    => $order['dir'] ?? 'asc',
            ]),
            'payout-pending-report.xlsx'
        );
    }


    public function exportDistribute()
    {
        $filters = [];
        if (request('date_From') || request('date_To')) {
            $filters = array(
                'date_From' => dateFormatChange(request('date_From'), 'Y-m-d'),
                'date_To' => dateFormatChange(request('date_To'), 'Y-m-d'),
                'investment_id' => request('investment_id')
            );
        }

        $search = request('search') ?? null;

        return Excel::download(new DistributeReportExport($search, $filters), 'payout-paid-report.xlsx');
    }
}
