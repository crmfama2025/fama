<?php

namespace App\Console\Commands;

use App\Models\Investment;
use App\Models\InvestorPayout;
use App\Repositories\Investment\InvestmentRepository;
use App\Repositories\Investment\InvestorAgreementRepository;
use App\Repositories\Investment\InvestorLedgerRepository;
use App\Services\Investment\InvestmentContractDocumentService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessInvestmentMaturity extends Command
{
    // protected $signature = 'investments:process-maturity';
    protected $signature = 'investments:process-maturity
                        {--investment= : Only process this investment ID}
                        {--dry-run : Show what would be processed, change nothing}';
    protected $description = 'Auto-terminate matured short-term investments and move capital to investor payouts';

    public function __construct(
        protected InvestmentRepository $investmentRepo,
        protected InvestorLedgerRepository $ledgerRepo,
        protected InvestorAgreementRepository $agreementRepo,
        protected InvestmentContractDocumentService $docService,
    ) {
        parent::__construct();
    }

    public function handle()
    {
        Auth::loginUsingId((int) config('app.system_user_id', 1));
        $query = Investment::where('investment_term_type', 2)
            ->where('investment_status', 1)
            ->where('terminate_status', 0)
            ->where('investment_amount', '>', 0)
            ->whereNotNull('maturity_date')
            ->whereDate('maturity_date', '<=', today());

        if ($id = $this->option('investment')) {
            $query->where('id', $id);
        }

        if ($this->option('dry-run')) {
            $this->table(
                ['ID', 'Code', 'Maturity', 'Capital'],
                $query->get()->map(fn($i) => [
                    $i->id,
                    $i->investment_code,
                    $i->getRawOriginal('maturity_date'),
                    $i->investment_amount,
                ])
            );
            return;
        }

        $query->orderBy('id')->chunkById(50, function ($investments) {
            foreach ($investments as $investment) {
                try {
                    $this->settle($investment->id);
                } catch (\Throwable $e) {
                    Log::error('Maturity settlement failed', [
                        'investment_id' => $investment->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        });

        $this->info('Maturity processing finished.');
        // Investment::where('investment_term_type', 2)      // short term
        //     ->where('investment_status', 1)
        //     ->where('terminate_status', 0)
        //     ->where('investment_amount', '>', 0)
        //     ->whereNotNull('maturity_date')
        //     ->whereDate('maturity_date', '<=', today())   // use '<' to wait until the day after
        //     ->orderBy('id')
        //     ->chunkById(50, function ($investments) {
        //         foreach ($investments as $investment) {
        //             try {
        //                 $this->settle($investment->id);
        //             } catch (\Throwable $e) {
        //                 // one failure must not stop the rest
        //                 Log::error('Maturity settlement failed', [
        //                     'investment_id' => $investment->id,
        //                     'error' => $e->getMessage(),
        //                 ]);
        //             }
        //         }
        //     });

        // $this->info('Maturity processing finished.');
    }

    protected function settle(int $investmentId): void
    {
        DB::transaction(function () use ($investmentId) {

            $inv = Investment::lockForUpdate()->find($investmentId);

            // Re-check inside the lock so a second run can't process it twice
            if (!$inv || (int) $inv->terminate_status !== 0 || (float) $inv->investment_amount <= 0) {
                return;
            }

            // The model accessors return d-m-Y strings, so use the raw value
            $maturity = Carbon::parse($inv->getRawOriginal('maturity_date'))->startOfDay();
            $capital  = (float) $inv->investment_amount;
            $userId   = auth()->id();

            // 1. Settlement/Termination document (type 5)
            $doc = $this->docService->createInvestorDocument($inv->investor_id, $inv->company_id, [
                'investor_id' => $inv->investor_id,
                'company_id' => $inv->company_id,
                'investor_agreement_template_id' => $this->agreementRepo->getActiveIdBytype(5),
                'investor_agreement_type_id' => 5,
                'added_by' => $userId,
                'applied_investments' => json_encode([$inv->id]),
                'generated_date' => $maturity,
                'investment_id' => 0,
            ]);

            // createInvestorDocument points at the latest LONG-term Mudarabah.
            // A short-term settlement should reference its own Mudarabah.
            if ($own = $inv->investmentContractDocument) {
                $doc->forceFill(['reference_mudarabah_id' => $own->id])->save();
            }

            // 2. Ledger entry (same shape as your manual termination)
            $ledger = $this->ledgerRepo->create([
                'investment_contract_document_id' => $doc->id,
                'investor_id' => $inv->investor_id,
                'company_id' => $inv->company_id,
                'investor_transaction_type_id' => '4',
                'transaction_amount' => $capital,
                'is_credit' => 0,
                'transaction_date' => $maturity,
                'added_by' => $userId,
                'investment_id' => 0,
                'withdrawal_status' => 1,   // 2 if maturity should skip manual approval
                'requested_date' => $maturity,
                'duration_days' => 0,
                'withdrawal_date' => $maturity,
                'withdrawal_month_profit' => 0,
            ]);

            // 3. Investment
            $this->investmentRepo->update($inv->id, [
                'has_partial_withdrawal' => 1,
                'investment_amount' => 0,
                'total_withdrawn_amount' => getTotalWithdrawnAmount($inv->id) + $capital,
                'terminate_status' => 1,
                'termination_requested_date' => $maturity->toDateString(),
                'termination_date' => $maturity->toDateString(),
                'termination_duration' => 0,
                'termination_requested_by' => $userId,
            ]);

            // 4. Bifurcation (assumes createPartialWithdrawal returns the model)
            $bif = $this->ledgerRepo->createPartialWithdrawal([
                'investment_id' => $inv->id,
                'ledger_id' => $ledger->id,
                'company_id' => $inv->company_id,
                'withdrawal_amount' => $capital,
                'previous_amount' => $capital,
                'balance_amount' => 0,
                'added_by' => $userId,
                'requested_date' => $maturity,
                'withdrawal_date' => $maturity,
                'duration_days' => 0,
                'withdrawal_month_profit' => 0,
                'balance_to_pay' => $capital,
            ]);

            // 5. Capital payout row
            InvestorPayout::firstOrCreate(
                [
                    'investment_id' => $inv->id,
                    'investor_id' => $inv->investor_id,
                    'payout_reference_id' => $bif->id,
                    'investment_profit_record_id' => null,
                    'bifurcation_id' => $bif->id,
                    'payout_type' => 6,
                    'payout_release_month' => now()->format('Y-m'),
                    'original_profit_date' => $maturity->format('Y-m-d'),
                ],
                [
                    'payout_amount' => $capital,
                    'amount_pending' => $capital,
                    'is_processed' => 0,
                ]
            );
        });
    }
}
