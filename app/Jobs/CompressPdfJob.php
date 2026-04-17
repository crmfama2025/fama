<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

// app/Jobs/CompressPdfJob.php
class CompressPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private string $tempPath,
        private string $finalPath,
        private string $finalDir,
        private ?int $modelId = null   // optional: to update a DB record when done
    ) {}

    public function handle(): void
    {
        dd("test");
        if (!file_exists($this->finalDir)) {
            mkdir($this->finalDir, 0777, true);
        }

        $gsPath  = PHP_OS_FAMILY === 'Windows' ? 'gswin64c' : 'gs';
        $command = $gsPath . ' -sDEVICE=pdfwrite '
            . '-dCompatibilityLevel=1.4 '
            . '-dPDFSETTINGS=/ebook '
            . '-dNOPAUSE -dQUIET -dBATCH '
            . '-sOutputFile=' . escapeshellarg($this->finalPath) . ' '
            . escapeshellarg($this->tempPath);

        exec($command, $output, $returnCode);

        if ($returnCode === 0 && file_exists($this->tempPath)) {
            unlink($this->tempPath);
        }

        // Optionally update your model status
        // if ($this->modelId) {
        //     YourModel::find($this->modelId)?->update(['status' => 'ready']);
        // }
    }
}
