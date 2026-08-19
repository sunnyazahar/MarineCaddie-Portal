<?php

namespace App\Repositories\Contracts;

interface CustomerResponsibleRepositoryInterface
{
    public function create(array $data): \App\Models\CustomerResponsible;

    public function updateOrCreate(array $attributes, array $values): \App\Models\CustomerResponsible;
}
