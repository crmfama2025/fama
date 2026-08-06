<?php

namespace App\Exports;

use App\Models\InvestorLedger;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class WithdrawalExport implements FromCollection, WithHeadings, ShouldAutoSize
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
        $permittedCompanyIds = getUserPermittedCompanyIds(auth()->user()->id, 'investor');

        //
        $query = InvestorLedger::query()
            ->with([
                'transactionType',
                'investor',
                'company',
                'addedBy',
                'approvedBy'
            ])
            ->where('status', 1)
            ->whereIn('investor_transaction_type_id', [3, 4]);

        $query->whereHas('company', function ($q) use ($permittedCompanyIds) {
            $q->whereIn('company_id', $permittedCompanyIds);
        });

        if (!empty($this->filters['investor_id'])) {
            $query->where('investor_id', $this->filters['investor_id']);
        }

        // dd($this->filters);

        if (!empty($this->filters['search'])) {

            $search = trim($this->filters['search']);

            $query->where(function ($q) use ($search) {

                $q->where('transaction_amount', 'like', "%{$search}%")

                    ->orWhereHas('transactionType', function ($q) use ($search) {
                        $q->where('transaction_type', 'like', "%{$search}%");
                    })

                    ->orWhereHas('company', function ($q) use ($search) {
                        $q->where('company_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('addedBy', function ($q) use ($search) {
                        $q->where('first_name', 'like', '%' . $search . '%')
                            ->orWhere('last_name', 'like', '%' . $search . '%');
                    })

                    ->orWhereHas('investor', function ($q) use ($search) {
                        $q->where('investor_name', 'like', "%{$search}%");
                    });
            });
        }

        if (isset($this->filters['status']) && $this->filters['status'] !== 'all') {
            $query->where('investor_ledgers.withdrawal_status', $this->filters['status']);
        }
        $results = $query->get();

        return $results->map(function ($row, $key) {
            return [
                'S.No' => $key + 1,
                // 'ID' => $row->id,
                'Company Name' => $row->company->company_name ?? '-',
                'Investor Name' => $row->investor->investor_name ?? '-',
                'Investor Email' => $row->investor->investor_email ?? '-',
                'Investor Mobile' => $row->investor->investor_mobile ?? '-',
                'Transaction Amount' => $row->transaction_amount ?? '-',
                'Transaction Type' => $row->transactionType->transaction_type ?? '-',
                'Withdrawal Status' => match ($row->withdrawal_status) {
                    1 => 'Requested',
                    2 => 'Approved',
                    3 => 'Withdrawal Done',
                    default => '-',
                },
                'Requested Date' => $row->requested_date
                    ? Carbon::parse($row->requested_date)->format('d-m-Y')
                    : '-',
                'Duration (Days)' => $row->duration_days ?? '-',

                'Withdrawal Date' => $row->withdrawal_date
                    ? Carbon::parse($row->withdrawal_date)->format('d-m-Y')
                    : '-',

                'Created By' => $row->addedBy->first_name . ' ' . $row->addedBy->last_name ?? '-',
                'Created Date' => $row->created_at
                    ? Carbon::parse($row->created_at)->format('d-m-Y')
                    : '-',
                'Approved By' => optional($row->approvedBy)->first_name && optional($row->approvedBy)->last_name
                    ? optional($row->approvedBy)->first_name . ' ' . optional($row->approvedBy)->last_name
                    : '-',
                'Approval Remarks' => $row->approval_remarks ?? '-',

                'Approved Date' => $row->approved_date
                    ? Carbon::parse($row->approved_date)->format('d-m-Y')
                    : '-',
            ];
        });
    }
    public function headings(): array
    {
        return [
            'S.No',
            // 'ID',
            'Company Name',
            'Investor Name',
            'Investor Email',
            'Investor Mobile',
            'Transaction Amount',
            'Transaction Type',
            'Withdrawal Status',
            'Requested Date',
            'Duration (Days)',
            'Withdrawal Date',
            'Created By',
            'Created Date',
            'Approved By',
            'Approval Remarks',
            'Approved Date',
        ];
    }
}
