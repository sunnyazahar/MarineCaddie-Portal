<?php

namespace Tests\Feature;

use App\Services\CombinedPoPdfMerger;
use App\Support\LogoHelper;
use Barryvdh\DomPDF\Facade\Pdf;
use Tests\TestCase;

class PdfSizeTest extends TestCase
{
    public function test_dompdf_subsets_fonts(): void
    {
        $this->assertTrue((bool) config('dompdf.options.enable_font_subsetting'));
    }

    public function test_rendered_and_consolidated_pdfs_stay_small(): void
    {
        $html = '<html><body style="font-family: DejaVu Sans, sans-serif">'
            . LogoHelper::pdfImgTag('220px')
            . '<h1>Invoice</h1><p><b>Freight charges</b> Dubai → Hamburg — 1,234.50 AED</p></body></html>';

        $single = Pdf::loadHTML($html)->setPaper('a4')->output();
        $merged = app(CombinedPoPdfMerger::class)->mergeContents([$single, $single, $single, $single], true);

        // Full DejaVu Sans embedding alone is ~900 KB per PDF.
        $this->assertLessThan(150 * 1024, strlen($single));
        $this->assertLessThan(600 * 1024, strlen($merged));
    }

    public function test_pdf_logo_uses_the_small_pdf_copy(): void
    {
        $this->assertStringContainsString(LogoHelper::base64DataUri('marinecaddie-logo-pdf.png'), LogoHelper::pdfImgTag());
        $this->assertLessThan(30 * 1024, filesize(public_path('files/assets/images/marinecaddie-logo-pdf.png')));
    }
}
