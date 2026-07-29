<?php

namespace App\Http\Controllers;

use App\Services\CompanyService;
use App\Services\Investment\InvestmentService;
use App\Services\Investment\InvestmentSoaService;
use Illuminate\Http\Request;

class InvesmentSOAController extends Controller
{
    //
    public function __construct(
        protected InvestmentSoaService $investmentSoaService,
        protected CompanyService $companyService,
    ) {}

    public function index()
    {
        $title = "Investor SOA";
        return view('admin.investment.investment_soa', compact('title'));
    }
    public function getData(Request $request)
    {
        $filters = [
            // 'company_id' => $request->company_id,
            'from'       => $request->from,
            'to'         => $request->to,
        ];

        $data = $this->investmentSoaService->getDataTable($filters);

        return response()->json(['data' => $data]);
    }
}
