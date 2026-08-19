<?php

namespace App\Repositories\Contracts;

interface ContactRepositoryInterface
{
    public function findOrFail(int $id, array $with = []): \App\Models\Contact;

    public function find(int $id): ?\App\Models\Contact;

    public function findByCategoryOrFail(int $id, string $category): \App\Models\Contact;

    public function byCategory(string $category): \Illuminate\Support\Collection;

    public function create(array $data): \App\Models\Contact;

    public function update(\App\Models\Contact $contact, array $data): bool;

    public function deleteById(int $id): bool;
}
