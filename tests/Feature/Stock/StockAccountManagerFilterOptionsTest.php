<?php

namespace Tests\Feature\Stock;

use App\Models\Contact;
use App\Models\Customer;
use App\Models\CustomerResponsible;
use App\Repositories\CrrRepository;
use Tests\RegressionTestCase;

class StockAccountManagerFilterOptionsTest extends RegressionTestCase
{
    public function test_stocks_account_manager_filter_only_lists_customer_account_managers(): void
    {
        $customerAm = Contact::create(['name' => 'Customer AM']);
        Contact::create(['name' => 'Unassigned Contact']);

        $customer = Customer::create(['customer_name' => 'Filter Customer']);
        CustomerResponsible::create([
            'customer_id' => $customer->id,
            'account_manager_id' => $customerAm->id,
        ]);

        $options = app(CrrRepository::class)->indexFilterOptions();

        $this->assertTrue($options['accountManagers']->contains('Customer AM'));
        $this->assertFalse($options['accountManagers']->contains('Unassigned Contact'));
    }

    public function test_stock_follow_up_account_manager_filter_only_lists_customer_account_managers(): void
    {
        $customerAm = Contact::create(['name' => 'Followup Customer AM']);
        Contact::create(['name' => 'Followup Unassigned Contact']);

        $customer = Customer::create(['customer_name' => 'Followup Filter Customer']);
        CustomerResponsible::create([
            'customer_id' => $customer->id,
            'account_manager_id' => $customerAm->id,
        ]);

        $options = app(CrrRepository::class)->stockFollowUpFilterOptions();

        $this->assertTrue($options['accountManagers']->contains('Followup Customer AM'));
        $this->assertFalse($options['accountManagers']->contains('Followup Unassigned Contact'));
    }

    public function test_pickup_work_list_account_manager_filter_only_lists_customer_account_managers(): void
    {
        $customerAm = Contact::create(['name' => 'Pickup Customer AM']);
        Contact::create(['name' => 'Pickup Unassigned Contact']);

        $customer = Customer::create(['customer_name' => 'Pickup Filter Customer']);
        CustomerResponsible::create([
            'customer_id' => $customer->id,
            'account_manager_id' => $customerAm->id,
        ]);

        $options = app(CrrRepository::class)->pickupWorkListFilterOptions(collect());

        $this->assertTrue($options['accountManagers']->contains('Pickup Customer AM'));
        $this->assertFalse($options['accountManagers']->contains('Pickup Unassigned Contact'));
    }
}
