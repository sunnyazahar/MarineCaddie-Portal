<?php

namespace App\Repositories;

use App\Models\Supplier;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use App\Support\ListSearch;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SupplierRepository extends BaseRepository implements SupplierRepositoryInterface
{
    protected string $modelClass = Supplier::class;

    public function paginate(array $filters, int $perPage = 25): LengthAwarePaginator
    {
        $search = trim((string) ($filters['search'] ?? ''));
        $like   = ListSearch::contains($search);

        return $this->query()
            ->with('country')
            ->when($like, function ($q, $pattern) {
                $q->where(function ($sub) use ($pattern) {
                    $sub->where('supplier_name', 'like', $pattern)
                        ->orWhere('supplier_address', 'like', $pattern)
                        ->orWhere('city', 'like', $pattern)
                        ->orWhere('email', 'like', $pattern)
                        ->orWhere('phone_number', 'like', $pattern)
                        ->orWhere('contact_person', 'like', $pattern)
                        ->orWhereHas('country', fn ($c) => $c->where('name', 'like', $pattern));
                });
            })
            ->orderBy('supplier_name')
            ->paginate($perPage);
    }

    public function findOrFail(int $id): Supplier
    {
        return parent::findModelOrFail($id);
    }

    public function findWithRelations(int $id, array $relations = []): Supplier
    {
        return parent::findModelOrFail($id, $relations);
    }

    public function create(array $data): Supplier
    {
        return parent::create($data);
    }

    public function update(Supplier $supplier, array $data): bool
    {
        return $supplier->update($data);
    }

    public function deleteById(int $id): bool
    {
        return parent::deleteById($id);
    }
}
