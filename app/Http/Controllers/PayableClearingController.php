<?php

namespace App\Http\Controllers;

use App\Exports\PayableExport;
use App\Exports\PayablePendingExport;
use App\Models\ContractPayableClear;
use App\Models\ContractPaymentDetail;
use App\Services\BankService;
use App\Services\CompanyService;
use Illuminate\Support\Facades\DB;
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
        $paymentmodes = $this->paymentModeService->getAll()->whereIn('id', ['1', '2', '3'])
            ->values();

        $vendors = getVendorsHaveContract();
        $properties = getPropertiesHaveContract();
        // $paymentmodes = getPaymentModeHaveContract();
        $companies = $this->companyService->getAll('finance', 'payable_cheque_clearing');

        return view('admin.finance.payable-cheque-clearing', compact(
            'banks',
            'vendors',
            'properties',
            'paymentmodes',
            'companies'
        ));
    }

    public function submitPayables(Request $request)
    {
        // dd($request->all());
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
                'company_id' => $request->company_id,
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

    public function exportPayablePending(Request $request)
    {

        // $filters = array(
        //     'date_from' => dateFormatChange(request('date_from'), 'Y-m-d'),
        //     'date_to' => dateFormatChange(request('date_to'), 'Y-m-d'),
        //     'vendor_id' => dateFormatChange(request('vendor_id'), 'Y-m-d'),
        //     'property_id' => dateFormatChange(request('property_id'), 'Y-m-d'),
        //     'payment_mode' => dateFormatChange(request('payment_mode'), 'Y-m-d'),
        // );

        // $search = request('search')['value'] ?? null;

        $filters = [
            'company_id'  => $request->company_id,
            'search'      => $request->search ?? null ?? null,
            'date_from'   => $request->date_from ?? null,
            'date_to'     => $request->date_to ?? null,
            'property_id' => $request->property_id ?? null,
            'vendor_id'     => $request->vendor_id ?? null,
            'mode_id' => $request->payment_mode ?? null,
        ];

        return Excel::download(new PayablePendingExport($request->search, $filters), 'payable-pending-report.xlsx');
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
    public function saveDiscount(Request $request)
    {
        $request->validate([
            'id'                    => 'required|exists:contract_payment_details,id',
            'loss_discount_date'    => 'required|date_format:d-m-Y',
            'loss_discount_amount'  => 'required|numeric|gt:0',
            'loss_discount_remarks' => 'nullable|string|max:1000',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $detail = ContractPaymentDetail::lockForUpdate()->findOrFail($request->id);

                // raw value, because the accessor returns a comma-formatted string
                $total   = (float) $detail->getRawOriginal('payment_amount');
                $already = (float) ContractPayableClear::where('contract_payment_detail_id', $detail->id)
                    ->sum('paid_amount');
                $pending = round($total - $already, 4);

                $discount = (float) $request->loss_discount_amount;

                if ($discount > $pending) {
                    throw new \Exception('Discount cannot be more than the pending amount (' . $pending . ').');
                }

                $newPending = round($pending - $discount, 4);
                $date = Carbon::createFromFormat('d-m-Y', $request->loss_discount_date)
                    ->format('Y-m-d H:i:s');

                // 1) discount info on the payment detail
                $detail->update([
                    'is_discounted'   => 1,
                    'discount_amount' => (float) $detail->getRawOriginal('discount_amount') + $discount,
                    'discount_date'   => $date,
                    'discount_reason' => $request->loss_discount_remarks,
                    'discounted_by'   => auth()->id(),
                    // replace 1 with whatever value your system uses for "fully paid"
                    'paid_status'     => $newPending <= 0 ? 1 : $detail->paid_status,
                ]);

                // 2) discount entry in the clearing table
                ContractPayableClear::create([
                    'contract_id'                => $detail->contract_id,
                    'contract_payment_detail_id' => $detail->id,
                    'paid_amount'                => $discount,
                    'pending_amount'             => $newPending,
                    'paid_date'                  => $date,
                    'paid_mode'                  => 1,
                    'paid_by'                    => auth()->id(),
                    'payment_remarks'            => $request->loss_discount_remarks,
                    'is_discount'                => 1,
                ]);
            });

            return response()->json(['message' => 'Discount saved successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
