<?php

namespace App\Exports;

use App\Models\Contract;
use App\Models\ContractPayableClear;
use App\Models\Investor;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InvestorExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */



    public function __construct(
        protected $search = null,
    ) {}

    public function collection()
    {
        $query = Investor::query()
            ->with([
                'nationality',
                'paymentMode',
                'countryOfResidence',
                'payoutBatch',
                'referral',
                'investorBanks'
            ]);


        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $searchLike = str_replace('-', '%', $search);

            $query->where(function ($q) use ($search, $searchLike) {
                $q->where('investor_name LIKE ?', '%' . $search . '%')
                    ->orWhere("investor_mobile  LIKE ?", '%' . $search . '%')
                    ->orWhere('investor_email  LIKE ?', '%' . $search . '%')
                    ->orWhere('id_number LIKE ?', '%' . $search . '%')
                    ->orWhere('passport_number LIKE ?', '%' . $search . '%')
                    ->orWhereRaw('profit_release_date  LIKE ?', '%' . $searchLike . '%')

                    ->orWhereHas('nationality', function ($q) use ($search) {
                        $q->orwhere('nationality_name', 'like', '%' . $search . '%');
                    })

                    ->orWhereHas('payoutBatch', function ($q) use ($search) {
                        $q->where('batch_name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('paymentMode', function ($q) use ($search) {
                        $q->where('payment_mode_name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('investorBanks', function ($q) use ($search) {
                        $q->where('investor_beneficiary', 'like', '%' . $search . '%')
                            ->where('investor_bank_name', 'like', '%' . $search . '%')
                            ->where('investor_iban', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('referral', function ($q) use ($search) {
                        $q->where('investor_name', 'like', '%' . $search . '%');
                    });
            });

            $query = '';
        }

        return $query->get()
            ->map(function ($investor) {
                return [
                    'Investor Name' => $investor->investor_name,
                    'Investor Mobile' => $investor->investor_mobile,
                    'Investor Email' => $investor->investor_email,
                    'Id Number' => $investor->id_number,
                    'Passport Number' => $investor->passport_number,
                    'Profit Release Date' => $investor->profit_release_date,
                    'Nationality Name' => $investor->nationality?->nationality_name,
                    'Country of Residence' => $investor->countryOfResidence->nationality_name,
                    'Batch Name' => 'Batch ' . $investor->payout_batch_id . ' (' . $investor->payoutBatch->batch_name . ')',
                    'Payment Mode' => $investor->paymentMode->payment_mode_name,
                    'Investor Beneficiary' => $investor->primaryBank->investor_beneficiary,
                    'Investor Bank Name' => $investor->primaryBank->investor_bank_name,
                    'Investor IBAN' => $investor->primaryBank->investor_iban,
                    'Referral' => $investor->referral?->investor_name,
                    'Total no of Investments' => $investor->total_no_of_investments,
                    'Total Invested Amount' => $investor->total_invested_amount,
                    'Total Profit Released' => $investor->total_profit_received,
                    // 'Current Month Pending Profit' => currMonthProfit('current_month_pending', $investor->id),
                    'Current Month Released Profit' => currMonthProfit('current_month_released', $investor->id),
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
            // 'Current Month Pending Profit',
            'Current Month Released Profit',
            'Total Commission Amount',
            'Total Commission received',
        ];
    }
}
