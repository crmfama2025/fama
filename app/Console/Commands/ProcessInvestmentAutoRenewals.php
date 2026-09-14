<?php

namespace App\Console\Commands;

use App\Services\Investment\InvestmentService;
use App\Services\Investment\InvestorAgreementService;
use DateTimeImmutable;
use Illuminate\Console\Command;
use Throwable;

class ProcessInvestmentAutoRenewals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'investments:process-auto-renewals
        {--max-maturity-date= : Include investments maturing on or before YYYY-MM-DD}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Renew eligible investments approaching maturity';

    /**
     * Execute the console command.
     */
    public function handle(InvestorAgreementService $investmentAgreementService): int
    {
        $maxMaturityDate = $this->option('max-maturity-date');

        if ($maxMaturityDate !== null) {
            $date = DateTimeImmutable::createFromFormat(
                '!Y-m-d',
                $maxMaturityDate
            );

            if (
                !$date ||
                $date->format('Y-m-d') !== $maxMaturityDate
            ) {
                $this->error(
                    'Invalid max maturity date. Use YYYY-MM-DD, e.g. 2026-10-31.'
                );

                return self::FAILURE;
            }
        }

        try {
            $investmentAgreementService->processUpcomingAutoRenewals($maxMaturityDate);

            $this->info('Investment auto-renewal completed.');

            return self::SUCCESS;
        } catch (Throwable $exception) {
            report($exception);

            $this->error($exception->getMessage());

            return self::FAILURE;
        }
    }
}
