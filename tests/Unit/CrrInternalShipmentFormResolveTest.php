<?php

namespace Tests\Unit;

use App\Models\Crr;
use App\Models\Shipment;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;

class CrrInternalShipmentFormResolveTest extends TestCase
{
    public function test_keeps_posted_etl_ktl_rtl_value(): void
    {
        $crr = new Crr(['internal_shipment' => 'AZA-1']);

        $this->assertSame('ETL', $crr->resolveInternalShipmentFromForm('ETL'));
        $this->assertSame('KTL', $crr->resolveInternalShipmentFromForm('KTL'));
    }

    public function test_empty_post_preserves_linked_shipment_number_on_column(): void
    {
        $crr = new Crr(['internal_shipment' => 'AZA-42842-0826']);

        $this->assertSame('AZA-42842-0826', $crr->resolveInternalShipmentFromForm(null));
        $this->assertSame('AZA-42842-0826', $crr->resolveInternalShipmentFromForm(''));
    }

    public function test_empty_post_clears_etl_when_not_linked_via_pivot(): void
    {
        $crr = new Crr(['internal_shipment' => 'ETL']);
        $crr->setRelation('shipments', new Collection);

        $this->assertNull($crr->resolveInternalShipmentFromForm(''));
    }

    public function test_empty_post_restores_from_linked_shipment_pivot_when_column_cleared(): void
    {
        $crr = new Crr(['internal_shipment' => null]);
        $shipment = new Shipment(['shipment_number' => 'AZA-42842-0826']);
        $crr->setRelation('shipments', new Collection([$shipment]));

        $this->assertSame('AZA-42842-0826', $crr->resolveInternalShipmentFromForm(''));
    }
}
