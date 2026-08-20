<?php

namespace Tests\Support;

use App\Models\Agent;
use App\Models\Country;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Hub;
use App\Models\OtherCompany;
use App\Models\Shipment;
use App\Models\Supplier;
use Illuminate\Support\Collection;

final class MigratedViewStubs
{
    public static function countries(): Collection
    {
        return collect([
            new Country([
                'id' => 1,
                'name' => 'United Arab Emirates',
                'iso_code' => 'AE',
                'flag_url' => 'https://flagcdn.com/w20/ae.png',
                'is_active' => true,
            ]),
        ]);
    }

    public static function agent(): Agent
    {
        $agent = new Agent([
            'agent_name' => 'Stub Agent',
            'code' => 'STB',
            'company_id' => 'A-001',
        ]);
        $agent->id = 1;

        return $agent;
    }

    public static function supplier(): Supplier
    {
        $supplier = new Supplier([
            'supplier_name' => 'Stub Supplier',
        ]);
        $supplier->id = 1;

        return $supplier;
    }

    public static function otherCompany(): OtherCompany
    {
        $company = new OtherCompany([
            'company_name' => 'Stub Company',
            'code' => 'SC',
        ]);
        $company->id = 1;

        return $company;
    }

    public static function hub(): Hub
    {
        $hub = new Hub([
            'hub_name' => 'Stub Hub',
            'code' => 'HUB1',
            'company_id' => 'H-001',
        ]);
        $hub->id = 1;

        return $hub;
    }

    public static function customer(): Customer
    {
        $customer = new Customer([
            'customer_name' => 'Stub Customer',
        ]);
        $customer->id = 1;
        $customer->setRelation('primaryAddress', new CustomerAddress([
            'port_code' => 'AEJEA',
            'city' => 'Dubai',
        ]));

        return $customer;
    }

    public static function shipment(): Shipment
    {
        $shipment = new Shipment([
            'shipment_number' => 'SHP-STUB-001',
            'departure_port_code' => 'SGSIN',
            'consignee_port_code' => 'AEJEA',
            'status' => 'Draft',
        ]);
        $shipment->id = 1;

        foreach ([
            'crrs', 'flights', 'seaLegs', 'truckLegs', 'courierLegs',
            'releaseLegs', 'handCarryLegs', 'onBoardLegs', 'irregularities',
        ] as $relation) {
            $shipment->setRelation($relation, collect());
        }

        return $shipment;
    }
}
