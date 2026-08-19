<?php

namespace App\Repositories;

use App\Models\Contact;
use App\Models\Country;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\CustomerResponsible;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CustomerRepository implements CustomerRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 25): LengthAwarePaginator
    {
        $search             = trim((string) ($filters['search'] ?? ''));
        $officesFilter      = array_values(array_filter((array) ($filters['responsible_office'] ?? [])));
        $accountMgrFilter   = array_values(array_filter((array) ($filters['account_manager'] ?? [])));
        $salesMgrFilter     = array_values(array_filter((array) ($filters['sales_manager'] ?? [])));
        $countriesFilter    = array_values(array_filter((array) ($filters['country'] ?? [])));

        return Customer::query()
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
        return Customer::query()
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
        return Country::query()
            ->whereIn('id', CustomerAddress::query()->where('type', 'primary')->whereNotNull('country_id')->pluck('country_id'))
            ->orderBy('name')
            ->pluck('name')
            ->values();
    }
}
