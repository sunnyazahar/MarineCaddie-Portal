<?php

namespace Tests\Unit;

use App\Rules\ValidPdfUpload;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Finder\Finder;
use Tests\TestCase;

class ValidPdfUploadTest extends TestCase
{
    /** 1x1 transparent PNG. */
    private const PNG_BASE64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';

    /** @var list<string> */
    private array $tempFiles = [];

    protected function tearDown(): void
    {
        foreach ($this->tempFiles as $path) {
            @unlink($path);
        }

        parent::tearDown();
    }

    public function test_real_pdf_is_accepted(): void
    {
        $this->assertTrue($this->passes($this->upload($this->realPdf(), 'invoice.pdf')));
    }

    public function test_text_file_renamed_to_pdf_is_rejected_with_file_name_in_message(): void
    {
        $validator = Validator::make(
            ['file' => $this->upload("<?php echo 'hacked';\n", 'invoice.pdf')],
            ['file' => ValidPdfUpload::rules(10240, required: true)]
        );

        $this->assertTrue($validator->fails());
        $this->assertContains('"invoice.pdf" is not a valid PDF file.', $validator->errors()->get('file'));
    }

    public function test_image_renamed_to_pdf_is_rejected_even_when_image_types_are_allowed(): void
    {
        // `mimes:pdf,png` alone accepts this because PNG content is in the allow-list.
        $png = $this->upload(base64_decode(self::PNG_BASE64), 'invoice.pdf');

        $this->assertFalse(Validator::make(['file' => $png], ['file' => ['mimes:pdf,png', new ValidPdfUpload()]])->passes());
    }

    public function test_pdf_renamed_to_other_extension_is_rejected(): void
    {
        $this->assertFalse($this->passes($this->upload($this->realPdf(), 'photo.jpg')));
    }

    public function test_pdf_with_fake_header_only_is_rejected(): void
    {
        $this->assertFalse($this->passes($this->upload("%PDF-1.4\nnot really a pdf\n", 'invoice.pdf')));
    }

    public function test_pdf_with_javascript_action_is_rejected(): void
    {
        $pdf = $this->minimalPdf('<< /Type /Catalog /OpenAction << /S /JavaScript /JS (app.alert(1)) >> >>');

        $this->assertFalse($this->passes($this->upload($pdf, 'invoice.pdf')));
    }

    public function test_pdf_with_hex_obfuscated_javascript_is_rejected(): void
    {
        $pdf = $this->minimalPdf('<< /Type /Catalog /OpenAction << /S /J#61vaScript /J#53 (x) >> >>');

        $this->assertFalse($this->passes($this->upload($pdf, 'invoice.pdf')));
    }

    public function test_pdf_with_launch_action_is_rejected(): void
    {
        $pdf = $this->minimalPdf('<< /Type /Catalog /OpenAction << /S /Launch /F (cmd.exe) >> >>');

        $this->assertFalse($this->passes($this->upload($pdf, 'invoice.pdf')));
    }

    public function test_javascript_hidden_in_compressed_object_stream_is_rejected(): void
    {
        $packed = (string) gzcompress('5 0 << /S /JavaScript /JS (app.alert(1)) >>');
        $object = "4 0 obj\n<< /Type /ObjStm /N 1 /First 4 /Filter /FlateDecode /Length " . strlen($packed) . " >>\nstream\n"
            . $packed . "\nendstream\nendobj\n";

        $this->assertFalse($this->passes($this->upload($this->minimalPdf('<< /Type /Catalog >>', $object), 'invoice.pdf')));
    }

    public function test_script_like_bytes_inside_page_streams_do_not_cause_false_rejection(): void
    {
        $content = "BT /JS 12 Tf (/JavaScript is just text here) Tj ET";
        $object = "4 0 obj\n<< /Length " . strlen($content) . " >>\nstream\n" . $content . "\nendstream\nendobj\n";

        $this->assertTrue($this->passes($this->upload($this->minimalPdf('<< /Type /Catalog >>', $object), 'invoice.pdf')));
    }

    public function test_truncated_pdf_is_rejected(): void
    {
        $pdf = $this->realPdf();

        $this->assertFalse($this->passes($this->upload(substr($pdf, 0, (int) (strlen($pdf) * 0.6)), 'invoice.pdf')));
    }

    public function test_non_pdf_documents_are_rejected(): void
    {
        $this->assertFalse($this->passes($this->upload(base64_decode(self::PNG_BASE64), 'photo.png')));
        $this->assertFalse($this->passes($this->upload("PK\x03\x04fake-office-document", 'report.docx')));
    }

    public function test_document_uploads_only_use_the_pdf_rule_set(): void
    {
        $offenders = [];

        foreach ((new Finder())->files()->in(app_path())->name('*.php')->notName('ValidPdfUpload.php') as $file) {
            foreach (preg_split('/\R/', $file->getContents()) ?: [] as $number => $line) {
                // Only image-only rules (customer logo) may use `mimes:` directly.
                if (preg_match('/mimes:[^\'"]*\b(pdf|docx?|xlsx?|eml|msg|zip|csv|txt)\b/', $line)) {
                    $offenders[] = $file->getRelativePathname() . ':' . ($number + 1);
                }
            }
        }

        $this->assertSame([], $offenders, 'Document uploads must use ValidPdfUpload::rules() (PDF only).');
    }

    private function passes(UploadedFile $file): bool
    {
        return Validator::make(
            ['file' => $file],
            ['file' => ValidPdfUpload::rules(10240, required: true)]
        )->passes();
    }

    private function upload(string $contents, string $clientName): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'pdfrule');
        file_put_contents($path, $contents);
        $this->tempFiles[] = $path;

        return new UploadedFile($path, $clientName, null, null, true);
    }

    private function realPdf(): string
    {
        $pdf = new \FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->Cell(0, 10, 'Invoice 1001');

        return $pdf->Output('S');
    }

    private function minimalPdf(string $catalog, string $extraObjects = ''): string
    {
        return "%PDF-1.4\n1 0 obj\n{$catalog}\nendobj\n{$extraObjects}trailer\n<< /Root 1 0 R >>\n%%EOF\n";
    }
}
