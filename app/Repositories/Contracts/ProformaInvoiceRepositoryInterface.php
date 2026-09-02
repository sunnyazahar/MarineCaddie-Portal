<?php

namespace App\Repositories\Contracts;

use App\Models\ProformaInvoice;

interface ProformaInvoiceRepositoryInterface
{
    public function findByShipmentId(int $shipmentId): ?ProformaInvoice;

    /**
     * @param  array<string, mixed>  $invoiceData
     * @param  list<array<string, mixed>>  $lineItems
     */
    public function saveForShipment(int $shipmentId, array $invoiceData, array $lineItems): ProformaInvoice;
}
