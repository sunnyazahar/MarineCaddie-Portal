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

    /**
     * Manifest footer: divider, centered shipped-by line, page number below it.
     */
    public function outputManifest(PDF $pdf): string
    {
        $pdf->render();

        $dompdf = $pdf->getDomPDF();
        $canvas = $dompdf->getCanvas();
        $font = $dompdf->getFontMetrics()->getFont('DejaVu Sans');
        $size = 10.0;
        $marginX = 28.35;
        $shippedBy = 'Shipped By: ' . CompanyAddress::NAME
            . ' | E-mail: ' . CompanyAddress::EMAIL
            . ' | Phone: ' . CompanyAddress::PHONE;

        $canvas->page_script(function (int $pageNumber, int $pageCount, Canvas $canvas, FontMetrics $fontMetrics) use (
            $font,
            $size,
            $marginX,
            $shippedBy
        ) {
            $width = $canvas->get_width();
            $height = $canvas->get_height();
            $ruleY = $height - 64.0;
            $shippedByY = $ruleY + 8.0;

            $canvas->line($marginX, $ruleY, $width - $marginX, $ruleY, [0.13, 0.13, 0.13], 0.6);

            $shippedByWidth = $fontMetrics->getTextWidth($shippedBy, $font, $size);
            $canvas->text(($width - $shippedByWidth) / 2, $shippedByY, $shippedBy, $font, $size);

            $pageSize = 8.0;
            $pageY = $shippedByY + 18.0;
            $pageLabel = $pageNumber . '/' . $pageCount;
            $pageLabelWidth = $fontMetrics->getTextWidth($pageLabel, $font, $pageSize);
            $canvas->text(($width - $pageLabelWidth) / 2, $pageY, $pageLabel, $font, $pageSize);
        });

        $output = $dompdf->output();

        if (! is_string($output) || strlen($output) < 100) {
            throw new \RuntimeException('PDF could not be generated with company footer.');
        }

        return $output;
    }

    /**
     * Render the PDF and stamp only the page number (e.g. 1/1) on every page.
     *
     * @param  float  $marginBottomMm  When set, places the page number just below the reserved footer band (proforma invoices).
     */
    public function outputPageNumbers(PDF $pdf, float $marginBottomMm = 0): string
    {
        $pdf->render();

        $dompdf = $pdf->getDomPDF();
        $canvas = $dompdf->getCanvas();
        $font = $dompdf->getFontMetrics()->getFont('DejaVu Sans');
        $size = 7.0;
        $physicalBottomOffsetPt = 10.0;

        $canvas->page_script(function (int $pageNumber, int $pageCount, Canvas $canvas, FontMetrics $fontMetrics) use (
            $font,
            $size,
            $marginBottomMm,
            $physicalBottomOffsetPt
        ) {
            $width = $canvas->get_width();
            $height = $canvas->get_height();

            if ($marginBottomMm > 0) {
                $marginBottomPt = $marginBottomMm * 2.834645669;
                $y = $height - $marginBottomPt + $physicalBottomOffsetPt;
            } else {
                $y = $height - $physicalBottomOffsetPt;
            }

            $pageLabel = $pageNumber . '/' . $pageCount;
            $pageLabelWidth = $fontMetrics->getTextWidth($pageLabel, $font, $size);
            $canvas->text(($width - $pageLabelWidth) / 2, $y, $pageLabel, $font, $size);
        });

        $output = $dompdf->output();

        if (! is_string($output) || strlen($output) < 100) {
            throw new \RuntimeException('PDF could not be generated with page numbers.');
        }

        return $output;
    }
}
