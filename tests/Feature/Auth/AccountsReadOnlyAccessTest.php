<?php

namespace Tests\Feature\Auth;

use App\Models\Shipment;
use Tests\RegressionTestCase;

class AccountsReadOnlyAccessTest extends RegressionTestCase
{
    public function test_accounts_user_can_open_billing_and_store_invoice(): void
    {
        $user = $this->createAdminUser(['role' => 'Accounts']);

        $shipment = Shipment::create([
            'shipment_number' => 'ACL-BILL-1',
            'status' => 'Completed',
            'service' => 'Airfreight',
        ]);

        $this->actingAsVerified($user)
            ->get(route('billing.invoicing'))
            ->assertOk();

        $this->actingAsVerified($user)
            ->postJson(route('billing.invoicing.store'), [
                'shipment_id' => $shipment->id,
                'proforma_date' => now()->format('d.m.Y'),
                'currency' => 'USD',
                'payment_type' => 'full_payment',
                'line_items' => [[
                    'description' => 'Freight charges',
                    'qty' => '1',
                    'rate' => '100.00',
                    'currency' => 'USD',
                    'amount' => '100.00',
                    'exchange_rate' => '1',
                    'tax_type' => 'T',
                    'non_taxable' => '0.00',
                    'taxable' => '100.00',
                    'igst_pct' => '0',
                    'igst_amt' => '0.00',
                    'cgst_pct' => '0',
                    'cgst_amt' => '0.00',
                    'sgst_pct' => '0',
                    'sgst_amt' => '0.00',
                ]],
            ])
            ->assertOk()
            ->assertJson(['success' => true]);
    }

    public function test_accounts_user_can_view_stocks_but_not_create(): void
    {
        $user = $this->createAdminUser(['role' => 'Accounts']);

        $this->actingAsVerified($user)
            ->get(route('stocks'))
            ->assertOk();

        $this->actingAsVerified($user)
            ->get(route('create-crr'))
            ->assertForbidden();
    }

    public function test_accounts_user_can_view_shipments_but_not_create(): void
    {
        $user = $this->createAdminUser(['role' => 'Accounts']);

        $this->actingAsVerified($user)
            ->get(route('shipments'))
            ->assertOk();

        $this->actingAsVerified($user)
            ->get(route('create-shipment'))
            ->assertForbidden();

        $this->actingAsVerified($user)
            ->post(route('shipments.store'), [])
            ->assertForbidden();
    }

    public function test_accounts_user_can_view_offices_but_not_create(): void
    {
        $user = $this->createAdminUser(['role' => 'Accounts']);

        $this->actingAsVerified($user)
            ->get(route('offices.index'))
            ->assertOk();

        $this->actingAsVerified($user)
            ->get(route('offices.create'))
            ->assertForbidden();
    }

    public function test_accounts_user_sees_billing_menu_but_not_users_menu(): void
    {
        $user = $this->createAdminUser(['role' => 'Accounts']);

        $html = $this->actingAsVerified($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('data-menu-key="billing"', $html);
        $this->assertStringNotContainsString('data-menu-key="users"', $html);
        $this->assertStringContainsString('accounts-readonly', $html);
    }

    public function test_operations_user_cannot_open_billing(): void
    {
        $user = $this->createAdminUser(['role' => 'Operations']);

        $this->actingAsVerified($user)
            ->get(route('billing.invoicing'))
            ->assertForbidden();
    }
}
