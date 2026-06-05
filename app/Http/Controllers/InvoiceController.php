<?php

namespace App\Http\Controllers;

use App\Models\AgreementTenant;
use App\Models\Bank;
use App\Models\PaymentMode;
use App\Models\TenantInvoice;
use App\Services\Agreement\AgreementService;
use App\Services\CompanyService;
use App\Services\InvoiceService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    //
    public function __construct(
        protected InvoiceService $invoiceService,
        protected CompanyService $companyService,
        protected AgreementService $agreementService,

    ) {}

    public function index()
    {
        $title = 'Invoices';
        $banks = Bank::all();
        $companies = $this->companyService->getWithIndustry('invoice');
        $properties = getPropertiesHaveContractB2b();
        $units = getUnitshaveAgreementB2b();
        // $agpaymentmodes = getPaymentModeHaveAgreement();
        $tenants = AgreementTenant::all();
        // $agreements = $this->agreementService->getAllAgreements();
        $agreements = getAllAgreementsB2b();

        $contracts = getContractsHaveAgreementB2b();
        // dd($units);
        return view("admin.invoices.invoices_list", compact("title", "banks", "companies", "properties", "units", "tenants", "agreements", "contracts"));
    }
    public function getInvoiceList(Request $request)
    {
        // dd("test");

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
                'status' => $request->status ?? 'all',
                'contract_id' => $request->contract_id ?? null,
            ];
            return $this->invoiceService->getDataTable($filters);
        }
    }
    public function store(Request $request)
    {
        // dd($request->all());
        try {
            $invoice = $this->invoiceService->create($request->all());
            return response()->json(['success' => true, 'data' => $invoice, 'message' => 'Invoice created successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'error'   => $e], 500);
        }
    }
    public function show(TenantInvoice $invoice)
    {
        // dd("test");
        $invoice = $this->invoiceService->getDetails($invoice->id);
        // dd($invoice);

        return view('admin.invoices.invoice_view', compact('invoice'));
    }
    public function approve(Request $request, $id)
    {
        // dd($request->all());
        // dd($id);
        $result = $this->invoiceService->approve($request->all(), $id);

        if ($result['success']) {
            return response()->json(['message' => $result['message']], 200);
        }

        return response()->json(['message' => $result['message']], 422);
    }
    public function update(Request $request, $id)
    {
        // dd($request);
        try {
            $invoice = $this->invoiceService->update($id, $request->all());

            return response()->json(['success' => true, 'data' => $invoice, 'message' => 'Invoice updated successfully'], 200);
        } catch (\Exception $e) {

            return response()->json(['success' => false, 'message' => $e->getMessage(), 'error'   => $e], 500);
        }
    }
    public function destroy(TenantInvoice $invoice)
    {
        $this->invoiceService->delete($invoice->id);
        return response()->json(['success' => true, 'message' => 'Invoice deleted successfully']);
    }
    public function admin_view($id)
    {
        $invoice = $this->invoiceService->getDetails($id);
        return view("admin.invoices.admin_comments", compact('invoice'));
    }
    public function comment(Request $request, $id)
    {
        // dd($request->all(), $id);
        try {
            $result = $this->invoiceService->addComment($request->all(), $id);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'redirect' => route('invoices.index')
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 422);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function getComments($id)
    {
        $invoice = TenantInvoice::with('comments.user')->findOrFail($id);
        // dd($invoice);

        $html = view('admin.invoices.comments', compact('invoice'))->render();

        return response()->json([
            'html' => $html
        ]);
    }
    public function getGeneratedInvoices()
    {
        $title = 'Generated Invoices';
        $companies = $this->companyService->getWithIndustry('invoice');
        // $invoices = $this->invoiceService->getGeneratedInvoices();
        return view('admin.invoices.generated_invoices', compact('title', 'companies'));
    }
    public function getGenerated(Request $request)
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
                'status' => $request->status ?? 'all',
            ];
            return $this->invoiceService->getGeneratedInvoices($filters);
        }
    }
    public function downloadPdf(TenantInvoice $invoice)
    {
        $invoice = $this->invoiceService->getDetails($invoice->id);
        // dd($invoice);

        $tenant        = $invoice->agreement->tenant ?? null;
        $tenantName    = $tenant->tenant_name ?? '-';
        $agreementUnit = $invoice->agreementUnit;
        $subunits      = $agreementUnit->agreementSubunitRentBifurcation ?? collect();
        $contract      = $invoice->contract;
        $buildingName  = $contract->property->property_name ?? '-';
        $flatNo        = $agreementUnit->contractUnitDetail->unit_number ?? '-';
        $area          = $contract->area->area_name ?? '-';
        $unitType      = $agreementUnit->contractUnitDetail->unit_type->unit_type ?? '-';
        $tenantType    = ($contract->contract_unit->business_type ?? 0) == 1 ? 'B2B' : 'B2C';
        $projectNo     = $contract->project_number;
        $isApproved    = ($invoice->status ?? '') === 2;
        $approvedAt    = $isApproved && $invoice->approved_date
            ? \Carbon\Carbon::parse($invoice->approved_date)->format('d/m/Y H:i') : null;
        // dd($approvedAt);
        $trn_number = $tenant?->tenantDocuments?->where('document_type', 3)->first()?->document_number ?? '-';

        $pdf = Pdf::loadView('admin.invoices.invoice-pdf', compact(
            'invoice',
            'tenant',
            'tenantName',
            'agreementUnit',
            'subunits',
            'contract',
            'buildingName',
            'flatNo',
            'area',
            'unitType',
            'tenantType',
            'projectNo',
            'isApproved',
            'approvedAt',
            'trn_number'

        ))
            ->setPaper('a4', 'portrait')
            // ->setOptions([
            //     'isHtml5ParserEnabled' => true,
            //     'isRemoteEnabled'      => true,
            //     'defaultFont'          => 'Arial',
            //     'dpi'                  => 150,
            // ]);
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
                'defaultFont'          => 'Arial',
                'dpi'                  => 150,
                'chroot'               => public_path(),
            ]);

        return $pdf->stream('invoice-' . $invoice->invoice_no . '.pdf');
    }
}
