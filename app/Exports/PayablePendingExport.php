<?php

namespace App\Exports;

use App\Models\Contract;
use App\Models\ContractPayableClear;
use App\Models\ContractPaymentDetail;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PayablePendingExport implements FromCollection, WithHeadings
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
        // Get company IDs where user has finance.payable permission
        $permittedCompanyIds = getUserPermittedCompanyIds(auth()->id(), 'finance.payable_cheque_clearing');

        $query = ContractPaymentDetail::query()
            ->with([
                'contract',
                'contract.vendor',
                'contract.property',
                'contract.locality',
                'contract.company',
                'contract.contract_type',
                'payment_mode',
            ]);

        $query->whereHas('contract', function ($q) use ($permittedCompanyIds) {
            $q->where('contract_status', 7)
                ->whereIn('company_id', $permittedCompanyIds);
        })
            ->where('paid_status', '!=', 1);

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
            ->map(function ($payable) {
                return [
                    'Project No' => "P - " . $payable->contract->project_number,
                    'Contract Type' => $payable->contract->contract_type->contract_type,
                    'Start Date'  => $payable->contract->contract_detail->start_date,
                    'End Date'  => $payable->contract->contract_detail->end_date,
                    'Company Name' => $payable->contract->company->company_name,
                    'Vendor Name' => $payable->contract->vendor->vendor_name,
                    'Buliding' => $payable->contract->property->property_name,
                    'Locality' => $payable->contract->locality->locality_name ?? '',
                    'Payment Due' => $payable->payment_date,
                    'Payment Amount' => $payable->payment_amount,
                    'Paid Amount' => totalPaidPayable($payable->payables),
                    'Balance' => (toNumeric($payable->payment_amount) - totalPaidPayable($payable->payables)),
                    'Paid Status' => match ($payable->paid_status) {
                        0 => 'Not Paid',
                        1 => 'Fully Paid',
                        2 => 'Half Paid'
                    },
                    'Cheque Returned' => match ($payable->has_returned) {
                        0 => '-',
                        1 => 'Returned',
                    },
                    'Cheque Returned Date' => $payable->returned_date ?? '-',
                    'Cheque Returned By' => $payable->returnedBy?->first_name . ' ' . $payable->returnedBy?->last_name,
                    'Payment Mode' => $payable->payment_mode->payment_mode_name,
                    'Bank Name' => $payable->bank?->bank_name,
                    'Cheque Number' => $payable->cheque_no,
                    'Project Status' => match ($payable->contract->contract_renewal_status) {
                        0 => 'New',
                        1 => 'Renewal (' . ($payable->contract->renewal_count ?? 0) . ')',
                    },
                    'Created_at' => $payable->created_at->format('d/m/Y'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Project No',
            'Contact Type',
            'Start Date',
            'End Date',
            'Company Name',
            'Vendor Name',
            'Buliding',
            'Locality',
            'Payment Due',
            'Payment Amount',
            'Paid Amount',
            'Balance',
            'Paid Status',
            'Cheque Returned',
            'Cheque Returned Date',
            'Cheque Returned By',
            'Payment Mode',
            'Bank Name',
            'Cheque Number',
            'Project Status',
            'Created_at',
        ];
    }
}
