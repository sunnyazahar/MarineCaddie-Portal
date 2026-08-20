<?php

namespace App\Repositories;

use App\Models\Agent;
use App\Repositories\Contracts\AgentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AgentRepository extends BaseRepository implements AgentRepositoryInterface
{
    protected string $modelClass = Agent::class;

    public function paginate(array $filters, int $perPage = 25): LengthAwarePaginator
    {
        $name     = trim((string) ($filters['name'] ?? ''));
        $code     = trim((string) ($filters['code'] ?? ''));
        $address  = trim((string) ($filters['address'] ?? ''));
        $city     = trim((string) ($filters['city'] ?? ''));
        $countries = array_values(array_filter((array) ($filters['country'] ?? [])));
        $types    = array_values(array_filter((array) ($filters['type'] ?? [])));
        $hideInactive = (bool) ($filters['hide_inactive'] ?? false);

        return $this->query()
            ->with('country')
            ->when($name !== '', fn ($q) => $q->where('agent_name', 'like', '%' . $name . '%'))
            ->when($code !== '', fn ($q) => $q->where('code', 'like', '%' . $code . '%'))
            ->when($city !== '', fn ($q) => $q->where('city', 'like', '%' . $city . '%'))
            ->when($address !== '', function ($q) use ($address) {
                $q->where(function ($sub) use ($address) {
                    $sub->where('agent_address', 'like', '%' . $address . '%')
                        ->orWhere('office_address', 'like', '%' . $address . '%')
                        ->orWhere('district_state', 'like', '%' . $address . '%')
                        ->orWhere('zip_code', 'like', '%' . $address . '%');
                });
            })
            ->when($countries, fn ($q) => $q->whereHas('country', fn ($sub) => $sub->whereIn('name', $countries)))
            ->when($types, fn ($q) => $q->whereIn('agent_type', $types))
            ->when($hideInactive, fn ($q) => $q->where('is_active', 1))
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function distinctCountries(): Collection
    {
        return $this->query()
            ->join('countries', 'countries.id', '=', 'agents.country_id')
            ->whereNotNull('countries.name')
            ->distinct()
            ->orderBy('countries.name')
            ->pluck('countries.name')
            ->values();
    }

    public function distinctTypes(): Collection
    {
        return $this->query()
            ->whereNotNull('agent_type')
            ->where('agent_type', '!=', '')
            ->distinct()
            ->orderBy('agent_type')
            ->pluck('agent_type')
            ->values();
    }

    public function findOrFail(int $id): Agent
    {
        return parent::findModelOrFail($id);
    }

    public function findWithRelations(int $id, array $relations = []): Agent
    {
        return parent::findModelOrFail($id, $relations);
    }

    public function create(array $data): Agent
    {
        return parent::create($data);
    }

    public function update(Agent $agent, array $data): bool
    {
        return $agent->update($data);
    }

    public function deleteById(int $id): bool
    {
        return parent::deleteById($id);
    }

    public function updateStatus(Agent $agent, bool $isActive): bool
    {
        return $agent->update(['is_active' => $isActive]);
    }
}
