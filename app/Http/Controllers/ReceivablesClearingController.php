<?php

namespace App\Http\Controllers;

use App\Exports\RecevableClearingExport;
use App\Models\AgreementTenant;
use App\Models\Bank;
use App\Models\PaymentMode;
use App\Repositories\TenantChequeRepository;
use App\Services\Agreement\AgreementService;
use App\Services\CompanyService;
use App\Services\TenantChequeService;
use Illuminate\Http\Request;
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
        // dd($units);
        $units = getUnitshaveAgreement();
        return view('admin.finance.tenant-cheque-clearing', compact('payment_modes', 'banks', 'companies', 'properties', 'agpaymentmodes', 'units', 'tenants', 'agreements'));
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
                'tenant_id' => $request->tenant_id ?? null
            ];

            return $this->tenantChequeService->getDataTable($filters);
        }
    }
    public function receivableChequeClearSubmit(Request $request)
    {
        // dd($request->all());
        try {
            $receivable = $this->tenantChequeService->clearReceivable($request->all());

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
        ];
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
}
