<?php

namespace App\Console\Commands;

use App\Models\InvestmentContractDocuments;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Throwable;

class MoveSignedAgreementPdfs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:move-signed-agreement-pdfs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move signed agreement PDFs and update their database paths';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $disk = Storage::disk('public');
        // $contractIds = $this->option('contract');
        $dryRun = (bool) $this->option('dry-run');

        $query = InvestmentContractDocuments::query()
            ->where('is_investor_signed', 1)
            ->where('is_company_signed', 1)
            ->where(function ($query) {
                $query
                    ->whereNull('signed_pdf_path')
                    ->orWhere('signed_pdf_path', '');
            });


        $moved = 0;
        $skipped = 0;
        $failed = 0;

        $query->orderBy('id')->chunkById(
            100,
            function ($contracts) use ($disk, $dryRun, &$moved, &$skipped, &$failed) {
                foreach ($contracts as $contract) {
                    $oldPath = $this->normalizeStoragePath($contract->contract_file_path);
                    dd($oldPath);
                    if (!$disk->exists($oldPath)) {
                        $this->warn("Contract {$contract->id}: source file missing: {$oldPath}");
                        $skipped++;
                        continue;
                    }

                    /*
                     * Process only PDF files.
                     */
                    if (strtolower(pathinfo($oldPath, PATHINFO_EXTENSION)) !== 'pdf') {
                        $this->warn("Contract {$contract->id}: source is not a PDF: {$oldPath}");
                        $skipped++;
                        continue;
                    }

                    $originalFileName = basename($oldPath);

                    $targetDirectory = sprintf(
                        'investments/%s/investments/%s',
                        $contract->investor->investor_code,
                        $contract->investment->investment_code
                    );

                    $newPath = sprintf(
                        '%s/%s',
                        $targetDirectory,
                        $originalFileName
                    );

                    if ($oldPath === $newPath) {
                        if (!$dryRun) {
                            $contract->forceFill([
                                'signed_pdf_path' => $newPath,
                            ])->save();
                        }

                        $this->info("Contract {$contract->id}: file already in target; path updated");
                        $moved++;
                        continue;
                    }

                    if ($disk->exists($newPath)) {
                        /*
                         * Target already exists. If appropriate, populate
                         * the new database column without overwriting it.
                         */
                        if (!$dryRun) {
                            $contract->forceFill([
                                'signed_pdf_path' => $newPath,
                            ])->save();
                        }

                        $this->warn("Contract {$contract->id}: target already exists; signed_pdf_path updated");
                        $skipped++;
                        continue;
                    }

                    if ($dryRun) {
                        $this->line("[DRY RUN] Contract {$contract->id}");
                        $this->line("  From: {$oldPath}");
                        $this->line("  To:   {$newPath}");
                        continue;
                    }

                    try {
                        $wasMoved = $disk->move(
                            $oldPath,
                            $newPath
                        );

                        if (!$wasMoved) {
                            throw new \RuntimeException('Storage move returned false.');
                        }

                        try {
                            $contract->forceFill([
                                'signed_pdf_path' => $newPath,
                            ])->save();
                        } catch (Throwable $databaseException) {
                            /*
                             * Restore the source file if the database
                             * update fails.
                             */
                            if ($disk->exists($newPath)) {
                                $disk->move(
                                    $newPath,
                                    $oldPath
                                );
                            }

                            throw $databaseException;
                        }

                        $this->info("Contract {$contract->id}: moved and updated");
                        $moved++;
                    } catch (Throwable $exception) {
                        $this->error("Contract {$contract->id}: {$exception->getMessage()}");
                        $failed++;
                    }
                }
            }
        );

        $this->newLine();

        $this->table(
            ['Result', 'Count'],
            [
                ['Moved/updated', $moved],
                ['Skipped', $skipped],
                ['Failed', $failed],
            ]
        );

        return $failed > 0
            ? self::FAILURE
            : self::SUCCESS;
    }

    private function normalizeStoragePath(string $path): string
    {
        $path = str_replace('\\', '/', trim($path));

        /*
         * Convert a public URL-style path:
         * /storage/contracts/file.pdf
         *
         * into a public-disk-relative path:
         * contracts/file.pdf
         */
        $path = preg_replace('#^(https?://[^/]+)?/storage/#', '', $path);

        return ltrim($path, '/');
    }
}
