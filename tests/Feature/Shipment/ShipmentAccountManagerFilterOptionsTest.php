<?php

namespace Tests\Feature\Shipment;

use App\Models\Contact;
use App\Models\Customer;
use App\Models\CustomerResponsible;
use App\Repositories\ShipmentRepository;
use Tests\RegressionTestCase;

class ShipmentAccountManagerFilterOptionsTest extends RegressionTestCase
{
    public function test_shipments_account_manager_filter_only_lists_customer_account_managers(): void
    {
        $customerAm = Contact::create(['name' => 'Shipment Customer AM']);
        Contact::create(['name' => 'Shipment Unassigned Contact']);

        $customer = Customer::create(['customer_name' => 'Shipment Filter Customer']);
        CustomerResponsible::create([
            'customer_id' => $customer->id,
            'account_manager_id' => $customerAm->id,
        ]);

        $options = app(ShipmentRepository::class)->indexFilterOptions();
        $names = $options['accountManagers']->pluck('name');

        $this->assertTrue($names->contains('Shipment Customer AM'));
        $this->assertFalse($names->contains('Shipment Unassigned Contact'));
    }

    public function test_shipment_follow_up_account_manager_filter_only_lists_customer_account_managers(): void
    {
        $customerAm = Contact::create(['name' => 'Followup Shipment AM']);
        Contact::create(['name' => 'Followup Shipment Unassigned']);

        $customer = Customer::create(['customer_name' => 'Followup Shipment Customer']);
        CustomerResponsible::create([
            'customer_id' => $customer->id,
            'account_manager_id' => $customerAm->id,
        ]);

        $options = app(ShipmentRepository::class)->followUpFilterOptions('delivery');
        $names = $options['accountManagers']->pluck('name');

        $this->assertTrue($names->contains('Followup Shipment AM'));
        $this->assertFalse($names->contains('Followup Shipment Unassigned'));
    }
}
