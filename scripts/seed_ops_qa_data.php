<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Crr;
use App\Models\CrrPackage;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

$user = User::query()
    ->where('email', 'sunnyazahar@gmail.com')
    ->orWhere('is_active', 1)
    ->orderBy('id')
    ->first();

if (! $user) {
    fwrite(STDERR, "No active user found for QA seed.\n");
    exit(1);
}

$prefix = 'QA' . now()->format('YmdHis');

DB::beginTransaction();
try {
    $crr = Crr::create([
        'stock_number' => $prefix . '-STK',
        'registered_by' => $user->id,
        'hub_agent' => 'QAHUB',
        'currency' => 'USD',
        'customs_value' => 1250.50,
        'status' => Crr::STATUS_PENDING,
        'flags' => Crr::defaultFlags(),
        'supplier' => 'QA Supplier',
        'vessel_name' => 'QA VESSEL',
        'content' => 'Shipspares',
    ]);

    CrrPackage::create([
        'crr_id' => $crr->id,
        'length' => 10,
        'width' => 12,
        'height' => 8,
        'weight' => 5,
        'cbm' => 0.01,
        'warehouse_location' => 'A-01',
        'remarks' => 'QA package',
    ]);

    $shipment = Shipment::create([
        'shipment_number' => $prefix . '-SHP',
        'departure' => 'hub:1',
        'service' => 'Airfreight',
        'consignee_att' => 'QA Contact',
        'consignee_email' => 'qa@example.com',
        'status' => 'Draft',
        'created_by' => $user->id,
        'flags' => Shipment::defaultFlags(),
    ]);

    $shipment->crrs()->sync([$crr->id]);

    DB::commit();

    echo json_encode([
        'ok' => true,
        'crr_id' => $crr->id,
        'crr_stock_number' => $crr->stock_number,
        'shipment_id' => $shipment->id,
        'shipment_number' => $shipment->shipment_number,
    ], JSON_PRETTY_PRINT) . PHP_EOL;
} catch (\Throwable $e) {
    DB::rollBack();
    fwrite(STDERR, 'Seed failed: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}
