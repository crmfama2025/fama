<?php

namespace App\Console\Commands;

use App\Services\Investment\InvestmentService;
use App\Services\Investment\InvestorAgreementService;
use Illuminate\Console\Command;
use Throwable;

class ProcessInvestmentAutoRenewals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'investments:process-auto-renewals';

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
        try {
            $investmentAgreementService->processUpcomingAutoRenewals();

            $this->info('Investment auto-renewal completed.');

            return self::SUCCESS;
        } catch (Throwable $exception) {
            report($exception);

            $this->error($exception->getMessage());

            return self::FAILURE;
        }
    }
}
