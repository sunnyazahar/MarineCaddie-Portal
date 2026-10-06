<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

/**
 * Rejects fake or dangerous PDFs. Applies when the file is named *.pdf or its
 * content is PDF; other types pass through (`mimes:pdf` in rules() rejects them).
 */
class ValidPdfUpload implements ValidationRule
{
    private const TAIL_BYTES = 4096;

    /** Cap per inflated object stream, so a zip-bomb cannot exhaust memory. */
    private const MAX_INFLATED_BYTES = 8388608;

    /** PDF names that run scripts or open external content when viewed. */
    private const ACTIVE_CONTENT = '~/(?:JavaScript|JS|Launch|RichMedia)(?=[\s/\[\]<>(){}%]|$)~';

    /**
     * Standard rules for every document/attachment upload (the application accepts PDF only).
     *
     * @return array<int, mixed>
     */
    public static function rules(int $maxKilobytes, bool $required = false): array
    {
        return array_merge(
            $required ? ['required'] : [],
            ['file', 'mimes:pdf', 'max:' . $maxKilobytes, new self()]
        );
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value instanceof UploadedFile || ! $value->isValid()) {
            return;
        }

        $namedPdf = strtolower((string) $value->getClientOriginalExtension()) === 'pdf';
        $contentIsPdf = $value->getMimeType() === 'application/pdf';

        if (! $namedPdf && ! $contentIsPdf) {
            return;
        }

        $data = @file_get_contents((string) $value->getRealPath());
        $name = '"' . $value->getClientOriginalName() . '"';

        if ($namedPdf !== $contentIsPdf || ! is_string($data) || ! $this->hasPdfStructure($data)) {
            $fail($name . ' is not a valid PDF file.');

            return;
        }

        if ($this->containsActiveContent($data)) {
            $fail($name . ' contains active content (scripts or launch actions) and cannot be uploaded.');
        }
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
