<?php

namespace App\Jobs;

use App\Models\InvestmentContractDocuments;
use App\Models\InvestmentSignatureEmailLog;
use App\Services\BrevoService;
use Spatie\Browsershot\Browsershot;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GenerateAndSendSignedAgreementPdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public int $backoff = 30;
    public int $timeout = 120; // Browsershot/Chromium can take longer than DomPDF did

    public function __construct(
        protected int $contractId
    ) {}

    public function handle(BrevoService $brevoService,): void
    {
        $contract = InvestmentContractDocuments::with(['investor', 'company'])
            ->findOrFail($this->contractId);


        $investorName = preg_replace(
            '/[^\p{L}\p{N}]+/u',
            '',
            $contract->investor->investor_name
        );

        $companyName = preg_replace(
            '/[^\p{L}\p{N}]+/u',
            '',
            $contract->company->company_name
        );
        $disk = Storage::disk('public');

        if (
            filled($contract->signed_pdf_path) &&
            $disk->exists($contract->signed_pdf_path)
        ) {
            $fileName = $contract->signed_pdf_path;
            $pdfBinary = $disk->get($fileName);

            if ($pdfBinary === '') {
                throw new \RuntimeException(
                    "Stored signed PDF is empty: {$fileName}"
                );
            }
        } else {
            $url = route('debug.contract-html', ['contractId' => $contract->id]);

            // $pdfBinary = Browsershot::url($url)
            //     ->setNodeBinary('/usr/bin/node')
            //     ->setNpmBinary('/usr/bin/npm')
            //     ->format('A4')
            //     ->showBackground()
            //     ->margins(0, 0, 0, 0)
            //     ->waitUntilNetworkIdle()
            //     ->timeout(60)
            //     ->pdf();

            $browsershot = Browsershot::url($url)
                ->windowSize(1240, 1754)
                ->deviceScaleFactor(1) // renders at 2x then downsamples - sharper text
                ->format('A4')
                ->showBackground()
                ->margins(0, 0, 0, 0)
                ->waitUntilNetworkIdle()
                ->timeout(60)
                ->noSandbox();

            if ($nodeBinary = config('services.browsershot.node_binary')) {
                $browsershot->setNodeBinary($nodeBinary);
            }

            if ($npmBinary = config('services.browsershot.npm_binary')) {
                $browsershot->setNpmBinary($npmBinary);
            }

            $pdfBinary = $browsershot
                ->waitForFunction(
                    "document.body.dataset.pdfReady === 'true'",
                    null,
                    15000
                )
                ->pdf();

            if ($pdfBinary === '') {
                throw new \RuntimeException(
                    "Browsershot generated an empty PDF for contract {$contract->id}."
                );
            }

            $path = 'investments/' .  $contract->investor->investor_code . '/investments';
            // if we want to store the pdf in storage, uncomment the following lines
            $fileName = $path . '/' . $contract->investment->investment_code . '/' . $investorName . '-' . $companyName . '-Signed-Agreement.pdf';
            // $fileName = 'contracts/' . $contract->id . '/signed-agreement-' . now()->format('Ymd-His') . '.pdf';
            // Storage::disk('public')->put($fileName, $pdfBinary);
            $stored = $disk->put($fileName, $pdfBinary);

            if (!$stored) {
                throw new \RuntimeException(
                    "Unable to store signed PDF: {$fileName}"
                );
            }

            $contract->forceFill([
                'signed_pdf_path' => $fileName,
            ])->save();
        }


        // $investorName = preg_replace('/[^A-Za-z0-9 ]/', ' ', $contract->investor->investor_name);
        // $investorName = preg_replace('/\s+/', ' ', trim($investorName));

        // $companyname = preg_replace('/[^A-Za-z0-9 ]/', ' ', $contract->company->company_name);

        // --- send to investor ---
        $this->sendAndLog(
            $brevoService,
            $contract,
            'investor',
            $contract->investor->investor_name,
            $contract->investor->investor_email,
            $pdfBinary,
            $investorName,
            $companyName
        );

        // --- send to company ---
        $this->sendAndLog(
            $brevoService,
            $contract,
            'company',
            $contract->company->company_name,
            $contract->company->owner_email,
            $pdfBinary,
            $investorName,
            $companyName
        );

        // dd($response->successful());
        // if (!$response->successful()) {
        //     Log::warning('Brevo signed-agreement email failed', [
        //         'contract_id' => $contract->id,
        //         'status'      => $response->status(),
        //         'body'        => $response->body(),
        //     ]);
        // }

        $contract->forceFill([
            'both_notified_at' => now(),
        ])->save();
    }

    /**
     * Send a single email and log the attempt/result.
     */
    private function sendAndLog(
        BrevoService $brevoService,
        InvestmentContractDocuments $contract,
        string $recipientType,
        string $recipientName,
        string $recipientEmail,
        string $pdfBinary,
        string $investorName,
        string $companyname
    ): void {
        $subject = 'Your Signed Investment Agreement';
        $template = 'admin.emails.signed-agreement-email';
        $fileName = $investorName . '-' . $companyname . '-Signed-Agreement.pdf';

        $log = InvestmentSignatureEmailLog::create([
            'investment_contract_document_id'  => $contract->id,
            'recipient_type'  => $recipientType,
            'recipient_email' => $recipientEmail,
            'recipient_name'  => $recipientName,
            'subject'         => $subject,
            'template'        => $template,
            'status'          => 'pending',
        ]);

        try {
            $result = $brevoService->sendEmail(
                [['email' => $recipientEmail, 'name' => $recipientName]],
                $subject,
                $template,
                ['name' => $recipientName],
                [['content' => base64_encode($pdfBinary), 'name' => $fileName]]
            );

            $success = $result === "true";

            $log->update([
                'status'   => $success ? 'success' : 'failed',
                'response' => is_string($result) ? $result : json_encode($result),
                'sent_at'  => $success ? now() : null,
            ]);

            if (!$success) {
                Log::warning('Signed agreement email failed', [
                    'contract_id'    => $contract->id,
                    'recipient_type' => $recipientType,
                    'result'         => $result,
                ]);
            }
        } catch (\Throwable $e) {
            $log->update([
                'status'   => 'failed',
                'response' => $e->getMessage(),
            ]);

            Log::error('Signed agreement email threw exception', [
                'contract_id'    => $contract->id,
                'recipient_type' => $recipientType,
                'error'          => $e->getMessage(),
            ]);
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Failed to generate/send signed agreement PDF', [
            'contract_id' => $this->contractId,
            'error'       => $exception->getMessage(),
        ]);
    }
}
