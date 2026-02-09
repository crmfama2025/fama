<?php

use App\Exports\Styles\DirectScopeStyles;
use App\Exports\Styles\FFScopeStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;


function paymentDetailScope($detail)
{
    // dd($detail);
    $modeReturn = '';
    if (in_array($detail->payment_mode_id, [1, 4])) {
        $modeReturn = $detail->payment_mode->payment_mode_name;
    } elseif ($detail->payment_mode_id == 2) {
        $modeReturn = $detail->bank->bank_name . ' - ' . $detail->payment_mode->payment_mode_name;
    } elseif ($detail->payment_mode_id == 3) {
        $modeReturn = $detail->bank->bank_name . ' - ' . $detail->cheque_no;
    }

    return $modeReturn;
}


// DF scope
function renderSummary($sheet, $contract, $title)
{
    // Example: add summary data to Excel
    $sheet->setCellValue('A1', $title);
    $sheet->mergeCells('A1:E1');
    $sheet->getStyle('A1')->getFont()->setBold(true);

    $summaryArr = [
        ['Building Name', '', $contract['property_name'], '', '', '', 'OTC', '', 'Furniture', ''],
        ['Number of Houses', '', $contract['total_units'] . ' Houses', '', '', '', 'Cost of Development', '', toNumeric($contract['cost_of_development']), ''],
        ['Vendor Name', '', $contract['vendor_name'], '', '', '', 'Cost of Beds', '', toNumeric($contract['cost_of_beds']), ''],
        ['Total Contract Amt', '', $contract['total_contract_amount'], '', '', '',  'Cost of Mattresses', '', toNumeric($contract['cost_of_mattresses']), ''],
        ['Unit Type', '', $contract['unit_type'], '', '', '', 'Cost of Cabinets', '', toNumeric($contract['cost_of_cabinets']), ''],
        ['Grace Period', '', $contract['grace_period'] . ' Month', '', '', '',  'Appliances', '', toNumeric($contract['appliances']), ''],
        ['Commission', '', $contract['commission'], '', '', '',  'Decoration', '', toNumeric($contract['decoration']), ''],
        ['Contract Fee', '', $contract['contract_fee'], '', '', '',  'Dewa Deposit', '', toNumeric($contract['dewa_deposit']), ''],
        ['Refundable Deposit', '', $contract['refundable_deposit'], '', '', '',  'Total OTC', '', toNumeric($contract['total_otc']), ''],
        ['Total Payment to Vendor', '', $contract['total_payment_to_vendor'], '', '', '',  'Expected Rental', '', toNumeric($contract['expected_rental']), ''],
        ['Total OTC', '', $contract['total_otc'], '', '', '', 'Number of Months', '', $contract['number_of_months'], ''],
        ['Final Cost', '', $contract['final_cost'], '', '', '', 'Total Rental', '', $contract['total_rental'], ''],
        ['Initial Investment', '', $contract['initial_investment'], '', '', '',  'Plot Number', '', $contract['plot_no'], ''],
        ['Expected Profit', '', $contract['expected_profit'], '', '', '',  'Renewal Status', '', $contract['renewal_status'], ''],
        ['ROI', '', $contract['roi'] . '%', '', '', '', 'Renewal Number', '', $contract['renewal_number'], ''],
    ];

    // Write the array starting at row 2
    $sheet->fromArray($summaryArr, null, 'A2');

    // Apply style for all rows in the summary table
    $lastSummRow = 2 + count($summaryArr) - 1;

    // Merge the third value (column C) across C, D, E for each row
    foreach ($summaryArr as $i => $row) {
        $currentRow = $i + 2; // because array starts at row 2
        // Merge columns C (3), D (4), E (5) for this row
        $sheet->mergeCells("C{$currentRow}:E{$currentRow}");
        $sheet->mergeCells("F{$currentRow}:F{$lastSummRow}");
        $sheet->mergeCells("G{$currentRow}:H{$currentRow}");
        $sheet->mergeCells("I{$currentRow}:J{$currentRow}");

        if ($currentRow >= 14) {
            $sheet->getStyle("G{$currentRow}:J{$currentRow}")->applyFromArray([
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color' => ['rgb' => 'D9D9D9'], // background color (blue)
                ],
            ]);
        } else {
            $sheet->getStyle("A{$currentRow}:J" . $lastSummRow)->applyFromArray([
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color' => ['rgb' => 'F8CBAD'],
                ],
            ]);
        }
    }


    $sheet->getStyle('A2:J' . $lastSummRow)->applyFromArray([
        'font' => ['bold' => true],
        'alignment' => ['horizontal' => 'left', 'vertical' => 'center'],
        'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
    ]);

    $sheet->getStyle('I3:I11')
        ->getNumberFormat()
        ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
}

function renderUnitDetails($sheet, $contract)
{
    $startColumn = 'K';
    $startRow    = 4;

    $unitDetaiArr = [];

    // ===============================
    // HEADER
    // ===============================
    $unitDetaiArr[] = [
        'Type',
        'Room',
        'Rent',
        'Sub Unit Type',
        'Sub unit Count',
        'Sub unit Rent',
        'Total',
    ];

    $rentAnnum = [];
    $totalPerContract = [];

    // ===============================
    // DATA ROWS
    // ===============================
    $subUnitTotalCount = 0;
    foreach ($contract['unit_details'] as $unitdetail) {

        $data     = getAccommodationDetails($unitdetail);
        $subunits = $data['subunits'];
        $total    = $data['total_price'];

        $isFirstRow = true;


        foreach ($subunits as $sub) {

            $unitDetaiArr[] = [
                $isFirstRow ? ($unitdetail->unit_type->unit_type ?? '') : '',
                $isFirstRow ? ($unitdetail->unit_number ?? '') : '',
                $isFirstRow ? (tonumeric($unitdetail->unit_rent_per_annum) ?? 0) : null,
                $sub['type'],
                $sub['count'],
                $sub['rent'],
                $isFirstRow ? $total : null,
            ];

            $isFirstRow = false;
            // $subUnitTotalCount += (int) $sub['count'];
        }

        $rentAnnum[] = $unitdetail->unit_rent_per_annum;
        $totalPerContract[] = $total;
    }

    // ===============================
    // SUMMARY ROWS
    // ===============================
    $unitDetaiArr[] = []; // blank row
    $unitDetaiArr[] = []; // blank row

    // TOTAL ROW
    $unitDetaiArr[] = [
        '',
        '',
        array_sum($rentAnnum), // M (Rent)
        '',                    // N
        $contract['sub_unit_count'],    // O (Sub unit Count)
        '',                    // P
        array_sum($totalPerContract), // Q (Total)
    ];

    // QUARTER RENT
    $unitDetaiArr[] = [
        '',
        '',
        array_sum($rentAnnum) / 4,
        '',
        '',
        '',
        ''
    ];

    // 10% VALUE
    $unitDetaiArr[] = [
        '',
        '',
        array_sum($rentAnnum) * 0.1,
        '',
        '',
        '',
        ''
    ];



    // ===============================
    // WRITE TO SHEET
    // ===============================
    $sheet->fromArray($unitDetaiArr, null, "{$startColumn}{$startRow}");

    $lastRow    = $startRow + count($unitDetaiArr) - 1;
    $lastColumn = 'Q'; // K → Q (7 columns)

    // ===============================
    // MERGE K L M Q BASED ON SUBUNITS
    // ===============================
    $currentRow = $startRow + 1;

    foreach ($contract['unit_details'] as $unitdetail) {

        $data     = getAccommodationDetails($unitdetail);
        $rowCount = count($data['subunits']);

        if ($rowCount > 1) {
            $endRow = $currentRow + $rowCount - 1;

            $sheet->mergeCells("K{$currentRow}:K{$endRow}");
            $sheet->mergeCells("L{$currentRow}:L{$endRow}");
            $sheet->mergeCells("M{$currentRow}:M{$endRow}");
            $sheet->mergeCells("Q{$currentRow}:Q{$endRow}");

            $sheet->getStyle("K{$currentRow}:Q{$endRow}")
                ->getAlignment()
                ->setVertical(Alignment::VERTICAL_CENTER);
        }

        $currentRow += $rowCount;
    }

    // ===============================
    // MERGE SUMMARY ROWS (K + L)
    // ===============================
    $summaryStartRow = $lastRow - 2;

    for ($r = $summaryStartRow; $r <= $lastRow; $r++) {
        $sheet->mergeCells("K{$r}:L{$r}");
    }

    // ===============================
    // HEADER STYLE (BLUE + BOLD)
    // ===============================
    $sheet->getStyle("K{$startRow}:{$lastColumn}{$startRow}")
        ->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '9BC2E6'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

    // ===============================
    // BODY STYLE (BLUE + BORDERS)
    // ===============================
    $sheet->getStyle("K" . ($startRow + 1) . ":{$lastColumn}{$lastRow}")
        ->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '9BC2E6'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);


    $sheet->getStyle("K" . ($summaryStartRow - 1) . ":{$lastColumn}" . ($summaryStartRow - 1))
        ->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '9BC2E6'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_NONE,
                ],
            ],
        ]);


    // ===============================
    // Highlight row yellow
    // ===============================
    $sheet->getStyle("K{$summaryStartRow}:Q{$summaryStartRow}")
        ->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFC000'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_NONE,
                ],
            ],
        ]);

    // ===============================
    // Fade other rows
    // ===============================
    $sheet->getStyle("K" . ($summaryStartRow + 1) . ":{$lastColumn}{$lastRow}")
        ->applyFromArray([
            'font' => [
                'color' => ['argb' => 'D0CECE'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_NONE,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_NONE,
                ],
            ],
        ]);

    // ===============================
    // NUMBER FORMAT
    // ===============================
    // Rent (M)
    $sheet->getStyle("M" . ($startRow + 1) . ":M{$lastRow}")
        ->getNumberFormat()
        ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

    // Sub unit Rent (P)
    $sheet->getStyle("P" . ($startRow + 1) . ":P{$lastRow}")
        ->getNumberFormat()
        ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

    // Total (Q)
    $sheet->getStyle("Q" . ($startRow + 1) . ":Q{$lastRow}")
        ->getNumberFormat()
        ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
}

function renderPayables($sheet, $contract, $title)
{
    // Apply to A18:J18
    $sheet->mergeCells('A18:J18');
    $sheet->setCellValue('A18', $title);
    $sheet->getStyle('A18')->applyFromArray(DirectScopeStyles::header());

    // Left scope payables
    $payableArr = [
        ['SN', 'Description', '', 'Total Payables in AED'],
        ['', '', '', ''],
        ['1', 'Rental', '', toNumeric($contract['total_contract_amount'])],
        ['2', 'Ref.Deposit', '', toNumeric($contract['refundable_deposit'])],
        ['3', 'Commission', '', toNumeric($contract['commission'])],
        ['4', 'OTC', '', toNumeric($contract['total_otc'])],
        ['5', 'Contractor fee', '', toNumeric($contract['contract_fee'])],
        ['6', '', '', ''],
        ['7', '', '', ''],
        ['8', '', '', ''],
        ['9', '', '', ''],
        ['10', '', '', ''],
        ['11', '', '', ''],
        ['12', '', '', ''],
        ['', '', '', ''],
        ['', '', '', ''],
        ['', '', '', ''],
        ['', '', '', ''],

    ];

    // Write the array starting at row 2
    $sheet->fromArray($payableArr, null, 'A19');

    // Apply style for all rows in the summary table
    $lastPayableRow = 2 + count($payableArr) - 1;

    $sheet->mergeCells("A19:A20");
    $sheet->mergeCells("B19:C20");
    $sheet->mergeCells("D19:D20");

    $sheet->getStyle('A19:D20')->applyFromArray([
        'font' => [
            'bold' => true,
        ],
    ]);

    // Merge the third value (column C) across C, D, E for each row
    foreach ($payableArr as $i => $row) {
        $currentRow = $i + 19; // because array starts at row 2
        // Merge columns C (3), D (4), E (5) for this row
        $sheet->mergeCells("B{$currentRow}:C{$currentRow}");
    }

    $sheet->getStyle("A19:C35")->applyFromArray([
        'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        'borders' => ['allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
        ]],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'color' => ['rgb' => 'F8CBAD'], // background color (blue)
        ],
    ]);

    $sheet->getStyle("D19:D35")->applyFromArray([
        'alignment' => ['horizontal' => 'right', 'vertical' => 'center'],
        'borders' => ['allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
        ]],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'color' => ['rgb' => 'F8CBAD'], // background color (blue)
        ],
    ]);
    $sheet->getStyle("D19:D35")
        ->getNumberFormat()
        ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
}


function renderPaymentToVendor($sheet, $contract)
{
    $installmentDet = $arrayofPayable = [];
    foreach ($contract['contract_payment_details'] as $key => $paymentDet) {
        $arrayofPayable[] = toNumeric($paymentDet->payment_amount);

        $installmentDet[] = [
            paymentDetailScope($paymentDet),
            DateTime::createFromFormat('d-m-Y', $paymentDet->payment_date)->format('d-M-Y'),
            toNumeric($paymentDet->payment_amount)
        ];
    }


    $payableInstallmentsArr = [
        ['Payment to vendor', '', ''],
        ['Bank & Cheque number', 'Date', 'AED'],
    ];
    $payableDetArr = array_merge($payableInstallmentsArr, $installmentDet);

    // Write the array starting at row 2
    $sheet->fromArray($payableDetArr, null, 'E19');

    $sheet->mergeCells("E19:G19");
    $sheet->getStyle('E19:G20')->applyFromArray([
        'font' => [
            'bold' => true,
        ],
    ]);

    $sheet->getStyle("E19:G35")->applyFromArray([
        'alignment' => ['horizontal' => 'right', 'vertical' => 'center'],
        'borders' => ['allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
        ]],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'color' => ['rgb' => 'B4C6E7'], // background color (blue)
        ],
    ]);
    $sheet->getStyle("E19:G35")
        ->getNumberFormat()
        ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
}

function renderReceivables($sheet, $contract)
{
    $installmentRec = $arrayofRec = [];
    foreach ($contract['contract_payment_receivables'] as $key => $paymentRec) {
        $arrayofRec[] = toNumeric($paymentRec->receivable_amount);
        $installmentRec[] = [
            DateTime::createFromFormat('d-m-Y', $paymentRec->receivable_date)->format('d-M-Y'),
            toNumeric($paymentRec->receivable_amount)
        ];
    }


    $payableRecArr = [
        ['Receivables from  - Cheques details', '', ''],
        ['Date', 'AED', 'Actual Receipts'],
    ];
    $receivableDetArr = array_merge($payableRecArr, $installmentRec);

    // Write the array starting at row 2
    $sheet->fromArray($receivableDetArr, null, 'H19');

    $sheet->mergeCells("H19:J19");
    $sheet->getStyle('H19:J20')->applyFromArray([
        'font' => [
            'bold' => true,
        ],
    ]);

    $sheet->getStyle("H19:J35")->applyFromArray([
        'alignment' => ['horizontal' => 'right', 'vertical' => 'center'],
        'borders' => ['allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
        ]],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'color' => ['rgb' => 'C6E0B4'], // background color (blue)
        ],
    ]);

    $centerAlign = [
        'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
    ];



    $sheet->getStyle("E19:J20")->applyFromArray($centerAlign);
    $sheet->getStyle("E19:J20")->applyFromArray($centerAlign);
    $sheet->getStyle("E21:F35")->applyFromArray($centerAlign);
    $sheet->getStyle("E21:F35")->applyFromArray($centerAlign);
    $sheet->getStyle("H21:H35")->applyFromArray($centerAlign);

    $sheet->getStyle("I21:J35")
        ->getNumberFormat()
        ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
}

function renderTotal($sheet, $contract)
{
    $leftAlign = [
        'alignment' => ['horizontal' => 'left', 'vertical' => 'center'],
    ];

    $totalArr = [
        ['Total', '', '', toNumeric($contract['final_cost']), '', '', toNumeric($contract['total_payment_to_vendor']), '', toNumeric($contract['total_rental']), '0.00'],
        ['Profit Margin', '', '', '', '', '', '', '', toNumeric($contract['expected_profit']), ''],
    ];

    $sheet->fromArray($totalArr, null, 'A36');
    $sheet->mergeCells("A36:C36");
    $sheet->getStyle("A36:C36")->applyFromArray(array_merge_recursive([
        $leftAlign,
        'font' => [
            'bold' => true,
        ],
        'borders' => ['allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
        ]],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'color' => ['rgb' => 'FFFF00'], // background color (Yellow)
        ],
    ]));
    $sheet->getStyle('D36:J36')->applyFromArray(array_merge_recursive([
        'alignment' => ['horizontal' => 'right', 'vertical' => 'center'],
        'font' => [
            'bold' => true,
        ],
        'borders' => ['allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
        ]],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'color' => ['rgb' => 'FFFF00'], // background color (Yellow)
        ],
    ]));


    $sheet->mergeCells("A37:H37");
    $sheet->getStyle("A37:J37")->applyFromArray(array_merge_recursive([
        'alignment' => ['horizontal' => 'right', 'vertical' => 'center'],
        'font' => [
            'bold' => true,
        ],
        'borders' => ['allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
        ]],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'color' => ['rgb' => 'F8CBAD'], // background color (Yellow)
        ],
    ]));

    $totalArr = [
        ['Profit%', toNumeric($contract['profit_percentage']) . '%'],
    ];

    $sheet->fromArray($totalArr, null, 'H38');
    $sheet->getStyle("H38:I38")->applyFromArray(array_merge_recursive([
        'alignment' => ['horizontal' => 'right', 'vertical' => 'center'],
        'font' => [
            'bold' => true,
        ],
        'borders' => ['allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
        ]],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'color' => ['rgb' => 'F4B084'], // background color (Yellow)
        ],
    ]));

    $totalotc = [[(toNumeric($contract['total_otc']) / 2)],];
    $sheet->fromArray($totalotc, null, 'L36');
    $sheet->getStyle('L36:L36')->applyFromArray(array_merge_recursive([
        'alignment' => ['horizontal' => 'right', 'vertical' => 'center'],
        'font' => [
            'bold' => true,
            'color' => ['rgb' => 'FF0000'],
        ],
    ]));

    $sheet->getStyle("A36:J37")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
}

function renderRenewDetailsDF($sheet, $contract)
{
    $cellnumber = '20';
    $sheet->setCellValue('M' . $cellnumber, 'Project ' . $contract['parent']->project_number . ' - Renewal');
    $sheet->mergeCells('M' . $cellnumber . ':N' . $cellnumber);
    $sheet->getStyle('M' . $cellnumber)->getFont()->setBold(true);
    $sheet->getStyle('M' . $cellnumber . ':N' . $cellnumber)->applyFromArray([
        'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => [
                'argb' => '305496', // Blue
            ],
        ],
        'font' => [
            'color' => [
                'argb' => 'FFFFFF', // white
            ],
        ],
    ]);

    $summaryArr = [
        ['Old Rental to Vendor', formatNumber($contract['parent']->contract_rentals?->total_payment_to_vendor)],
        ['Profit Percentage', $contract['parent']->contract_rentals?->profit_percentage . '%'],
        ['Ref Deposit',    $contract['parent']->contract_rentals?->commission],
        ['Commission', $contract['parent']->contract_rentals?->deposit],
        ['Development Cost', $contract['parent']->contract_rentals?->total_otc],
        ['Total Profit earned', formatNumber($contract['parent']->contract_rentals?->expected_profit)],
        ['Total Rental Received( ' . $contract['parent']->contract_rentals?->installment->installment_name . ' Installements)', formatNumber($contract['parent']->contract_rentals?->rent_receivable_per_annum)],
        ['Old Monthly Rental', formatNumber($contract['parent']->contract_rentals?->rent_receivable_per_month)],
        ['', ''],
        ['New Rental to Vendor', formatNumber($contract['total_payment_to_vendor'])],
        ['Profit Percentage', $contract['profit_percentage'] . '%'],
        ['Total Profit earned', formatNumber($contract['expected_profit'])],
        ['Total Rental Receivables( ' . $contract['number_of_months'] . ' Installments)', $contract['total_rental']],
        ['New Monthly Rental', formatNumber($contract['expected_rental'])],
    ];

    // // Write the array starting at row 2
    $sheet->fromArray($summaryArr, null, 'M' . $cellnumber + 1);

    // // Apply style for all rows in the summary table
    $lastSummRow =  $cellnumber + 1 + count($summaryArr) - 1;
    // dd($lastSummRow);

    $sheet->getStyle('M' . ($cellnumber + 1) . ':M' . $lastSummRow)->applyFromArray(FFScopeStyles::renewalleftSummaryFF());
    $sheet->getStyle('N' . ($cellnumber + 1) . ':N' . $lastSummRow)->applyFromArray(FFScopeStyles::renewalCenterSummaryFF());


    $sheet->getStyle('M' . ($cellnumber + 9) . ':N' . ($cellnumber + 9))->applyFromArray(FFScopeStyles::clearstyleFF());
    $sheet->getStyle('M' . ($cellnumber + 8) . ':N' . ($cellnumber + 8))->applyFromArray(FFScopeStyles::renewColorChange());
    $sheet->getStyle('M' . ($cellnumber + 14) . ':N' . ($cellnumber + 14))->applyFromArray(FFScopeStyles::renewColorChange());
}

// FF scope
function renderFamaPaymentSummary($sheet, $contract)
{
    // Example: add summary data to Excel
    $sheet->setCellValue('A2', 'FAMA REAL ESTATE');
    $sheet->getStyle('A2')->getFont()->setBold(true);
    $sheet->getStyle('A2')->applyFromArray(FFScopeStyles::headerFama());

    $summaryArr = [
        [$contract['property_name'], ''],
        ['Rent Payable', $contract['total_contract_amount']],
        ['Deposit', $contract['refundable_deposit']],
        ['Commission', $contract['commission']],
        ['Ejari Registration Fee', $contract['ejari']],
        ['Total to be paid', $contract['total_payment_to_vendor']],
        ['Total Revenue', $contract['total_rental']],
        ['Initial Rent', $contract['initial_rent']],
        ['One Time Cost', $contract['total_otc']],
        ['Initial Investment', $contract['initial_investment']],
        ['Profit', $contract['expected_profit']],
        ['ROI', $contract['roi'] . '%'],
        ['Project Scope', ''],
    ];

    // Write the array starting at row 2
    $sheet->fromArray($summaryArr, null, 'A3');

    // Apply style for all rows in the summary table
    $lastSummRow = 3 + count($summaryArr) - 1;


    $sheet->getStyle('A3:A' . $lastSummRow)->applyFromArray(FFScopeStyles::summaryLeft());

    $sheet->getStyle('B3:B' . $lastSummRow)->applyFromArray(FFScopeStyles::summaryRight());
}

function renderFamaPayables($sheet, $contract)
{
    $sheet->mergeCells('A19:D19');
    $sheet->setCellValue('A19', 'Payment Details');
    $sheet->getStyle('A19:D19')->applyFromArray(FFScopeStyles::headerFama());

    $installmentDet = $arrayofPayable = [];
    foreach ($contract['contract_payment_details'] as $paymentDet) {
        $arrayofPayable[] = toNumeric($paymentDet->payment_amount);

        $installmentDet[] = [
            $paymentDet->bank?->bank_name ?? $paymentDet->payment_mode?->payment_mode_name,
            $paymentDet->cheque_no,
            DateTime::createFromFormat('d-m-Y', $paymentDet->payment_date)->format('d-M-Y'),
            toNumeric($paymentDet->payment_amount)
        ];
    }


    $payableInstallmentsArr = [
        ['Bank Name', 'Cheque Number', 'Cheque Date', 'Amount'],
    ];
    $payableDetArr = array_merge($payableInstallmentsArr, $installmentDet);

    $payableDetArr = array_merge($payableDetArr, [['', '', '', toNumeric($contract['total_payment_to_vendor'])]]);
    // dd($payableDetArr);

    // Write the array starting at row 2
    $sheet->fromArray($payableDetArr, null, 'A20');

    $sheet->getStyle('A20:D20')->applyFromArray([
        'font' => [
            'bold' => true,
        ],
        'borders' => ['allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
        ]],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'color' => ['rgb' => 'FFFF00'], // background color (yellow)
        ],
    ]);

    $sheet->getStyle("A21:C25")->applyFromArray([
        'alignment' => ['horizontal' => 'left', 'vertical' => 'center'],
        'borders' => ['allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
        ]],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'color' => ['rgb' => 'FFFF00'], // background color (yellow)
        ],
    ]);
    $sheet->getStyle("D21:D25")->applyFromArray([
        'alignment' => ['horizontal' => 'right', 'vertical' => 'center'],
        'borders' => ['allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
        ]],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'color' => ['rgb' => 'FFFF00'], // background color (yellow)
        ],
    ]);
    $sheet->getStyle('D25:D25')->applyFromArray([
        'font' => [
            'bold' => true,
        ],
    ]);
    $sheet->getStyle("D21:D25")
        ->getNumberFormat()
        ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
}

function renderReceivablesFF($sheet, $contract)
{
    $installmentRec = $arrayofRec = [];

    foreach ($contract['contract_payment_receivables'] as $paymentRec) {
        $amount = toNumeric($paymentRec->receivable_amount);

        $arrayofRec[] = $amount;
        $installmentRec[] = [
            DateTime::createFromFormat('d-m-Y', $paymentRec->receivable_date)->format('d-M-Y'),
            $amount
        ];
    }

    $payableRecArr = [
        ['Sold To Faateh', 'Receivables'],
        ['Total Receivables in 12 Cheques', toNumeric($contract['total_rental'])],
        ['Receivables Date From Faateh to Fama', ''],
    ];

    $receivableDetArr = array_merge($payableRecArr, $installmentRec);

    $startRow = 27;
    $sheet->fromArray($receivableDetArr, null, 'A' . $startRow);

    /* ------------------ Dynamic row calculation ------------------ */

    $headerRows = count($payableRecArr);       // 3
    $installmentRows = count($installmentRec);

    $firstInstallmentRow = $startRow + $headerRows;
    $lastDataRow = $firstInstallmentRow + $installmentRows - 1;
    $totalRow = $lastDataRow + 1;

    /* ------------------ Styles ------------------ */

    $leftAlign = [
        'alignment' => ['horizontal' => 'left', 'vertical' => 'center'],
    ];

    $greenFill = [
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
            ]
        ],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'color' => ['rgb' => 'C6E0B4'],
        ],
    ];

    $sheet->getStyle("A{$startRow}:B" . ($startRow + 2))
        ->applyFromArray(array_merge_recursive(
            ['font' => ['bold' => true]],
            $leftAlign,
            $greenFill
        ));

    $sheet->getStyle("B" . ($startRow + 1))
        ->getAlignment()
        ->setHorizontal('right');

    $sheet->getStyle("A{$firstInstallmentRow}:B{$totalRow}")
        ->applyFromArray(array_merge_recursive(
            ['alignment' => ['horizontal' => 'right', 'vertical' => 'center']],
            $greenFill
        ));

    /* ------------------ Total row ------------------ */

    // $sheet->setCellValue("A{$totalRow}", 'Total');
    $sheet->setCellValue("B{$totalRow}", formatNumber(array_sum($arrayofRec)));

    $sheet->getStyle("A{$totalRow}:B{$totalRow}")
        ->applyFromArray([
            'font' => ['bold' => true],
        ]);


    /* ------------------ Number format ------------------ */

    $sheet->getStyle("B" . ($startRow + 1) . ":B{$totalRow}")
        ->getNumberFormat()
        ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
}

function renderSummaryFF($sheet, $contract)
{
    // Example: add summary data to Excel
    $sheet->setCellValue('F4', 'Contract Details');
    $sheet->mergeCells('F4:G4');
    $sheet->getStyle('F4')->getFont()->setBold(true);
    $sheet->getStyle('F4:G4')->applyFromArray([
        'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'color' => ['rgb' => 'B4C6E7'], // background color (green)
        ],
    ]);

    $summaryArr = [
        ['Building Name', $contract['property_name']],
        ['Property Type', $contract['unit_property_type']],
        ['Plot Number', $contract['plot_no']],
        ['Project Status', $contract['renewal_status']],
        ['Renewal Number', $contract['renewal_number']],
        ['Flat No.', $contract['unit_numbers']],
        ['Number of Floors', $contract['no_of_floors']],
        ['Floor Number ', $contract['floor_numbers']],
        ['Number of Houses', $contract['total_units'] . ' Houses'],
        ['Closing Date', $contract['closing_date']],
        ['Unit Type', $contract['unit_type']],
        ['Contract Start Date ', $contract['start_date']],
        ['Contract End Date', $contract['end_date']],
        ['Grace Period', $contract['grace_period'] . ' Month'],
        ['Total Amount of Contract', $contract['total_contract_amount']],
        ['Deposit ' . toNumeric($contract['deposit_perc']) . '%', $contract['refundable_deposit']],
        ['Commission ' . toNumeric($contract['commission_perc']) . '%', $contract['commission']],
        ['Ejari Fee', ''],
        ['Payment in (Number of Cheques)', $contract['payable_installment']],
        ['Total Payable Amount', $contract['total_payment_to_vendor']]
    ];

    // Write the array starting at row 2
    $sheet->fromArray($summaryArr, null, 'F5');

    // Apply style for all rows in the summary table
    $lastSummRow = 5 + count($summaryArr) - 2;


    $sheet->getStyle('F5:G' . $lastSummRow)->applyFromArray(FFScopeStyles::summaryFF());

    $sheet->getStyle('F24:G24')->applyFromArray(array_merge_recursive(
        FFScopeStyles::summaryFF(),
        [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                ],
            ],
        ]
    ));
    $sheet->getStyle('F24:G24')->getFont()->setBold(true);

    $sheet->getStyle('G5:G24')->applyFromArray([
        'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
    ]);

    $sheet->getStyle('F4:G23')->applyFromArray([
        'borders' => [
            'outline' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
            ],
        ],
    ]);
}

function renderPaymentSummary($sheet, $contract)
{
    $sheet->setCellValue('F28', 'Summary');
    $sheet->mergeCells('F28:G28');
    $sheet->getStyle('F28')->getFont()->setBold(true);
    $sheet->getStyle('F28:G28')->applyFromArray([
        'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],

    ]);

    $summaryArr = [
        ['Total Cost of Fama', toNumeric($contract['total_payment_to_vendor'])],
        ['Profit on Cost', toNumeric($contract['expected_profit'])],
        ['Total Revenue of Fama', toNumeric($contract['total_rental'])],
    ];

    // Write the array starting at row 2
    $sheet->fromArray($summaryArr, null, 'F29');

    // Apply style for all rows in the summary table
    $lastSummRow = 30 + count($summaryArr) - 2;
    // dd($lastSummRow);
    $sheet->getStyle('F29:G' . $lastSummRow)->applyFromArray(FFScopeStyles::paymentSummaryFF());

    $sheet->getStyle('G29:G31')->applyFromArray([
        'alignment' => ['horizontal' => 'right', 'vertical' => 'center'],
    ]);

    $sheet->getStyle("G29:G31")
        ->getNumberFormat()
        ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
}

function renderUnitDetailsFF($sheet, $contract)
{
    $profitPerc = $profit = $rentAnnum = $revenue = $comm = $depo = $rentPayable = [];
    foreach ($contract['unit_details'] as $key => $unitdetail) {

        // $subunitdet = getAccommodationDetails($unitdetail);



        $commonHead = ['Flat No', 'Unit Type', 'Rent Amount',];
        $commonData =  [
            $unitdetail->unit_number ?? '',
            $unitdetail->unit_type->unit_type ?? '',
            toNumeric($unitdetail->unit_rent_per_annum) ?? 0,
        ];

        if ($contract['parent']) {
            $extraHead = [];
            $extraData = [];
        } else {
            $extraHead = ['commission', 'deposit',];
            $extraData = [
                toNumeric($unitdetail->unit_commission) ?? 0,
                toNumeric($unitdetail->unit_deposit) ?? 0,
            ];
        }

        $commonHeadEnd = ['Total Amount Payable', 'Profit %', 'Profit', 'Revenue'];
        $commonDataEnd = [
            toNumeric($unitdetail->unit_amount_payable) ?? 0,
            $unitdetail->unit_profit_perc . '%' ?? 0,
            toNumeric($unitdetail->unit_profit) ?? 0,
            toNumeric($unitdetail->unit_revenue) ?? 0,
        ];
        if ($key == 0)
            $unitDetaiArr[] = array_merge(array_merge($commonHead, $extraHead), $commonHeadEnd);
        $unitDetaiArr[] = array_merge(array_merge($commonData, $extraData), $commonDataEnd);

        $profitPerc[] = toNumeric($unitdetail->unit_profit_perc);
        $profit[] = toNumeric($unitdetail->unit_profit);
        $comm[] = toNumeric($unitdetail->unit_commission);
        $depo[] = toNumeric($unitdetail->unit_deposit);
        $rentAnnum[] = toNumeric($unitdetail->unit_rent_per_annum);
        $rentPayable[] = toNumeric($unitdetail->unit_amount_payable);
        $revenue[] = toNumeric($unitdetail->unit_revenue);
    }

    $unitDetaiArr[] = [];
    $unitDetaiArr[] = [];
    $unitDetaiArr[] = [];

    $totrentannum = formatNumber(array_sum($rentAnnum));
    $totrentPayable = formatNumber(array_sum($rentPayable));


    if ($contract['parent']) {
        $unitDetaiArr[] = [
            '',
            '',
            $totrentannum,
            $totrentPayable,
            formatNumber(array_sum($profitPerc) / count($profitPerc)) . '%',
            formatNumber(array_sum($profit)),
            formatNumber(array_sum($revenue))
        ];

        $unitDetaiArr[] = ['', '', formatNumber(array_sum($rentAnnum) / 4), '', '', '', ''];
        $unitDetaiArr[] = ['', '', formatNumber(array_sum($rentAnnum) * 0.1), '', '', '', ''];
    } else {
        $unitDetaiArr[] = [
            '',
            '',
            $totrentannum,
            formatNumber(array_sum($comm)),
            formatNumber(array_sum($depo)),
            $totrentPayable,
            formatNumber(array_sum($profitPerc) / count($profitPerc)) . '%',
            formatNumber(array_sum($profit)),
            formatNumber(array_sum($revenue))
        ];

        $unitDetaiArr[] = ['', '', formatNumber(array_sum($rentAnnum) / 4), '', '', '', ''];
        $unitDetaiArr[] = ['', '', formatNumber(array_sum($rentAnnum) * 0.1), '', '', '', ''];
    }

    // Write the array starting at row 4, column K
    $sheet->fromArray($unitDetaiArr, null, 'I5');

    // Calculate the last row
    $lastRow = 5 + count($unitDetaiArr) - 1;

    // Auto-detect last column 
    $lastColumn = $sheet->getHighestColumn();
    // $lastRow    = $sheet->getHighestRow();

    // 🔹 Apply style for entire table (borders, fill, alignment)
    $sheet->getStyle("I5:" . $lastColumn . ($lastRow - 3))->applyFromArray([
        'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        'borders' => ['allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
        ]],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'color' => ['rgb' => 'FFFF00'],
        ],
    ]);

    // 🔹 Make header row (only first row) bold
    $sheet->getStyle('I5:' . $lastColumn . '5')->getFont()->setBold(true);

    $sheet->getStyle("I" . ($lastRow - 3) . ":" . $lastColumn . ($lastRow - 1))
        ->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_NONE,
                ],
            ],
        ]);


    $sheet->getStyle("K" . ($lastRow - 2) . ":" . $lastColumn . ($lastRow - 2))
        ->applyFromArray([
            'alignment' => ['horizontal' => 'right', 'vertical' => 'center'],
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'F4B084', // yellow
                ],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_NONE,
                ],
            ],
        ]);

    $sheet->getStyle("K" . ($lastRow - 1) . ":{$lastColumn}" . $lastRow)
        ->applyFromArray([
            'alignment' => ['horizontal' => 'right', 'vertical' => 'center'],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_NONE,
                ],
            ],
            // 'font' => [
            //     'color' => [
            //         'argb' => 'D0CECE', // black text color
            //     ],
            // ],

        ]);


    $sheet->getStyle("K5:Q15")
        ->getNumberFormat()
        ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
}

function renderRenewDetailsFF($sheet, $contract)
{
    $cellnumber = '34';
    $sheet->setCellValue('F' . $cellnumber, 'Project ' . $contract['parent']->project_number . ' - Renewal');
    $sheet->mergeCells('F' . $cellnumber . ':G' . $cellnumber);
    $sheet->getStyle('F' . $cellnumber)->getFont()->setBold(true);
    $sheet->getStyle('F' . $cellnumber . ':G' . $cellnumber)->applyFromArray([
        'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => [
                'argb' => '305496', // Blue
            ],
        ],
        'font' => [
            'color' => [
                'argb' => 'FFFFFF', // white
            ],
        ],
    ]);

    $summaryArr = [
        ['Old Rental to Vendor', formatNumber($contract['parent']->contract_rentals?->total_payment_to_vendor)],
        ['Profit Percentage', $contract['parent']->contract_rentals?->profit_percentage . '%'],
        ['Total Profit earned', formatNumber($contract['parent']->contract_rentals?->expected_profit)],
        ['Total Rental Received( ' . $contract['parent']->contract_rentals?->installment->installment_name . ' Installements)', formatNumber($contract['parent']->contract_rentals?->rent_receivable_per_annum)],
        ['Old Monthly Rental', formatNumber($contract['parent']->contract_rentals?->rent_receivable_per_month)],
        ['', ''],
        ['New Rental to Vendor', formatNumber($contract['total_payment_to_vendor'])],
        ['Profit Percentage', $contract['profit_percentage'] . '%'],
        ['Total Profit earned', formatNumber($contract['expected_profit'])],
        ['Total Rental Receivables( ' . $contract['number_of_months'] . ' Installments)', $contract['total_rental']],
        ['New Monthly Rental', formatNumber($contract['expected_rental'])],
    ];

    // // Write the array starting at row 2
    $sheet->fromArray($summaryArr, null, 'F' . $cellnumber + 1);

    // // Apply style for all rows in the summary table
    $lastSummRow =  $cellnumber + 1 + count($summaryArr) - 1;
    // dd($lastSummRow);

    $sheet->getStyle('F' . ($cellnumber + 1) . ':F' . $lastSummRow)->applyFromArray(FFScopeStyles::renewalleftSummaryFF());
    $sheet->getStyle('G' . ($cellnumber + 1) . ':G' . $lastSummRow)->applyFromArray(FFScopeStyles::renewalCenterSummaryFF());


    $sheet->getStyle('F' . ($cellnumber + 6) . ':G' . ($cellnumber + 6))->applyFromArray(FFScopeStyles::clearstyleFF());
    $sheet->getStyle('F' . ($cellnumber + 5) . ':G' . ($cellnumber + 5))->applyFromArray(FFScopeStyles::renewColorChange());
    $sheet->getStyle('F' . ($cellnumber + 11) . ':G' . ($cellnumber + 11))->applyFromArray(FFScopeStyles::renewColorChange());
}
