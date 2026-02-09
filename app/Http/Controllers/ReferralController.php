<?php

namespace App\Http\Controllers;

use App\Models\InvestmentReferral;
use App\Repositories\Investment\InvestmentRepository;
use App\Services\Investment\InvestmentService;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    //
    public function __construct(
        protected InvestmentService $investmentService,
        protected InvestmentRepository $investmentRepository
    ) {}
    public function referrals()
    {
        $title = 'Referrals';
        return view('admin.investment.investment.referrals-list', compact('title'));
    }
    public function getReferrals(Request $request)
    {
        if ($request->ajax()) {
            $filters = [
                'company_id' => auth()->user()->company_id,
                'search' => $request->search['value'] ?? null
            ];
            return $this->investmentService->getReferrals($filters);
        }
    }
    public function show(InvestmentReferral $referral)
    {
        // dd('test');
        // dd($referral);
        $referraldata = $this->investmentService->getReferralDetails($referral->id);
        $title = "Referral View";
        // dd($referraldata);
        return view('admin.investment.investment.view-referral', compact('referral', 'title'));
    }
}
