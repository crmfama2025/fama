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
        $nextWeek = Carbon::today()->addDays(7);

        $permittedCompanyIds = getUserPermittedCompanyIds(auth()->user()->id, 'finance.payout');

        $query = InvestorPayout::query()
            ->with([
                'investor:id,investor_code,investor_name,investor_mobile,payment_mode_id',
                'investment:id,investment_code,next_profit_release_date,next_referral_commission_release_date,terminate_status,termination_date,company_id'
            ])
            ->whereColumn('investor_payouts.payout_amount', '>', 'investor_payouts.amount_paid')
            ->where('investor_payouts.is_processed', 0)
            ->whereHas('investment', function ($q) use ($nextWeek, $filters, $permittedCompanyIds) {
                $q->whereIn('company_id', $permittedCompanyIds);
                // $q->where('terminate_status', '!=', 2);

                if (empty($filters['filter'])) {
                    $q->where(function ($dateQuery) use ($nextWeek) {

                        // PROFIT
                        $dateQuery->where(function ($profit) use ($nextWeek) {
                            $profit->whereNotNull('next_profit_release_date')
                                ->whereDate('next_profit_release_date', '<=', $nextWeek);
                        })

                            // COMMISSION
                            ->orWhere(function ($commission) use ($nextWeek) {
                                $commission->whereNotNull('next_referral_commission_release_date')
                                    ->whereDate('next_referral_commission_release_date', '<=', $nextWeek);
                            })

                            // 🔹 PRINCIPAL RETURN (termination requested)
                            ->orWhere(function ($principal) use ($nextWeek) {
                                $principal->where('terminate_status', 1)
                                    ->whereNotNull('termination_date')
                                    ->whereDate('termination_date', '<=', $nextWeek);
                            });
                    });
                }
            });

        if (!empty($filters['filter'])) {
            $filter = $filters['filter'];

            // Vendor filter
            if ($filter['month']) {
                $month = str_pad($filter['month'], 2, '0', STR_PAD_LEFT);

                $query->where('investor_payouts.payout_release_month', 'like', "%-$month");
            }

            // property filter
            if ($filter['batch_id']) {
                $query->whereHas('investment', function ($q) use ($filter) {
                    $q->where('payout_batch_id', $filter['batch_id']);
                });
            }

            // payment mode filter
            if ($filter['investor_id']) {
                $query->where('investor_payouts.investor_id', $filter['investor_id']);
            }
            if ($filter['investment_id']) {
                $query->whereHas('investment', function ($q) use ($filter) {
                    $q->where('id', $filter['investment_id']);
                });
            }
        }

        $query->orderBy('investor_payouts.id');


        return $query;
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
