<?php

namespace App\Repositories;

use App\Models\ProformaInvoice;
use App\Models\ProformaInvoiceLineItem;
use App\Repositories\Contracts\ProformaInvoiceRepositoryInterface;

class ProformaInvoiceRepository extends BaseRepository implements ProformaInvoiceRepositoryInterface
{
    protected string $modelClass = ProformaInvoice::class;

    public function findByShipmentId(int $shipmentId): ?ProformaInvoice
    {
        return $this->query()
            ->with('lineItems')
            ->where('shipment_id', $shipmentId)
            ->first();
    }

    public function saveForShipment(int $shipmentId, array $invoiceData, array $lineItems): ProformaInvoice
    {
        return $this->transaction(function () use ($shipmentId, $invoiceData, $lineItems) {
            $invoice = $this->query()->where('shipment_id', $shipmentId)->first();

            if ($invoice === null) {
                $invoice = $this->create(array_merge($invoiceData, [
                    'shipment_id' => $shipmentId,
                ]));
            } else {
                $this->updateModel($invoice, $invoiceData);
            }

            $invoice->lineItems()->delete();

            foreach ($lineItems as $index => $lineItem) {
                ProformaInvoiceLineItem::query()->create(array_merge($lineItem, [
                    'proforma_invoice_id' => $invoice->id,
                    'sort_order' => $index,
                ]));
            }

            return $invoice->fresh(['lineItems']);
        });
    }
}
