<?php

namespace App\Support;

use DateTimeInterface;
use ZipArchive;

/**
 * Minimal .xlsx writer (Office Open XML) without PhpSpreadsheet.
 * Supports typed cells (string / int / float / date), column widths, and wrap text.
 */
class SimpleXlsxWriter
{
    private const DATE_NUM_FMT_ID = 164;

    private const STYLE_DEFAULT = 0;

    private const STYLE_HEADER = 1;

    private const STYLE_WRAP = 2;

    private const STYLE_DATE = 3;

    private const STYLE_DECIMAL = 4;

    private const STYLE_HEADER_WRAP = 5;

    /**
     * @param  list<string>  $headers
     * @param  list<list<mixed>>  $rows
     * @param  array{
     *     columnTypes?: list<string>,
     *     wrapColumns?: list<int>,
     *     fixedWidths?: array<int, float>,
     *     maxAutoWidth?: float,
     *     minAutoWidth?: float
     * }  $options
     */
    public static function build(array $headers, array $rows, string $sheetName = 'Sheet1', array $options = []): string
    {
        $tmp = tempnam(sys_get_temp_dir(), 'xlsx_');
        if ($tmp === false) {
            throw new \RuntimeException('Unable to create temporary xlsx file.');
        }

        $zipPath = $tmp.'.xlsx';
        @unlink($zipPath);
        rename($tmp, $zipPath);

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            @unlink($zipPath);
            throw new \RuntimeException('Unable to open xlsx archive.');
        }

        $safeSheetName = self::sanitizeSheetName($sheetName);
        $columnTypes = array_values($options['columnTypes'] ?? []);
        $wrapColumns = array_values(array_map('intval', $options['wrapColumns'] ?? []));
        $fixedWidths = $options['fixedWidths'] ?? [];
        $maxAutoWidth = (float) ($options['maxAutoWidth'] ?? 42);
        $minAutoWidth = (float) ($options['minAutoWidth'] ?? 10);

        $widths = self::resolveColumnWidths($headers, $rows, $columnTypes, $fixedWidths, $minAutoWidth, $maxAutoWidth);

        $zip->addFromString('[Content_Types].xml', self::contentTypesXml());
        $zip->addFromString('_rels/.rels', self::rootRelsXml());
        $zip->addFromString('xl/workbook.xml', self::workbookXml($safeSheetName));
        $zip->addFromString('xl/_rels/workbook.xml.rels', self::workbookRelsXml());
        $zip->addFromString('xl/styles.xml', self::stylesXml());
        $zip->addFromString(
            'xl/worksheets/sheet1.xml',
            self::sheetXml($headers, $rows, $columnTypes, $wrapColumns, $widths)
        );
        $zip->close();

        $binary = file_get_contents($zipPath);
        @unlink($zipPath);

        if ($binary === false) {
            throw new \RuntimeException('Unable to read generated xlsx file.');
        }

        return $binary;
    }

    private static function sanitizeSheetName(string $name): string
    {
        $name = preg_replace('/[\\\\\/\\?\\*\\[\\]:]/', '', $name) ?? 'Sheet1';
        $name = trim($name);

        return $name !== '' ? mb_substr($name, 0, 31) : 'Sheet1';
    }

    private static function contentTypesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            .'<Default Extension="xml" ContentType="application/xml"/>'
            .'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            .'<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            .'<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            .'</Types>';
    }

    private static function rootRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            .'</Relationships>';
    }

    private static function workbookXml(string $sheetName): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
            .'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            .'<sheets>'
            .'<sheet name="'.self::xml($sheetName).'" sheetId="1" r:id="rId1"/>'
            .'</sheets>'
            .'</workbook>';
    }

    private static function workbookRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            .'<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            .'</Relationships>';
    }

    private static function stylesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<numFmts count="1">'
            .'<numFmt numFmtId="'.self::DATE_NUM_FMT_ID.'" formatCode="dd/mm/yy"/>'
            .'</numFmts>'
            .'<fonts count="2">'
            .'<font><sz val="14"/><color theme="1"/><name val="Calibri"/><family val="2"/></font>'
            .'<font><b/><sz val="14"/><color rgb="FF000000"/><name val="Calibri"/><family val="2"/></font>'
            .'</fonts>'
            .'<fills count="3">'
            .'<fill><patternFill patternType="none"/></fill>'
            .'<fill><patternFill patternType="gray125"/></fill>'
            .'<fill><patternFill patternType="solid"><fgColor rgb="FFCCCCCC"/><bgColor indexed="64"/></patternFill></fill>'
            .'</fills>'
            .'<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
            .'<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            .'<cellXfs count="6">'
            // 0 default
            .'<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            // 1 header
            .'<xf numFmtId="0" fontId="1" fillId="2" borderId="0" xfId="0" applyFont="1" applyFill="1">'
            .'<alignment vertical="center" wrapText="1"/>'
            .'</xf>'
            // 2 wrap text
            .'<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0" applyAlignment="1">'
            .'<alignment vertical="top" wrapText="1"/>'
            .'</xf>'
            // 3 date dd/mm/yy
            .'<xf numFmtId="'.self::DATE_NUM_FMT_ID.'" fontId="0" fillId="0" borderId="0" xfId="0" applyNumberFormat="1"/>'
            // 4 decimal 0.00
            .'<xf numFmtId="2" fontId="0" fillId="0" borderId="0" xfId="0" applyNumberFormat="1"/>'
            // 5 header wrap (same as header)
            .'<xf numFmtId="0" fontId="1" fillId="2" borderId="0" xfId="0" applyFont="1" applyFill="1" applyAlignment="1">'
            .'<alignment vertical="center" wrapText="1"/>'
            .'</xf>'
            .'</cellXfs>'
            .'<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            .'</styleSheet>';
    }

    /**
     * @param  list<string>  $headers
     * @param  list<list<mixed>>  $rows
     * @param  list<string>  $columnTypes
     * @param  list<int>  $wrapColumns
     * @param  list<float>  $widths
     */
    private static function sheetXml(
        array $headers,
        array $rows,
        array $columnTypes,
        array $wrapColumns,
        array $widths
    ): string {
        $colCount = count($headers);
        $wrapLookup = array_fill_keys($wrapColumns, true);

        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">';

        $xml .= '<cols>';
        foreach ($widths as $index => $width) {
            $col = $index + 1;
            $xml .= '<col min="'.$col.'" max="'.$col.'" width="'.self::xmlNumber($width).'" customWidth="1"/>';
        }
        $xml .= '</cols>';

        $xml .= '<sheetData>';

        $headerHeight = self::estimateRowHeight(
            array_map(static fn ($h) => (string) $h, $headers),
            $widths,
            array_fill(0, $colCount, true),
            20
        );
        $xml .= '<row r="1" ht="'.self::xmlNumber($headerHeight).'" customHeight="1">';
        foreach (array_values($headers) as $colIndex => $header) {
            $cell = self::cellRef(1, $colIndex + 1);
            $style = isset($wrapLookup[$colIndex]) ? self::STYLE_HEADER_WRAP : self::STYLE_HEADER;
            $xml .= '<c r="'.$cell.'" t="inlineStr" s="'.$style.'"><is><t>'.self::xml((string) $header).'</t></is></c>';
        }
        $xml .= '</row>';

        foreach (array_values($rows) as $rowIndex => $row) {
            $excelRow = $rowIndex + 2;
            $displayValues = [];
            for ($colIndex = 0; $colIndex < $colCount; $colIndex++) {
                $displayValues[$colIndex] = self::displayLengthValue($row[$colIndex] ?? null, $columnTypes[$colIndex] ?? 'string');
            }

            $rowHeight = self::estimateRowHeight($displayValues, $widths, $wrapLookup, 18);
            $xml .= '<row r="'.$excelRow.'" ht="'.self::xmlNumber($rowHeight).'" customHeight="1">';

            for ($colIndex = 0; $colIndex < $colCount; $colIndex++) {
                $cell = self::cellRef($excelRow, $colIndex + 1);
                $type = $columnTypes[$colIndex] ?? 'string';
                $value = $row[$colIndex] ?? null;
                $wrap = isset($wrapLookup[$colIndex]);
                $xml .= self::typedCellXml($cell, $value, $type, $wrap);
            }

            $xml .= '</row>';
        }

        $xml .= '</sheetData></worksheet>';

        return $xml;
    }

    private static function typedCellXml(string $cell, mixed $value, string $type, bool $wrap): string
    {
        if ($value === null || $value === '') {
            return $wrap
                ? '<c r="'.$cell.'" s="'.self::STYLE_WRAP.'"/>'
                : '<c r="'.$cell.'"/>';
        }

        if ($type === 'date') {
            $serial = self::toExcelDateSerial($value);
            if ($serial === null) {
                return '<c r="'.$cell.'" t="inlineStr"><is><t>'.self::xml((string) $value).'</t></is></c>';
            }

            return '<c r="'.$cell.'" s="'.self::STYLE_DATE.'"><v>'.self::xmlNumber($serial).'</v></c>';
        }

        if ($type === 'int') {
            if (! is_numeric($value)) {
                return '<c r="'.$cell.'" t="inlineStr"><is><t>'.self::xml((string) $value).'</t></is></c>';
            }

            return '<c r="'.$cell.'"><v>'.(int) $value.'</v></c>';
        }

        if ($type === 'float') {
            if (! is_numeric($value)) {
                return '<c r="'.$cell.'" t="inlineStr"><is><t>'.self::xml((string) $value).'</t></is></c>';
            }

            return '<c r="'.$cell.'" s="'.self::STYLE_DECIMAL.'"><v>'.self::xmlNumber((float) $value).'</v></c>';
        }

        $stringValue = (string) $value;
        $style = $wrap ? ' s="'.self::STYLE_WRAP.'"' : '';

        return '<c r="'.$cell.'" t="inlineStr"'.$style.'><is><t>'.self::xml($stringValue).'</t></is></c>';
    }

    private static function toExcelDateSerial(mixed $value): ?float
    {
        try {
            if ($value instanceof DateTimeInterface) {
                $dt = \DateTimeImmutable::createFromInterface($value);
            } else {
                $dt = new \DateTimeImmutable((string) $value);
            }
        } catch (\Throwable) {
            return null;
        }

        $utc = $dt->setTimezone(new \DateTimeZone('UTC'))->setTime(0, 0, 0);
        $epoch = new \DateTimeImmutable('1899-12-30 00:00:00', new \DateTimeZone('UTC'));

        return ($utc->getTimestamp() - $epoch->getTimestamp()) / 86400;
    }

    /**
     * @param  list<string>  $headers
     * @param  list<list<mixed>>  $rows
     * @param  list<string>  $columnTypes
     * @param  array<int, float>  $fixedWidths
     * @return list<float>
     */
    private static function resolveColumnWidths(
        array $headers,
        array $rows,
        array $columnTypes,
        array $fixedWidths,
        float $minAutoWidth,
        float $maxAutoWidth
    ): array {
        $colCount = count($headers);
        $widths = [];

        for ($col = 0; $col < $colCount; $col++) {
            if (isset($fixedWidths[$col])) {
                $widths[$col] = (float) $fixedWidths[$col];
                continue;
            }

            $maxLen = self::visualLength((string) ($headers[$col] ?? ''));
            foreach ($rows as $row) {
                $maxLen = max(
                    $maxLen,
                    self::visualLength(self::displayLengthValue($row[$col] ?? null, $columnTypes[$col] ?? 'string'))
                );
            }

            // Calibri 14 ≈ 1.15 width units per character + padding
            $auto = max($minAutoWidth, min($maxAutoWidth, ($maxLen * 1.15) + 2.5));
            $widths[$col] = round($auto, 2);
        }

        return $widths;
    }

    /**
     * @param  list<string>  $values
     * @param  list<float>  $widths
     * @param  array<int, bool>  $wrapLookup
     */
    private static function estimateRowHeight(array $values, array $widths, array $wrapLookup, float $baseHeight): float
    {
        $lines = 1;
        foreach ($values as $col => $value) {
            if (! isset($wrapLookup[$col]) || $wrapLookup[$col] !== true) {
                continue;
            }

            $width = max(4.0, (float) ($widths[$col] ?? 10));
            $charsPerLine = max(4, (int) floor($width * 0.95));
            $text = (string) $value;
            $estimated = max(1, (int) ceil(max(1, self::visualLength($text)) / $charsPerLine));
            $lines = max($lines, $estimated);
        }

        return max($baseHeight, $lines * ($baseHeight * 0.95));
    }

    private static function displayLengthValue(mixed $value, string $type): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        if ($type === 'date') {
            try {
                $dt = $value instanceof DateTimeInterface
                    ? \DateTimeImmutable::createFromInterface($value)
                    : new \DateTimeImmutable((string) $value);

                return $dt->format('d/m/y');
            } catch (\Throwable) {
                return (string) $value;
            }
        }

        if ($type === 'float' && is_numeric($value)) {
            return number_format((float) $value, 2, '.', '');
        }

        if ($type === 'int' && is_numeric($value)) {
            return (string) (int) $value;
        }

        return (string) $value;
    }

    private static function visualLength(string $value): int
    {
        return max(1, mb_strlen($value));
    }

    private static function cellRef(int $row, int $col): string
    {
        $name = '';
        while ($col > 0) {
            $col--;
            $name = chr(65 + ($col % 26)).$name;
            $col = intdiv($col, 26);
        }

        return $name.$row;
    }

    private static function xml(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    private static function xmlNumber(float|int $value): string
    {
        if (is_int($value) || fmod((float) $value, 1.0) === 0.0) {
            return (string) (int) $value;
        }

        return rtrim(rtrim(sprintf('%.10F', $value), '0'), '.');
    }
}
