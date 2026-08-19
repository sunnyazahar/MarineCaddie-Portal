<?php

namespace App\Repositories\Criteria;

use Illuminate\Database\Eloquent\Builder;

abstract class AbstractCriteria implements CriteriaInterface
{
    final public function apply(Builder $query): Builder
    {
        return $this->handle($query);
    }

    abstract protected function handle(Builder $query): Builder;
}
