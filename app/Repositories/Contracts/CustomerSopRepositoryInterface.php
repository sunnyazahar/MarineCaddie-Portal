<?php

namespace App\Repositories\Contracts;

interface CustomerSopRepositoryInterface
{
    public function create(array $data): \App\Models\CustomerSop;

    public function updateOrCreate(array $attributes, array $values): \App\Models\CustomerSop;
}
