<?php

namespace App\Exports;

use App\Models\Nationality;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class NationalityExport implements FromCollection, WithHeadings
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
        $query = Nationality::query(); //with('company')

        if ($this->search) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('nationality_name', 'like', "%{$search}%")
                    ->orWhere('nationality_code', 'like', "%{$search}%")
                    ->orWhere('nationality_short_code', 'like', "%{$search}%")
                    // ->orWhereHas('company', function ($q2) use ($search) {
                    //     $q2->where('company_name', 'like', "%{$search}%");
                    // })
                    ->orWhereRaw("CAST(nationalities.id AS CHAR) LIKE ?", ["%{$search}%"]);
            });
        }

        // if ($this->filter) {
        //     $query->where('company_id', $this->filter);
        // }

        return $query->get()
            ->map(function ($nationality) {
                return [
                    'ID' => $nationality->id,
                    'Nationality Code' => $nationality->nationality_code,
                    // 'Company' => $nationality->company->company_name ?? '',
                    'Nationality Name' => $nationality->nationality_name,
                    'Nationality Name In Arabic' => $nationality->nationality_arabic_name,
                    'Nationality Short Code' => $nationality->nationality_short_code,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nationality Code',
            // 'Company',
            'Nationality Name',
            'Nationality Name In Arabic',
            'Nationality Short Code'
        ];
    }
}
