<?php

namespace App\Repositories\Reports;

use App\Models\Investment;

use App\Models\InvestorPayout;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;


class InvestmentReportRepository
{


    public function getInvestmentQuery(array $filters = []): Builder
    {
        $permittedCompanyIds = getUserPermittedCompanyIds(
            auth()->user()->id,
            'investment'
        );

        $query = Investment::with([
            'investor',
            'payoutBatch',
            'profitInterval',
            'company',
            'investmentReferral',
            'investedCompany',
        ]);
        // dd($query->get());

        $query->whereHas('company', function ($q) use ($permittedCompanyIds) {
            $q->whereIn('company_id', $permittedCompanyIds);
        });

        // Search
        if (!empty($filters['search'])) {
            $search = $filters['search'];

            $query->where(function ($q) use ($search) {
                $q->where('investment_amount', 'like', "%{$search}%")
                    ->orWhere('investment_date', 'like', "%{$search}%")
                    ->orWhere('investment_code', 'like', "%{$search}%")
                    ->orWhere('maturity_date', 'like', "%{$search}%")
                    ->orWhere('profit_perc', 'like', "%{$search}%")
                    ->orWhere('received_amount', 'like', "%{$search}%")
                    ->orWhere('profit_release_date', 'like', "%{$search}%")
                    ->orWhere('nominee_name', 'like', "%{$search}%")
                    ->orWhere('nominee_email', 'like', "%{$search}%")
                    ->orWhere('nominee_phone', 'like', "%{$search}%")
                    ->orWhere('investment_tenure', 'like', "%{$search}%")

                    ->orWhereHas('investor', function ($q) use ($search) {
                        $q->where('investor_name', 'like', "%{$search}%");
                    })

                    ->orWhereHas('profitInterval', function ($q) use ($search) {
                        $q->where('profit_interval_name', 'like', "%{$search}%");
                    })

                    ->orWhereHas('payoutBatch', function ($q) use ($search) {
                        $q->where('batch_name', 'like', "%{$search}%");
                    })

                    ->orWhereHas('company', function ($q) use ($search) {
                        $q->where('company_name', 'like', "%{$search}%");
                    })

                    ->orWhereHas('investmentReferral', function ($q) use ($search) {
                        $q->where(
                            'referral_commission_amount',
                            'like',
                            "%{$search}%"
                        );

                        $q->whereHas('referrer', function ($qr) use ($search) {
                            $qr->where(
                                'investor_name',
                                'like',
                                "%{$search}%"
                            );
                        });
                    })

                    ->orWhereRaw(
                        "CAST(investments.id AS CHAR) LIKE ?",
                        ["%{$search}%"]
                    );
            });
        }

        // Date filter
        if (!empty($filters['maturitydate_from']) && !empty($filters['maturitydate_to'])) {

            $from = Carbon::createFromFormat(
                'd-m-Y',
                $filters['maturitydate_from']
            )->startOfDay();

            $to = Carbon::createFromFormat(
                'd-m-Y',
                $filters['maturitydate_to']
            )->endOfDay();

            $query->whereBetween('maturity_date', [
                $from,
                $to
            ]);
        }
        // dd($query->get());
        // Date filter
        if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
            $query->whereBetween('investment_date', [
                Carbon::createFromFormat(
                    'd-m-Y',
                    $filters['date_from']
                )->format('Y-m-d'),

                Carbon::createFromFormat(
                    'd-m-Y',
                    $filters['date_to']
                )->format('Y-m-d'),
            ]);
        }

        // Company
        if (!empty($filters['company_id'])) {
            $query->whereHas('company', function ($q) use ($filters) {
                $q->where('id', $filters['company_id']);
            });
        }

        // Investor
        if (!empty($filters['investor_id'])) {
            // dd("test");
            //
            $query->whereHas('investor', function ($q) use ($filters) {
                $q->where('id', $filters['investor_id']);
            });
        }

        // Investment term type
        if (!empty($filters['investment_term_type'])) {
            $query->where(
                'investment_term_type',
                $filters['investment_term_type']
            );
        }

        // Status
        if (
            isset($filters['investment_status']) &&
            $filters['investment_status'] !== ''
        ) {
            $query->where(
                'investment_status',
                $filters['investment_status']
            );
        }

        // profit Interval
        if (!empty($filters['profit_interval_id'])) {
            $query->where(
                'profit_interval_id',
                $filters['profit_interval_id']
            );
        }
        // dd($filters);


        // payout batch
        if (!empty($filters['payout_batch_id'])) {
            $query->where(
                'payout_batch_id',
                $filters['payout_batch_id']
            );
        }

        //profit perc
        if (!empty($filters['profit_perc'])) {
            $query->where(
                'profit_perc',
                $filters['profit_perc']
            );
        }


        //tenure
        if (!empty($filters['investment_tenure'])) {
            $query->where(
                'investment_tenure',
                $filters['investment_tenure']
            );
        }

        return $query;
    }
    public function getPendings(array $filters = []): Builder
    {
        $permittedCompanyIds = getUserPermittedCompanyIds(
            auth()->user()->id,
            'finance.payout'
        );

        $query = InvestorPayout::query()
            ->with([
                'investor:id,investor_code,investor_name,investor_mobile,payment_mode_id',
                'investor.paymentMode:id,payment_mode_name',
                'investor.primaryBank:id,investor_id,investor_bank_name',

                'investment:id,investment_code,next_profit_release_date,next_referral_commission_release_date,terminate_status,termination_date,company_id,investment_term_type,investment_status,investment_date',

                'investment.company:id,company_name',
                'latestPaymentDistribution:id,payout_id,amount_paid,paid_mode_id,paid_bank,paid_cheque_number,paid_date,paid_by,paid_company_id',
                'latestPaymentDistribution.paymentMode:id,payment_mode_name',
                'latestPaymentDistribution.paidBank:id,bank_name',
                'latestPaymentDistribution.paidCompany:id,company_name',
            ])
            ->whereHas('investment', function ($q) use ($permittedCompanyIds) {
                $q->whereIn('company_id', $permittedCompanyIds);
            });


        /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */

        if (!empty($filters['search'])) {

            $search = trim($filters['search']);

            $query->where(function ($q) use ($search) {

                // Investor
                $q->whereHas('investor', function ($investor) use ($search) {
                    $investor->where('investor_name', 'like', "%{$search}%")
                        ->orWhere('investor_code', 'like', "%{$search}%")
                        ->orWhere('investor_mobile', 'like', "%{$search}%");
                })

                    // Investment
                    ->orWhereHas('investment', function ($investment) use ($search) {
                        $investment->where('investment_code', 'like', "%{$search}%");
                    })

                    // Payout fields
                    ->orWhere('investor_payouts.payout_amount', 'like', "%{$search}%")
                    ->orWhere('investor_payouts.amount_paid', 'like', "%{$search}%");
            });
        }


        /*
   /*
|--------------------------------------------------------------------------
| PAYOUT DATE FROM
|--------------------------------------------------------------------------
*/

        if (!empty($filters['date_from'])) {

            $dateFrom = Carbon::createFromFormat(
                'd-m-Y',
                $filters['date_from']
            )->format('Y-m-d');

            $query->whereDate(
                'investor_payouts.payout_date',
                '>=',
                $dateFrom
            );
        }


        /*
|--------------------------------------------------------------------------
| PAYOUT DATE TO
|--------------------------------------------------------------------------
*/

        if (!empty($filters['date_to'])) {

            $dateTo = Carbon::createFromFormat(
                'd-m-Y',
                $filters['date_to']
            )->format('Y-m-d');

            $query->whereDate(
                'investor_payouts.payout_date',
                '<=',
                $dateTo
            );
        }

        /*
    |--------------------------------------------------------------------------
    | INVESTOR
    |--------------------------------------------------------------------------
    */

        if (!empty($filters['investor_id'])) {

            $query->where(
                'investor_payouts.investor_id',
                $filters['investor_id']
            );
        }


        /*
    |--------------------------------------------------------------------------
    | COMPANY
    |--------------------------------------------------------------------------
    */

        if (!empty($filters['company_id'])) {

            $query->whereHas('investment', function ($q) use ($filters) {
                $q->where('company_id', $filters['company_id']);
            });
        }


        /*
    |--------------------------------------------------------------------------
    | INVESTMENT TERM TYPE
    |--------------------------------------------------------------------------
    */

        if (
            $filters['investment_term_type'] !== null &&
            $filters['investment_term_type'] !== ''
        ) {

            $query->whereHas('investment', function ($q) use ($filters) {
                $q->where(
                    'investment_term_type',
                    $filters['investment_term_type']
                );
            });
        }


        /*
    |--------------------------------------------------------------------------
    | INVESTMENT STATUS
    |--------------------------------------------------------------------------
    */

        if (
            $filters['investment_status'] !== null &&
            $filters['investment_status'] !== ''
        ) {

            $query->whereHas('investment', function ($q) use ($filters) {
                $q->where(
                    'investment_status',
                    $filters['investment_status']
                );
            });
        }

        // month
        if ($filters['month']) {
            $month = str_pad($filters['month'], 2, '0', STR_PAD_LEFT);

            $query->where('investor_payouts.payout_release_month', 'like', "%-$month");
        }

        // payout batch
        if (!empty($filters['payout_batch_id'])) {

            $query->whereHas('investment', function ($q) use ($filters) {
                $q->where('payout_batch_id', $filters['payout_batch_id']);
            });
        }

        // payout batch
        if (!empty($filters['is_processed'])) {

            $query->where('is_processed', $filters['is_processed']);
        }



        $query->orderBy('investor_payouts.id', 'desc');

        return $query;
    }
}
