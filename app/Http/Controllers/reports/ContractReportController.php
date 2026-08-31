<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateInventoryExport;
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


    // project report
    public function projectReport(Request $request)
    {
        $title = 'Project Report';
        $companies = $this->companyService->getAll('finance', 'payable_cheque_clearing');
        $vendors = getVendorsHaveContract();
        $properties = getPropertiesHaveContract();
        $areas = getAreasHaveContract();
        $localities = getLocalitiesHaveContract();

        return view('admin.reports.contract.project_report', compact(
            'title',
            'companies',
            'vendors',
            'properties',
            'areas',
            'localities'
        ));
    }

    public function projectReportData(Request $request)
    {
        abort_unless($request->ajax(), 404);
        return $this->ContractReportService->getProjectDataTable($this->projectFilters($request));
    }

    public function projectReportExport(Request $request)
    {
        return $this->ContractReportService->exportProject($this->projectFilters($request));
    }

    private function projectFilters(Request $request): array
    {
        return $request->only([
            'company_id',
            'vendor_id',
            'property_id',
            'area_id',
            'locality_id',
            'contract_status',
            'date_from',
            'date_to'
        ])
            + ['search' => $request->keyword];
    }
    // project report


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




    // inventory report
    public function inventoryReport(Request $request)
    {
        $title = 'Inventory Report';

        $companies  = $this->companyService
            ->getAll('finance', 'payable_cheque_clearing');

        $vendors    = getVendorsHaveContract();
        $properties = getPropertiesHaveContract();
        $areas      = getAreasHaveContract();
        $localities = getLocalitiesHaveContract();

        return view('admin.reports.contract.inventory_report', compact(
            'title',
            'companies',
            'vendors',
            'properties',
            'areas',
            'localities'
        ));
    }

    public function inventoryReportData(Request $request)
    {
        abort_unless($request->ajax(), 404);

        return $this->ContractReportService->getInventoryDataTable(
            $this->inventoryFilters($request)
        );
    }

    public function inventoryReportExport(Request $request)
    {
        return $this->ContractReportService->exportInventory(
            $this->inventoryFilters($request)
        );
    }

    private function inventoryFilters(Request $request): array
    {
        return [
            'company_id'      => $request->company_id,
            'vendor_id'       => $request->vendor_id,
            'property_id'     => $request->property_id,
            'area_id'         => $request->area_id,
            'locality_id'     => $request->locality_id,
            'contract_status' => $request->contract_status,
            'date_from'       => $request->date_from,
            'date_to'         => $request->date_to,
            'search'          => $request->keyword,
        ];
    }
    // inventory report


    // Occupancy report
    public function occupancyReport(Request $request)
    {
        $title = 'Occupancy Report';
        $companies = $this->companyService->getAll('finance', 'payable_cheque_clearing');
        $vendors = getVendorsHaveContract();
        $properties = getPropertiesHaveContract();
        $areas = getAreasHaveContract();
        $localities = getLocalitiesHaveContract();

        return view('admin.reports.contract.occupancy_report', compact(
            'title',
            'companies',
            'vendors',
            'properties',
            'areas',
            'localities'
        ));
    }

    public function occupancyReportData(Request $request)
    {
        abort_unless($request->ajax(), 404);

        return $this->ContractReportService->getOccupancyDataTable(
            $this->occupancyFilters($request)
        );
    }

    public function occupancyReportExport(Request $request)
    {
        return $this->ContractReportService->exportOccupancy(
            $this->occupancyFilters($request)
        );
    }

    private function occupancyFilters(Request $request): array
    {
        return $request->only([
            'company_id',
            'vendor_id',
            'property_id',
            'area_id',
            'locality_id',
            'occupancy_status',
            'subunit_type',
            'date_from',
            'date_to',
        ]) + ['search' => $request->keyword];
    }
    // Occupancy report
}
