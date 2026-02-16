<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Investment;
use App\Services\Contracts\ContractService;
use App\Services\DashboardService;
use App\Services\PropertyService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        protected ContractService $contractServ,
        protected DashboardService $dashboardService,
        protected PropertyService $propertService
    ) {}

    public function index()
    {
        $title = 'Dashboard';
        $renewalCount = $this->contractServ->getRenewalDataCount();
        $companyId = null;
        $companies = Company::all();

        // Get chart & investment data
        $widgets = $this->dashboardService->widgetsData($companyId);
        $data = $this->dashboardService->investmentChart($companyId);
        // $inventoryData = $this->dashboardService->inventoryChart($companyId);
        $properties = $this->propertService->getAll($companyId);
        $topInvestors = $this->dashboardService->toIinvestorChart($companyId);
        // dd($inventoryData);
        // dd($properties);
        $inventoryData = [
            'inventoryData' => $this->dashboardService->inventoryChart($companyId)
        ];
        // dd($inventoryData);

        return view('admin.dashboard', array_merge(
            compact('title', 'renewalCount', 'properties', 'companies'),
            $data,
            $widgets,
            $inventoryData,
            $topInvestors

        ));
    }
    public function show(Request $request)
    {
        // dd("test");
        $companyId = $request->company_id;

        return response()->json([
            'widgets'        => $this->dashboardService->widgetsData($companyId),
            'investment'     => $this->dashboardService->investmentChart($companyId),
            'inventory'      => $this->dashboardService->inventoryChart($companyId),
            'topInvestors'   => $this->dashboardService->toIinvestorChart($companyId),

            // 'renewalCount'   => $this->contractServ->getRenewalDataCount([
            //     'company_id' => $companyId
            // ]),
            // 'expiryCount'    => getAgreementExpiringCounts($companyId),
            // 'pendingApproval' => statusCount(4, $companyId),
        ]);
    }
}
