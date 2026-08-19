<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

interface BaseRepositoryInterface
{
    public function query(): Builder;

    public function findModelOrFail(int $id, array $with = []): Model;

    public function create(array $data): Model;

    public function updateModel(Model $model, array $data): bool;

    public function deleteById(int $id): bool;

    public function paginateQuery(Builder $query, int $perPage = 25): LengthAwarePaginator;

    public function transaction(callable $callback): mixed;
}
