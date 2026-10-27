<?php

namespace App\Http\Controllers;

use App\Exports\RecevableClearingExport;
use App\Models\AgreementPaymentDetail;
use App\Models\AgreementTenant;
use App\Models\Bank;
use App\Models\Contract;
use App\Models\PaymentMode;
use App\Models\ReceiptAllocation;
use App\Repositories\TenantChequeRepository;
use App\Services\Agreement\AgreementService;
use App\Services\CompanyService;
use App\Services\TenantChequeService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use ReceivablesExport;

class ReceivablesClearingController extends Controller
{
    public function __construct(
        protected TenantChequeService $tenantChequeService,
        protected TenantChequeRepository $tenantChequeRepository,
        protected CompanyService $companyService,
        protected AgreementService $agreementService
    ) {}
    //
    public function receivableChequeClearing()
    {
        $payment_modes = PaymentMode::all();
        $banks = Bank::all();
        $companies = $this->companyService->getWithIndustry('finance', 'receivable_cheque_clearing');
        $properties = getPropertiesHaveContract();
        $units = getUnitshaveAgreement();
        $agpaymentmodes = getPaymentModeHaveAgreement();
        $tenants = AgreementTenant::all();
        $agreements = $this->agreementService->getAllAgreements();
        $receiptAlloctaions = ReceiptAllocation::all();
        $contracts = Contract::select('id', 'project_number', 'company_id')->get();
        // dd($receiptAlloctaions);
        // dd($units);
        // $units = getUnitshaveAgreement();
        // $allReceivables = $this->tenantChequeService->getAllReceivables();
        // dd($allReceivables);
        // $allReceivables  = [];
        return view('admin.finance.tenant-cheque-clearing', compact('payment_modes', 'contracts', 'receiptAlloctaions', 'banks', 'companies', 'properties', 'agpaymentmodes', 'units', 'tenants', 'agreements'));
    }
    public function receivableChequeClearingList(Request $request)
    {
        // dd("test");
        // dd($request->all());
        // dd($request->company_id);
        if ($request->ajax()) {
            $filters = [
                'company_id'  => $request->company_id,
                'search'      => $request->search['value'] ?? null,
                'date_from'   => $request->date_from ?? null,
                'date_to'     => $request->date_to ?? null,
                'property_id' => $request->property_id ?? null,
                'unit_id'     => $request->unit_id ?? null,
                'mode_id' => $request->mode_id ?? null,
                'tenant_id' => $request->tenant_id ?? null,
                'contract_id' => $request->contract_id ?? null,
            ];

            return $this->tenantChequeService->getDataTable($filters);
        }
    }
    public function receivableChequeClearSubmit(Request $request)
    {
        // dd($request->all());
        try {
            // $receivable = $this->tenantChequeService->clearReceivable($request->all());
            $data = $request->all();

            // ── pass allocated amounts from allocation panel ──
            $data['allocated_amounts'] = $request->input('allocated_amounts', []);

            $receivable = $this->tenantChequeService->clearReceivable($data);

            return response()->json(['success' => true, 'data' => $receivable, 'message' => 'Payment cleared successfully and receivable updated.'], 200);
        } catch (\Exception $e) {

            return response()->json(['success' => false, 'message' => $e->getMessage(), 'error'   => $e], 500);
        }
    }
    public function receivableChequeBounceSubmit(Request $request)
    {
        // dd($request);
        try {
            $bounced = $this->tenantChequeService->bouncedCheque($request->all());

            return response()->json(['success' => true, 'data' => $bounced, 'message' => 'Bounced Data Updated Successfully.'], 200);
        } catch (\Exception $e) {

            return response()->json(['success' => false, 'message' => $e->getMessage(), 'error'   => $e], 500);
        }
    }
    public function export(Request $request)
    {
        // dd($request);
        $search = request('search');
        $filters = [
            'company_id'  => $request->company_id,
            'search'      => $request->search ?? null ?? null,
            'date_from'   => $request->date_from ?? null,
            'date_to'     => $request->date_to ?? null,
            'property_id' => $request->property_id ?? null,
            'unit_id'     => $request->unit_id ?? null,
            'mode_id' => $request->mode_id ?? null,
            'contract_id' => $request->contract_id ?? null,
        ];
        // dd($filters);
        return Excel::download(new \App\Exports\RecevableClearingExport($request->search, $filters), 'receivables.xlsx');
    }
    public function receivableReport()
    {
        return view('admin.finance.tenant-cheque-clearing-report');
    }
    public function receivableReportList(Request $request)
    {
        // dd("test");
        // dd($request->all());
        // dd($request->company_id);
        if ($request->ajax()) {
            $filters = [
                'company_id'  => $request->company_id,
                'search'      => $request->search['value'] ?? null,
                'date_from'   => $request->date_from ?? null,
                'date_to'     => $request->date_to ?? null,
                'property_id' => $request->property_id ?? null,
                'unit_id'     => $request->unit_id ?? null,
                'mode_id' => $request->mode_id ?? null,
            ];

            return $this->tenantChequeService->getReportDataTable($filters);
        }
    }
    public function receivableReportExport(Request $request)
    {
        // $search = request('search');
        $search = request('search');
        $filters = [
            'search'      => $request->search ?? null,
            'date_from'   => $request->date_from ?? null,
            'date_to'     => $request->date_to ?? null,
        ];
        // dd($filters);
        return Excel::download(new \App\Exports\receivableReportExport($filters), 'clearedReceivables.xlsx');
    }

    public function getTenantPendingReceivables(Request $request)
    {
        $request->validate([
            // 'tenant_id' => 'required|integer|exists:agreement_tenants,id',
            'from_date' => 'nullable|date_format:d-m-Y',
            'to_date'   => 'nullable|date_format:d-m-Y|after_or_equal:from_date',
            'company_id' => 'required|integer|exists:companies,id',

        ]);
        // dd($request->all());



        $tenantId = $request->tenant_id;
        $companyId = $request->company_id;
        if (!auth()->user()->hasAnyPermission(['finance.receivable_cheque_clearing'], $companyId)) {
            return response()->json(['data' => [], 'message' => 'Unauthorized'], 403);
        }
        $fromDate = $request->from_date ? Carbon::createFromFormat('d-m-Y', $request->from_date)->format('Y-m-d') : null;
        $toDate   = $request->to_date ? Carbon::createFromFormat('d-m-Y', $request->to_date)->format('Y-m-d') : null;

        $receivables = AgreementPaymentDetail::query()
            ->with([
                'agreementPayment.installment',
                'agreementPayment.agreement.contract.property',
                'agreement.contract',
                'paymentMode',
                'agreementUnit.contractUnitDetail',
                'agreementUnit.contractSubunitDetail'
            ])
            ->whereHas('agreementPayment.agreement', function ($q) use ($companyId) {
                // $q->where('tenant_id', $tenantId);
                $q->where('company_id', $companyId);
            })
            ->withSum('clearedReceivables', 'paid_amount')
            ->where('is_payment_received', '!=', 1)
            ->where('terminate_status', 0)
            ->where(function ($q) {
                $q->where('has_bounced', 0)
                    ->orWhereNull('has_bounced');  // handle null safely
            })
            // ->whereDate('payment_date', '<=', Carbon::today()->addWeeks(2))
            ->when($fromDate && $toDate, function ($q) use ($fromDate, $toDate) {
                $q->whereDate('payment_date', '>=', $fromDate)
                    ->whereDate('payment_date', '<=', $toDate);
            }, function ($q) {
                // fallback to original default window when no range is given
                $q->whereDate('payment_date', '<=', Carbon::today()->addWeeks(2));
            })
            // ->orderBy('payment_date', 'asc') // oldest first
            // dd($receivables->toSql(), $receivables->getBindings(), $receivables->count());
            ->orderBy('agreement_payment_details.payment_date', 'asc')
            ->orderBy('agreement_payment_details.id', 'asc')
            ->get();
        //  Pre-load ALL installments for the unit IDs we already have — one query
        $unitIds = $receivables->pluck('agreement_unit_id')->unique()->filter();

        $installmentsByUnit = AgreementPaymentDetail::whereIn('agreement_unit_id', $unitIds)
            ->orderBy('payment_date')
            ->get()
            ->groupBy('agreement_unit_id');
        // dd($installmentsByUnit);

        $receivables = $receivables->map(function ($detail)  use ($installmentsByUnit) {
            // same calculation as getReceivableAmount() but no extra queries
            $totalPaid       = (float) ($detail->cleared_receivables_sum_paid_amount ?? 0);
            $originalAmount  = (float) $detail->payment_amount;
            $remainingAmount = max(0, $originalAmount - $totalPaid);

            //  Use pre-loaded collection — no DB hit
            $installments = $installmentsByUnit->get($detail->agreement_unit_id, collect());
            $total        = $installments->count();
            $current      = 0;

            foreach ($installments as $index => $inst) {
                if ($inst->payment_date == $detail->payment_date) {
                    $current = $index + 1;
                    break;
                }
            }

            $label = $total > 0 ? "{$current}/{$total}" : '-';

            return [
                'id'       => $detail->id,
                'date'     => $detail->payment_date
                    ? \Carbon\Carbon::parse($detail->payment_date)
                    ->format('d-m-Y')
                    : '-',
                // 'amount'   => (float) $detail->payment_amount,
                'amount'   => $remainingAmount,
                'mode'     => $detail->paymentMode?->payment_mode_name ?? '-',
                // 'label' => $detail->agreementPayment?->installment?->installment_name
                //     ?? 'Due: ' . \Carbon\Carbon::parse($detail->payment_date)->format('M Y'),
                'label' => $label,
                'property' => $detail->agreementPayment
                    ?->agreement
                    ?->contract
                    ?->property
                    ?->property_name ?? '-',
                'unit_number' => $detail->agreementUnit->contractUnitDetail?->unit_number ?? '-',
                'subunit_number' => $detail->agreementUnit->contractSubunitDetail?->subunit_no ?? '-',
                'project_number' => $detail->agreement->contract->project_number ?? '-',
                'company_name' => $detail->agreementPayment?->agreement?->contract?->company?->company_short_code ?? '-',
            ];
        });
        // dd($receivables);
        // dd($receivables->count());

        return response()->json([
            'success' => true,
            'data'    => $receivables,
        ]);
    }
    public function getAllPendingReceivables(Request $request)
    {
        $allReceivables = $this->tenantChequeService->getAllReceivables();

        return response()->json([
            'success' => true,
            'data'    => $allReceivables,
        ]);
    }
    public function storeLossDiscount(Request $request)
    {
        // dd($request->all());
        try {
            $update = $this->tenantChequeService->storeLossDiscount($request->all());

            return response()->json(['success' => true, 'data' => $update, 'message' => 'Loss/Discount Data Updated Successfully.'], 200);
        } catch (\Exception $e) {

            return response()->json(['success' => false, 'message' => $e->getMessage(), 'error'   => $e], 500);
        }
    }
    // public function saveExcessAmount(Request $request)
    // {
    //     // dd($request->all());
    //     try {
    //         $update = $this->tenantChequeService->saveExcessAmount($request->all());

    //         return response()->json(['success' => true, 'data' => $update, 'message' => 'Excess Data Updated Successfully.'], 200);
    //     } catch (\Exception $e) {

    //         return response()->json(['success' => false, 'message' => $e->getMessage(), 'error'   => $e], 500);
    //     }
    // }
}
