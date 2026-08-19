<?php

namespace App\Repositories;

use App\Models\Hub;
use App\Repositories\Contracts\HubRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class HubRepository implements HubRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 25): LengthAwarePaginator
    {
        $name         = trim((string) ($filters['name'] ?? ''));
        $code         = trim((string) ($filters['code'] ?? ''));
        $address      = trim((string) ($filters['address'] ?? ''));
        $city         = trim((string) ($filters['city'] ?? ''));
        $countries    = array_values(array_filter((array) ($filters['country'] ?? [])));
        $hideInactive = (bool) ($filters['hide_inactive'] ?? true);

        return Hub::query()
            ->when($name !== '', fn ($q) => $q->where('hub_name', 'like', '%' . $name . '%'))
            ->when($code !== '', fn ($q) => $q->where('code', 'like', '%' . $code . '%'))
            ->when($city !== '', fn ($q) => $q->where('city', 'like', '%' . $city . '%'))
            ->when($address !== '', function ($q) use ($address) {
                $q->where(function ($sub) use ($address) {
                    $sub->where('hub_address', 'like', '%' . $address . '%')
                        ->orWhere('office_address', 'like', '%' . $address . '%')
                        ->orWhere('district_state', 'like', '%' . $address . '%')
                        ->orWhere('zip_code', 'like', '%' . $address . '%');
                });
            })
            ->when($countries, fn ($q) => $q->whereIn('country', $countries))
            ->when($hideInactive, fn ($q) => $q->where('hide_in_portal', false))
            ->orderBy('hub_name')
            ->paginate($perPage);
    }

    public function distinctCountries(): Collection
    {
        return Hub::query()
            ->whereNotNull('country')
            ->where('country', '!=', '')
            ->distinct()
            ->orderBy('country')
            ->pluck('country')
            ->values();
    }

    public function countryFlags(): Collection
    {
        return DB::table('countries')
            ->whereNotNull('flag_url')
            ->pluck('flag_url', 'name');
    }

    public function findOrFail(int $id): Hub
    {
        return Hub::findOrFail($id);
    }

    public function findWithRelations(int $id, array $relations = []): Hub
    {
        return Hub::with($relations)->findOrFail($id);
    }

    public function create(array $data): Hub
    {
        return Hub::create($data);
    }

    public function update(Hub $hub, array $data): bool
    {
        return $hub->update($data);
    }

    public function delete(int $id): bool
    {
        return (bool) Hub::findOrFail($id)->delete();
    }

    public function updateStatus(Hub $hub, bool $isInactive): bool
    {
        return $hub->update(['hide_in_portal' => $isInactive]);
    }
}
