<?php

namespace Tests\Feature\Api;

use App\Models\Country;
use App\Models\Port;
use Tests\RegressionTestCase;

class PortsLookupTest extends RegressionTestCase
{
    public function test_ports_lookup_returns_active_matching_codes(): void
    {
        $user = $this->createAdminUser();
        $country = Country::create(['name' => 'United Arab Emirates', 'is_active' => true]);

        Port::create([
            'type' => Port::TYPE_AIRPORT,
            'iata_code' => 'DXB',
            'port_name' => 'Dubai International',
            'city' => 'Dubai',
            'country_id' => $country->id,
            'country_name' => $country->name,
            'is_active' => true,
        ]);

        Port::create([
            'type' => Port::TYPE_SEAPORT,
            'un_locode' => 'AEJEA',
            'port_name' => 'Jebel Ali',
            'city' => 'Dubai',
            'country_id' => $country->id,
            'country_name' => $country->name,
            'is_active' => true,
        ]);

        $response = $this->actingAsVerified($user)->get('/api/ports?q=DXB');

        $response->assertOk();
        $response->assertJsonStructure(['results' => [['id', 'text', 'code', 'city', 'port_name', 'country']]]);
        $this->assertSame('DXB', $response->json('results.0.code'));
    }

    public function test_ports_lookup_requires_authentication(): void
    {
        $response = $this->get('/api/ports?q=DXB');

        $response->assertRedirect('/login');
    }
}
