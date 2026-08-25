<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\ContractType;
use App\Models\Vendor;
use App\Services\CompanyService;
use App\Services\PaymentModeService;
use App\Services\Reports\ContractReportService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ContractReportController extends Controller
{
    public function __construct(
        protected PaymentModeService $paymentModeService,
        // protected BankService $bankService,
        protected Vendor $vendorService,
        protected CompanyService $companyService,
        protected ContractReportService $ContractReportService
    ) {}

    // payable report
    public function payableReport(Request $request)
    {
        $title = 'Payable Report';
        $companies   = $this->companyService->getAll('finance', 'payable_cheque_clearing');
        $vendors = getVendorsHaveContract();
        $properties = getPropertiesHaveContract();
        $areas       = getAreasHaveContract();
        $localities  = getLocalitiesHaveContract();
        $paymentModes = $this->paymentModeService->getAll();
        $contractTypes = ContractType::all(); // Direct / Faateh

        return view('admin.reports.contract.payable_report', compact(
            'title',
            'companies',
            'vendors',
            'properties',
            'areas',
            'localities',
            'paymentModes',
            'contractTypes'
        ));
    }

    public function payableReportData(Request $request)
    {
        abort_unless($request->ajax(), 404);

        return $this->ContractReportService->getPayableDataTable(
            $this->Payablefilters($request)
        );
    }

    public function payableReportExport(Request $request)
    {
        return $this->ContractReportService->export($this->Payablefilters($request));
    }

    private function Payablefilters(Request $request): array
    {
        return [
            'company_id' => $request->company_id,
            'payment_status' => $request->payment_status,
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'search' => $request->keyword,
        ];
    }
    // payable report
}
