<?php

namespace App\Repositories\Contracts;

interface CustomerDocumentRepositoryInterface
{
    public function create(array $data): \App\Models\CustomerDocument;

    public function findOrFail(int $id): \App\Models\CustomerDocument;

    public function find(int $id): ?\App\Models\CustomerDocument;

    public function findByCustomerOrFail(int $customerId, int $docId): \App\Models\CustomerDocument;

    public function delete(\App\Models\CustomerDocument $document): bool;
}
