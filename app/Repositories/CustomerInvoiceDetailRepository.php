<?php

namespace App\Repositories;

use App\Models\CustomerInvoiceDetail;
use App\Repositories\Contracts\CustomerInvoiceDetailRepositoryInterface;

class CustomerInvoiceDetailRepository extends BaseRepository implements CustomerInvoiceDetailRepositoryInterface
{
    protected string $modelClass = CustomerInvoiceDetail::class;

    public function create(array $data): CustomerInvoiceDetail
    {
        return parent::create($data);
    }

    public function updateOrCreate(array $attributes, array $values): CustomerInvoiceDetail
    {
        return $this->query()->updateOrCreate($attributes, $values);
    }
}
