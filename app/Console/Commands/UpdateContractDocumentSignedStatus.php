<?php

namespace App\Console\Commands;

use App\Models\Contract;
use App\Models\ContractDocument;
use App\Models\ContractStatusLogs;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateContractDocumentSignedStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contract:update-contract-document-signed-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $contractDocs = ContractDocument::where('signed_status', '!=', 2)
            ->where('document_type_id', 1)
            ->limit(5)
            ->get();

        $dataArr = $ids = array();
        foreach ($contractDocs as $contractDoc) {

            $dataArr = [
                'signed_document_path' => $contractDoc->original_document_path,
                'signed_document_name' => $contractDoc->original_document_name,
                'signed_status' => 2,
            ];

            $ids[] = $contractDoc->id;

            $contractDoc->update($dataArr);
        }

        $this->info("Date : " . now() . ", updated id : " . json_encode($ids) . " Modified status updated:" . $contractDocs->count());
    }
}
