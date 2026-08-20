<?php

namespace App\Services\Reports;


use App\Repositories\Reports\InvestmentReportRepository;

class InvestmentReportService
{
    public function __construct(
        protected InvestmentReportRepository $investmentReportRepository,


    ) {}

    public function getInvestmentDataTable(array $filters = [])
    {
        $query = $this->investmentReportRepository->getInvestmentQuery($filters);

        $columns = [
            ['data' => 'DT_RowIndex', 'name' => 'id'],
            ['data' => 'company_name', 'name' => 'company.company_name'],
            ['data' => 'invested_company_name', 'name' => 'investedCompany.company_name'],
            ['data' => 'investor_name', 'name' => 'investor.investor_name'],
            ['data' => 'investment_amount', 'name' => 'investment_amount'],
            ['data' => 'total_received_amount', 'name' => 'total_received_amount'],
            ['data' => 'investment_date', 'name' => 'investment_date'],
            ['data' => 'profit_interval', 'name' => 'profit_interval_name'],
            ['data' => 'profit_perc', 'name' => 'profit_perc'],
            ['data' => 'maturity_date', 'name' => 'maturity_date'],
            ['data' => 'profit_release_date', 'name' => 'profit_release_date'],
            ['data' => 'grace_period', 'name' => 'grace_period'],
            ['data' => 'payout_batch', 'name' => 'payoutBatch.batch_name'],
            ['data' => 'nominee_name', 'name' => 'nominee_name'],
            ['data' => 'total_profit_released', 'name' => 'total_profit_released'],
            ['data' => 'current_month_released', 'name' => 'current_month_released'],
            ['data' => 'outstanding_profit', 'name' => 'outstanding_profit'],
            ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false],
        ];

        return datatables()
            ->of($query)
            ->addIndexColumn()
            ->addColumn('company_name', fn($row) => $row->company->company_name ?? '-')
            ->addColumn(
                'invested_company_name',
                fn($row) =>
                $row->investedCompany->company_name ?? '-'
            )
            ->addColumn('investor_name', fn($row) => $row->investor->investor_name . " - " . $row->investor->investor_code ?? '-')

            ->addColumn('investment_amount', fn($row) => number_format($row->investment_amount, 2))
            ->addColumn('received_amount', fn($row) => number_format($row->total_received_amount, 2))
            ->addColumn('investment_date', fn($row) => getFormattedDate($row->investment_date))
            ->addColumn('profit_interval', fn($row) => $row->profitInterval->profit_interval_name ?? '-')
            ->addColumn('profit_perc', fn($row) => $row->profit_perc . '%')
            ->addColumn('maturity_date', fn($row) => getFormattedDate($row->maturity_date))
            ->addColumn('profit_release_date', fn($row) => $row->profit_release_date)
            ->addColumn('investment_tenure', fn($row) => $row->investment_tenure)

            ->addColumn('grace_period', fn($row) => $row->grace_period ?? '-')
            ->addColumn('batch_name', fn($row) => 'Batch ' . $row->payout_batch_id . ' (' . $row->payoutBatch->batch_name . ')' ?? '-')
            ->addColumn('nominee_details', function ($row) {
                $name  = $row->nominee_name ?? '-';
                $email = $row->nominee_email ?? '-';
                $phone = $row->nominee_phone ?? '-';

                return "
                    <strong class='text-capitalize'>{$name}</strong>
                    <p class='mb-0 text-primary'>{$email}</p>
                    <p class='text-muted small mb-0'>
                        <i class='fa fa-phone-alt text-danger'></i>
                        <span class='font-weight-bold'>{$phone}</span>
                    </p>
                ";
            })
            ->addColumn('referral_commission_amount', fn($row) => $row->investmentReferral->referral_commission_amount ?? '-')
            ->addColumn('referral_commission_perc', fn($row) => $row->investmentReferral->referral_commission_perc ?? '-')
            ->addColumn('investment_term_type', function ($row) {
                if ($row->investment_term_type == 1) {
                    return '<span class="badge bg-pink">Long Term</span>';
                }

                if ($row->investment_term_type == 2) {
                    return '<span class="badge bg-orange">Short Term</span>';
                }

                return '<span class="badge badge-secondary">-</span>';
            })


            ->rawColumns(['nominee_details', 'investment_term_type'])
            ->toJson();
    }
    public function getPendingList(array $filters = [])
    {
        $query = $this->investmentReportRepository->getPendings($filters);

        $columns = [
            ['data' => 'checkbox', 'name' => 'checkbox'],
            ['data' => 'investor_name', 'name' => 'investor.investor_name'],
            ['data' => 'company_name', 'name' => 'investment.company.company_name'],
            ['data' => 'investment_code', 'name' => 'investment.investment_code'],
            ['data' => 'payout_date', 'name' => 'payout_date'],
            ['data' => 'payout_type', 'name' => 'payout_type'],
            ['data' => 'payout_amount', 'name' => 'amount_pending'],
            ['data' => 'payment_mode', 'name' => 'payment_mode'],
            ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false],
        ];

        return datatables()
            ->of($query)
            ->addIndexColumn()
            ->addColumn('checkbox', function ($row) {
                return '<div class="icheck-primary d-inline">
                            <input type="checkbox" id="ichek' . $row->id . '" class="groupCheckbox"
                                name="investor_payout_id[' . $row->id . ']" value="' . $row->id . '">
                            <label for="ichek' . $row->id . '">
                            </label>
                        </div>';
            })

            ->addColumn('investor_name', function ($row) {
                $investor = $row->investor;

                if (!$investor) return '-';

                return "
                <a href='" . route('investor.show', $investor->id) . "' target='_blank'>
            <strong class='text-capitalize'>{$investor->investor_name}</strong>
            <p class='mb-0 text-primary'>{$investor->investor_email}</p>
            <p class='text-muted small'>
                <i class='fa fa-phone-alt text-danger'></i>
                <span class='font-weight-bold'>{$investor->investor_mobile}</span>
            </p>
            </a>
        ";
            })
            ->addColumn('company_name', fn($row) => ($row->investment->company->company_name) ? "<a href='" . route('company.show', $row->investment->company->id) . "' target='_blank'>" . $row->investment->company->company_name . "</a>" : '-')

            ->addColumn('investment_code', fn($row) => ($row->investment->investment_code) ? "<a href='" . route('investment.show', $row->investment->id) . "' target='_blank'>" . $row->investment->investment_code . "</a>" : '-')
            ->addColumn('payout_date', function ($row) {
                return getPayoutDate($row);
            })

            ->addColumn('payout_type', function ($row) {
                return match ($row->payout_type) {
                    1 => '<span class="badge badge-success">Profit</span>',
                    2 => '<span class="badge badge-info">Commission</span>',
                    // 3 => '<span class="badge badge-warning">Principal</span>',
                    // 4 => '<span class="badge badge-secondary">Pending Profit</span>',
                    // 6 => '<span class="badge bg-orange">Withdrawal/Settlement</span>',
                    6 => ($row->investment && $row->investment->terminate_status == 1)
                        ? '<span class="badge bg-danger">Settlement</span>'
                        : '<span class="badge bg-orange">Withdrawal</span>',
                    default => '-',
                };
            })

            ->addColumn('payout_amount', function ($row) {
                return number_format($row->amount_pending, 2);
            })

            ->addColumn('payment_mode', function ($row) {
                $investor = $row->investor;

                if (!$investor || !$investor->paymentMode) return '-';

                if (in_array($investor->paymentMode->id, [1, 4])) {
                    return $investor->paymentMode->payment_mode_name;
                }

                if ($investor->paymentMode->id == 2) {
                    $bankName = $investor->primaryBank->investor_bank_name ?? '-';
                    return $investor->paymentMode->payment_mode_name . ' - ' . $bankName;
                }

                return '-';
            })



            ->rawColumns(['investor_name', 'payout_type', 'checkbox', 'investment_code', 'company_name'])
            ->with(['columns' => $columns])
            ->toJson();
    }
}
