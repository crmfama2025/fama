<?php

namespace App\Exports;

use App\Models\InvestorPayout;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DistributePendingExport implements FromCollection, WithHeadings
{
    protected $search;
    protected $filter;

    public function __construct($search = null, $filter = [])
    {
        $this->search = trim((string) $search);
        $this->filter = $filter ?? [];
    }

    public function collection()
    {
        $filters = $this->filter;

        $nextWeek = Carbon::today()->addDays(7)->endOfDay();

        $permittedCompanyIds = getUserPermittedCompanyIds(
            auth()->id(),
            'finance.payout'
        );

        $query = InvestorPayout::query()
            ->with([
                'investor.paymentMode',
                'investor.primaryBank',
                'investment.company',
            ])
            ->whereColumn(
                'investor_payouts.payout_amount',
                '>',
                'investor_payouts.amount_paid'
            )
            ->where('investor_payouts.is_processed', 0)
            ->whereHas('investment', function ($q) use ($permittedCompanyIds) {
                $q->whereIn('company_id', $permittedCompanyIds);
            });

        /*
         * Apply the same default visibility rule as the pending
         * payout listing when no dropdown filters are selected.
         *
         * This uses the investment's next release dates rather
         * than treating payout_release_month as a date.
         */
        if (empty($filters)) {
            $query->whereHas('investment', function ($q) use ($nextWeek) {
                $q->where('terminate_status', '!=', 2)
                    ->where(function ($dateQuery) use ($nextWeek) {
                        $dateQuery
                            ->where(function ($profit) use ($nextWeek) {
                                $profit->whereNotNull(
                                    'next_profit_release_date'
                                )->whereDate(
                                    'next_profit_release_date',
                                    '<=',
                                    $nextWeek->toDateString()
                                );
                            })
                            ->orWhere(function ($commission) use ($nextWeek) {
                                $commission->whereNotNull(
                                    'next_referral_commission_release_date'
                                )->whereDate(
                                    'next_referral_commission_release_date',
                                    '<=',
                                    $nextWeek->toDateString()
                                );
                            })
                            ->orWhere(function ($principal) use ($nextWeek) {
                                $principal->where('terminate_status', 1)
                                    ->whereNotNull(
                                        'next_profit_release_date'
                                    )
                                    ->whereDate(
                                        'next_profit_release_date',
                                        '<=',
                                        $nextWeek->toDateString()
                                    );
                            });
                    });
            });
        }

        // Month filter: original profit date.
        if (!empty($filters['month'])) {
            $month = (int) $filters['month'];

            if ($month >= 1 && $month <= 12) {
                $query->whereMonth(
                    'investor_payouts.original_profit_date',
                    $month
                );
            }
        }

        // Batch filter.
        if (!empty($filters['batch_id'])) {
            $query->whereHas('investment', function ($q) use ($filters) {
                $q->where(
                    'payout_batch_id',
                    $filters['batch_id']
                );
            });
        }

        // Investor filter.
        if (!empty($filters['investor_id'])) {
            $query->where(
                'investor_payouts.investor_id',
                $filters['investor_id']
            );
        }

        // Investment filter.
        if (!empty($filters['investment_id'])) {
            $query->where(
                'investor_payouts.investment_id',
                $filters['investment_id']
            );
        }

        // Company filter.
        if (!empty($filters['company_id'])) {
            $query->whereHas('investment', function ($q) use ($filters) {
                $q->where('company_id', $filters['company_id']);
            });
        }

        // Global DataTable search.
        if ($this->search !== '') {
            $search = $this->search;

            $query->where(function ($q) use ($search) {
                $q->whereHas('investor', function ($investorQuery) use ($search) {
                    $investorQuery
                        ->where('investor_name', 'like', "%{$search}%")
                        ->orWhere('investor_email', 'like', "%{$search}%")
                        ->orWhere('investor_mobile', 'like', "%{$search}%");
                })
                ->orWhereHas('investment', function ($investmentQuery) use ($search) {
                    $investmentQuery
                        ->where('investment_code', 'like', "%{$search}%")
                        ->orWhereHas('company', function ($companyQuery) use ($search) {
                            $companyQuery->where(
                                'company_name',
                                'like',
                                "%{$search}%"
                            );
                        });
                });

                // Search the numeric pending amount.
                $numericSearch = str_replace(',', '', $search);

                if (is_numeric($numericSearch)) {
                    $q->orWhere(
                        'investor_payouts.amount_pending',
                        '=',
                        (float) $numericSearch
                    );
                }
            });
        }

        $payouts = $query
            ->orderBy('investor_payouts.id')
            ->get();

        return $payouts->map(function ($payout) {
            $investor = $payout->investor;
            $investment = $payout->investment;
            $paymentModeText = '-';

            if ($investor && $investor->paymentMode) {
                $mode = $investor->paymentMode;

                if (in_array((int) $mode->id, [1, 4], true)) {
                    $paymentModeText = $mode->payment_mode_name;
                } elseif ((int) $mode->id === 2) {
                    $bankName = $investor->primaryBank
                        ->investor_bank_name ?? '-';

                    $paymentModeText =
                        $mode->payment_mode_name . ' - ' . $bankName;
                }
            }

            return [
                'Investor Name' => $investor->investor_name ?? '-',

                'Company Name' =>
                    $investment?->company?->company_name ?? '-',

                'Investment Code' =>
                    $investment?->investment_code ?? '-',

                'Payout Date' => getPayoutDate($payout),

                'Payout Type' => match ((int) $payout->payout_type) {
                    1 => 'Profit',
                    2 => 'Commission',
                    6 => (
                        $investment &&
                        (int) $investment->terminate_status === 1
                    ) ? 'Settlement' : 'Withdrawal',
                    default => '-',
                },

                'Payout Amount' => number_format(
                    (float) $payout->amount_pending,
                    2
                ),

                'Payment Mode' => $paymentModeText,

                'Created At' => $payout->created_at
                    ? $payout->created_at->format('d/m/Y')
                    : '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Investor Name',
            'Company Name',
            'Investment Code',
            'Payout Date',
            'Payout Type',
            'Payout Amount',
            'Payment Mode',
            'Created At',
        ];
    }
}
