<?php

namespace Tests\Feature\Stock;

use App\Models\Agent;
use App\Models\Hub;
use Tests\RegressionTestCase;

class CreateCrrHubAgentOptionsTest extends RegressionTestCase
{
    public function test_create_crr_lists_all_hubs_and_agents_including_filtered_ones(): void
    {
        $user = $this->createAdminUser();

        Hub::withoutEvents(fn () => Hub::create([
            'hub_name' => 'Visible Hub',
            'code' => 'HUB-VIS',
            'hide_in_portal' => false,
        ]));
        Hub::withoutEvents(fn () => Hub::create([
            'hub_name' => 'Hidden Portal Hub',
            'code' => 'HUB-HID',
            'hide_in_portal' => true,
        ]));
        Agent::withoutEvents(fn () => Agent::create([
            'agent_name' => 'Active Agent',
            'code' => 'AG-ACT',
            'is_active' => true,
        ]));
        Agent::withoutEvents(fn () => Agent::create([
            'agent_name' => 'Inactive Agent',
            'code' => 'AG-INA',
            'is_active' => false,
        ]));

        $response = $this->actingAsVerified($user)->get(route('create-crr'));

        $response->assertOk();
        $response->assertSee('HUB-VIS', false);
        $response->assertSee('HUB-HID', false);
        $response->assertSee('Hidden Portal Hub');
        $response->assertSee('AG-ACT', false);
        $response->assertSee('AG-INA', false);
        $response->assertSee('Inactive Agent');
        $response->assertSee('select2-hub', false);
    }
}
