<?php

namespace App\Repositories\Contracts;

interface OfficeBankAccountRepositoryInterface
{
    public function create(array $data): \App\Models\OfficeBankAccount;
}
