<?php

namespace App\Repositories;

use App\Models\CrrDocument;
use App\Repositories\Contracts\CrrDocumentRepositoryInterface;
use Illuminate\Support\Collection;

class CrrDocumentRepository implements CrrDocumentRepositoryInterface
{
    public function pdfDocumentsForShipment(string $shipmentNumber, array $crrIds): Collection
    {
        return CrrDocument::query()
            ->with('crr:id,stock_number,vessel_name,internal_shipment')
            ->where(function ($query) use ($shipmentNumber, $crrIds) {
                if (! empty($crrIds)) {
                    $query->whereIn('crr_id', $crrIds);
                }

                $query->orWhereHas('crr', function ($crrQuery) use ($shipmentNumber) {
                    $crrQuery->where('internal_shipment', $shipmentNumber);
                });
            })
            ->where(function ($query) {
                $query->whereRaw('LOWER(file_name) LIKE ?', ['%.pdf'])
                    ->orWhereRaw('LOWER(file_path) LIKE ?', ['%.pdf']);
            })
            ->orderBy('created_at')
            ->get()
            ->unique('file_path')
            ->values();
    }

    public function create(array $attributes): CrrDocument
    {
        return CrrDocument::create($attributes);
    }
}
