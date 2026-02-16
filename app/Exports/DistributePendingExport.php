<?php

namespace App\Exports;

use App\Models\InvestorPayout;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DistributePendingExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */



    public function __construct(
        protected $search = null,
        protected $filter = null,
    ) {}

    public function collection()
    {
        $nextWeek = Carbon::today()->addDays(7);
        $filters = $this->filter;

        $permittedCompanyIds = getUserPermittedCompanyIds(auth()->user()->id, 'finance.payout');

        $query = InvestorPayout::query()
            ->with([
                'investment',
                'investor',
                'investmentReferral',
            ])

            ->whereColumn('investor_payouts.payout_amount', '>', 'investor_payouts.amount_paid')
            ->where('investor_payouts.is_processed', 0)
            ->whereHas('investment', function ($q) use ($nextWeek, $filters, $permittedCompanyIds) {
                $q->whereIn('company_id', $permittedCompanyIds);
                $q->where('terminate_status', '!=', 2);

                if (empty($filters)) {
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
                                    ->whereNotNull('next_profit_release_date')
                                    ->whereDate('next_profit_release_date', '<=', $nextWeek);
                            });
                    });
                }
            });

        if (!empty($filters)) {

            // month filter
            if ($filters['month']) {
                $month = str_pad($filters['month'], 2, '0', STR_PAD_LEFT);

                $query->where('investor_payouts.payout_release_month', 'like', "%-$month");
            }

            // batch filter
            if ($filters['batch_id']) {
                $query->whereHas('investment', function ($q) use ($filters) {
                    $q->where('payout_batch_id', $filters['batch_id']);
                });
            }

            // investor filter
            if ($filters['investor_id']) {
                $query->where('investor_payouts.investor_id', $filters['investor_id']);
            }
            if (!empty($filters['investment_id'])) {
                $query->whereHas('investment', function ($q) use ($filters) {
                    $q->where('id', $filters['investment_id']);
                });
            }
        }

        $query->orderBy('investor_payouts.id');

        return $query->get()
            ->map(function ($payout) {

                $paymentModeText = '-';
                $investor = $payout->investor;

                if ($investor && $investor->paymentMode) {

                    if (in_array($investor->paymentMode->id, [1, 4])) {
                        $paymentModeText = $investor->paymentMode->payment_mode_name;
                    }

                    if ($investor->paymentMode->id == 2) {
                        $bankName = $investor->primaryBank->investor_bank_name ?? '-';
                        $paymentModeText = $investor->paymentMode->payment_mode_name . ' - ' . $bankName;
                    }
                }

                return [
                    'Investor Name' => $investor->investor_name ?? '-',
                    'Company Name' => $payout->investment?->company?->company_name ?? '-',
                    'Payout Date' => match ($payout->payout_type) {
                        1 => optional($payout->investment)->next_profit_release_date,
                        2 => optional($payout->investment)->next_referral_commission_release_date,
                        default => null,
                    },

                    'Payout Type' => match ($payout->payout_type) {
                        1 => 'Profit',
                        2 => 'Commission',
                        3 => 'Principal',
                        default => '-',
                    },

                    'Amount Pending' => number_format($payout->amount_pending, 2),

                    'Payment Mode' => $paymentModeText,

                    'Created At' => $payout->created_at->format('d/m/Y'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Investor Name',
            'Company Name',
            'Payout Date',
            'Payout Type',
            'Amount Pending',
            'Payment Mode',
            'Created At',
        ];
    }
}
