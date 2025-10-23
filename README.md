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
[
    'passport_number' => '123456789',
    'first_name'      => 'John',
    'last_name'       => 'Doe',
    'nationality'     => 'USA',
    'birth_date'      => '1990-01-01',
    'expiry_date'     => '2030-01-01',
    'gender'          => 'M',
    'country_code'    => 'USA'
]
```

## Requirements

```
PHP 8.0+

Laravel 10+

Tesseract OCR installed on your system

PDF must contain a clear image of the MRZ section
```