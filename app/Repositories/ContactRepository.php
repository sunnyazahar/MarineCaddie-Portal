<?php

namespace App\Repositories;

use App\Models\Contact;
use App\Repositories\Contracts\ContactRepositoryInterface;
use Illuminate\Support\Collection;

class ContactRepository extends BaseRepository implements ContactRepositoryInterface
{
    protected string $modelClass = Contact::class;

    public function findOrFail(int $id, array $with = []): Contact
    {
        return parent::findModelOrFail($id, $with);
    }

    public function find(int $id): ?Contact
    {
        return $this->query()->find($id);
    }

    public function findByCategoryOrFail(int $id, string $category): Contact
    {
        return $this->query()
            ->where('category', $category)
            ->findOrFail($id);
    }

    public function byCategory(string $category): Collection
    {
        return $this->query()
            ->where('category', $category)
            ->get();
    }

    public function create(array $data): Contact
    {
        return parent::create($data);
    }

    public function update(Contact $contact, array $data): bool
    {
        return parent::updateModel($contact, $data);
    }

    public function deleteById(int $id): bool
    {
        return parent::deleteById($id);
    }
}
