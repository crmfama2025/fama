<?php

namespace App\Repositories\Reports;

use App\Models\Investment;

use App\Models\InvestorPayout;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;

class InvestmentReportRepository
{

    public function getInvestmentQuery(array $filters = []): Builder
    {
        $permittedCompanyIds = getUserPermittedCompanyIds(auth()->user()->id, 'investment');

        $query = Investment::with('investor', 'payoutBatch', 'profitInterval', 'company', 'investmentReferral', 'investedCompany');

        $query->whereHas('company', function ($q) use ($permittedCompanyIds) {
            $q->whereIn('company_id', $permittedCompanyIds);
        });


        $result = $query->get();
        // dd($result);
        if (!empty($filters['search'])) {
            $query->orWhere('investment_amount', 'like', '%' . $filters['search'] . '%')
                ->orWhere('investment_date', 'like', '%' . $filters['search'] . '%')
                ->orWhere('investment_code', 'like', '%' . $filters['search'] . '%')
                ->orWhere('maturity_date', 'like', '%' . $filters['search'] . '%')
                ->orWhere('profit_perc', 'like', '%' . $filters['search'] . '%')
                ->orWhere('received_amount', 'like', '%' . $filters['search'] . '%')
                ->orWhere('profit_release_date', 'like', '%' . $filters['search'] . '%')
                ->orWhere('nominee_name', 'like', '%' . $filters['search'] . '%')
                ->orWhere('nominee_email', 'like', '%' . $filters['search'] . '%')
                ->orWhere('nominee_phone', 'like', '%' . $filters['search'] . '%')
                ->orWhere('investment_tenure', 'like', '%' . $filters['search'] . '%')
                ->orWhereHas('investor', function ($q) use ($filters) {
                    $q->where('investor_name', 'like', '%' . $filters['search'] . '%');
                })
                ->orWhereHas('profitInterval', function ($q) use ($filters) {
                    $q->where('profit_interval_name', 'like', '%' . $filters['search'] . '%');
                })
                ->orWhereHas('payoutBatch', function ($q) use ($filters) {
                    $q->where('batch_name', 'like', '%' . $filters['search'] . '%');
                })
                ->orWhereHas('company', function ($q) use ($filters) {
                    $q->where('company_name', 'like', '%' . $filters['search'] . '%');
                })->orWhereHas('investmentReferral', function ($q) use ($filters) {
                    $q->where('referral_commission_amount', 'like', '%' . $filters['search'] . '%');
                    $q->whereHas('referrer', function ($qr) use ($filters) {
                        $qr->where('investor_name', 'like', '%' . $filters['search'] . '%');
                    });
                })
                ->orWhereRaw("CAST(investments.id AS CHAR) LIKE ?", ['%' . $filters['search'] . '%']);
        }
        // Date filter
        if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
            $query->whereBetween('investment_date', [
                Carbon::createFromFormat('d-m-Y', $filters['date_from'])->format('Y-m-d'),
                Carbon::createFromFormat('d-m-Y', $filters['date_to'])->format('Y-m-d'),
            ]);
        }
        if (!empty($filters['company_id'])) {
            $query->whereHas('company', function ($q) use ($filters) {
                $q->where('id', $filters['company_id']);
            });
        }
        if (!empty($filters['investor_id'])) {
            $query->whereHas('investor', function ($q) use ($filters) {
                $q->where('id', $filters['investor_id']);
            });
        }
        if (!empty($filters['investment_term_type'])) {
            $query->where(
                'investment_term_type',
                $filters['investment_term_type']
            );
        }
        if ($filters['investment_status'] !== null && $filters['investment_status'] !== '') {
            $query->where(
                'investment_status',
                $filters['investment_status']
            );
        }
        // if (!empty($filters['company_id'])) {
        //     $query->Where('company_id', $filters['company_id']);
        // }

        return $query;
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
}
