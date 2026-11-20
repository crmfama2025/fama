<?php

namespace App\Exports;

use App\Models\InvestorPaymentDistribution;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DistributeReportExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function __construct(
        protected $search = null,
        protected $filter = [],
    ) {}

    public function collection()
    {
        $filter = $this->filter ?? [];

        $permittedCompanyIds = getUserPermittedCompanyIds(auth()->user()->id, 'finance.payout');

        $query = InvestorPaymentDistribution::query()
            ->with([
                'investorPayout',
                'investor',
                'investment.company',
                'paymentMode',
                'paidBank',
            ]);

        $query->whereHas('investment', function ($q) use ($permittedCompanyIds) {
            $q->whereIn('company_id', $permittedCompanyIds);
        });

        // Dates: same defaults as the table (start of month -> today)
        $fromDate = !empty($filter['date_From']) ? $filter['date_From'] : now()->startOfMonth()->toDateString();
        $toDate   = !empty($filter['date_To'])   ? $filter['date_To']   : now()->toDateString();

        $query->whereBetween('investor_payment_distributions.paid_date', [$fromDate, $toDate]);

        if (!empty($filter['investment_id'])) {
            $query->whereHas('investorPayout.investment', function ($q) use ($filter) {
                $q->where('id', $filter['investment_id']);
            });
        }
        if (!empty($this->filters['investor_id'])) {
            $query->where('investor_payment_distributions.investor_id', $this->filters['investor_id']);
        }

        if (!empty($this->filters['company_id'])) {
            $query->whereHas('investment', function ($q) {
                $q->where('company_id', $this->filters['company_id']);
            });
        }

        if (!empty($filter['investor_id'])) {
            $query->where('investor_payment_distributions.investor_id', $filter['investor_id']);
        }

        if (!empty($filter['company_id'])) {
            $query->whereHas('investment', function ($q) use ($filter) {
                $q->where('company_id', $filter['company_id']);
            });
        }

        // Search: same rules as the table (investor + company)
        $search = trim((string) $this->search);
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->whereHas('investor', function ($iq) use ($search) {
                    $iq->where('investor_name', 'like', "%{$search}%")
                        ->orWhere('investor_email', 'like', "%{$search}%")
                        ->orWhere('investor_mobile', 'like', "%{$search}%");
                })->orWhereHas('investment.company', function ($cq) use ($search) {
                    $cq->where('company_name', 'like', "%{$search}%");
                });
            });
        }

        return $query->get()->map(function ($row) {
            $mode = $row->paymentMode;
            $paymentModeText = '-';

            if ($mode) {
                if (in_array((int) $mode->id, [1, 4], true)) {
                    $paymentModeText = $mode->payment_mode_name;
                } elseif ((int) $mode->id === 2) {
                    $paymentModeText = $mode->payment_mode_name . ' - ' . ($row->paidBank?->bank_name ?? '-');
                } elseif ((int) $mode->id === 3) {
                    $paymentModeText = $mode->payment_mode_name
                        . ' - ' . ($row->paidBank?->bank_name ?? '-')
                        . ' - ' . ($row->cheque_no ?? $row->paid_cheque_number ?? '-');
                }
            }

            return [
                'Investor Name'   => $row->investor?->investor_name ?? '-',
                'Company Name'    => $row->investment?->company?->company_name ?? '-',
                'Investment Code' => $row->investment?->investment_code ?? '-',
                'Paid Date'       => $row->paid_date,
                'Payout Type'     => match ((int) ($row->investorPayout?->payout_type)) {
                    1 => 'Profit',
                    2 => 'Commission',
                    3 => 'Principal',
                    default => '-',
                },
                'Amount Paid'     => round((float) $row->amount_paid, 2),
                'Payment Mode'    => $paymentModeText,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Investor Name',
            'Company Name',
            'Investment Code',
            'Paid Date',
            'Payout Type',
            'Amount Paid',
            'Payment Mode',
        ];
    }
}
