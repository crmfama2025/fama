<?php

namespace App\Services;

class PdfCompressionService
{
    public function compress($file, $path, $filename)
    {
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

        // exec($command);
        exec($command, $output, $returnCode);
        // dd($command, $output, $returnCode);

        unlink($inputPath);

        return $path . '/' . $filename;
    }
}
