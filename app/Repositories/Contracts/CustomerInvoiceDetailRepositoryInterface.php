<?php

namespace App\Repositories\Contracts;

interface CustomerInvoiceDetailRepositoryInterface
{
    public function create(array $data): \App\Models\CustomerInvoiceDetail;

    public function updateOrCreate(array $attributes, array $values): \App\Models\CustomerInvoiceDetail;
}
