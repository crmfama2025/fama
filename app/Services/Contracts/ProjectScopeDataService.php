<?php

namespace App\Services\Contracts;

use App\Models\Contract;
use App\Repositories\Contracts\ContractRepository;
use App\Repositories\Contracts\ContractScopeRepository;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ProjectScopeDataService
{
    public function __construct(
        protected ContractScopeRepository $scopeRepo,
        protected ContractRepository $contractrepo
    ) {}

    public static function getContractData($contractId)
    {
        $contract = Contract::with([
            'contract_unit',
            'contract_rentals',
            'contract_detail',
            'contract_otc',
            'contract_payments',
            'contract_payment_details',
            'contract_unit_details',
            'contract_payment_receivables',
            'vendor',

        ])
            // ->whereIn('contract_status', [0, 1])
            ->find($contractId);

        // dd($contract);

        if (!$contract) {
            return [];
        }

        if ($contract->contract_type_id == 2) {
            $total_otc = formatNumber(
                $contract->contract_rentals?->commission +
                    $contract->contract_rentals?->deposit +
                    $contract->contract_detail?->ejari
            );
        } else {
            $total_otc = formatNumber($contract->contract_rentals?->total_otc);
        }

        return [
            'id' => $contract->id,
            'project_number' => $contract->project_number ?? '',
            'contract_type_id' => $contract->contract_type_id ?? 0,
            'property_name' => $contract->property?->property_name ?? '',
            'area' => $contract->area?->area_name ?? '',
            'locality' => $contract->locality?->locality_name ?? '',
            'vendor_name' => $contract->vendor?->vendor_name ?? '',
            'start_date' => $contract->contract_detail?->start_date ?? '',
            'end_date' => $contract->contract_detail?->end_date ?? '',
            'ejari' => $contract->contract_detail?->ejari ?? '',
            'total_units' => $contract->contract_unit?->no_of_units ?? 0,
            'total_contract_amount' => formatNumber($contract->contract_rentals?->rent_per_annum_payable),
            'unit_type' => $contract->contract_unit?->unit_type_count,


            'grace_period' => $contract->contract_detail?->grace_period,
            'commission' => formatNumber($contract->contract_rentals?->commission),
            'contract_fee' => formatNumber($contract->contract_detail?->contract_fee),
            'refundable_deposit' => formatNumber($contract->contract_rentals?->deposit),
            'deposit_perc' => $contract->contract_rentals?->deposit_percentage,
            'commission_perc' => $contract->contract_rentals?->commission_percentage,
            'total_payment_to_vendor' => formatNumber($contract->contract_rentals?->total_payment_to_vendor),
            'total_otc' => $total_otc,
            'final_cost' => formatNumber($contract->contract_rentals?->final_cost),
            'initial_rent' => formatNumber(toNumeric($contract->contract_rentals?->rent_per_annum_payable) / 4),
            'initial_investment' => formatNumber($contract->contract_rentals?->initial_investment),
            'expected_profit' => formatNumber($contract->contract_rentals?->expected_profit),
            'roi' => $contract->contract_rentals?->roi_perc,
            'profit_percentage' => $contract->contract_rentals?->profit_percentage,


            'cost_of_development' => formatNumber($contract->contract_otc?->cost_of_development),
            'cost_of_beds' => formatNumber($contract->contract_otc?->cost_of_bed),
            'cost_of_mattresses' => formatNumber($contract->contract_otc?->cost_of_matress),
            'cost_of_cabinets' => formatNumber($contract->contract_otc?->cost_of_cabinets),
            'appliances' => formatNumber($contract->contract_otc?->appliances),
            'decoration' => formatNumber($contract->contract_otc?->decoration),
            'dewa_deposit' => formatNumber($contract->contract_otc?->dewa_deposit),


            'expected_rental' => formatNumber($contract->contract_rentals?->rent_receivable_per_month),
            'number_of_months' => $contract->contract_rentals?->installment->installment_name,
            'total_rental' => formatNumber($contract->contract_rentals?->rent_receivable_per_annum),
            'plot_no' => $contract->property?->plot_no,
            'renewal_status' => $contract->parent_contract_id ? 'Renew' : 'New',
            'renewal_number' => $contract->renewal_count,
            'unit_details' => $contract->contract_unit_details,
            'contract_payment_details' => $contract->contract_payment_details,
            'contract_payment_receivables' => $contract->contract_payment_receivables,


            'plot_no' => $contract->property?->plot_no ?? '',
            'unit_numbers' => $contract->contract_unit?->unit_numbers,
            'sub_unit_count' => $contract->contract_unit?->total_subunit_count_per_contract,
            'closing_date' => $contract->contract_detail?->closing_date,
            'payable_installment' => $contract->contract_payments?->installment?->installment_name,

            'unit_property_type' => $contract->contract_unit?->property_type,
            'no_of_floors' => $contract->contract_unit?->no_of_floors,
            'floor_numbers' => $contract->contract_unit?->floor_numbers,
            'parent' => $contract->parent ?? '',
        ];
    }

    public function scopeCreate($sheet = null, $contractId, $filename)
    {
        $hasScope = $this->scopeRepo->findBYContractId($contractId);
        // dd($sheet);
        // $spreadsheet = new Spreadsheet();
        $spreadsheet = $sheet->getParent();

        ob_start();
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        $binaryExcel = ob_get_clean();

        $data['scope'] = base64_encode($binaryExcel);


        // $binaryExceldwn = base64_decode($data['scope']);
        // file_put_contents(storage_path('app/testup.xlsx'), $binaryExceldwn);

        $data['contract_id'] = $contractId;
        // $serialized = serialize($sheet);
        // $encoded = base64_encode($serialized);

        $data['file_name'] = $filename;

        if ($hasScope) {
            $scope = $this->scopeRepo->update($data, $hasScope->id);
        } else {
            $scope = $this->scopeRepo->create($data);
        }



        $upData['is_scope_generated'] = 1;
        $upData['contract_status'] = 1;
        $upData['scope_generated_by'] = auth()->user()->id;

        $this->contractrepo->update($contractId, $upData);

        return $scope;
    }

    public function getScope($scopeId)
    {
        $scopeData = $this->scopeRepo->find($scopeId);

        return $scopeData;
    }

    // Helper to convert XLSX binary to array
    // protected function convertXlsxToArray($binary)
    // {
    //     $temp = tempnam(sys_get_temp_dir(), 'xlsx');
    //     file_put_contents($temp, $binary);

    //     $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
    //     $spreadsheet = $reader->load($temp);

    //     return $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
    // }


    public function exportSpreadsheet($sheet)
    {
        $spreadsheet = new Spreadsheet();
        $activesheet = $spreadsheet->getActiveSheet();

        foreach ($sheet as $r => $row) {
            foreach ($row as $c => $val) {
                $columnLetter = Coordinate::stringFromColumnIndex($c + 1);
                $cell = $columnLetter . ($r + 1);

                $activesheet->setCellValue($cell, $val);
            }
        }

        return $spreadsheet;
    }
}
