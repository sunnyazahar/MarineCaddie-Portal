<?php

namespace App\Repositories;

use App\Models\Office;
use App\Repositories\Contracts\OfficeRepositoryInterface;
use Illuminate\Support\Collection;

class OfficeRepository extends BaseRepository implements OfficeRepositoryInterface
{
    protected string $modelClass = Office::class;

    public function all(): Collection
    {
        return $this->query()->with('country')->orderByDesc('id')->get();
    }

    public function findWithRelations(int $id, array $relations = []): Office
    {
        return parent::findModelOrFail($id, $relations);
    }

    public function create(array $data): Office
    {
        return parent::create($data);
    }

    public function update(Office $office, array $data): bool
    {
        return parent::updateModel($office, $data);
    }

    public function findOrFail(int $id): Office
    {
        return parent::findModelOrFail($id);
    }
}
