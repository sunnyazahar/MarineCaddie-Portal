<?php

namespace Tests\Feature\Agents;

use App\Models\Agent;
use App\Models\Country;
use Tests\RegressionTestCase;

class AgentListTest extends RegressionTestCase
{
    public function test_agents_index_returns_page_for_verified_user(): void
    {
        $user = $this->createAdminUser();
        $country = Country::create(['name' => 'United Arab Emirates', 'is_active' => true]);
        Agent::create([
            'agent_name' => 'Regression Agent',
            'code' => 'REG-A',
            'city' => 'Dubai',
            'country_id' => $country->id,
            'is_active' => true,
        ]);

        $response = $this->actingAsVerified($user)->get('/Agents');

        $response->assertOk();
        $response->assertSee('Regression Agent');
    }

    public function test_agents_ajax_filter_returns_list_contract(): void
    {
        $user = $this->createAdminUser();
        Agent::create([
            'agent_name' => 'Filter Agent',
            'code' => 'FIL-A',
            'is_active' => true,
        ]);

        $response = $this->actingAsVerified($user)
            ->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->get('/Agents?name=Filter');

        $response->assertOk();
        $response->assertJsonStructure(['html', 'pagination', 'total']);
        $this->assertStringContainsString('Filter Agent', $response->json('html'));
        $this->assertSame(1, $response->json('total'));
    }
}
