<?php

namespace App\Repositories;

use App\Models\AdministrationChangeLog;
use App\Models\AgentUser;
use App\Models\Contact;
use App\Models\CustomerVessel;
use App\Models\HubUser;
use App\Repositories\Contracts\AdministrationChangeLogRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AdministrationChangeLogRepository extends BaseRepository implements AdministrationChangeLogRepositoryInterface
{
    protected string $modelClass = AdministrationChangeLog::class;

    public function search(array $filters, int $perPage = 50): LengthAwarePaginator
    {
        $query = $this->query()
            ->with([
                'user',
                'loggable' => function (MorphTo $morphTo) {
                    $morphTo->morphWith([
                        Contact::class => ['office', 'customer', 'hub', 'agent', 'supplier', 'otherCompany'],
                        CustomerVessel::class => ['customer'],
                        HubUser::class => ['hub'],
                        AgentUser::class => ['agent'],
                    ]);
                },
            ])
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        $entityType = (string) ($filters['entity_type'] ?? '');
        $userId = $filters['user_id'] ?? null;
        $search = trim((string) ($filters['search'] ?? ''));
        $dateFrom = $filters['date_from'] ?? null;
        $dateTo = $filters['date_to'] ?? null;

        if ($entityType !== '') {
            $query->where('loggable_type', $entityType);
        }

        if ($userId !== null && $userId !== '') {
            $query->where('user_id', $userId);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhere('field', 'like', '%' . $search . '%');
            });
        }

        if ($dateFrom) {
            try {
                $query->where('created_at', '>=', Carbon::parse($dateFrom)->startOfDay());
            } catch (\Throwable) {
                // Ignore invalid date_from.
            }
        }

        if ($dateTo) {
            try {
                $query->where('created_at', '<=', Carbon::parse($dateTo)->endOfDay());
            } catch (\Throwable) {
                // Ignore invalid date_to.
            }
        }

        return $query->paginate($perPage);
    }
}
