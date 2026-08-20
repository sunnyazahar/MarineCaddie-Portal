<?php

namespace App\Http\Controllers\Shipments;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\ManagesShipmentPersistence;
use App\Repositories\Contracts\ShipmentRepositoryInterface;

abstract class BaseShipmentController extends Controller
{
    use ManagesShipmentPersistence;

    public function __construct(protected ShipmentRepositoryInterface $shipmentRepository)
    {
    }
}
