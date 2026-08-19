<?php

namespace App\Repositories;

use App\Models\OtherCompany;
use App\Repositories\Contracts\OtherCompanyRepositoryInterface;
use App\Support\ListSearch;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class OtherCompanyRepository implements OtherCompanyRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 25): LengthAwarePaginator
    {
        $name     = trim((string) ($filters['name'] ?? ''));
        $code     = trim((string) ($filters['code'] ?? ''));
        $address  = trim((string) ($filters['address'] ?? ''));
        $city     = trim((string) ($filters['city'] ?? ''));
        $countries = array_values(array_filter((array) ($filters['country'] ?? [])));

        return OtherCompany::query()
            ->with('country')
            ->when(ListSearch::contains($name), fn ($q, $p) => $q->where('company_name', 'like', $p))
            ->when(ListSearch::contains($code), fn ($q, $p) => $q->where('code', 'like', $p))
            ->when(ListSearch::contains($city), fn ($q, $p) => $q->where('city', 'like', $p))
            ->when(ListSearch::contains($address), function ($q, $p) {
                $q->where(function ($sub) use ($p) {
                    $sub->where('street_address', 'like', $p)
                        ->orWhere('office_street_address', 'like', $p)
                        ->orWhere('district_state', 'like', $p)
                        ->orWhere('zip_code', 'like', $p);
                });
            })
            ->when($countries, fn ($q) => $q->whereHas('country', fn ($sub) => $sub->whereIn('name', $countries)))
            ->orderBy('company_name')
            ->paginate($perPage);
    }

    public function distinctCountries(): Collection
    {
        return OtherCompany::query()
            ->join('countries', 'countries.id', '=', 'other_companies.country_id')
            ->whereNotNull('countries.name')
            ->distinct()
            ->orderBy('countries.name')
            ->pluck('countries.name')
            ->values();
    }

    public function findOrFail(int $id): OtherCompany
    {
        return OtherCompany::findOrFail($id);
    }

    public function create(array $data): OtherCompany
    {
        return OtherCompany::create($data);
    }

    public function update(OtherCompany $company, array $data): bool
    {
        return $company->update($data);
    }

    public function delete(OtherCompany $company): bool
    {
        return (bool) $company->delete();
    }
}
