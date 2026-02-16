<?php

namespace App\Exports;

use App\Models\Contract;
use App\Models\ContractPayableClear;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PayableExport implements FromCollection, WithHeadings
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

        $query = ContractPayableClear::query()
            ->with([
                'contractPaymentDetail',
                'contractPaymentDetail.contract',
                'contractPaymentDetail.contract.vendor',
                'contractPaymentDetail.contract.property',
                'contractPaymentDetail.contract.locality',
                'contractPaymentDetail.contract.company',
                'contractPaymentDetail.contract.contract_type',
                'paidMode',
                'paidBank'
            ]);

        $query->whereHas('company', function ($q) use ($permittedCompanyIds) {
            $q->whereIn('company_id', $permittedCompanyIds);
        });

        if ($this->search) {
            $search = trim($this->search);
            $searchLike = str_replace('-', '%', $search);

            $query->where(function ($q) use ($search, $searchLike) {
                $q->whereRaw('paid_date LIKE ?', ["%{$searchLike}%"])
                    ->orWhereRaw("CAST(paid_amount AS CHAR) LIKE ?", ["%{$search}%"])
                    ->orWhereRaw("CAST(pending_amount AS CHAR) LIKE ?", ["%{$search}%"])

                    ->orWhereHas('contractPaymentDetail', function ($q) use ($search) {
                        $q->where('payment_date', 'like', "%{$search}%");
                    })
                    ->orWhereHas('contractPaymentDetail.contract', function ($q) use ($search) {
                        $q->where('project_number', 'like', "%{$search}%")
                            ->orWhere('project_code', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('contractPaymentDetail.contract.company', function ($q) use ($search) {
                        $q->where('company_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('contractPaymentDetail.contract.vendor', function ($q) use ($search) {
                        $q->where('vendor_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('contractPaymentDetail.contract.contract_type', function ($q) use ($search) {
                        $q->where('contract_type', 'like', "%{$search}%");
                    })
                    ->orWhereHas('contractPaymentDetail.contract.locality', function ($q) use ($search) {
                        $q->where('locality_name',  'like', "%{$search}%");
                    })
                    ->orWhereHas('contractPaymentDetail.contract.property', function ($q) use ($search) {
                        $q->where('property_name',  'like', "%{$search}%");
                    })
                    ->orWhereHas('paidMode', function ($q) use ($search) {
                        $q->where('payment_mode_name',  'like', "%{$search}%");
                    });
            });
        }

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
                    'Project No' => "P - " . $payable->contractPaymentDetail->contract->project_number,
                    'Contract Type' => $payable->contractPaymentDetail->contract->contract_type->contract_type,
                    'Start Date'  => $payable->contractPaymentDetail->contract->contract_detail->start_date,
                    'End Date'  => $payable->contractPaymentDetail->contract->contract_detail->end_date,
                    'Company Name' => $payable->contractPaymentDetail->contract->company->company_name,
                    'Vendor Name' => $payable->contractPaymentDetail->contract->vendor->vendor_name,
                    'Buliding' => $payable->contractPaymentDetail->contract->property->property_name,
                    'Locality' => $payable->contractPaymentDetail->contract->locality->locality_name ?? '',
                    'Payment Due' => $payable->contractPaymentDetail->payment_date,
                    'Clear Date' => $payable->paid_date,
                    'Amount Paid' => $payable->paid_amount,
                    'Pending Amount' => $payable->pending_amount,
                    'Payment Mode' => $payable->paidMode?->payment_mode_name,
                    'Bank Name' => $payable->paidBank?->bank_name,
                    'Cheque Number' => $payable->paid_cheque_number,
                    'Payable Cleared By' => $payable->paidBy?->first_name . ' ' . $payable->paidBy?->last_name,
                    'Project Status' => match ($payable->contractPaymentDetail->contract->contract_renewal_status) {
                        0 => 'New',
                        1 => 'Renewal (' . ($payable->contractPaymentDetail->contract->renewal_count ?? 0) . ')',
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
            'Clear Date',
            'Amount Paid',
            'Pending Amount',
            'Payment Mode',
            'Bank Name',
            'Cheque Number',
            'Payable Cleared By',
            'Project Status',
            'Created_at',
        ];
    }
}
