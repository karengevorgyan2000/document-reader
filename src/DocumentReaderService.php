<?php

namespace Kareng\DocumentReader;

use Spatie\PdfToImage\Pdf;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Storage;
use Exception;

class DocumentReaderService
{
    private $tesseractPath;

    /**
     * Constructor allows optional Tesseract path.
     *
     * @param string|null $tesseractPath
     */
    public function __construct(string $tesseractPath = null)
    {
        if ($tesseractPath && file_exists($tesseractPath)) {
            $this->tesseractPath = $tesseractPath;
        } else {
            $path = trim(shell_exec(PHP_OS_FAMILY === 'Windows' ? 'where tesseract' : 'which tesseract'));
            if (!$path || !file_exists($path)) {
                throw new Exception(
                    "Tesseract OCR is not installed or not found. " .
                    "Please install it and make sure it's in your PATH, " .
                    "or provide the path in the constructor."
                );
            }
            $this->tesseractPath = $path;
        }
    }

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
        $imagePath = storage_path('app/temp_passport_image_' . uniqid() . '.jpg');
        $pdf->setPage(1)->saveImage($imagePath);

        $process = new Process([
            $this->tesseractPath,
            $imagePath,
            'stdout',
            '-l', 'ocrb',
            '--psm', '6',
            '--oem', '1'
        ]);

        $process->run();

        @unlink($imagePath);

        if (!$process->isSuccessful()) {
            throw new Exception("Tesseract OCR failed: " . $process->getErrorOutput());
        }

        $text = trim($process->getOutput());
        return $this->parseMrzLines($text);
    }

    /**
     * Parse MRZ data from OCR output.
     */
    protected function parseMrzLines (string $text) : ?array
    {
        $lines = preg_split('/\r\n|\r|\n/', trim($text));

        $mrzLines = [];

        foreach ($lines as $line) {
            $cleanLine = preg_replace('/[^A-Z0-9<]/', '', strtoupper($line));
            if (strlen($cleanLine) === 44) {
                $mrzLines[] = $cleanLine;
            }
        }

        return count($mrzLines) === 2 ? $mrzLines : null;
    }
}
