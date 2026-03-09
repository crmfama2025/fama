<?php

namespace App\Console\Commands;

use App\Models\Contract;
use App\Models\ContractStatusLogs;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateContractApprovalStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contracts:update-contract-approval-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update approval status and data for data entry';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $contracts = Contract::where('contract_status', 1)
            ->where('is_vendor_contract_uploaded', 1)
            ->whereHas('contract_documents', function ($query) {
                $query->where('document_type_id', 1)
                    ->where('signed_status', 2);
            })
            // ->limit(5)
            ->get();

        $dataArr = $ids = array();
        foreach ($contracts as $contract) {

            $dataArr = [
                'contract_status' => 7,
                'contract_id' => $contract->id,
                'approved_by' => 1,
                'signed_by' => $contract->added_by,
                'approved_date' => Carbon::now(),
                'signed_at' => Carbon::now(),
            ];

            ContractStatusLogs::create([
                'contract_id' => $contract->id,
                'old_status'   => $contract->contract_status,
                'new_status'   => 7,
                'changed_at'   => now(),
            ]);

            $ids[] = $contract->id;

            $contract->update($dataArr);
        }

        $this->info("Date : " . now() . ", updated id : " . json_encode($ids) . " Approved status updated:" . $contracts->count());
    }
}
