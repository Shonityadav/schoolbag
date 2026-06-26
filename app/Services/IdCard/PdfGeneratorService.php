<?php

namespace App\Services\IdCard;

use Barryvdh\DomPDF\Facade\Pdf;
use ZipArchive;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PdfGeneratorService
{
    /**
     * Generate a single PDF for multiple ID Cards.
     * 
     * @param array $htmlCards Array of HTML strings, each representing a single ID card wrapper.
     * @param string $format 'A4' or custom array [0,0,width,height]
     * @return string Raw PDF data
     */
    public function generateSinglePdf(array $htmlCards, $format = 'a4')
    {
        // Wrap all cards into a single printable HTML document
        $content = implode("\n", $htmlCards);
        
        $html = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"/>
            <style>
                @page { margin: 0px; }
                body { margin: 0px; padding: 0px; }
                .pdf-card-wrapper {
                    page-break-after: always;
                }
            </style>
        </head>
        <body>
            {$content}
        </body>
        </html>
        ";

        $pdf = Pdf::loadHTML($html)->setPaper($format, 'portrait');
        return $pdf->output();
    }

    /**
     * Generates a ZIP file containing multiple individual PDFs.
     * 
     * @param array $cardsData Array of arrays: ['html' => '...', 'filename' => 'xyz.pdf']
     * @param string $outputPath Where to save the generated ZIP
     * @param string $format 'A4' or custom size
     * @return bool
     */
    public function generateZip(array $cardsData, string $outputPath, $format = 'a4')
    {
        $tempDir = storage_path('app/temp_id_cards_' . Str::random(10));
        File::makeDirectory($tempDir, 0755, true);

        $zip = new ZipArchive();
        if ($zip->open($outputPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            
            foreach ($cardsData as $card) {
                $pdfData = $this->generateSinglePdf([$card['html']], $format);
                $filePath = $tempDir . '/' . $card['filename'];
                File::put($filePath, $pdfData);
                $zip->addFile($filePath, $card['filename']);
            }
            
            $zip->close();
            File::deleteDirectory($tempDir);
            return true;
        }

        File::deleteDirectory($tempDir);
        return false;
    }
}
