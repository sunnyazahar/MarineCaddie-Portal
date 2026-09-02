<?php

namespace App\Services;

use RuntimeException;
use setasign\Fpdi\Fpdi;

class CombinedPoPdfMerger
{
    private const PAGE_NUMBER_FONT_SIZE_PT = 7.0;

    private const PAGE_NUMBER_BOTTOM_OFFSET_PT = 10.0;

    public function merge(array $absolutePaths, bool $stampContinuousPageNumbers = false): string
    {
        $pdf = $this->createFpdi();
        $pagesAdded = 0;
        $currentPage = 0;
        $totalPages = $stampContinuousPageNumbers ? $this->countPagesInFiles($absolutePaths) : 0;

        foreach ($absolutePaths as $file) {
            if (! is_readable($file)) {
                continue;
            }

            try {
                $pageCount = $pdf->setSourceFile($file);
            } catch (\Throwable) {
                continue;
            }

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);
                $orientation = $size['width'] > $size['height'] ? 'L' : 'P';
                $pdf->AddPage($orientation, [$size['width'], $size['height']]);
                $pdf->useTemplate($templateId);

                if ($stampContinuousPageNumbers && $totalPages > 0) {
                    $currentPage++;
                    $this->stampPageNumber($pdf, $currentPage, $totalPages);
                }

                $pagesAdded++;
            }
        }

        if ($pagesAdded === 0) {
            throw new RuntimeException('Unable to merge the selected PDF documents.');
        }

        return $pdf->Output('S');
    }

    /**
     * @param  list<string>  $pdfContents
     */
    public function mergeContents(array $pdfContents, bool $stampContinuousPageNumbers = false): string
    {
        $paths = [];

        try {
            foreach ($pdfContents as $content) {
                if (! is_string($content) || $content === '') {
                    continue;
                }

                $path = tempnam(sys_get_temp_dir(), 'mc_pdf_');
                if ($path === false) {
                    continue;
                }

                file_put_contents($path, $content);
                $paths[] = $path;
            }

            if ($paths === []) {
                throw new RuntimeException('Unable to merge the selected PDF documents.');
            }

            return $this->merge($paths, $stampContinuousPageNumbers);
        } finally {
            foreach ($paths as $path) {
                @unlink($path);
            }
        }
    }

    /**
     * @param  list<string>  $absolutePaths
     */
    private function countPagesInFiles(array $absolutePaths): int
    {
        $totalPages = 0;

        foreach ($absolutePaths as $file) {
            if (! is_readable($file)) {
                continue;
            }

            try {
                $counter = $this->createFpdi();
                $totalPages += $counter->setSourceFile($file);
            } catch (\Throwable) {
                continue;
            }
        }

        return $totalPages;
    }

    private function createFpdi(): Fpdi
    {
        return new Fpdi('P', 'pt');
    }

    private function stampPageNumber(Fpdi $pdf, int $pageNumber, int $pageCount): void
    {
        $label = $pageNumber . '/' . $pageCount;
        $pdf->SetFont('Helvetica', '', self::PAGE_NUMBER_FONT_SIZE_PT);
        $pdf->SetTextColor(0, 0, 0);

        $pageWidth = $pdf->GetPageWidth();
        $pageHeight = $pdf->GetPageHeight();
        $labelWidth = $pdf->GetStringWidth($label);
        $x = ($pageWidth - $labelWidth) / 2;
        $y = $pageHeight - self::PAGE_NUMBER_BOTTOM_OFFSET_PT;

        $pdf->Text($x, $y, $label);
    }
}
