<?php

namespace App\Exports;

use App\Models\PaymentMode;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PaymentModeExport implements FromCollection, WithHeadings
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
        $query = PaymentMode::query(); // use query() to get a Builder (was PaymentMode::all())

        if ($this->search) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('payment_mode_name', 'like', "%{$search}%")
                    ->orWhere('payment_mode_code', 'like', "%{$search}%")
                    ->orWhere('payment_mode_short_code', 'like', "%{$search}%")
                    // ->orWhereHas('company', function ($q2) use ($search) {
                    //     $q2->where('company_name', 'like', "%{$search}%");
                    // })
                    ->orWhereRaw("CAST(payment_modes.id AS CHAR) LIKE ?", ["%{$search}%"]);
            });
        }

        // if ($this->filter) {
        //     $query->where('company_id', $this->filter);
        // }

        return $query->get()
            ->map(function ($paymentMode) {
                return [
                    'ID' => $paymentMode->id,
                    'Payment Mode Code' => $paymentMode->payment_mode_code,
                    // 'Company' => $paymentMode->company->company_name ?? '',
                    'Payment Mode Name' => $paymentMode->payment_mode_name,
                    'Payment Mode Name Arabic' => $paymentMode->payment_mode_arabic_name,
                    'Payment Mode Short Code' => $paymentMode->payment_mode_short_code,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Payment Mode Code',
            // 'Company',
            'Payment Mode Name',
            'Payment Mode Name In Arabic',
            'Payment Mode Short Code'
        ];
    }
}
