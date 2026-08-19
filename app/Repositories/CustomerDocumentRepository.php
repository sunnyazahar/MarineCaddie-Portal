<?php

namespace App\Repositories;

use App\Models\CustomerDocument;
use App\Repositories\Contracts\CustomerDocumentRepositoryInterface;

class CustomerDocumentRepository extends BaseRepository implements CustomerDocumentRepositoryInterface
{
    protected string $modelClass = CustomerDocument::class;

    public function create(array $data): CustomerDocument
    {
        return parent::create($data);
    }

    public function findOrFail(int $id): CustomerDocument
    {
        return parent::findModelOrFail($id);
    }

    public function find(int $id): ?CustomerDocument
    {
        return $this->query()->find($id);
    }

    public function findByCustomerOrFail(int $customerId, int $docId): CustomerDocument
    {
        return $this->query()
            ->where('customer_id', $customerId)
            ->findOrFail($docId);
    }

    public function delete(CustomerDocument $document): bool
    {
        return (bool) $document->delete();
    }
}
