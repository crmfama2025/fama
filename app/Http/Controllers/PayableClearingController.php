<?php

namespace App\Http\Controllers;

use App\Exports\PayableExport;
use App\Exports\PayablePendingExport;
use App\Models\ContractPayableClear;
use App\Models\ContractPaymentDetail;
use App\Services\BankService;
use App\Services\CompanyService;
use App\Services\PayableClearingService;
use App\Services\PaymentModeService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PayableClearingController extends Controller
{
    public function __construct(
        protected PaymentModeService $paymentModeService,
        protected BankService $bankService,
        protected PayableClearingService $payableClear,
        protected CompanyService $companyService,
    ) {}

    public function payableChequeClearing()
    {
        $title = 'Payable Cheque Clearing';
        $banks = $this->bankService->getAll();
        $paymentmodes = $this->paymentModeService->getAll()->whereIn('id', ['2', '3'])
            ->values();

        $vendors = getVendorsHaveContract();
        $properties = getPropertiesHaveContract();
        // $paymentmodes = getPaymentModeHaveContract();
        $companies = $this->companyService->getAll('finance', 'payable_cheque_clearing');

        return view('admin.finance.payable-cheque-clearing', compact(
            'paymentmodes',
            'banks',
            'vendors',
            'properties',
            'paymentmodes',
            'companies'
        ));
    }

    public function submitPayables(Request $request)
    {
        try {
            $paidDet = $this->payableClear->PayableSave($request->all());

            return response()->json(['success' => true, 'data' => $paidDet, 'message' => 'Payables cleared successfully'], 200);
        } catch (\Exception $e) {

            return response()->json(['success' => false, 'message' => $e->getMessage(), 'error'   => $e], 500);
        }
    }

    public function getPayables(Request $request)
    {

        if ($request->ajax()) {
            $filterData = array(
                'date_from' => dateFormatChange($request->date_from, 'Y-m-d'),
                'date_to' => dateFormatChange($request->date_to, 'Y-m-d'),
                'vendor_id' => $request->vendor_id,
                'property_id' => $request->property_id,
                'payment_mode' => $request->payment_mode,
            );

            $filters = [
                'search' => $request->search['value'] ?? null,
                'filter' => $filterData,
            ];

            return $this->payableClear->getDataTable($filters);
        }
    }

    public function submitReturns(Request $request)
    {
        try {
            $paidDet = $this->payableClear->ReturnSave($request->all());

            return response()->json(['success' => true, 'data' => $paidDet, 'message' => 'Returns cleared successfully'], 200);
        } catch (\Exception $e) {

            return response()->json(['success' => false, 'message' => $e->getMessage(), 'error'   => $e], 500);
        }
    }

    public function crearedList()
    {
        $title = 'Payable Cleared';
        $companies = $this->companyService->getAll();

        return view('admin.finance.payable-cleared-report', compact(
            'companies'
        ));
    }

    public function getClearedData(Request $request)
    {
        if ($request->ajax()) {
            $filterData = array(
                'date_from' => dateFormatChange($request->date_from, 'Y-m-d'),
                'date_to' => dateFormatChange($request->date_to, 'Y-m-d'),
            );

            $filters = [
                'search' => $request->search['value'] ?? null,
                'filter' => $filterData,
            ];

            return $this->payableClear->getClearedList($filters);
        }
    }

    public function exportPayablePending(ContractPaymentDetail $detail)
    {
        $filters = array(
            'date_from' => dateFormatChange(request('date_from'), 'Y-m-d'),
            'date_to' => dateFormatChange(request('date_to'), 'Y-m-d'),
            'vendor_id' => dateFormatChange(request('vendor_id'), 'Y-m-d'),
            'property_id' => dateFormatChange(request('property_id'), 'Y-m-d'),
            'payment_mode' => dateFormatChange(request('payment_mode'), 'Y-m-d'),
        );

        $search = request('search')['value'] ?? null;

        return Excel::download(new PayablePendingExport($search, $filters), 'payable-pending-report.xlsx');
    }

    public function exportPayables(ContractPayableClear $payable)
    {
        $filters = array(
            'date_from' => dateFormatChange(request('date_from'), 'Y-m-d'),
            'date_to' => dateFormatChange(request('date_to'), 'Y-m-d'),
        );

        $search = request('search') ?? null;

        return Excel::download(new PayableExport($search, $filters), 'payable-paid-report.xlsx');
    }
}
