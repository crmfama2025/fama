<?php

namespace App\Services;

use App\Jobs\CompressPdfJob;

class PdfCompressionService
{
    public function compress($file, $path, $filename)
    {
        // $extension = pathinfo($filename, PATHINFO_EXTENSION);
        // $baseName  = pathinfo($filename, PATHINFO_FILENAME);
        // $baseName  = preg_replace('/[^A-Za-z0-9._\-]/', '_', $baseName);
        // $filename  = $baseName . '.' . $extension;
        // Temp upload
        $tempPath = $file->storeAs('temp', $filename, 'public');

        $inputPath = storage_path('app/public/' . $tempPath);
        $finalDir = storage_path('app/public/' . $path);
        $finalPath = $finalDir . '/' . $filename;

        if (!file_exists($finalDir)) {
            mkdir($finalDir, 0777, true);
        }

        $gsPath = PHP_OS_FAMILY === 'Windows' ? 'gswin64c' : 'gs';

        $command = $gsPath . ' -sDEVICE=pdfwrite '
            . '-dCompatibilityLevel=1.4 '
            . '-dPDFSETTINGS=/ebook '
            . '-dNOPAUSE -dQUIET -dBATCH '
            . '-sOutputFile=' . escapeshellarg($finalPath) . ' '
            . escapeshellarg($inputPath);


        // // dd($inputPath, $finalPath, $command);
        // $originalSize   = filesize($inputPath);
        // $compressedSize = filesize($finalPath);
        // dd($originalSize, $compressedSize);

        // exec($command);
        exec($command, $output, $returnCode);
        // dd($command, $output, $returnCode);

        unlink($inputPath);

        return $path . '/' . $filename;
    }
    // public function compress($file, $path, $filename): string
    // {
    //     // dd("test");
    //     // Store to temp immediately — fast, no blocking
    //     $tempPath  = $file->storeAs('temp', $filename, 'public');
    //     $inputPath = storage_path('app/public/' . $tempPath);

    //     $finalDir  = storage_path('app/public/' . $path);
    //     $finalPath = $finalDir . '/' . $filename;

    //     // Dispatch compression to background — returns instantly
    //     CompressPdfJob::dispatch($inputPath, $finalPath, $finalDir);

    //     // Return the expected final path immediately
    //     return $path . '/' . $filename;
    // }
}
