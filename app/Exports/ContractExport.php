<?php

namespace App\Exports;

use App\Models\Contract;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class ContractExport implements FromCollection, WithHeadings, WithColumnFormatting
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
        $query = Contract::with('company', 'vendor', 'contract_type',);
        $filters = $this->filter;

        if ($this->search) {
            $search = $this->search;
            $searchLike = str_replace('-', '%', $search);

            $query->where(function ($q) use ($search, $searchLike) {

                $q->where('project_code', 'like', "%{$search}%")
                    ->orWhere('project_number', 'like', "%{$search}%")
                    ->orWhereHas('company', function ($q) use ($search) {
                        $q->where('company_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('vendor', function ($q) use ($search) {
                        $q->where('vendor_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('contract_type', function ($q) use ($search) {
                        $q->where('contract_type', 'like', "%{$search}%");
                    })
                    ->orWhereHas('locality', function ($q) use ($search) {
                        $q->where('locality_name',  'like', "%{$search}%");
                    })
                    ->orWhereHas('property', function ($q) use ($search) {
                        $q->where('property_name',  'like', "%{$search}%");
                    })
                    ->orWhereHas('contract_type', function ($q) use ($search) {
                        $q->where('contract_type', 'like', '%' . $search . '%')
                            ->orWhere('shortcode', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('contract_detail', function ($q) use ($search, $searchLike) {
                        $q->where('start_date', 'like', "%{$searchLike}%")
                            ->orWhere('end_date', 'like', "%{$searchLike}%");
                    })
                    ->orWhereHas('contract_unit', function ($q) use ($search) {
                        $q->whereRaw("
                    CASE
                        WHEN business_type = 1 THEN 'B2B'
                        WHEN business_type = 2 THEN 'B2C'
                    END LIKE ?
                ", ['%' . $search . '%']);
                    })
                    ->orWhereHas('contract_rentals', function ($q) use ($search) {
                        $q->where('roi_perc', 'like', '%' . $search . '%')
                            ->orWhere('expected_profit', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('locality', function ($q) use ($search) {
                        $q->where('locality_name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('property', function ($q) use ($search) {
                        $q->where('property_name', 'like', '%' . $search . '%');
                    })
                    ->orWhereRaw("
                    CASE
                        WHEN contract_status = 0 THEN 'Pending'
                        WHEN contract_status = 1 THEN 'Processing'
                        WHEN contract_status = 2 THEN 'Approved'
                        WHEN contract_status = 3 THEN 'Rejected'
                        WHEN contract_status = 4 THEN 'Approval Pending'
                        WHEN contract_status = 5 THEN 'Approval on Hold'
                        WHEN contract_status = 6 THEN 'Partially Signed'
                        WHEN contract_status = 7 THEN 'Fully Signed'
                        WHEN contract_status = 8 THEN 'Expired'
                        WHEN contract_status = 9 THEN 'Terminated'
                    END LIKE ?
                ", ['%' . $search . '%'])
                    ->orWhereRaw("
                    CASE
                        WHEN indirect_status = 1 THEN 'indirect'

                    END LIKE ?
                ", ['%' . $search . '%'])
                    ->orWhereRaw("CAST(contracts.id AS CHAR) LIKE ?", ["%{$search}%"]);
            });
        }

        if (!empty($filters['companyId'])) {
            $query->where('contracts.company_id', $filters['companyId']);
        }

        if (!empty($filters['contractId'])) {
            $query->where('contracts.id', $filters['contractId']);
        }

        if (!empty($filters['startDate']) || !empty($filters['endDate'])) {
            $query->whereHas('contract_detail', function ($q) use ($filters) {
                if (!empty($filters['startDate'])) {
                    $from = Carbon::createFromFormat('d-m-Y', $filters['startDate'])->format('Y-m-d');
                    $q->whereDate('start_date', '>=', $from);
                }
                if (!empty($filters['endDate'])) {
                    $to = Carbon::createFromFormat('d-m-Y', $filters['endDate'])->format('Y-m-d');
                    $q->whereDate('end_date', '<=', $to);
                }
            });
        }

        // "0" (Pending) is a valid status, so don't use empty()
        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('contracts.contract_status', $filters['status']);
        }

        return $query->get()
            ->map(function ($contract) {
                if ($contract->indirect_status == 1) {
                    $indirect = "Indirect";
                }
                return [
                    'Project ID' => "Project " . $contract->project_number,
                    'Project CODE' => $contract->project_code,
                    'Contract Type' => $contract->contract_type->contract_type,
                    'Direct/Indirect/Under Faateh' => $indirect ?? " - ",
                    'Business Type' => match ($contract->contract_unit->business_type) {
                        1 => "B2B",
                        2 => "B2C"
                    },
                    'Start Date'  => $contract->contract_detail?->start_date
                        ? Date::PHPToExcel(Carbon::parse($contract->contract_detail->start_date))
                        : null,
                    'End Date'  => $contract->contract_detail?->end_date
                        ? Date::PHPToExcel(Carbon::parse($contract->contract_detail->end_date))
                        : null,
                    'Company Name' => $contract->company->company_name,
                    'Indirect Company' => $contract->indirectCompany?->company_name
                        ? $contract->indirectCompany->company_name . ' - Project ' . ($contract->indirectContract?->project_number ?? '')
                        : ' - ',
                    'Vendor Name' => $contract->vendor->vendor_name,
                    'Buliding' => $contract->property->property_name,
                    'Area' => $contract->property->area?->area_name ?? '',
                    'Locality' => $contract->locality->locality_name ?? '',
                    'Plot Number' => $contract->property->plot_no ?? '',
                    'Makani Number' => $contract->property->makani_number ?? '',
                    'Total Units' => $contract->contract_unit->no_of_units ?? '',
                    'Unit' => $contract->contract_unit->unit_numbers ?? '',
                    'UniT Type' => $contract->contract_unit->unit_type_count ?? '',
                    'Commission' => (float) ($contract->contract_rentals->commission ?? 0),
                    'Deposit' => (float) ($contract->contract_rentals->deposit ?? 0),
                    'Rent Per Annum' => (float) ($contract->contract_rentals->rent_per_annum_payable ?? 0),
                    'Total Vendor Payment' => (float) ($contract->contract_rentals->total_payment_to_vendor ?? 0),
                    'Total OTC' => (float) ($contract->contract_rentals->total_otc ?? 0),
                    'Total Project Cost' => (float) ($contract->contract_rentals->final_cost ?? 0),
                    'Total Vendor Payment' => (float) ($contract->contract_rentals->total_payment_to_vendor ?? 0),
                    'Tenure' => $contract->contract_detail->duration_in_months . "M",
                    'Rent Receivable per Annum' => $contract->contract_rentals->rent_receivable_per_annum,
                    'Rent Receivable per Month' => $contract->contract_rentals->rent_receivable_per_month,
                    'ROI' =>  (float) ($contract->contract_rentals->roi_perc ?? 0),
                    'Profit %' => (float) ($contract->contract_rentals->profit_percentage ?? 0),
                    'Profit' => (float) ($contract->contract_rentals->expected_profit ?? 0),
                    'Building Type' => match ($contract->contract_unit->building_type) {
                        1 => 'Full Building',
                        0 => ''
                    },
                    'Total Sub Units' => $contract->contract_unit->total_subunit_count_per_contract,
                    'Allocated Sub Units' => getOccupiedUnits($contract->id)['occupied'],
                    'Vacant Sub Units' => getOccupiedUnits($contract->id)['vacant'],
                    'Status' => match ($contract->contract_renewal_status) {
                        0 => 'New',
                        1 => 'Renewal',
                    },
                    'Project Status' => match ($contract->contract_renewal_status) {
                        0 => 'New',
                        1 => 'Renewal (' . ($contract->renewal_count ?? 0) . ')',
                    },

                    'Created_at' => $contract->created_at
                        ? Date::PHPToExcel($contract->created_at)
                        : null,


                ];
            });
    }

    public function headings(): array
    {
        return [
            'Project Number',
            'Project CODE',
            'Contact Type',
            'Direct/Indirect/Under Faateh',
            'Business Type',
            'Start Date',
            'End Date',
            'Company Name',
            'Indirect Company',
            'Vendor Name',
            'Buliding',
            'Area',
            'Locality',
            'Plot Number',
            'Makani Number',
            'Total Units',
            'Unit',
            'UniT Type',
            'Commission',
            'Deposit',
            'Rent Per Annum',
            'Total Vendor Payment',
            'Total OTC',
            'Total Project Cost',
            'Tenure',
            'Rent Receivable per Annum',
            'Rent Receivable per Month',
            'ROI',
            'Profit %',
            'Profit',
            'Building Type',
            'Total Sub Units',
            'Allocated Sub Units',
            'Vacant Sub Units',
            'Status',
            'Project Status',
            'Created_at',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F'  => NumberFormat::FORMAT_DATE_DDMMYYYY, // dd/mm/yyyy
            'G'  => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'AK' => NumberFormat::FORMAT_DATE_DDMMYYYY,

            // money (1,234.00)
            'S'  => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Commission
            'T'  => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Deposit
            'U'  => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Rent Per Annum
            'V'  => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Total Vendor Payment
            'W'  => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Total OTC
            'X'  => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Total Project Cost
            'Z'  => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Rent Receivable / Annum
            'AA' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Rent Receivable / Month
            'AD' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Profit

            // percentages
            'AB' => NumberFormat::FORMAT_NUMBER_00,       // ROI
            'AC' => NumberFormat::FORMAT_NUMBER_00,       // Profit %
        ];
    }
}
