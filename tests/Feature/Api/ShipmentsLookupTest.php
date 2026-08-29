<?php

namespace Tests\Feature\Api;

use App\Models\Shipment;
use Tests\RegressionTestCase;

class ShipmentsLookupTest extends RegressionTestCase
{
    public function test_shipments_lookup_matches_prefix_without_month_year_suffix(): void
    {
        $user = $this->createAdminUser();

        $match = Shipment::create([
            'shipment_number' => 'AZA-93183-0826',
            'status' => 'In process',
        ]);
        Shipment::create([
            'shipment_number' => 'BOB-93183-0826',
            'status' => 'In process',
        ]);

        $response = $this->actingAsVerified($user)->get('/api/shipments?q=AZA-93183');

        $response->assertOk();
        $response->assertJsonCount(1, 'results');
        $response->assertJsonPath('results.0.id', $match->id);
        $response->assertJsonPath('results.0.text', 'AZA-93183-0826');
    }

    public function test_shipments_lookup_matches_full_shipment_number(): void
    {
        $user = $this->createAdminUser();

        $match = Shipment::create([
            'shipment_number' => 'AZA-93183-0826',
            'status' => 'In process',
        ]);

        $response = $this->actingAsVerified($user)->get('/api/shipments?q=AZA-93183-0826');

        $response->assertOk();
        $response->assertJsonPath('results.0.id', $match->id);
    }

    public function test_shipments_lookup_ignores_short_prefix_only_queries(): void
    {
        $user = $this->createAdminUser();

        Shipment::create([
            'shipment_number' => 'AZA-93183-0826',
            'status' => 'In process',
        ]);

        $response = $this->actingAsVerified($user)->get('/api/shipments?q=AZA-');

        $response->assertOk();
        $response->assertJsonCount(0, 'results');
    }

    public function test_shipments_lookup_matches_digits_without_user_prefix(): void
    {
        $user = $this->createAdminUser();

        $match = Shipment::create([
            'shipment_number' => 'AZA-93183-0826',
            'status' => 'In process',
        ]);
        Shipment::create([
            'shipment_number' => 'AZA-11111-0826',
            'status' => 'In process',
        ]);

        $response = $this->actingAsVerified($user)->get('/api/shipments?q=93183');

        $response->assertOk();
        $response->assertJsonCount(1, 'results');
        $response->assertJsonPath('results.0.id', $match->id);
        $response->assertJsonPath('results.0.text', 'AZA-93183-0826');
    }

    public function test_create_pre_alert_loads_shipment_from_digits_only(): void
    {
        $user = $this->createAdminUser();

        $match = Shipment::create([
            'shipment_number' => 'AZA-93183-0826',
            'status' => 'In process',
        ]);

        $response = $this->actingAsVerified($user)->get('/create-pre-alert?q=93183');

        $response->assertRedirect(route('create-pre-alert', ['shipment' => $match->id]));
    }

    public function test_create_pre_alert_loads_shipment_from_partial_number(): void
    {
        $user = $this->createAdminUser();

        $match = Shipment::create([
            'shipment_number' => 'AZA-93183-0826',
            'status' => 'In process',
        ]);

        $response = $this->actingAsVerified($user)->get('/create-pre-alert?q=AZA-93183');

        $response->assertRedirect(route('create-pre-alert', ['shipment' => $match->id]));
    }

    public function test_transit_loads_shipment_from_digits_only(): void
    {
        $user = $this->createAdminUser();

        $match = Shipment::create([
            'shipment_number' => 'AZA-93183-0826',
            'status' => 'In transit',
        ]);

        $response = $this->actingAsVerified($user)->get('/transit?q=93183');

        $response->assertRedirect(route('transit', ['shipment' => $match->id]));
    }

    public function test_transit_loads_shipment_from_partial_number(): void
    {
        $user = $this->createAdminUser();

        $match = Shipment::create([
            'shipment_number' => 'AZA-93183-0826',
            'status' => 'In transit',
        ]);

        $response = $this->actingAsVerified($user)->get('/transit?q=AZA-93183');

        $response->assertRedirect(route('transit', ['shipment' => $match->id]));
    }

    public function test_shipments_lookup_requires_authentication(): void
    {
        $response = $this->get('/api/shipments?q=AZA-93183');

        $response->assertRedirect('/login');
    }
}
