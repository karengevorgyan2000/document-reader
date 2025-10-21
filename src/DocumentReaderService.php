<?php

namespace Kareng\DocumentReader;

use Spatie\PdfToImage\Pdf;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Storage;
use Exception;

class DocumentReaderService
{
    /**
     * Extract MRZ data from a passport PDF using Tesseract OCR.
     *
     * @param string $pdfPath Absolute path to the PDF file.
     * @return array
     * @throws Exception
     */
    public function extractMrzData(string $pdfPath): array
    {
        if (!file_exists($pdfPath)) {
            throw new Exception("PDF file not found at path: {$pdfPath}");
        }

        $pdf = new Pdf($pdfPath);
        $imagePath = storage_path('app/temp_passport_image.jpg');
        $pdf->setPage(1)->saveImage($imagePath);

        $process = new Process([
            'tesseract',
            $imagePath,
            'stdout',
            '-l', 'ocrb',
            '--psm', '6',
            '--oem', '1'
        ]);

        $process->run();

        if (!$process->isSuccessful()) {
            throw new Exception("Tesseract OCR failed: " . $process->getErrorOutput());
        }

        $text = trim($process->getOutput());
        $mrz = $this->parseMrz($text);

        @unlink($imagePath);

        return $mrz;
    }

    /**
     * Parse MRZ data from OCR output.
     */
    private function parseMrz(string $text): array
    {
        $lines = array_values(array_filter(array_map('trim', explode("\n", $text))));
        $mrz = implode('', $lines);

        return [
            'raw' => $mrz,
            'passport_number' => substr($mrz, 0, 9) ?? null,
            'country' => substr($mrz, 10, 3) ?? null,
        ];
    }
}
