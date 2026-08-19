<?php

namespace App\Repositories;

use App\Models\Contact;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\CustomerResponsible;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Support\CountryCache;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CustomerRepository extends BaseRepository implements CustomerRepositoryInterface
{
    protected string $modelClass = Customer::class;

    public function findOrFail(int $id, array $with = []): Customer
    {
        return parent::findModelOrFail($id, $with);
    }

    public function find(int $id): ?Customer
    {
        return $this->query()->find($id);
    }

    public function create(array $data): Customer
    {
        return parent::create($data);
    }

    public function update(Customer $customer, array $data): bool
    {
        return parent::updateModel($customer, $data);
    }

    public function paginate(array $filters, int $perPage = 25): LengthAwarePaginator
    {
        $search             = trim((string) ($filters['search'] ?? ''));
        $officesFilter      = array_values(array_filter((array) ($filters['responsible_office'] ?? [])));
        $accountMgrFilter   = array_values(array_filter((array) ($filters['account_manager'] ?? [])));
        $salesMgrFilter     = array_values(array_filter((array) ($filters['sales_manager'] ?? [])));
        $countriesFilter    = array_values(array_filter((array) ($filters['country'] ?? [])));

        return $this->query()
            ->with([
                'primaryAddress.country',
                'responsible.accountManager.office',
                'responsible.salesManager',
                'contacts' => fn ($q) => $q->where('is_main_contact', true),
            ])
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('customer_name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('contact_person', 'like', '%' . $search . '%')
                        ->orWhere('customer_number', 'like', '%' . $search . '%');
                });
            })
            ->when($officesFilter, function ($q) use ($officesFilter) {
                $q->whereHas('responsible.accountManager.office', function ($sub) use ($officesFilter) {
                    $sub->where(function ($o) use ($officesFilter) {
                        $o->whereIn('office_short_name', $officesFilter)
                            ->orWhereIn('office_name', $officesFilter);
                    });
                });
            })
            ->when($accountMgrFilter, fn ($q) => $q->whereHas('responsible.accountManager', fn ($sub) => $sub->whereIn('name', $accountMgrFilter)))
            ->when($salesMgrFilter, fn ($q) => $q->whereHas('responsible.salesManager', fn ($sub) => $sub->whereIn('name', $salesMgrFilter)))
            ->when($countriesFilter, fn ($q) => $q->whereHas('primaryAddress.country', fn ($sub) => $sub->whereIn('name', $countriesFilter)))
            ->orderBy('customer_name')
            ->paginate($perPage);
    }

    public function filterOffices(): Collection
    {
        return $this->query()
            ->join('customer_responsibles', 'customer_responsibles.customer_id', '=', 'customers.id')
            ->join('contacts as account_contacts', 'account_contacts.id', '=', 'customer_responsibles.account_manager_id')
            ->join('offices', 'offices.id', '=', 'account_contacts.office_id')
            ->select('offices.office_short_name')
            ->whereNotNull('offices.office_short_name')
            ->distinct()
            ->orderBy('offices.office_short_name')
            ->pluck('offices.office_short_name')
            ->values();
    }

    public function filterAccountManagers(): Collection
    {
        return Contact::query()
            ->whereIn('id', CustomerResponsible::query()->whereNotNull('account_manager_id')->pluck('account_manager_id'))
            ->orderBy('name')
            ->pluck('name')
            ->values();
    }

    public function filterSalesManagers(): Collection
    {
        return Contact::query()
            ->whereIn('id', CustomerResponsible::query()->whereNotNull('sales_manager_id')->pluck('sales_manager_id'))
            ->orderBy('name')
            ->pluck('name')
            ->values();
    }

    public function filterCountries(): Collection
    {
        $usedCountryIds = CustomerAddress::query()
            ->where('type', 'primary')
            ->whereNotNull('country_id')
            ->pluck('country_id');

        return CountryCache::active()
            ->whereIn('id', $usedCountryIds)
            ->pluck('name')
            ->values();
    }
}
