<?php

namespace App\Repositories\Contracts;

use App\Models\CrrDocument;
use Illuminate\Support\Collection;

interface CrrDocumentRepositoryInterface
{
    /**
     * @param  array<int, int>  $crrIds
     */
    public function pdfDocumentsForShipment(string $shipmentNumber, array $crrIds): Collection;

    public function create(array $attributes): CrrDocument;
}
