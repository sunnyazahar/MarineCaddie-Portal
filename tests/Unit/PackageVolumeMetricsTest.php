<?php

namespace Tests\Unit;

use App\Models\CrrPackage;
use App\Support\PackageVolumeMetrics;
use Tests\RegressionTestCase;

class PackageVolumeMetricsTest extends RegressionTestCase
{
    public function test_air_volume_weight_matches_dimensions_over_stored_cbm(): void
    {
        $package = new CrrPackage([
            'length' => 120,
            'width' => 80,
            'height' => 80,
            'cbm' => 0.768,
        ]);

        $this->assertSame(128.0, PackageVolumeMetrics::totalAirVolumeWeightKg(collect([$package])));
        $this->assertSame(153.6, PackageVolumeMetrics::totalCourierVolumeWeightKg(collect([$package])));
        $this->assertSame(0.77, PackageVolumeMetrics::totalCbm(collect([$package])));
    }

    public function test_air_volume_weight_falls_back_to_cbm_when_dimensions_missing(): void
    {
        $package = new CrrPackage([
            'length' => null,
            'width' => null,
            'height' => null,
            'cbm' => 0.768,
        ]);

        $this->assertSame(128.0, PackageVolumeMetrics::totalAirVolumeWeightKg(collect([$package])));
    }
}
