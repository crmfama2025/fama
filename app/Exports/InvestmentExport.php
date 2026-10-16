<?php

namespace App\Exports;

use App\Models\Investment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InvestmentExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $search;
    protected array $filters;

    public function __construct($search = null, array $filters = [])
    {
        $this->search = $search;
        $this->filters = $filters;
    }

    /**
     * Return filtered investment records for Excel export.
     */
    public function collection()
    {
        $filters = $this->filters;

        // Support search passed either separately or through the filters.
        $search = trim((string) (
            $this->search ?? ($filters['search'] ?? '')
        ));

        // Company access permissions.
        $permittedCompanyIds = getUserPermittedCompanyIds(
            auth()->user()->id,
            'investment'
        );

        $query = Investment::with([
            'investor',
            'payoutBatch',
            'profitInterval',
            'company',
            'companyAllocations.company',
            'investmentReferral.referrer',
            'investmentReferral.commissionFrequency',
            'investmentReferral.paymentTerm',
            'investedCompany',
        ]);

        // Preserve the existing company permission restriction.
        $query->whereHas('company', function ($q) use ($permittedCompanyIds) {
            $q->whereIn('company_id', $permittedCompanyIds);
        });

        /*
         |--------------------------------------------------------------------------
         | Investor filter
         |--------------------------------------------------------------------------
         */
        if (!empty($filters['investor_id'])) {
            $query->where(
                'investor_id',
                $filters['investor_id']
            );
        }

        /*
         |--------------------------------------------------------------------------
         | Company Name filter
         |--------------------------------------------------------------------------
         | Filters investments by investments.company_id.
         */
        if (!empty($filters['company_id'])) {
            $query->where(
                'company_id',
                $filters['company_id']
            );
        }

        /*
         |--------------------------------------------------------------------------
         | Investment Term Type filter
         |--------------------------------------------------------------------------
         | 1 = Long Term
         | 2 = Short Term
         */
        if (
            isset($filters['investment_term_type']) &&
            $filters['investment_term_type'] !== ''
        ) {
            $query->where(
                'investment_term_type',
                (int) $filters['investment_term_type']
            );
        }

        /*
         |--------------------------------------------------------------------------
         | DataTables global search
         |--------------------------------------------------------------------------
         */
        if ($search !== '') {
            $like = '%' . $search . '%';

            $query->where(function (Builder $q) use ($like, $search) {
                $q->where('investment_amount', 'like', $like)
                    ->orWhere('investment_date', 'like', $like)
                    ->orWhere('investment_code', 'like', $like)
                    ->orWhere('maturity_date', 'like', $like)
                    ->orWhere('profit_perc', 'like', $like)
                    ->orWhere('received_amount', 'like', $like)
                    ->orWhere('total_received_amount', 'like', $like)
                    ->orWhere('profit_release_date', 'like', $like)
                    ->orWhere('nominee_name', 'like', $like)
                    ->orWhere('nominee_email', 'like', $like)
                    ->orWhere('nominee_phone', 'like', $like)

                    // Investment code / ID searches.
                    ->orWhereRaw(
                        'CAST(investments.id AS CHAR) LIKE ?',
                        [$like]
                    )

                    // Investor name and code.
                    ->orWhereHas('investor', function ($investorQuery) use ($like) {
                        $investorQuery
                            ->where('investor_name', 'like', $like)
                            ->orWhere('investor_code', 'like', $like);
                    })

                    // Company name.
                    ->orWhereHas('company', function ($companyQuery) use ($like) {
                        $companyQuery->where(
                            'company_name',
                            'like',
                            $like
                        );
                    })

                    // Allocated invested company names.
                    ->orWhereHas(
                        'companyAllocations.company',
                        function ($companyQuery) use ($like) {
                            $companyQuery->where(
                                'company_name',
                                'like',
                                $like
                            );
                        }
                    )

                    // Profit interval.
                    ->orWhereHas('profitInterval', function ($intervalQuery) use ($like) {
                        $intervalQuery->where(
                            'profit_interval_name',
                            'like',
                            $like
                        );
                    })

                    // Payout batch.
                    ->orWhereHas('payoutBatch', function ($batchQuery) use ($like) {
                        $batchQuery->where(
                            'batch_name',
                            'like',
                            $like
                        );
                    })

                    // Referral commission and referrer name.
                    ->orWhereHas(
                        'investmentReferral',
                        function ($referralQuery) use ($like) {
                            $referralQuery
                                ->where(
                                    'referral_commission_amount',
                                    'like',
                                    $like
                                )
                                ->orWhereHas(
                                    'referrer',
                                    function ($referrerQuery) use ($like) {
                                        $referrerQuery->where(
                                            'investor_name',
                                            'like',
                                            $like
                                        );
                                    }
                                );
                        }
                    );

                // Allow searching by the displayed term type.
                if (strtolower($search) === 'long term') {
                    $q->orWhere('investment_term_type', 1);
                } elseif (strtolower($search) === 'short term') {
                    $q->orWhere('investment_term_type', 2);
                }
            });
        }

        $results = $query->get();

        /*
         |--------------------------------------------------------------------------
         | Prepare Excel rows
         |--------------------------------------------------------------------------
         */
        return $results->map(function ($row) {
            $investedCompanies = '-';

            if ($row->companyAllocations->isNotEmpty()) {
                $investedCompanies = $row->companyAllocations
                    ->map(function ($allocation) {
                        return ($allocation->company?->company_name ?? '-')
                            . ' - '
                            . number_format(
                                (float) $allocation->allocated_amount,
                                2
                            );
                    })
                    ->implode(",\n");
            } elseif ($row->invested_company_id) {
                $investedCompanies = (
                    $row->investedCompany?->company_name ?? '-'
                ) . ' - ' . number_format(
                    (float) $row->investment_amount,
                    2
                );
            }

            $referral = $row->investmentReferral;

            $investmentType = match ((int) $row->investment_type) {
                0 => 'New',
                1 => 'Renew',
                default => '-',
            };

            $termType = match ((int) $row->investment_term_type) {
                1 => 'Long Term',
                2 => 'Short Term',
                default => '-',
            };

            $payoutBatch = $row->payoutBatch
                ? 'Batch - ' . $row->payoutBatch->id
                    . ' (' . $row->payoutBatch->batch_name . ')'
                : '-';

            return [
                'ID' => $row->id,

                'Company Name' =>
                    $row->company?->company_name ?? '-',

                'Invested Companies' => $investedCompanies,

                'Investor Name' =>
                    $row->investor?->investor_name ?? '-',

                'Investor Email' =>
                    $row->investor?->investor_email ?? '-',

                'Investor Mobile' =>
                    $row->investor?->investor_mobile ?? '-',

                'Investment Type' => $investmentType,

                'Investment Term Type' => $termType,

                'Investment Amount' =>
                    $row->investment_amount ?? '-',

                'Received Amount' =>
                    $row->total_received_amount ?? '-',

                'Payout Batch' => $payoutBatch,

                'Investment Date' => $row->investment_date
                    ? Carbon::parse($row->investment_date)->format('d-m-Y')
                    : '-',

                'Maturity Date' => $row->maturity_date
                    ? Carbon::parse($row->maturity_date)->format('d-m-Y')
                    : '-',

                'Profit Percentage' =>
                    $row->profit_perc ?? '-',

                'Profit Release Date' =>
                    $row->profit_release_date ?? '-',

                'Profit Release Frequency' =>
                    $row->profitInterval?->profit_interval_name ?? '-',

                'Nominee Name' =>
                    $row->nominee_name ?? '-',

                'Nominee Email' =>
                    $row->nominee_email ?? '-',

                'Nominee Phone' =>
                    "'" . ($row->nominee_phone ?? '-'),

                'Referral Name' =>
                    $referral?->referrer?->investor_name ?? '-',

                'Referral Commission Amount' =>
                    $referral?->referral_commission_amount ?? '-',

                'Referral Commission %' =>
                    $referral?->referral_commission_perc ?? '-',

                'Referral Commission Frequency' =>
                    $referral?->commissionFrequency?->commission_frequency_name ?? '-',

                'Payment Terms' =>
                    $referral?->paymentTerm?->term_name ?? '-',
            ];
        });
    }

    /**
     * Excel column headings.
     */
    public function headings(): array
    {
        return [
            'ID',
            'Company Name',
            'Invested Companies',
            'Investor Name',
            'Investor Email',
            'Investor Mobile',
            'Investment Type',
            'Investment Term Type',
            'Investment Amount',
            'Received Amount',
            'Payout Batch',
            'Investment Date',
            'Maturity Date',
            'Profit Percentage',
            'Profit Release Date',
            'Profit Release Frequency',
            'Nominee Name',
            'Nominee Email',
            'Nominee Phone',
            'Referral Name',
            'Referral Commission Amount',
            'Referral Commission %',
            'Referral Commission Frequency',
            'Payment Terms',
        ];
    }

    /**
     * Excel worksheet formatting.
     */
    public function styles(Worksheet $sheet)
    {
        // Wrap text for the Invested Companies column.
        $sheet->getStyle('C1:C' . max(1, $sheet->getHighestRow()))
            ->getAlignment()
            ->setWrapText(true);

        // Wrap the heading row.
        $sheet->getStyle('A1:X1')
            ->getFont()
            ->setBold(true);

        return [];
    }
}