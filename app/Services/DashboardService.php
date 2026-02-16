<?php

namespace App\Services;

use App\Models\AgreementTenant;
use App\Models\Company;
use App\Models\Contract;
use App\Models\ContractRental;
use App\Models\Investment;
use App\Models\Investor;
use App\Models\PayoutBatch;
use App\Models\ProfitInterval;
use App\Models\ReferralCommissionFrequency;
use App\Repositories\Investment\InvestmentRepository;
use App\Repositories\Investment\InvestmentSoaRepository;
use App\Repositories\Investment\InvestorRepository;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Null_;

class DashboardService
{
    public function __construct(
        protected InvestmentRepository $investmentRepository,
        protected InvestorRepository $investorRepository,
        protected InvestmentRepository $investmentReferralRepository,
        protected InvestmentSoaRepository $investmentSoaRepository,

    ) {}

    public function investmentChart($companyId = null)
    {
        // Get last 2 months of investments
        // $monthlyData = Investment::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(investment_amount) as total_amount, COUNT(*) as total_count')
        //     // ->where('created_at', '>=', now()->subMonths(2))
        //     ->where('created_at', '<', now())
        //     ->where('company_id', $companyId)

        //     ->groupBy('year', 'month')
        //     ->orderBy('year')
        //     ->orderBy('month')
        //     ->get();
        $query = Investment::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(investment_amount) as total_amount, COUNT(*) as total_count')
            ->where('created_at', '>=', now()->subMonths(2))
            ->where('created_at', '<=', now());

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        $monthlyData = $query->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();


        // $labels = [];
        // $amounts = [];
        // $counts = [];

        // foreach ($monthlyData as $data) {
        //     $labels[] = date('M', mktime(0, 0, 0, $data->month, 1));
        //     $amounts[] = (float) $data->total_amount;
        //     $counts[] = (int) $data->count;
        // }

        $totalInvestmentQuery = Investment::query();
        $totalCountQuery = Investment::query();

        if ($companyId) {
            $totalInvestmentQuery->where('company_id', $companyId);
            $totalCountQuery->where('company_id', $companyId);
        }

        $totalInvestment = $totalInvestmentQuery->sum('investment_amount');
        $totalCount = $totalCountQuery->count();

        $percentageChange = 0;
        $arrowUp = true;
        // dd($monthlyData);
        // dd($monthlyData->count());

        if ($monthlyData->count() >= 2) {
            $lastMonth = $monthlyData[$monthlyData->count() - 2]->total_amount;
            $thisMonth = $monthlyData[$monthlyData->count() - 1]->total_amount;
            // dd($thisMonth, $lastMonth);
            // $test = ($thisMonth / $lastMonth) * 100;
            // dd($test);

            if ($lastMonth > 0) {
                $percentageChange = round((($thisMonth - $lastMonth) / $lastMonth) * 100, 2);
                $arrowUp = $percentageChange >= 0;
            }
        }

        return [
            'investmentMonthlyRaw' => $monthlyData,
            'totalInvestment' => $totalInvestment,
            'totalCount' => $totalCount,
            'percentageChange' => $percentageChange,
            'arrowUp' => $arrowUp
        ];

        // return compact('labels', 'amounts', 'counts', 'totalInvestment', 'totalCount', 'percentageChange', 'arrowUp');
    }

    // public function widgetsData()
    // {
    //     $wid_totalContracts = Contract::where('contract_renewal_status', 0)->count();
    //     $wid_totalRenewals = Contract::where('contract_renewal_status', 1)->count();
    //     $wid_totalInvestors = Investor::count();
    //     $wid_totalInvestments = Investment::count();
    //     $wid_revenue = ContractRental::sum('rent_receivable_per_annum');
    //     $wid_tenants = AgreementTenant::count();
    //     // $wid_tenants = 23456778;
    //     // $wid_totalContracts = 5000;
    //     // $wid_totalInvestors = 6000;
    //     // $wid_totalInvestments = 3000;
    //     return compact('wid_totalContracts', 'wid_totalInvestors', 'wid_totalInvestments', 'wid_revenue', 'wid_tenants', 'wid_totalRenewals');
    // }
    public function widgetsData($companyId = null)
    {
        $contracts = Contract::query();

        if ($companyId) {
            $contracts->where('company_id', $companyId);
        }

        $wid_totalContracts = (clone $contracts)
            ->where('contract_renewal_status', 0)
            ->count();

        $wid_totalRenewals = (clone $contracts)
            ->where('contract_renewal_status', 1)
            ->count();

        $wid_totalInvestors = Investor::when($companyId, function ($q) use ($companyId) {
            $q->whereHas('investments', function ($inv) use ($companyId) {
                $inv->where('company_id', $companyId);
            });
        })->count();

        $wid_totalInvestments = Investment::when($companyId, function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })->count();

        $wid_revenue = ContractRental::when($companyId, function ($q) use ($companyId) {
            $q->whereHas('contract', function ($c) use ($companyId) {
                $c->where('company_id', $companyId);
            });
        })->sum('rent_receivable_per_annum');

        $wid_tenants = AgreementTenant::when($companyId, function ($q) use ($companyId) {
            $q->whereHas('agreement', function ($q2) use ($companyId) {
                $q2->whereHas('contract', function ($c) use ($companyId) {
                    $c->where('company_id', $companyId);
                });
            });
        })->count();

        // dd($wid_tenants);

        return compact(
            'wid_totalContracts',
            'wid_totalRenewals',
            'wid_totalInvestors',
            'wid_totalInvestments',
            'wid_revenue',
            'wid_tenants'
        );
    }

    // public function inventoryChart()
    // {

    //     $companies = Company::with(['contracts.contract_unit'])->get();

    //     $companyNames = [];
    //     $companyUnits = [];

    //     foreach ($companies as $company) {
    //         $companyNames[] = $company->company_name;

    //         // sum all contract units under this company
    //         $totalUnits = $company->contracts->sum(function ($contract) {
    //             return $contract->contract_unit->no_of_units;
    //         });
    //         // dd($totalUnits);

    //         $companyUnits[] = $totalUnits;
    //     }
    //     dd($companyUnits);

    //     $totalUnits = array_sum($companyUnits);

    //     $percentChange = 10;

    //     return compact('companyNames', 'companyUnits', 'totalUnits', 'percentChange');
    // }


    public function inventoryChart($companyId = null)
    {

        $companiesQuery = Company::with(['contracts.contract_unit']);
        if ($companyId) {
            $companiesQuery->where('id', $companyId);
        }

        $companies = $companiesQuery->get();

        $companyNames = [];
        $dfUnits = [];
        $ffUnits = [];
        $totalUnits = [];

        $thisMonthStart = Carbon::now()->startOfMonth();
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd   = Carbon::now()->subMonth()->endOfMonth();

        $thisMonthUnits = 0;
        $lastMonthUnits = 0;

        foreach ($companies as $company) {
            $companyNames[] = $company->company_name;

            $dfTotal = 0;
            $ffTotal = 0;

            foreach ($company->contracts as $contract) {
                $units = $contract->contract_unit->no_of_units ?? 0;

                if ($contract->contract_type_id === 1) {
                    $dfTotal += $units;
                }

                if ($contract->contract_type_id === 2) {
                    $ffTotal += $units;
                }

                if ($contract->created_at >= $thisMonthStart) {
                    $thisMonthUnits += $units;
                }

                if (
                    $contract->created_at >= $lastMonthStart &&
                    $contract->created_at <= $lastMonthEnd
                ) {
                    $lastMonthUnits += $units;
                }
            }

            $dfUnits[] = $dfTotal;
            $ffUnits[] = $ffTotal;
            $totalUnits[] = $dfTotal + $ffTotal;
        }

        $grandTotal = array_sum($totalUnits);
        // dd($grandTotal);

        $difference = $thisMonthUnits - $lastMonthUnits;

        $percentChange = $lastMonthUnits > 0
            ? round(($difference / $lastMonthUnits) * 100, 2)
            : 100;

        $arrow = $difference > 0
            ? 'up'
            : ($difference < 0 ? 'down' : 'same');

        return compact(
            'companyNames',
            'dfUnits',
            'ffUnits',
            'totalUnits',
            'grandTotal',
            'thisMonthUnits',
            'lastMonthUnits',
            'difference',
            'percentChange',
            'arrow'
        );
    }
    public function toIinvestorChart($companyId = null)
    {
        // $topInvestors = Investor::select('investor_name', 'total_no_of_investments')
        //     ->orderByDesc('total_no_of_investments')
        //     ->where('total_no_of_investments', '>', 0)
        //     ->limit(10)
        //     ->get();
        $topInvestors = Investor::select(
            'investors.investor_name',
            DB::raw('COUNT(investments.id) as total_no_of_investments')
        )
            ->join('investments', 'investors.id', '=', 'investments.investor_id')
            ->when($companyId, function ($q) use ($companyId) {
                $q->where('investments.company_id', $companyId);
            })
            ->groupBy('investors.id', 'investors.investor_name')
            ->having('total_no_of_investments', '>', 0)
            ->orderByDesc('total_no_of_investments')
            ->limit(10)
            ->get();

        $investorNames = $topInvestors->pluck('investor_name');
        $investorCounts = $topInvestors->pluck('total_no_of_investments');
        // $topinvestor = $investorNames->first();
        $maxCount = $investorCounts->first();
        $topInvestorsMax = $topInvestors->filter(function ($investor) use ($maxCount) {
            return $investor->total_no_of_investments === $maxCount;
        });
        // dd($topinvestorsMax, $maxCount);

        return compact('topInvestors', 'investorNames', 'investorCounts', 'topInvestorsMax', 'maxCount');
    }

    // public function investmentChart()
    // {
    //     $monthlyData = Investment::selectRaw('
    //         YEAR(created_at) as year,
    //         MONTH(created_at) as month,
    //         SUM(investment_amount) as total_amount,
    //         COUNT(*) as total_count
    //     ')
    //         ->where('created_at', '<', now())
    //         ->groupBy('year', 'month')
    //         ->orderBy('year')
    //         ->orderBy('month')
    //         ->get();

    //     $totalInvestment = Investment::sum('investment_amount');
    //     $totalCount = Investment::count();

    //     // Month-on-month comparison (latest two months)
    //     $percentageChange = 0;
    //     $arrowUp = true;

    //     if ($monthlyData->count() >= 2) {
    //         $last = $monthlyData[$monthlyData->count() - 2]->total_amount;
    //         $current = $monthlyData[$monthlyData->count() - 1]->total_amount;

    //         if ($last > 0) {
    //             $percentageChange = round((($current - $last) / $last) * 100, 2);
    //             $arrowUp = $percentageChange >= 0;
    //         }
    //     }

    //     return [
    //         'investmentMonthlyRaw' => $monthlyData,
    //         'totalInvestment' => $totalInvestment,
    //         'totalCount' => $totalCount,
    //         'percentageChange' => $percentageChange,
    //         'arrowUp' => $arrowUp
    //     ];
    // }
}
