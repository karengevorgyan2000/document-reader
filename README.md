# Document Reader

A **Laravel package** for extracting **MRZ (Machine Readable Zone) data** from passport PDFs using **Tesseract OCR**. Effortlessly extract passport information from PDFs in a few lines of code.

---

## Installation

Install the package via Composer:

```bash
  composer require kareng/document-reader
```

## Usage

```
use Kareng\DocumentReader\DocumentReaderService;

$reader = new DocumentReaderService();

$data = $reader->extractMrzFromPdf($pathToPdf);
```

## Example of returned data:

```
P<UTOERIKSSON<<ANNA<MARIA<<<<<<<<<<<<<<<<<<<
L898902C36UTO7408122F1204159ZE184226B<<<<<10
```

## Requirements

```
PHP 8.0+

Laravel 10+

Tesseract OCR installed on your system

PDF must contain a clear image of the MRZ section
```