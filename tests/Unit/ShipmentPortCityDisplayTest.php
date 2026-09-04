<?php

namespace Tests\Unit;

use App\Models\Port;
use App\Models\Shipment;
use Tests\RegressionTestCase;

class ShipmentPortCityDisplayTest extends RegressionTestCase
{
    public function test_batch_resolve_port_cities_prefers_port_city_over_code(): void
    {
        Port::create([
            'type' => Port::TYPE_AIRPORT,
            'iata_code' => 'BOM',
            'city' => 'Mumbai',
            'port_name' => 'Chhatrapati Shivaji',
            'is_active' => true,
        ]);
        Port::create([
            'type' => Port::TYPE_AIRPORT,
            'iata_code' => 'DEL',
            'city' => 'New Delhi',
            'port_name' => 'Indira Gandhi',
            'is_active' => true,
        ]);

        $shipment = Shipment::create([
            'shipment_number' => 'SHIP-CITY-1',
            'status' => 'In process',
            'departure_port_code' => 'BOM',
            'consignee_port_code' => 'DEL',
        ]);

        $cities = Shipment::batchResolvePortCities(collect([$shipment]));

        $this->assertSame('Mumbai', $shipment->departureCityDisplay($cities));
        $this->assertSame('New Delhi', $shipment->destinationCityDisplay($cities));
    }

    public function test_normalize_port_city_label_strips_airport_noise(): void
    {
        $this->assertSame('Mumbai', Shipment::normalizePortCityLabel('Bombay (Mumbai)'));
        $this->assertSame('Incheon', Shipment::normalizePortCityLabel('Incheon, Incheon International Airport'));
        $this->assertSame('Dubai', Shipment::normalizePortCityLabel('Dubai - Dubai International Airport'));
    }

    public function test_destination_falls_back_to_consignee_city_without_port_code(): void
    {
        $shipment = Shipment::create([
            'shipment_number' => 'SHIP-CITY-2',
            'status' => 'In process',
            'consignee_city' => 'Rotterdam',
            'consignee_country' => 'Netherlands',
        ]);

        $this->assertSame('Rotterdam', $shipment->destinationCityDisplay([]));
    }

    public function test_unresolved_port_code_shows_dash_not_code(): void
    {
        $shipment = Shipment::create([
            'shipment_number' => 'SHIP-CITY-3',
            'status' => 'In process',
            'departure_port_code' => 'ZZZ',
        ]);

        $this->assertSame('—', $shipment->departureCityDisplay([]));
    }
}
