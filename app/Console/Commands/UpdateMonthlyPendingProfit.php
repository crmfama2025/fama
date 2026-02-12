<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Investment;
use App\Models\InvestorPayout;
use Carbon\Carbon;
use DB;

class UpdateMonthlyPendingProfit extends Command
{
    protected $signature = 'profit:update-monthly-pending';
    protected $description = 'Update current month pending profit release for all investments';



    public function handle()
    {
        $now = now();


        // if ($now->day !== 1 || $now->hour !== 0 || $now->minute < 30) {
        //     return;
        // }

        $currentMonth = $now->startOfMonth();


        $currentMonthStart = $now->copy()->startOfMonth();
        // dd($currentMonthStart);
        $currentMonthEnd = $now->copy()->endOfMonth();

        Investment::with('profitInterval', 'investmentReferral')
            ->where('investment_status', 1)
            ->where('terminate_status', '!=', 2)
            ->chunkById(100, function ($investments) use ($currentMonthStart, $currentMonthEnd, $currentMonth) {
                // dd($investments);

                foreach ($investments as $investment) {

                    // ----------------------------
                    // PROFIT PAYOUT
                    // ----------------------------
                    if ($investment->profitInterval && $investment->next_profit_release_date) {
                        $nextProfitRelease = Carbon::parse($investment->next_profit_release_date);
                        $termDate = Carbon::parse($investment->termination_date);
                        // $monthsGap = 12 / $investment->profitInterval->no_of_installments;
                        // $monthsDiff = $nextProfitRelease->diffInMonths($currentMonth);

                        // if ($monthsDiff % $monthsGap !== 0) continue;
                        // if (!$nextProfitRelease->isSameMonth($currentMonth)) {
                        //     continue;
                        // }

                        // Check if next profit release is **within current month**
                        // if ($nextProfitRelease->between($currentMonthStart, $currentMonthEnd)) {
                        $payout = null;
                        if ($nextProfitRelease->lt($currentMonthStart) || $nextProfitRelease->isSameMonth($currentMonth)) {
                            if ($termDate && ($termDate->isSameMonth($currentMonth) || $termDate->lt($nextProfitRelease))) {
                                // $investment->next_profit_release_date = $investment->next_profit_release_date;
                                $investment->next_profit_release_date = null;

                                $investment->save();
                            } else {
                                $payout = $this->createInvestorpayout(1, $currentMonthStart, $investment);
                            }
                            // dd($payout);
                        }
                        if ($payout) {

                            // $outstandingProfit += $payout->pending_amount;
                            $investment->update([
                                'outstanding_profit' => $payout->amount_pending,
                                'is_profit_processed' => 1
                            ]);
                        }
                        // dd($investment->investmentReferral->next_referral_commission_release_date);

                    }

                    // ----------------------------
                    // REFERRAL PAYOUT
                    // ----------------------------
                    if ($investment->investmentReferral && $investment->next_referral_commission_release_date && ($investment->investmentReferral->referral_commission_status !== 1)) {
                        // dd('text');
                        $nextReferralRelease = Carbon::parse($investment->next_referral_commission_release_date);

                        // Check if next referral commission release is **within current month**
                        // if ($nextReferralRelease->between($currentMonthStart, $currentMonthEnd)) {
                        if ($nextReferralRelease->lt($currentMonthStart) || $nextReferralRelease->isSameMonth($currentMonth)) {
                            if ($termDate && ($termDate->isSameMonth($currentMonth) || $termDate->lt($nextReferralRelease))) {
                                // $investment->next_referral_commission_release_date = $investment->next_referral_commission_release_date;
                                $investment->next_referral_commission_release_date = null;
                                $investment->save();
                            } else {
                                $this->createInvestorpayout(2, $currentMonthStart, $investment);
                            }
                        }
                    }

                    // ----------------------------
                    // TERMINATION PAYOUT
                    // ----------------------------
                    if ($investment->terminate_status == 1 && $investment->termination_date) {
                        // dd("test");
                        $terminationDate = Carbon::parse($investment->termination_date);

                        // Check if termination date is **within current month**
                        // if ($terminationDate->between($currentMonthStart, $currentMonthEnd)) {
                        if ($terminationDate->lt($currentMonthStart) || $terminationDate->isSameMonth($currentMonth)) {
                            // dd("test");
                            $this->createInvestorpayout(3, $currentMonthStart, $investment);
                            if ($investment->termination_outstanding != 0) {
                                $this->createInvestorpayout(4, $currentMonthStart, $investment);
                            }
                        }
                    }
                }
            });


        $this->info('Monthly profit payout records generated successfully.');
    }
    public function createInvestorpayout($payout_type, $currentMonth, $investment)
    {
        return DB::transaction(function () use ($investment, $currentMonth, $payout_type) {

            $amount = 0;

            switch ($payout_type) {
                case 1: // PROFIT
                    $amount = ($investment->profit_amount_per_interval) + ($investment->outstanding_profit);
                    $investorId = $investment->investor_id;
                    break;
                case 2: // REFERRAL
                    $referral = $investment->investmentReferral;

                    $amount = 0;
                    $investorId = null;
                    $payoutReferrenceId = null;

                    if ($referral) {
                        $amount = $referral->referral_commission_amount;
                        $investorId = $referral->investor_referror_id;
                        $payoutReferrenceId = $referral->id;


                        // Adjust based on frequency
                        // switch ($referral->referral_commission_frequency_id) {

                        //     case 1: // Full payout at once
                        //         $amount = $amount / 1; // basically unchanged
                        //         break;
                        //     case 2: // Twice
                        //         $amount = $amount / 2;
                        //         break;
                        //     case 3: // Monthly
                        //         $amount = $amount / 12;
                        //         break;
                        // }
                        switch ($referral->payment_terms_id) {

                            case 1: // end of year
                                $amount = $amount / 1;
                                break;
                            case 2: // monthly
                                $amount = $amount / 12;
                                break;
                            case 3: // on contract date
                                $amount = $amount / 1;
                                break;
                            case 4: //every two months
                                $amount = $amount / 6;
                                break;
                            case 5: // twice in an year
                                $amount = $amount / 2;
                                break;
                        }
                    }
                    break;

                case 3: // TERMINATION
                    // $amount = ($investment->investment_amount) + ($investment->outstanding_profit);
                    $amount = $investment->investment_amount;
                    $investorId = $investment->investor_id;
                    break;
                case 4: // PENDING PROFIT
                    $amount = $investment->termination_outstanding;
                    $investorId = $investment->investor_id;
                    break;
                case 5: // PENDING COMMISSION
                    $referral = $investment->investmentReferral;
                    if ($referral) {
                        $amount = $investment->termination_referral_commission_outstanding;
                        $investorId = $referral->investor_referror_id;
                        $payoutReferrenceId = $referral->id;
                    }
                    break;
            }

            return  InvestorPayout::firstOrCreate(
                [

                    'investment_id'        => $investment->id,
                    'investor_id'          => $investorId,
                    'payout_reference_id'  => $payoutReferrenceId ?? null,
                    'payout_type'          => $payout_type,
                    'payout_release_month' => $currentMonth->format('Y-m'),
                ],
                [

                    'payout_amount'  => $amount,
                    'amount_pending' => $amount,
                    'is_processed'   => 0,
                ]
            );
            // dd($data);

            // return $payout;
        });
    }
}
