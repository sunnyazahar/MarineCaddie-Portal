<?php

namespace App\Repositories\Criteria;

use Illuminate\Database\Eloquent\Builder;

interface CriteriaInterface
{
    public function apply(Builder $query): Builder;
}
