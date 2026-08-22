<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class GenericExport implements FromCollection, WithHeadings, WithStyles
{
    public function __construct(
        protected $data,
        protected array $headings,
        protected array $dateColumns = [],
        protected array $phoneColumns = [],
        protected array $amountColumns = []
    ) {}

    public function collection()
    {
        return collect($this->data)->map(function ($row) {

            $row = is_array($row) ? $row : (array) $row;

            foreach ($row as $key => $value) {

                // Convert Carbon to real Excel date
                if ($value instanceof Carbon) {
                    $row[$key] = Date::PHPToExcel($value);
                }

                // Convert phone numbers to string
                if (in_array($key, $this->phoneColumns)) {
                    $row[$key] = $value !== null
                        ? (string) $value
                        : '';
                }
            }

            return $row;
        });
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        /*
        |--------------------------------------------------------------------------
        | Header
        |--------------------------------------------------------------------------
        */

        $sheet->getStyle('1:1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => [
                    'rgb' => 'FFFFFF',
                ],
            ],
            'fill' => [
                'fillType' => 'solid',
                'color' => [
                    'rgb' => '1F4E78',
                ],
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(25);

        /*
        |--------------------------------------------------------------------------
        | All cells
        |--------------------------------------------------------------------------
        */

        $sheet->getStyle("A1:{$highestColumn}{$highestRow}")
            ->applyFromArray([
                'alignment' => [
                    'vertical' => 'center',
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => 'thin',
                        'color' => [
                            'rgb' => 'D9E1F2',
                        ],
                    ],
                ],
            ]);

        /*
        |--------------------------------------------------------------------------
        | Freeze Header
        |--------------------------------------------------------------------------
        */

        $sheet->freezePane('A2');

        /*
        |--------------------------------------------------------------------------
        | Filter
        |--------------------------------------------------------------------------
        */

        // $sheet->setAutoFilter(
        //     "A1:{$highestColumn}{$highestRow}"
        // );

        /*
        |--------------------------------------------------------------------------
        | Auto Size
        |--------------------------------------------------------------------------
        */

        foreach ($sheet->getColumnIterator() as $column) {
            $sheet->getColumnDimension(
                $column->getColumnIndex()
            )->setAutoSize(true);
        }

        /*
        |--------------------------------------------------------------------------
        | Date Columns
        |--------------------------------------------------------------------------
        */

        foreach ($this->dateColumns as $column) {

            $sheet->getStyle(
                "{$column}2:{$column}{$highestRow}"
            )
                ->getNumberFormat()
                ->setFormatCode('dd/mm/yyyy');
        }

        /*
        |--------------------------------------------------------------------------
        | Phone Columns
        |--------------------------------------------------------------------------
        */

        foreach ($this->phoneColumns as $column) {

            $sheet->getStyle(
                "{$column}2:{$column}{$highestRow}"
            )
                ->getNumberFormat()
                ->setFormatCode('@');
        }

        /*
        |--------------------------------------------------------------------------
        | Amount Columns
        |--------------------------------------------------------------------------
        */

        foreach ($this->amountColumns as $column) {

            $sheet->getStyle(
                "{$column}2:{$column}{$highestRow}"
            )
                ->getNumberFormat()
                ->setFormatCode('#,##0.00');
        }

        return $sheet;
    }
}
