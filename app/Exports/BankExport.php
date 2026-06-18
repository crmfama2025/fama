<?php

namespace App\Exports;

use App\Models\Bank;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BankExport implements FromCollection, WithHeadings
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
        $query = Bank::query(); //with('company')

        if ($this->search) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('bank_name', 'like', "%{$search}%")
                    ->orWhere('bank_code', 'like', "%{$search}%")
                    ->orWhere('bank_short_code', 'like', "%{$search}%")
                    ->orWhereHas('company', function ($q2) use ($search) {
                        $q2->where('company_name', 'like', "%{$search}%");
                    })
                    ->orWhereRaw("CAST(banks.id AS CHAR) LIKE ?", ["%{$search}%"]);
            });
        }

        if ($this->filter) {
            $query->where('company_id', $this->filter);
        }

        return $query->get()
            ->map(function ($bank) {
                return [
                    'ID' => $bank->id,
                    'Bank Code' => $bank->bank_code,
                    'Company' => $bank->company->company_name ?? '',
                    'Bank Name' => $bank->bank_name,
                    'Bank Name in Arabic' => $bank->bank_arabic_name ?? '',
                    'Bank Short Code' => $bank->bank_short_code,
                    'status' => ($bank->status ?? 1) == 1 ? 'Active' : 'Inactive',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Bank Code',
            'Company',
            'Bank Name',
            'Bank Name In Arabic',
            'Bank Short Code',
            'Status'
        ];
    }
}
