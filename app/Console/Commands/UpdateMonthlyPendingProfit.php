<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Investment;
use App\Models\InvestmentProfitRecord;
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
                    $termDate = $investment->termination_date
                        ? Carbon::parse($investment->termination_date)
                        : null;

                    // ----------------------------
                    // Partial withdrawal Payout
                    // ----------------------------

                    // Get partial withdrawal for current month partial  withdrawal
                    $withdrawal = DB::table('partial_withdrawal_bifurcations')
                        ->where('investment_id', $investment->id)
                        ->whereMonth('withdrawal_date', $currentMonthStart->month)
                        ->whereYear('withdrawal_date', $currentMonthStart->year)
                        ->where('payout_status', '!=', 2)
                        ->get();

                    if ($withdrawal->isNotEmpty()) {

                        foreach ($withdrawal as $wd) {

                            $exists = InvestorPayout::where('payout_type', 6)
                                ->where('investment_id', $investment->id)
                                ->where('bifurcation_id', $wd->id)
                                ->exists();

                            if (!$exists) {

                                $this->createInvestorpayout(
                                    6,
                                    $currentMonthStart,
                                    $investment,
                                    $wd->balance_to_pay,
                                    $wd->id
                                );
                            }
                        }
                    }
                    // ----------------------------
                    // PROFIT PAYOUT
                    // ----------------------------
                    if ($investment->profitInterval && $investment->next_profit_release_date) {
                        $nextProfitRelease = Carbon::parse($investment->next_profit_release_date);

                        // dd($investment);
                        // $monthsGap = 12 / $investment->profitInterval->no_of_installments;
                        // $monthsDiff = $nextProfitRelease->diffInMonths($currentMonth);

                        // if ($monthsDiff % $monthsGap !== 0) continue;
                        // if (!$nextProfitRelease->isSameMonth($currentMonth)) {
                        //     continue;
                        // }

                        // Check if next profit release is **within current month**
                        // if ($nextProfitRelease->between($currentMonthStart, $currentMonthEnd)) {
                        $payout = null;
                        $payoutMultiple = [];



                        // Get partial withdrawal for current month profit
                        $partialWithdrawal = DB::table('partial_withdrawal_bifurcations')
                            ->where('investment_id', $investment->id)
                            ->whereMonth('withdrawal_date', $currentMonthStart->month)
                            ->whereYear('withdrawal_date', $currentMonthStart->year)
                            ->where('profit_payout_status', '!=', 2)
                            ->get();



                        if ($nextProfitRelease->lt($currentMonthStart) || $nextProfitRelease->isSameMonth($currentMonth)) {
                            if ($termDate && ($termDate->isSameMonth($currentMonth) || $termDate->lt($nextProfitRelease))) {
                                // $investment->next_profit_release_date = $investment->next_profit_release_date;
                                $investment->next_profit_release_date = null;

                                $investment->save();
                            } else {
                                //  CASE 1: Partial withdrawal in current month
                                if ($partialWithdrawal->isNotEmpty()) {

                                    // 👉 Profit from bifurcation
                                    // $bifurcations = DB::table('partial_withdrawal_bifurcations')
                                    //     ->where('investment_id', $investment->id)
                                    //     ->whereMonth('withdrawal_date', $currentMonthStart->month)
                                    //     ->whereYear('withdrawal_date', $currentMonthStart->year)
                                    //     ->where('profit_payout_status', '!=', 2)
                                    //     ->get();

                                    foreach ($partialWithdrawal as $bifurcation) {

                                        $exists = InvestorPayout::where('payout_type', 1)
                                            ->where('investment_id', $investment->id)
                                            ->where('bifurcation_id', $bifurcation->id)
                                            ->exists();

                                        if (!$exists) {

                                            $payout =  $this->createInvestorpayout(
                                                1,
                                                $currentMonthStart,
                                                $investment,
                                                $bifurcation->withdrawal_month_profit,
                                                $bifurcation->id
                                            );
                                        }
                                    }
                                } else {
                                    if ($nextProfitRelease->lt($currentMonthStart)) {
                                        $profitRecords = InvestmentProfitRecord::where('investment_id', $investment->id)
                                            ->whereRaw(
                                                "DATE_FORMAT(profit_release_month, '%Y-%m') <= ?",
                                                [$currentMonth->format('Y-m')]
                                            )
                                            ->where('has_profit_amount', 1)
                                            ->get();
                                        // ->toRawSql();

                                        if ($profitRecords) {
                                            foreach ($profitRecords as $profitRecord) {
                                                $exists = InvestorPayout::where('payout_type', 1)
                                                    ->where('investment_id', $investment->id)
                                                    ->where('investment_profit_record_id', $profitRecord->id)
                                                    ->exists();

                                                if (!$exists) {
                                                    $payout =  $this->createInvestorpayout(
                                                        1,
                                                        $currentMonthStart,
                                                        $investment,
                                                        null,
                                                        null,
                                                        Carbon::parse($profitRecord->profit_release_month),
                                                        $profitRecord->id
                                                    );

                                                    if ($payout) {
                                                        $payoutMultiple[] = $payout;
                                                    }
                                                }
                                            }
                                        }
                                    } else {

                                        $profitRecord = InvestmentProfitRecord::where('investment_id', $investment->id)
                                            ->whereMonth('profit_release_month', Carbon::parse($currentMonthStart)->month)
                                            ->whereYear('profit_release_month', Carbon::parse($currentMonthStart)->year)
                                            ->first();
                                        $profitRecordId = $profitRecord ? $profitRecord->id : null;

                                        $payout = $this->createInvestorpayout(1, $currentMonthStart, $investment, null, null, $nextProfitRelease, $profitRecordId);
                                    }
                                }
                            }

                            // dd($payout);
                        }
                        if ($payout) {

                            if (!empty($payoutMultiple)) {
                                $outstandingProfit = collect($payoutMultiple)
                                    ->sum(function ($payout) {
                                        return $payout->amount_pending ?? $payout->pending_amount ?? 0;
                                    });
                            } else {
                                $outstandingProfit = $payout->amount_pending;
                            }

                            // $outstandingProfit += $payout->pending_amount;
                            $investment->update([
                                'outstanding_profit' => $outstandingProfit,
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

                                $exists = InvestorPayout::where('payout_type', 2)
                                    ->where('investment_id', $investment->id)
                                    ->where('payout_release_month', $currentMonthStart->format('Y-m'))
                                    ->exists();
                                if (!$exists) {
                                    $this->createInvestorpayout(2, $currentMonthStart, $investment);
                                }
                            }
                        }
                    }

                    // ----------------------------
                    // TERMINATION PAYOUT
                    // ----------------------------
                    // if ($investment->terminate_status == 1 && $investment->termination_date) {
                    //     // dd("test");
                    //     $terminationDate = Carbon::parse($investment->termination_date);

                    //     // Check if termination date is **within current month**
                    //     // if ($terminationDate->between($currentMonthStart, $currentMonthEnd)) {
                    //     if ($terminationDate->lt($currentMonthStart) || $terminationDate->isSameMonth($currentMonth)) {
                    //         // dd("test");
                    //         $this->createInvestorpayout(3, $currentMonthStart, $investment);
                    //         if ($investment->termination_outstanding != 0) {
                    //             $this->createInvestorpayout(4, $currentMonthStart, $investment);
                    //         }
                    //     }
                    // }
                }
            });


        $this->info('Monthly profit payout records generated successfully.');
    }
    public function createInvestorpayout(
        $payout_type,
        $currentMonth,
        $investment,
        $amountOverride = null,
        $bifurcationId = null,
        $originalDate = null,
        $profitRecordId = null
    ) {
        return DB::transaction(function () use (
            $investment,
            $currentMonth,
            $payout_type,
            $amountOverride,
            $bifurcationId,
            $originalDate,
            $profitRecordId
        ) {
            $amount = 0;
            $payoutReferrenceId = null;
            $org = $originalDate ? $originalDate->copy() : null;

            switch ($payout_type) {
                case 1: // PROFIT
                    if ($amountOverride > 0) { //partial withdrwal
                        $amount = $amountOverride;
                        $payoutReferrenceId = $bifurcationId;
                    } else { //profit payout

                        if ($originalDate->startOfMonth() != $currentMonth) {
                            $amount = $investment->profit_amount_per_interval;
                        } else {
                            $amount = ($investment->profit_amount_per_interval) + ($investment->outstanding_profit);
                        }
                    }
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
                // case 4: // PENDING PROFIT
                //     $amount = $investment->termination_outstanding;
                //     $investorId = $investment->investor_id;
                //     break;
                // case 5: // PENDING COMMISSION
                //     $referral = $investment->investmentReferral;
                //     if ($referral) {
                //         $amount = $investment->termination_referral_commission_outstanding;
                //         $investorId = $referral->investor_referror_id;
                //         $payoutReferrenceId = $referral->id;
                //     }
                //     break;
                case 6: // Partial Withdrawal
                    $amount = $amountOverride ?? 0;
                    $payoutReferrenceId = $bifurcationId;
                    $investorId = $investment->investor_id;
                    break;
            }

            return  InvestorPayout::firstOrCreate(
                [

                    'investment_id'        => $investment->id,
                    'investor_id'          => $investorId,
                    'payout_reference_id'  => $payoutReferrenceId ?? null,
                    'investment_profit_record_id' => $profitRecordId ?? null,
                    'bifurcation_id'       => $bifurcationId,
                    'payout_type'          => $payout_type,
                    'payout_release_month' => $currentMonth->format('Y-m'),
                    'original_profit_date' => $org->format('Y-m-d')
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
