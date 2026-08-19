<?php

namespace App\Repositories\Contracts;

interface HubDocumentRepositoryInterface
{
    public function findOrFailByType(string $type, int $id): \Illuminate\Database\Eloquent\Model;
}
