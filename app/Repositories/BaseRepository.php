<?php

namespace App\Repositories;

use App\Repositories\Contracts\BaseRepositoryInterface;
use App\Repositories\Criteria\CriteriaInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

abstract class BaseRepository implements BaseRepositoryInterface
{
    /** @var class-string<Model> */
    protected string $modelClass;

    public function query(): Builder
    {
        /** @var Model $model */
        $model = new $this->modelClass();

        return $model->newQuery();
    }

    public function findModelOrFail(int $id, array $with = []): Model
    {
        $query = $this->query();
        if ($with !== []) {
            $query->with($with);
        }

        return $query->findOrFail($id);
    }

    public function create(array $data): Model
    {
        /** @var Model $model */
        $model = new $this->modelClass();

        return $model->create($data);
    }

    public function updateModel(Model $model, array $data): bool
    {
        return $model->update($data);
    }

    public function deleteById(int $id): bool
    {
        return (bool) $this->findModelOrFail($id)->delete();
    }

    public function paginateQuery(Builder $query, int $perPage = 25): LengthAwarePaginator
    {
        return $query->paginate($perPage);
    }

    /**
     * @param  array<int, CriteriaInterface>  $criteria
     */
    protected function applyCriteria(Builder $query, array $criteria): Builder
    {
        foreach ($criteria as $criterion) {
            $query = $criterion->apply($query);
        }

        return $query;
    }

    public function transaction(callable $callback): mixed
    {
        return DB::transaction($callback);
    }
}
