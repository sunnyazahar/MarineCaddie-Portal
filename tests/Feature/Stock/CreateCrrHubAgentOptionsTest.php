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

    public function test_create_crr_no_longer_renders_conversational_assistant(): void
    {
        $user = $this->createAdminUser();

        $response = $this->actingAsVerified($user)->get(route('create-crr'));

        $response->assertOk();
        $response->assertSee('Vessel <span class="text-danger">*</span>', false);
        $response->assertSee('Supplier <span class="text-danger">*</span>', false);
        $response->assertSee('Hub/agent <span class="text-danger">*</span>', false);
        $response->assertSee('Packages <span class="text-danger">*</span>', false);
        $response->assertDontSee('CRR Assistant');
        $response->assertDontSee('data-role="crr-assistant"', false);
        $response->assertDontSee('crrAssistantLauncher', false);
        $response->assertDontSee('aria-controls="crrAssistantPanel"', false);
        $response->assertDontSee('crrAssistantInput', false);
        $response->assertDontSee('crrAssistantSuggestions', false);
        $response->assertDontSee('aria-autocomplete="list"', false);
        $response->assertDontSee('crr-assistant-thread', false);
    }

    public function test_create_crr_requires_vessel_supplier_and_other_core_fields(): void
    {
        $user = $this->createAdminUser();

        $this->actingAsVerified($user);

        $response = $this->from(route('create-crr'))->post(route('stocks.crr.store'), []);

        $response->assertRedirect(route('create-crr'));
        $response->assertSessionHasErrors([
            'vessel_name',
            'supplier',
            'hub_agent',
            'currency',
            'customs_value',
            'packages',
        ]);
    }
}
