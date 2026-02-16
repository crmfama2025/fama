<?php

namespace App\Exports;

use App\Models\Investment;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InvestmentExport implements FromCollection, WithHeadings, ShouldAutoSize
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
        $permittedCompanyIds = getUserPermittedCompanyIds(auth()->user()->id, 'investment');

        //
        $query = Investment::with('investor', 'payoutBatch', 'profitInterval', 'company', 'investmentReferral');

        $query->whereHas('company', function ($q) use ($permittedCompanyIds) {
            $q->whereIn('company_id', $permittedCompanyIds);
        });

        if (!empty($filters['investor_id'])) {
            $query->where('investor_id', $filters['investor_id']);
        }
        // dd($result);
        if (!empty($filters['search'])) {
            $query->orWhere('investment_amount', 'like', '%' . $filters['search'] . '%')
                ->orWhere('investment_date', 'like', '%' . $filters['search'] . '%')
                ->orWhere('maturity_date', 'like', '%' . $filters['search'] . '%')
                ->orWhere('profit_perc', 'like', '%' . $filters['search'] . '%')
                ->orWhere('received_amount', 'like', '%' . $filters['search'] . '%')
                ->orWhere('profit_release_date', 'like', '%' . $filters['search'] . '%')
                ->orWhere('nominee_name', 'like', '%' . $filters['search'] . '%')
                ->orWhere('nominee_email', 'like', '%' . $filters['search'] . '%')
                ->orWhere('nominee_phone', 'like', '%' . $filters['search'] . '%')
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
        $results = $query->get();

        return $results->map(function ($row) {
            return [
                'ID' => $row->id,
                'Company Name' => $row->company->company_name ?? '-',
                'Investor Name' => $row->investor->investor_name ?? '-',
                'Investor Email' => $row->investor->investor_email ?? '-',
                'Investor Mobile' => $row->investor->investor_mobile ?? '-',
                'Investment Type' => $row->getType(),
                'Investment Amount' => $row->investment_amount ?? '-',
                'Received Amount' => $row->total_received_amount ?? '-',
                'Payout Batch' => 'Batch -' . $row->payoutBatch->id . '(' . $row->payoutBatch->batch_name . ')' ?? '-',
                'Investment Date' => $row->investment_date ? Carbon::parse($row->investment_date)->format('d-m-Y') : '-',
                'Maturity Date' => $row->maturity_date ? Carbon::parse($row->maturity_date)->format('d-m-Y') : '-',
                'Profit Percentage' => $row->profit_perc ?? '-',
                'Profit Release Date' => $row->profit_release_date ?? '-',
                'Profit Release Frequency' => $row->profitInterval->profit_interval_name ?? '-',
                'Nominee Name' => $row->nominee_name ?? '-',
                'Nominee Email' => $row->nominee_email ?? '-',
                'Nominee Phone' => "'" . ($row->nominee_phone ?? '-'),
                'Referral Name' => $row->investmentReferral->referrer->investor_name ?? '-',
                'Referral Commission Amount' => $row->investmentReferral->referral_commission_amount ?? '-',
                'Referral Commission %' => $row->investmentReferral->referral_commission_perc ?? '-',
                'Referral Commission Frequncy' => $row->investmentReferral->commissionFrequency->commission_frequency_name ?? '-',
                'Payment Terms' => $row->investmentReferral->paymentTerm->term_name ?? '-',

            ];
        });
    }
    public function headings(): array
    {
        return [
            'ID',
            'Company Name',
            'Investor Name',
            'Investor Email',
            'Investor Mobile',
            'Investment Type',
            'Investment Amount',
            'Received Amount',
            'Payout Batch',
            'Investment Date',
            'Maturity Date',
            'Profit Percentage',
            'Profit Release Date',
            'Profit Release Frequency',
            'Nominee Name',
            'Nominee Email',
            'Nominee Phone',
            'Referral Name',
            'Referral Commission Amount',
            'Referral Commission %',
            'Referral Commission Frequncy',
            'Payment Terms'
        ];
    }
}
