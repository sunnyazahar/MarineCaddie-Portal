<?php

namespace App\Services;

use App\Models\ProformaInvoice;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

class ProformaNumberGenerator
{
    private const PREFIX = 'MC-AE';

    public static function prefix(): string
    {
        return self::PREFIX;
    }

    public function financialYearLabel(?CarbonInterface $date = null): string
    {
        $date ??= now();

        $year = (int) $date->format('y');

        if ((int) $date->format('n') >= 4) {
            $start = $year;
            $end = $year + 1;
        } else {
            $start = $year - 1;
            $end = $year;
        }

        return sprintf('%02d-%02d', $start, $end);
    }

    public function previewNext(?CarbonInterface $date = null): string
    {
        $financialYearLabel = $this->financialYearLabel($date);
        $nextSequence = $this->resolveNextSequence($financialYearLabel);

        return $this->formatNumber($financialYearLabel, $nextSequence);
    }

    public function reserveNext(?CarbonInterface $date = null): array
    {
        return DB::transaction(function () use ($date) {
            $financialYearLabel = $this->financialYearLabel($date);

            $lastSequence = ProformaInvoice::query()
                ->where('financial_year_label', $financialYearLabel)
                ->lockForUpdate()
                ->max('sequence_no');

            $nextSequence = ((int) $lastSequence) + 1;

            return [
                'financial_year_label' => $financialYearLabel,
                'sequence_no' => $nextSequence,
                'proforma_no' => $this->formatNumber($financialYearLabel, $nextSequence),
            ];
        });
    }

    private function resolveNextSequence(string $financialYearLabel): int
    {
        $lastSequence = ProformaInvoice::query()
            ->where('financial_year_label', $financialYearLabel)
            ->max('sequence_no');

        return ((int) $lastSequence) + 1;
    }

    private function formatNumber(string $financialYearLabel, int $sequence): string
    {
        return sprintf('%s%s-%04d', self::PREFIX, $financialYearLabel, $sequence);
    }
}
