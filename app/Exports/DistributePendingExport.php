<?php

namespace App\Exports;

use App\Models\Company;
use App\Models\Investment;
use App\Models\Investor;
use App\Models\InvestorPayout;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DistributePendingExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected string $search;
    protected array $filter;
    protected array $order;

    public function __construct($search = null, $filter = [], $order = [])
    {
        $this->search = trim((string) $search);
        $this->filter = $filter ?? [];
        $this->order  = $order ?? [];
    }

    public function collection()
    {
        $filters = $this->filter;

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

        if (!empty($filters['month'])) {
            $month = (int) $filters['month'];

            if ($month >= 1 && $month <= 12) {
                $query->whereMonth('investor_payouts.original_profit_date', $month);
            }
        }

        if (!empty($filters['batch_id'])) {
            $query->whereHas('investment', function ($q) use ($filters) {
                $q->where('payout_batch_id', $filters['batch_id']);
            });
        }

        if (!empty($filters['investor_id'])) {
            $query->where('investor_payouts.investor_id', $filters['investor_id']);
        }

        if (!empty($filters['investment_id'])) {
            $query->where('investor_payouts.investment_id', $filters['investment_id']);
        }

        if (!empty($filters['company_id'])) {
            $query->whereHas('investment', function ($q) use ($filters) {
                $q->where('company_id', $filters['company_id']);
            });
        }

        if ($this->search !== '') {
            $this->applySearch($query, $this->search);
        }

        $this->applyOrdering($query);

        return $query->get()->map(function ($payout) {
            $investor   = $payout->investor;
            $investment = $payout->investment;

            return [
                'Investor Name'   => $investor->investor_name ?? '-',
                'Company Name'    => $investment?->company?->company_name ?? '-',
                'Investment Code' => $investment?->investment_code ?? '-',
                'Payout Date'     => getPayoutDate($payout),
                'Payout Type'     => $this->payoutTypeText($payout),
                'Payout Amount'   => round((float) $payout->amount_pending, 2),
                'Payment Mode'    => $this->paymentModeText($investor),
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


    protected function applySearch($query, string $search): void
    {
        $query->where(function ($q) use ($search) {

            $q->whereHas('investor', function ($iq) use ($search) {
                $iq->where('investor_name', 'like', "%{$search}%")
                    ->orWhere('investor_email', 'like', "%{$search}%")
                    ->orWhere('investor_mobile', 'like', "%{$search}%");
            });

            $q->orWhereHas('investment.company', function ($cq) use ($search) {
                $cq->where('company_name', 'like', "%{$search}%");
            });

            $q->orWhereHas('investment', function ($iq) use ($search) {
                $iq->where('investment_code', 'like', "%{$search}%");
            });

            $types = $this->payoutTypesFromKeyword($search);
            if (!empty($types)) {
                $q->orWhereIn('investor_payouts.payout_type', $types);
            }

            $numeric = str_replace(',', '', $search);
            if (is_numeric($numeric)) {
                $q->orWhere('investor_payouts.amount_pending', '=', (float) $numeric);
            }
        });
    }

    protected function payoutTypesFromKeyword(string $keyword): array
    {
        $keyword = strtolower(trim($keyword));
        $types = [];

        if (str_contains('profit', $keyword) || str_contains($keyword, 'profit')) {
            $types[] = 1;
        }
        if (str_contains('commission', $keyword) || str_contains($keyword, 'commission')) {
            $types[] = 2;
        }
        if (
            str_contains('withdrawal', $keyword) || str_contains($keyword, 'withdrawal') ||
            str_contains('settlement', $keyword) || str_contains($keyword, 'settlement')
        ) {
            $types[] = 6;
        }
        if (is_numeric($keyword)) {
            $types[] = (int) $keyword;
        }

        return array_unique($types);
    }

    protected function applyOrdering($query): void
    {
        $map = [
            1 => 'investor_name',
            2 => 'company_name',
            3 => 'investment_code',
            4 => 'payout_date',
            5 => 'payout_type',
            6 => 'payout_amount',
        ];

        $column = isset($this->order['column']) ? (int) $this->order['column'] : 4;
        $dir    = strtolower($this->order['dir'] ?? 'asc') === 'desc' ? 'desc' : 'asc';
        $key    = $map[$column] ?? 'payout_date';

        switch ($key) {
            case 'investor_name':
                $query->orderBy(
                    Investor::select('investor_name')
                        ->whereColumn('investors.id', 'investor_payouts.investor_id'),
                    $dir
                );
                break;

            case 'company_name':
                $query->orderBy(
                    Company::select('company_name')
                        ->join('investments', 'investments.company_id', '=', 'companies.id')
                        ->whereColumn('investments.id', 'investor_payouts.investment_id'),
                    $dir
                );
                break;

            case 'investment_code':
                $query->orderBy(
                    Investment::select('investment_code')
                        ->whereColumn('investments.id', 'investor_payouts.investment_id'),
                    $dir
                );
                break;

            case 'payout_type':
                $query->orderBy('investor_payouts.payout_type', $dir);
                break;

            case 'payout_amount':
                $query->orderBy('investor_payouts.amount_pending', $dir);
                break;

            case 'payout_date':
            default:
                $query->orderByRaw('investor_payouts.original_profit_date IS NULL')
                    ->orderBy('investor_payouts.original_profit_date', $dir);
                break;
        }

        $query->orderBy('investor_payouts.id', 'asc');
    }

    protected function payoutTypeText($payout): string
    {
        return match ((int) $payout->payout_type) {
            1 => 'Profit',
            2 => 'Commission',
            6 => ($payout->investment && (int) $payout->investment->terminate_status === 1)
                ? 'Settlement'
                : 'Withdrawal',
            default => '-',
        };
    }

    protected function paymentModeText($investor): string
    {
        if (!$investor || !$investor->paymentMode) {
            return '-';
        }

        $mode = $investor->paymentMode;

        if (in_array((int) $mode->id, [1, 4], true)) {
            return $mode->payment_mode_name;
        }

        if ((int) $mode->id === 2) {
            return $mode->payment_mode_name . ' - '
                . ($investor->primaryBank->investor_bank_name ?? '-');
        }

        return '-';
    }
}