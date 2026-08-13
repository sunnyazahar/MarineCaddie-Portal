<?php

namespace App\Services;

use App\Support\CompanyAddress;
use Barryvdh\DomPDF\PDF;
use Dompdf\Canvas;
use Dompdf\FontMetrics;

class ShipmentPdfCompanyFooter
{
    /**
     * Render the PDF and stamp the MarineCaddie company footer on every page.
     */
    public function output(PDF $pdf, string $createdAt): string
    {
        $pdf->render();

        $dompdf = $pdf->getDomPDF();
        $canvas = $dompdf->getCanvas();
        $font = $dompdf->getFontMetrics()->getFont('DejaVu Sans');
        $size = 7.0;
        $lineHeight = 9.0;
        $marginX = 28.35; // ~10mm
        $bottomOffset = 42.0;

        $leftLines = CompanyAddress::footerLeftLines();
        $rightLines = [
            'Phone ' . CompanyAddress::PHONE,
            'Email ' . CompanyAddress::EMAIL,
            'Created on ' . $createdAt,
        ];

        $canvas->page_script(function (int $pageNumber, int $pageCount, Canvas $canvas, FontMetrics $fontMetrics) use (
            $font,
            $size,
            $lineHeight,
            $marginX,
            $bottomOffset,
            $leftLines,
            $rightLines
        ) {
            $width = $canvas->get_width();
            $height = $canvas->get_height();
            $y = $height - $bottomOffset;

            foreach ($leftLines as $index => $line) {
                $canvas->text($marginX, $y + ($index * $lineHeight), $line, $font, $size);
            }

            $pageLabel = $pageNumber . '/' . $pageCount;
            $pageLabelWidth = $fontMetrics->getTextWidth($pageLabel, $font, $size);
            $canvas->text(($width - $pageLabelWidth) / 2, $y + $lineHeight, $pageLabel, $font, $size);

            foreach ($rightLines as $index => $line) {
                $lineWidth = $fontMetrics->getTextWidth($line, $font, $size);
                $canvas->text($width - $marginX - $lineWidth, $y + ($index * $lineHeight), $line, $font, $size);
            }
        });

        $output = $dompdf->output();

        if (! is_string($output) || strlen($output) < 100) {
            throw new \RuntimeException('PDF could not be generated with company footer.');
        }

        return $output;
    }
}
