<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

/**
 * Accepts genuine PDFs only (rejects other types, fake or dangerous PDFs and
 * oversized files), with one user-facing message that names the file.
 */
class ValidPdfUpload implements ValidationRule
{
    private const TAIL_BYTES = 4096;

    /** Cap per inflated object stream, so a zip-bomb cannot exhaust memory. */
    private const MAX_INFLATED_BYTES = 8388608;

    /** PDF names that run scripts or open external content when viewed. */
    private const ACTIVE_CONTENT = '~/(?:JavaScript|JS|Launch|RichMedia)(?=[\s/\[\]<>(){}%]|$)~';

    public function __construct(private ?int $maxKilobytes = null) {}

    /**
     * Standard rules for every document/attachment upload (the application accepts PDF only).
     * `bail` + this rule first: the user gets one clear message naming the file instead of
     * generic "The file field must be..." errors; mimes/max stay as a backstop.
     *
     * @return array<int, mixed>
     */
    public static function rules(int $maxKilobytes, bool $required = false): array
    {
        return array_merge(
            ['bail', new self($maxKilobytes)],
            $required ? ['required'] : [],
            ['file', 'mimes:pdf', 'max:' . $maxKilobytes]
        );
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value instanceof UploadedFile || ! $value->isValid()) {
            return;
        }

        $name = '"' . $value->getClientOriginalName() . '"';
        $namedPdf = strtolower((string) $value->getClientOriginalExtension()) === 'pdf';
        $contentIsPdf = $value->getMimeType() === 'application/pdf';

        if (! $namedPdf && ! $contentIsPdf) {
            $fail($name . ' is not a PDF. Only PDF files can be uploaded.');

            return;
        }

        if ($this->maxKilobytes !== null && (int) $value->getSize() > $this->maxKilobytes * 1024) {
            $fail($name . ' is larger than the ' . $this->formatLimit($this->maxKilobytes) . ' limit.');

            return;
        }

        $data = @file_get_contents((string) $value->getRealPath());

        if ($namedPdf !== $contentIsPdf || ! is_string($data) || ! $this->hasPdfStructure($data)) {
            $fail($name . ' is not a valid PDF file.');

            return;
        }

        if ($this->containsActiveContent($data)) {
            $fail($name . ' contains active content (scripts or launch actions) and cannot be uploaded.');
        }
    }

    private function formatLimit(int $kilobytes): string
    {
        if ($kilobytes < 1024) {
            return $kilobytes . ' KB';
        }

        return $kilobytes % 1024 === 0
            ? ($kilobytes / 1024) . ' MB'
            : round($kilobytes / 1024, 1) . ' MB';
    }

    private function hasPdfStructure(string $data): bool
    {
        return preg_match('/^%PDF-[12]\.\d/', $data) === 1
            && str_contains(substr($data, -self::TAIL_BYTES), '%%EOF');
    }

    /**
     * Scans object dictionaries only: page/image streams are skipped (binary data
     * would give false hits), compressed object streams are inflated and scanned,
     * and #xx name escapes are decoded so /J#61vaScript is caught too.
     */
    private function containsActiveContent(string $data): bool
    {
        $objects = '';
        $offset = 0;
        $length = strlen($data);

        while (($start = strpos($data, 'stream', $offset)) !== false) {
            if ($start >= 3 && substr($data, $start - 3, 3) === 'end') {
                $offset = $start + 6;

                continue;
            }

            $dictionary = substr($data, $offset, $start - $offset);
            $objects .= $dictionary;

            $end = strpos($data, 'endstream', $start + 6);
            if ($end === false) {
                $offset = $length;
                break;
            }

            $tail = substr($dictionary, -512);
            if (str_contains($tail, '/ObjStm') && str_contains($tail, '/FlateDecode')) {
                $body = ltrim(substr($data, $start + 6, $end - $start - 6), "\r\n");
                $inflated = @gzuncompress($body, self::MAX_INFLATED_BYTES);
                $objects .= "\n" . (is_string($inflated) ? $inflated : '');
            }

            $offset = $end + 9;
        }

        $objects .= substr($data, $offset);
        $objects = preg_replace_callback(
            '/#([0-9A-Fa-f]{2})/',
            static fn (array $hex) => chr((int) hexdec($hex[1])),
            $objects
        ) ?? $objects;

        return preg_match(self::ACTIVE_CONTENT, $objects) === 1;
    }
}
