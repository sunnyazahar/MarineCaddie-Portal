<?php

namespace App\Repositories;

use App\Models\Agent;
use App\Models\Hub;
use App\Models\Office;
use App\Models\Supplier;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    protected string $modelClass = User::class;

    public function paginateForAdmin(array $filters, int $perPage): LengthAwarePaginator
    {
        $search = trim((string) ($filters['search'] ?? ''));
        $role = trim((string) ($filters['role'] ?? ''));
        $status = $filters['status'] ?? null;

        return $this->query()
            ->with(['offices:id,office_name', 'hubs:id,hub_name,code', 'agents:id,agent_name,code', 'suppliers:id,supplier_name'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('phone_number', 'like', '%' . $search . '%');
                });
            })
            ->when($role !== '', fn ($query) => $query->where('role', $role))
            ->when($status !== null && $status !== '', fn ($query) => $query->where('is_active', (bool) $status))
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function assignmentOptions(): array
    {
        return [
            'assignmentOffices' => Office::query()->orderBy('office_name')->get(['id', 'office_name']),
            'assignmentHubs' => Hub::query()->orderBy('hub_name')->get(['id', 'hub_name', 'code']),
            'assignmentAgents' => Agent::query()->orderBy('agent_name')->get(['id', 'agent_name', 'code']),
            'assignmentSuppliers' => Supplier::query()->orderBy('supplier_name')->get(['id', 'supplier_name']),
        ];
    }

    public function createUser(array $attributes): User
    {
        return $this->modelClass::create($attributes);
    }

    public function usersForChangeLog(): Collection
    {
        return $this->query()->orderBy('name')->get(['id', 'name']);
    }

    public function notificationRecipientsForAgent(int $agentId, ?int $excludeUserId = null): Collection
    {
        return $this->query()
            ->where(function ($q) use ($agentId) {
                $q->where('role', 'Admin')
                    ->orWhereHas('agents', fn ($aq) => $aq->where('agents.id', $agentId));
            })
            ->when($excludeUserId, fn ($q) => $q->where('id', '!=', $excludeUserId))
            ->pluck('id');
    }
}
