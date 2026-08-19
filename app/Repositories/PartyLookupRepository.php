<?php

namespace App\Repositories;

use App\Models\Agent;
use App\Models\Customer;
use App\Models\Hub;
use App\Models\Office;
use App\Models\OtherCompany;
use App\Models\Supplier;
use App\Repositories\Contracts\PartyLookupRepositoryInterface;

class PartyLookupRepository implements PartyLookupRepositoryInterface
{
    public function findHubWithContacts(int $id): ?Hub
    {
        return Hub::with('contacts')->find($id);
    }

    public function findAgentWithCountryAndContacts(int $id): ?Agent
    {
        return Agent::with(['country', 'contacts'])->find($id);
    }

    public function findOfficeWithCountryAndContacts(int $id): ?Office
    {
        return Office::with(['country', 'contacts'])->find($id);
    }

    public function findCustomerWithContacts(int $id): ?Customer
    {
        return Customer::with('contacts')->find($id);
    }

    public function findSupplierWithContacts(int $id): ?Supplier
    {
        return Supplier::with('contacts')->find($id);
    }

    public function findOtherCompanyWithCountryAndContacts(int $id): ?OtherCompany
    {
        return OtherCompany::with(['country', 'contacts'])->find($id);
    }

    public function findHubByPortCode(string $portCode): ?Hub
    {
        return Hub::query()->where('port_code', $portCode)->first();
    }

    public function findAgentByPortCodeWithCountry(string $portCode): ?Agent
    {
        return Agent::query()
            ->with('country')
            ->where('port_code', $portCode)
            ->first();
    }

    public function findHubByCodePortOrLocode(string $value): ?Hub
    {
        return Hub::query()
            ->where('code', $value)
            ->orWhere('port_code', $value)
            ->orWhere('un_locode', $value)
            ->first();
    }

    public function findAgentByCodePortOrLocode(string $value): ?Agent
    {
        return Agent::query()
            ->where('code', $value)
            ->orWhere('port_code', $value)
            ->orWhere('un_locode', $value)
            ->first();
    }

    public function findCustomerByNumberLocodeOrAddressPort(string $value): ?Customer
    {
        return Customer::query()
            ->where('customer_number', $value)
            ->orWhere('un_locode', $value)
            ->orWhereHas('addresses', function ($query) use ($value) {
                $query->where('port_code', $value);
            })
            ->first();
    }

    public function findHubById(int $id): ?Hub
    {
        return Hub::find($id);
    }

    public function findAgentById(int $id): ?Agent
    {
        return Agent::find($id);
    }

    public function findOfficeById(int $id): ?Office
    {
        return Office::find($id);
    }
}
