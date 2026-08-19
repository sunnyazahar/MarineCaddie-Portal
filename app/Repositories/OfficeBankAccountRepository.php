<?php

namespace App\Repositories;

use App\Models\OfficeBankAccount;
use App\Repositories\Contracts\OfficeBankAccountRepositoryInterface;

class OfficeBankAccountRepository extends BaseRepository implements OfficeBankAccountRepositoryInterface
{
    protected string $modelClass = OfficeBankAccount::class;

    public function create(array $data): OfficeBankAccount
    {
        return parent::create($data);
    }
}
