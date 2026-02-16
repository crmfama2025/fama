<?php

namespace App\Exports;

use App\Models\InvestorPaymentDistribution;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DistributeReportExport implements FromCollection, WithHeadings
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

        if (!empty($this->filter)) {
            $filter = $this->filter;

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

        // if ($this->search) {
        //     $search = $this->search;
        //     $query->where(function ($q) use ($search) {

        //         $q->where('project_code', 'like', "%{$search}%")
        //             ->orWhere('project_number', 'like', "%{$search}%")
        //             ->orWhereHas('company', function ($q) use ($search) {
        //                 $q->where('company_name', 'like', "%{$search}%");
        //             })
        //             ->orWhereHas('vendor', function ($q) use ($search) {
        //                 $q->where('vendor_name', 'like', "%{$search}%");
        //             })
        //             ->orWhereHas('contract_type', function ($q) use ($search) {
        //                 $q->where('contract_type', 'like', "%{$search}%");
        //             })
        //             ->orWhereHas('locality', function ($q) use ($search) {
        //                 $q->where('locality_name',  'like', "%{$search}%");
        //             })
        //             ->orWhereHas('property', function ($q) use ($search) {
        //                 $q->where('property_name',  'like', "%{$search}%");
        //             })
        //             ->orWhereRaw("CAST(contracts.id AS CHAR) LIKE ?", ["%{$search}%"]);
        //     });
        // }

        // if ($this->filter) {
        //     $query->where('contracts.id', $this->filter);
        // }
        return $query->get()
            ->map(function ($row) {

                // dd($row);

                $paymentModeText = '-';
                if ($row && $row->paymentMode) {

                    if (in_array($row->paymentMode->id, [1, 4])) {
                        $paymentModeText = $row->paymentMode->payment_mode_name;
                    }

                    if ($row->paymentMode->id == 2) {
                        $bankName = $row->primaryBank->row_bank_name ?? '-';
                        $paymentModeText = $row->paymentMode->payment_mode_name . ' - ' . $bankName;
                    }
                }

                return [
                    'Investor Name' => $row->investor->investor_name,
                    'Company Name' => $row->investment?->company?->company_name ?? '-',
                    'Paid Date' => $row->paid_date,
                    'Payout Type' => match ($row->investorPayout->payout_type) {
                        1 => 'Profit',
                        2 => 'Commission',
                        3 => 'Principal',
                        default => '-',
                    },
                    'Amount Paid' => number_format($row->amount_paid, 2),
                    'Payment Mode' => $paymentModeText,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Investor Name',
            'Company Name',
            'Paid Date',
            'Payout Type',
            'Amount Paid',
            'Payment Mode',
        ];
    }
}
