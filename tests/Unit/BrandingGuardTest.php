<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * White-label guard: company identity must come from Company Settings, never from code.
 */
class BrandingGuardTest extends TestCase
{
    private const FORBIDDEN = '/marine\s*caddie|marincaddie|marinetrans|\bMC-AE\b|\bMC Assistant\b/i';

    public function test_application_code_contains_no_hardcoded_legacy_brand(): void
    {
        $root = dirname(__DIR__, 2);
        $offenders = [];

        foreach (['app', 'resources', 'config', 'routes', 'database/seeders'] as $directory) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($root . '/' . $directory, RecursiveDirectoryIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                if (! $file->isFile() || ! in_array($file->getExtension(), ['php', 'js', 'css', 'json'], true)) {
                    continue;
                }

                foreach (file($file->getPathname()) ?: [] as $number => $line) {
                    if (preg_match(self::FORBIDDEN, $line)) {
                        $offenders[] = substr($file->getPathname(), strlen($root) + 1) . ':' . ($number + 1);
                    }
                }
            }
        }

        $this->assertSame([], $offenders, "Hardcoded brand found:\n" . implode("\n", $offenders));
    }
}
