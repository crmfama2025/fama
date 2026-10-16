<?php

namespace App\Exports;

use App\Models\Investor;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InvestorExport implements FromCollection, WithHeadings
{
    protected ?string $search;
    protected string $orderColumn;
    protected string $orderDirection;

    public function __construct(
        ?string $search = null,
        string $orderColumn = 'id',
        string $orderDirection = 'desc'
    ) {
        $this->search = $search;
        $this->orderColumn = $orderColumn;
        $this->orderDirection =
            strtolower($orderDirection) === 'asc' ? 'asc' : 'desc';
    }

    public function collection()
    {
        $query = Investor::query()->with([
            'nationality',
            'paymentMode',
            'countryOfResidence',
            'payoutBatch',
            'referral',
            'investorBanks',
        ]);

        $search = trim((string) $this->search);

        if ($search !== '') {
            $searchLike = str_replace('-', '%', $search);

            $query->where(function ($q) use ($search, $searchLike) {
                $q->where('investor_name', 'like', "%{$search}%")
                    ->orWhere('investor_code', 'like', "%{$search}%")
                    ->orWhereRaw(
                        'CAST(investor_mobile AS CHAR) LIKE ?',
                        ["%{$searchLike}%"]
                    )
                    ->orWhere('investor_email', 'like', "%{$search}%")
                    ->orWhere('id_number', 'like', "%{$search}%")
                    ->orWhere('passport_number', 'like', "%{$search}%")
                    ->orWhereRaw(
                        'DATE_FORMAT(profit_release_date, "%Y-%m-%d") LIKE ?',
                        ["%{$searchLike}%"]
                    )
                    ->orWhereHas('nationality', function ($related) use ($search) {
                        $related->where(
                            'nationality_name',
                            'like',
                            "%{$search}%"
                        );
                    })
                    ->orWhereHas('countryOfResidence', function ($related) use ($search) {
                        $related->where(
                            'nationality_name',
                            'like',
                            "%{$search}%"
                        );
                    })
                    ->orWhereHas('payoutBatch', function ($related) use ($search) {
                        $related->where('batch_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('paymentMode', function ($related) use ($search) {
                        $related->where(
                            'payment_mode_name',
                            'like',
                            "%{$search}%"
                        );
                    })
                    ->orWhereHas('investorBanks', function ($related) use ($search) {
                        $related->where(function ($bank) use ($search) {
                            $bank->where(
                                'investor_beneficiary',
                                'like',
                                "%{$search}%"
                            )->orWhere(
                                'investor_bank_name',
                                'like',
                                "%{$search}%"
                            )->orWhere(
                                'investor_iban',
                                'like',
                                "%{$search}%"
                            );
                        });
                    })
                    ->orWhereHas('referral', function ($related) use ($search) {
                        $related->where(
                            'investor_name',
                            'like',
                            "%{$search}%"
                        );
                    });
            });
        }

        $allowedColumns = [
            'id',
            'investor_name',
            'investor_code',
            'investor_mobile',
            'investor_email',
            'investor_address',
            'id_number',
            'profit_release_date',
        ];

        $column = in_array($this->orderColumn, $allowedColumns, true)
            ? $this->orderColumn
            : 'id';

        $query->orderBy($column, $this->orderDirection);

        return $query->get()->map(function ($investor) {
            $bank = $investor->primaryBank;

            return [
                'Investor Name' => $investor->investor_name,
                'Investor Mobile' => $investor->investor_mobile,
                'Investor Email' => $investor->investor_email,
                'Id Number' => $investor->id_number,
                'Passport Number' => $investor->passport_number,
                'Profit Release Date' => $investor->profit_release_date,
                'Nationality Name' => $investor->nationality?->nationality_name,
                'Country of Residence' => $investor->countryOfResidence?->nationality_name,
                'Batch Name' => $investor->payoutBatch
                    ? 'Batch ' . $investor->payout_batch_id .
                        ' (' . $investor->payoutBatch->batch_name . ')'
                    : '',
                'Payment Mode' => $investor->paymentMode?->payment_mode_name,
                'Investor Beneficiary' => $bank?->investor_beneficiary,
                'Investor Bank Name' => $bank?->investor_bank_name,
                'Investor IBAN' => $bank?->investor_iban,
                'Referral' => $investor->referral?->investor_name,
                'Total no of Investments' => $investor->total_no_of_investments,
                'Total Invested Amount' => $investor->total_invested_amount,
                'Total Profit Released' => $investor->total_profit_received,
                'Current Month Released Profit' => currMonthProfit(
                    'current_month_released',
                    $investor->id
                ),
                'Total Commission Amount' => $investor->total_referal_commission,
                'Total Commission Received' => $investor->total_referal_commission_received,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Investor Name',
            'Investor Mobile',
            'Investor Email',
            'Id Number',
            'Passport Number',
            'Profit Release Date',
            'Nationality Name',
            'Country of Residence',
            'Batch Name',
            'Payment Mode',
            'Investor Beneficiary',
            'Investor Bank Name',
            'Investor IBAN',
            'Referral',
            'Total no of Investments',
            'Total Invested Amount',
            'Total Profit Released',
            'Current Month Released Profit',
            'Total Commission Amount',
            'Total Commission received',
        ];
    }
}
