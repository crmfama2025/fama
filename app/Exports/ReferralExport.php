<?php

namespace App\Exports;

use App\Models\InvestmentReferral;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReferralExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $search;
    protected $filters;

    public function __construct($search = null, array $filters = [])
    {
        $this->search = $search;
        $this->filters = $filters;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        //

        $query = InvestmentReferral::with('referrer', 'investment', 'commissionFrequency', 'investor');

        // dd($result);
        // Global search
        if (!empty($this->search)) {

            $search = $this->search;

            $query->where(function ($q) use ($search) {
                $q->where('referral_commission_perc', 'like', "%{$search}%")
                    ->orWhere('referral_commission_amount', 'like', "%{$search}%")

                    ->orWhereHas('investor', function ($q) use ($search) {
                        $q->where('investor_name', 'like', "%{$search}%");
                    })

                    ->orWhereHas('referrer', function ($q) use ($search) {
                        $q->where('investor_name', 'like', "%{$search}%");
                    })

                    ->orWhereHas('commissionFrequency', function ($q) use ($search) {
                        $q->where('commission_frequency_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('investment', function ($q) use ($search) {

                        // $search = $filters['search'];

                        try {
                            $date = \Carbon\Carbon::createFromFormat('d-m-Y', $search)->format('Y-m-d');

                            $q->whereDate('investment_date', $date);
                        } catch (\Exception $e) {
                            $q->where('investment_date', 'like', '%' . $search . '%');
                        }
                    })

                    ->orWhereRaw("CAST(investment_referrals.id AS CHAR) LIKE ?", ["%{$search}%"]);
            });
        }
        $results = $query->get();

        return $results->map(function ($row) {
            return [
                'ID' => $row->id,
                'Referrer Name' => $row->referrer->investor_name ?? '-',
                'Date of Referral' => $row->investment->investment_date ? Carbon::parse($row->investment->investment_date)->format('d-m-Y') : '-',
                'Commission Percentage' => $row->referral_commission_perc . ' % ' ?? '-',
                'Referral Commission Amount' => $row->referral_commission_amount ?? '-',
                'Frequency' => $row->commissionFrequency->commission_frequency_name,
                // 'Investment Amount' => $row->investment_amount ?? '-',
                'Referred Investor Name' => $row->investor->investor_name ?? '-',
                'Referred Investment Amount' => $row->investment->investment_amount ?? '-',

            ];
        });
    }
    public function headings(): array
    {
        return [
            'ID',
            'Referrer Name',
            'Date of Referral',
            'Commission Percentage',
            'Referral Commission Amount',
            'Frequency',
            'Referred Investor Name',
            'Referred Investment Amount',


        ];
    }
}
