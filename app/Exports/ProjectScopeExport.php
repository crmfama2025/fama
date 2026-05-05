<?php

namespace App\Exports;

use App\Exports\Styles\DirectScopeStyles;
use App\Exports\Styles\FFScopeStyles;
use App\Models\ContractScope;
use App\Services\Contracts\ProjectScopeDataService;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProjectScopeExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{
    protected $contract;
    protected $scopeService;

    public function __construct(protected $contractId, $scopeService)
    {
        $this->contract = ProjectScopeDataService::getContractData($contractId);
        $this->scopeService = $scopeService;
        // dd($this->contract);
    }

    public function title(): string
    {
        return 'Project Scope';
    }

    public function array(): array
    {
        return [[$this->contract['property_name']]];
    }

    public function headings(): array
    {
        return [[], [], [], []];
    }

    public function styles(Worksheet $sheet): array
    {
        if (!$this->contract) return [];

        if (!isset($sheet)) {
            return [];
        }
        $filename = "Project " . $this->contract['project_number'] . (($this->contract['contract_type_id'] == 1) ? '_Direct' : '') . (($this->contract['parent']) ? '_Renewal' : '') . '_' . $this->contract['property_name'] . ' Building Summary.xlsx';
        $title = "Project {$this->contract['project_number']} {$this->contract['property_name']}, {$this->contract['locality']}, {$this->contract['area']} ({$this->contract['vendor_name']}) Contract Period: {$this->contract['start_date']} to {$this->contract['end_date']}";

        if ($this->contract['contract_type_id'] == 1) {
            $sheet->mergeCells('A1:J1');
            $sheet->setCellValue('A1', $title);
            $sheet->getStyle('A1')->applyFromArray(DirectScopeStyles::header());

            // Then just call helper methods:
            renderSummary($sheet, $this->contract, $title);
            renderUnitDetails($sheet, $this->contract);
            renderPayables($sheet, $this->contract, $title);
            renderPaymentToVendor($sheet, $this->contract);
            renderReceivables($sheet, $this->contract);
            renderTotal($sheet, $this->contract);
            if ($this->contract['parent']) {
                renderRenewDetailsDF($sheet, $this->contract);
            }

            foreach (range('A', 'P') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // dd($sheet);
        } else {
            $sheet->mergeCells('A1:G1');
            $sheet->setCellValue('A1', $title);
            $sheet->getStyle('A1')->applyFromArray(FFScopeStyles::header());
            // dd('FF');

            renderFamaPaymentSummary($sheet, $this->contract);
            renderFamaPayables($sheet, $this->contract);
            renderReceivablesFF($sheet, $this->contract);
            renderSummaryFF($sheet, $this->contract);
            renderPaymentSummary($sheet, $this->contract);
            renderUnitDetailsFF($sheet, $this->contract);
            if ($this->contract['parent']) {
                renderRenewDetailsFF($sheet, $this->contract);
            }


            foreach (range('A', 'P') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
        }

        $this->scopeService->scopeCreate($sheet, $this->contract['id'], $filename);
        // dd($returnVal);
        return [];
    }
}
