<?php

namespace App\Repositories;

use App\Models\CustomerVessel;
use App\Repositories\Contracts\VesselRepositoryInterface;
use App\Support\ListSearch;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class VesselRepository implements VesselRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 25): LengthAwarePaginator
    {
        $name = trim((string) ($filters['name'] ?? ''));
        $imo  = trim((string) ($filters['imo'] ?? ''));
        $type = trim((string) ($filters['type'] ?? ''));

        return CustomerVessel::query()
            ->with('customer')
            ->when(ListSearch::contains($name), function ($q, $p) {
                $q->where(function ($sub) use ($p) {
                    $sub->where('vessel', 'like', $p)
                        ->orWhere('vessel_name_alias', 'like', $p);
                });
            })
            ->when(ListSearch::contains($imo), fn ($q, $p) => $q->where('vessel_imo', 'like', $p))
            ->when($type !== '', fn ($q) => $q->where('vessel_type_alias', $type))
            ->orderBy('vessel')
            ->paginate($perPage);
    }

    public function distinctTypes(): Collection
    {
        return CustomerVessel::query()
            ->whereNotNull('vessel_type_alias')
            ->where('vessel_type_alias', '!=', '')
            ->distinct()
            ->orderBy('vessel_type_alias')
            ->pluck('vessel_type_alias');
    }
}
