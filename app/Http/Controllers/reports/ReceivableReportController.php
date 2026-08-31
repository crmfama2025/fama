<?php

namespace App\Http\Controllers\reports;

use App\Exports\GenericExport;
use App\Http\Controllers\Controller;
use App\Models\AgreementTenant;
use App\Models\Bank;
use App\Models\PaymentMode;
use App\Services\Agreement\AgreementService;
use App\Services\CompanyService;
use App\Services\Reports\ReceivableReportService;
use App\Services\TenantChequeService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReceivableDetailedReportExport;
use App\Models\Contract;
use DB;

class ReceivableReportController extends Controller
{
    //
    public function __construct(
        protected ReceivableReportService $receivableReportService,
        protected CompanyService $companyService,
        protected AgreementService $agreementService
    ) {}
    public function index()
    {
        $title = "Receivable Report";
        $payment_modes = PaymentMode::query()
            ->select('id', 'payment_mode_name')
            ->orderBy('id')
            ->get();

        $banks = Bank::query()
            ->select('id', 'bank_name')
            ->orderBy('id')
            ->get();
        $companies = $this->companyService->getWithIndustry('finance', 'receivable_cheque_clearing');
        $contracts = Contract::query()
            ->select(
                'id',
                'project_number',
                'company_id',
                'property_id'
            )
            ->where('has_agreement', 1)
            ->orderBy('project_number')
            ->get();

        $properties = getPropertiesHaveContract();
        $units = getUnitshaveAgreement();
        // $agpaymentmodes = getPaymentModeHaveAgreement();
        $tenants = AgreementTenant::select('id', 'tenant_name')
            ->orderBy('tenant_name')
            ->get();
        $agreements = $this->agreementService->getAllAgreements();

        return view('admin.reports.real_estate.receivable_report', compact('title', 'contracts', 'banks', 'companies', 'properties', 'payment_modes', 'units', 'tenants', 'agreements'));
    }

    public function getReceivables(Request $request)
    {

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


                // Paid date
                'paid_date_from' => $request->input('paid_date_from'),
                'paid_date_to' => $request->input('paid_date_to'),

                // Payment status
                'is_payment_received' => $request->input('is_payment_received'),

                // Paid company
                'paid_company_id' => $request->input('paid_company_id'),

                // Invoice status
                'is_invoice_added' => $request->input('is_invoice_added'),

                'contract_id' => $request->input('contract_id')
            ];
            // dd($filters);

            return $this->receivableReportService->getDataTable($filters);
        }
    }
    // public function exportReceivables(Request $request)
    // {
    //     $filters = [
    //         'search' => $request->input('search'),

    //         // Payment date
    //         'date_from' => $request->input('date_from'),
    //         'date_to' => $request->input('date_to'),

    //         // Main filters
    //         'company_id' => $request->input('company_id'),
    //         'property_id' => $request->input('property_id'),
    //         'unit_id' => $request->input('unit_id'),
    //         'tenant_id' => $request->input('tenant_id'),
    //         'mode_id' => $request->input('mode_id'),

    //         // Paid date
    //         'paid_date_from' => $request->input('paid_date_from'),
    //         'paid_date_to' => $request->input('paid_date_to'),

    //         // Payment status
    //         'is_payment_received' => $request->input('is_payment_received'),

    //         // Paid company
    //         'paid_company_id' => $request->input('paid_company_id'),

    //         // Invoice status
    //         'is_invoice_added' => $request->input('is_invoice_added'),
    //     ];

    //     $data = $this->receivableReportService
    //         ->getReceivableExportData($filters);

    //     return Excel::download(
    //         new GenericExport(
    //             $data,
    //             $this->receivableReportService->receivableExportHeadings()
    //         ),
    //         'receivables.xlsx'
    //     );
    // }

    public function exportReceivables(Request $request)
    {
        $filters = [
            'search' => $request->input('search'),

            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),

            'company_id' => $request->input('company_id'),
            'property_id' => $request->input('property_id'),
            'unit_id' => $request->input('unit_id'),
            'tenant_id' => $request->input('tenant_id'),
            'mode_id' => $request->input('mode_id'),

            'paid_date_from' => $request->input('paid_date_from'),
            'paid_date_to' => $request->input('paid_date_to'),

            'is_payment_received' => $request->input('is_payment_received'),

            'paid_company_id' => $request->input('paid_company_id'),

            'is_invoice_added' => $request->input('is_invoice_added'),
            'contract_id' => $request->input('contract_id')
        ];

        return $this->receivableReportService
            ->exportReceivables($filters);
    }

    public function projectsByCompany(Request $request)
    {
        $query = DB::table('contracts')
            ->select('id', 'project_number')
            ->where('company_id', $request->company_id)
            ->whereNotNull('project_number')
            ->whereNull('deleted_at')
            ->orderBy('project_number');

        return response()->json(
            $query->get()
        );
    }
}
