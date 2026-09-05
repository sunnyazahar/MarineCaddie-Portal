<?php

namespace App\Support;

use App\Models\CrrPackage;
use Illuminate\Support\Collection;

/**
 * Consistent package volume / volumetric weight (cm-based dimensions).
 */
final class PackageVolumeMetrics
{
    public const CBM_DIVISOR = 1000000;

    public const AIR_VOLUME_DIVISOR = 6000;

    public const COURIER_VOLUME_DIVISOR = 5000;

    public static function volumeCm3(?float $length, ?float $width, ?float $height): float
    {
        if ($length === null || $width === null || $height === null) {
            return 0.0;
        }

        return (float) $length * (float) $width * (float) $height;
    }

    public static function volumeCm3FromCbm(?float $cbm): float
    {
        if ($cbm === null || (float) $cbm <= 0) {
            return 0.0;
        }

        return (float) $cbm * self::CBM_DIVISOR;
    }

    public static function volumeCm3ForPackage(CrrPackage $package): float
    {
        $fromDimensions = self::volumeCm3(
            $package->length !== null ? (float) $package->length : null,
            $package->width !== null ? (float) $package->width : null,
            $package->height !== null ? (float) $package->height : null,
        );

        if ($fromDimensions > 0) {
            return $fromDimensions;
        }

        return self::volumeCm3FromCbm($package->cbm !== null ? (float) $package->cbm : null);
    }

  /**
   * @param  Collection<int, CrrPackage>|iterable<int, CrrPackage>  $packages
   */
    public static function totalVolumeCm3(iterable $packages): float
    {
        $total = 0.0;

        foreach ($packages as $package) {
            $total += self::volumeCm3ForPackage($package);
        }

        return $total;
    }

  /**
   * @param  Collection<int, CrrPackage>|iterable<int, CrrPackage>  $packages
   */
    public static function totalCbm(iterable $packages): float
    {
        return self::round2(self::totalVolumeCm3($packages) / self::CBM_DIVISOR);
    }

  /**
   * @param  Collection<int, CrrPackage>|iterable<int, CrrPackage>  $packages
   */
    public static function totalAirVolumeWeightKg(iterable $packages): float
    {
        return self::round2(self::totalVolumeCm3($packages) / self::AIR_VOLUME_DIVISOR);
    }

  /**
   * @param  Collection<int, CrrPackage>|iterable<int, CrrPackage>  $packages
   */
    public static function totalCourierVolumeWeightKg(iterable $packages): float
    {
        return self::round2(self::totalVolumeCm3($packages) / self::COURIER_VOLUME_DIVISOR);
    }

    public static function airVolumeWeightKg(
        ?float $length,
        ?float $width,
        ?float $height,
        ?float $cbm = null,
    ): float {
        $volumeCm3 = self::volumeCm3($length, $width, $height);
        if ($volumeCm3 <= 0) {
            $volumeCm3 = self::volumeCm3FromCbm($cbm);
        }

        if ($volumeCm3 <= 0) {
            return 0.0;
        }

        return self::round2($volumeCm3 / self::AIR_VOLUME_DIVISOR);
    }

    public static function round2(float $value): float
    {
        return round($value, 2);
    }

    /**
     * Display CBM with exactly 2 decimal places (project-wide).
     */
    public static function formatCbm(float|int|string|null $value, string $empty = '0.00'): string
    {
        if ($value === null || $value === '') {
            return $empty;
        }

        return number_format(self::round2((float) $value), 2, '.', '');
    }
}
