<?php

namespace App\Services;

use App\Jobs\CompressPdfJob;

class PdfCompressionService
{

    public function compress($file, $path, $filename): string
    {
        dd("test");
        // Store to temp immediately — fast, no blocking
        $tempPath  = $file->storeAs('temp', $filename, 'public');
        $inputPath = storage_path('app/public/' . $tempPath);

        $finalDir  = storage_path('app/public/' . $path);
        $finalPath = $finalDir . '/' . $filename;

        // Dispatch compression to background — returns instantly
        CompressPdfJob::dispatch($inputPath, $finalPath, $finalDir);

        // Return the expected final path immediately
        return $path . '/' . $filename;
    }
}
