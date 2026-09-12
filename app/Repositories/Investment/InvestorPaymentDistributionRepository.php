<?php

namespace App\Repositories\Investment;

use App\Models\Investment;
use App\Models\InvestorPaymentDistribution;
use App\Models\InvestorPayout;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class InvestorPaymentDistributionRepository
{
    public function all()
    {
        return InvestorPaymentDistribution::all();
    }

    public function find($id)
    {
        return InvestorPaymentDistribution::findOrFail($id);
    }


    public function create($data)
    {
        return InvestorPaymentDistribution::create($data);
    }
    public function update(int $id, array $data)
    {
        $investmentDocument = InvestorPaymentDistribution::findOrFail($id);
        return $investmentDocument->update($data);
    }

    public function createMany(array $dataArray)
    {
        $detId = [];
        foreach ($dataArray as $data) {
            $detId[] = InvestorPaymentDistribution::create($data);
        }
        return  $detId;
    }

    public function updateMany(array $data)
    {
        $detId = [];
        foreach ($data as $key => $value) {
            $paymentdet = $this->find($key);
            $paymentdet->update($value);

            $detId[] = $key;
        }
        return  $detId;
    }

    public function getPendings(array $filters = []): Builder
    {
        $nextWeek = Carbon::today()
            ->addDays(7)
            ->endOfDay();

        $permittedCompanyIds = getUserPermittedCompanyIds(
            auth()->id(),
            'finance.payout'
        );

        $filter = $filters['filter'] ?? [];

        $query = InvestorPayout::query()
            ->with([
                'investor:id,investor_code,investor_name,investor_mobile,payment_mode_id',

                'investment:id,investment_code,next_profit_release_date,next_referral_commission_release_date,terminate_status,termination_date,company_id,payout_batch_id',
            ])
            ->whereColumn(
                'investor_payouts.payout_amount',
                '>',
                'investor_payouts.amount_paid'
            )
            ->where('investor_payouts.is_processed', 0)

            /*
         * Use the investment relationship only for company access.
         * Do not filter all payout rows using the investment's latest
         * next-profit date.
         */
            ->whereHas('investment', function ($query) use (
                $permittedCompanyIds
            ) {
                $query->whereIn(
                    'company_id',
                    $permittedCompanyIds
                );
            });

        if (empty($filter)) {
            $query->where(function ($query) use ($nextWeek) {
                /*
             * Each payout is checked against its own release date.
             * This keeps older unpaid payouts visible even when the
             * investment's next release date has advanced.
             */
                $query
                    ->whereNotNull(
                        'investor_payouts.payout_release_month'
                    )
                    ->whereDate(
                        'investor_payouts.payout_release_month',
                        '<=',
                        $nextWeek->toDateString()
                    );
            });
        }

        if (!empty($filter['month'])) {
            $query->whereMonth(
                'investor_payouts.payout_release_month',
                (int) $filter['month']
            );
        }

        if (!empty($filter['batch_id'])) {
            $query->whereHas(
                'investment',
                function ($query) use ($filter) {
                    $query->where(
                        'payout_batch_id',
                        $filter['batch_id']
                    );
                }
            );
        }

        if (!empty($filter['investor_id'])) {
            $query->where(
                'investor_payouts.investor_id',
                $filter['investor_id']
            );
        }

        if (!empty($filter['investment_id'])) {
            $query->where(
                'investor_payouts.investment_id',
                $filter['investment_id']
            );
        }

        return $query->orderBy(
            'investor_payouts.payout_release_month'
        );
    }

    public function getDistributedList(array $filters = []): Builder
    {

        $fromDate = now()->startOfMonth()->toDateString();
        $todate = now()->toDateString();

        $permittedCompanyIds = getUserPermittedCompanyIds(auth()->user()->id, 'finance.payout');

        $query = InvestorPaymentDistribution::query()
            ->with([
                'investorPayout',
                'Investor',
                'investorPayout.investment',
                'investorPayout.investmentReferral',
                'paymentMode',
                'paidBank'
            ]);

        $query->whereHas('investment', function ($q) use ($permittedCompanyIds) {
            $q->whereIn('company_id', $permittedCompanyIds);
        });


        if (!empty($filters['filter'])) {
            $filter = $filters['filter'];


            if (!empty($filter['date_From'])) {
                $fromDate = $filter['date_From'];
            }

            if (!empty($filter['date_To'])) {
                $todate = $filter['date_To'];
            }
            if (!empty($filter['investment_id'])) {
                $query->whereHas('investorPayout.investment', function ($q) use ($filter) {
                    $q->where('id', $filter['investment_id']);
                });
            }
        }

        $query->whereBetween('investor_payment_distributions.paid_date', [
            $fromDate,
            $todate
        ]);


        return $query;
    }
}
