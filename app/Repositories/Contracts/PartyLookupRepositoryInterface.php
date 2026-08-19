<?php

namespace App\Repositories\Contracts;

use App\Models\Agent;
use App\Models\Customer;
use App\Models\Hub;
use App\Models\Office;
use App\Models\OtherCompany;
use App\Models\Supplier;

interface PartyLookupRepositoryInterface
{
    public function findHubWithContacts(int $id): ?Hub;

    public function findAgentWithCountryAndContacts(int $id): ?Agent;

    public function findOfficeWithCountryAndContacts(int $id): ?Office;

    public function findCustomerWithContacts(int $id): ?Customer;

    public function findSupplierWithContacts(int $id): ?Supplier;

    public function findOtherCompanyWithCountryAndContacts(int $id): ?OtherCompany;

    public function findHubByPortCode(string $portCode): ?Hub;

    public function findAgentByPortCodeWithCountry(string $portCode): ?Agent;

    public function findHubByCodePortOrLocode(string $value): ?Hub;

    public function findAgentByCodePortOrLocode(string $value): ?Agent;

    public function findCustomerByNumberLocodeOrAddressPort(string $value): ?Customer;

    public function findHubById(int $id): ?Hub;

    public function findAgentById(int $id): ?Agent;

    public function findOfficeById(int $id): ?Office;
}
