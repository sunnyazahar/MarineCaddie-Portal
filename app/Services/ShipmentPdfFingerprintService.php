<?php

namespace App\Services;

use App\Models\Crr;
use App\Models\Shipment;
use DateTimeInterface;

class ShipmentPdfFingerprintService
{
    public function __construct(
        private ShipmentStockSnapshotService $stockSnapshotService,
    ) {}

    /**
     * New manifest PDF only when:
     * 1. stock items are added or removed
     * 2. consignee name / address / country / port code change
     * 3. departure service / additional service change
     */
    public function manifestFingerprint(Shipment $shipment): string
    {
        return $this->hash($this->manifestRevisionPayload($shipment));
    }

    /**
     * New pre-alert PDF only when:
     * 1. stock items are added or removed
     * 2. service details tab data changes (legs, repacked fields; service type for leg shape)
     * 3. consignee name / address / country / port code change
     *
     * Same rules on shipments/edit and create-pre-alert.
     */
    public function preAlertFingerprint(Shipment $shipment): string
    {
        return $this->hash($this->preAlertRevisionPayload($shipment));
    }

    /**
     * Fingerprint for service-details-only comparisons (legacy helper).
     */
    public function serviceDetailsFingerprint(Shipment $shipment): string
    {
        return $this->hash([
            'service' => $shipment->service,
            'additional_service' => $this->normalizeValue($shipment->additional_service),
            'service_details' => $this->serviceDetailsPayload($shipment),
            'repacked_items' => $this->normalizeValue($shipment->repacked_items),
            'repacked_weight' => $this->normalizeValue($shipment->repacked_weight),
        ]);
    }

    public function prepareForFingerprint(Shipment $shipment): Shipment
    {
        $shipment->loadMissing($this->relations());

        $this->stockSnapshotService->applyResolvedStockCrrs($shipment);

        return $shipment;
    }

    /**
     * @return list<string>
     */
    public function relations(): array
    {
        return [
            'crrs.packages',
            'crrs.customerVessel.customer',
            'accountManager.office',
            'creator',
            'flights',
            'seaLegs',
            'truckLegs',
            'courierLegs',
            'releaseLegs',
            'handCarryLegs',
            'onBoardLegs',
            'stockSnapshots',
        ];
    }

    private function manifestPayload(Shipment $shipment): array
    {
        return [
            'shipment' => $this->normalizeShipmentAttributes($shipment),
            'stocks' => $this->normalizeStocks($shipment->crrs),
        ];
    }

    private function manifestRevisionPayload(Shipment $shipment): array
    {
        return [
            'service' => $this->normalizeValue($shipment->service),
            'additional_service' => $this->normalizeValue($shipment->additional_service),
            'consignee' => $this->normalizeValue($shipment->consignee),
            'consignee_address' => $this->normalizeValue($shipment->consignee_address),
            'consignee_country' => $this->normalizeValue($shipment->consignee_country),
            'consignee_port_code' => $this->normalizeValue($shipment->consignee_port_code),
            'stock_ids' => $this->revisionStockIds($shipment),
        ];
    }

    private function preAlertRevisionPayload(Shipment $shipment): array
    {
        return [
            'stock_ids' => $this->revisionStockIds($shipment),
            'consignee' => $this->normalizeValue($shipment->consignee),
            'consignee_address' => $this->normalizeValue($shipment->consignee_address),
            'consignee_country' => $this->normalizeValue($shipment->consignee_country),
            'consignee_port_code' => $this->normalizeValue($shipment->consignee_port_code),
            'service' => $this->normalizeValue($shipment->service),
            'service_details' => $this->serviceDetailsPayload($shipment),
            'repacked_items' => $this->normalizeValue($shipment->repacked_items),
            'repacked_weight' => $this->normalizeValue($shipment->repacked_weight),
        ];
    }

    /**
     * @return list<int>
     */
    private function revisionStockIds(Shipment $shipment): array
    {
        return $shipment->crrs
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->sort()
            ->values()
            ->all();
    }

    private function normalizeShipmentAttributes(Shipment $shipment): array
    {
        // Only fields that affect manifest/pre-alert PDF content are fingerprinted.
        // Administrative and comment fields (dates, status, account manager, etc.) are excluded.
        $keys = [
            'departure',
            'departure_port_code',
            'service',
            'additional_service',
            'consignee',
            'consignee_address',
            'consignee_city',
            'consignee_district',
            'consignee_zip',
            'consignee_country',
            'consignee_port_code',
            'consignee_att',
            'consignee_email',
            'location',
            'deadline_arrival',
            'repacked_items',
            'repacked_weight',
        ];

        $data = [];

        foreach ($keys as $key) {
            $data[$key] = $this->normalizeValue($shipment->{$key});
        }

        return $data;
    }

    private function normalizeStocks($crrs): array
    {
        return $crrs
            ->sortBy('id')
            ->values()
            ->map(function (Crr $crr) {
                return [
                    'id' => $crr->id,
                    'stock_number' => $crr->stock_number,
                    'vessel_name' => $crr->vessel_name,
                    'po_numbers' => $crr->po_numbers,
                    'supplier' => $crr->supplier,
                    'content' => $crr->content,
                    'customs_value' => $this->normalizeValue($crr->customs_value),
                    'currency' => $crr->currency,
                    'hub_agent' => $crr->hub_agent,
                    'location' => $crr->location,
                    'transit_id' => $crr->transit_id,
                    'expected_delivery_date' => $this->normalizeValue($crr->expected_delivery_date),
                    'hs_code' => $crr->hs_code,
                    'customer' => $crr->customerVessel?->customer?->customer_name,
                    'packages' => $crr->packages
                        ->sortBy('id')
                        ->values()
                        ->map(fn ($package) => [
                            'length' => $this->normalizeValue($package->length),
                            'width' => $this->normalizeValue($package->width),
                            'height' => $this->normalizeValue($package->height),
                            'weight' => $this->normalizeValue($package->weight),
                            'cbm' => $this->normalizeValue($package->cbm),
                            'warehouse_location' => $package->warehouse_location,
                            'remarks' => $package->remarks,
                        ])
                        ->all(),
                ];
            })
            ->all();
    }

    private function serviceDetailsPayload(Shipment $shipment): array
    {
        return match ($shipment->service) {
            'Airfreight' => $shipment->flights
                ->sortBy('sort_order')
                ->values()
                ->map(fn ($flight) => [
                    'leg_reference' => $flight->leg_reference,
                    'flight_number' => $flight->flight_number,
                    'departure_date' => $this->normalizeValue($flight->departure_date),
                    'arrival_date' => $this->normalizeValue($flight->arrival_date),
                    'arrival_time' => $flight->arrival_time,
                ])
                ->all(),
            'Sea freight' => $shipment->seaLegs
                ->sortBy('sort_order')
                ->values()
                ->map(fn ($leg) => [
                    'bill_of_lading' => $leg->bill_of_lading,
                    'container_number' => $leg->container_number,
                    'transport_vessel_imo' => $leg->transport_vessel_imo,
                    'transport_vessel_name' => $leg->transport_vessel_name,
                    'etd' => $this->normalizeValue($leg->etd),
                    'eta' => $this->normalizeValue($leg->eta),
                    'arrival_time' => $leg->arrival_time,
                ])
                ->all(),
            'Truck' => $shipment->truckLegs
                ->sortBy('sort_order')
                ->values()
                ->map(fn ($leg) => [
                    'cmr' => $leg->cmr,
                    'freight_company' => $leg->freight_company,
                    'departure_date' => $this->normalizeValue($leg->departure_date),
                    'arrival_date' => $this->normalizeValue($leg->arrival_date),
                    'arrival_time' => $leg->arrival_time,
                ])
                ->all(),
            'Courier' => $shipment->courierLegs
                ->sortBy('sort_order')
                ->values()
                ->map(fn ($leg) => [
                    'airway_bill' => $leg->airway_bill,
                    'carrier' => $leg->carrier,
                    'departure_date' => $this->normalizeValue($leg->departure_date),
                    'arrival_date' => $this->normalizeValue($leg->arrival_date),
                    'arrival_time' => $leg->arrival_time,
                ])
                ->all(),
            'Release' => $shipment->releaseLegs
                ->sortBy('sort_order')
                ->values()
                ->map(fn ($leg) => [
                    'freight_company' => $leg->freight_company,
                    'delivery_date' => $this->normalizeValue($leg->delivery_date),
                    'delivery_time' => $leg->delivery_time,
                ])
                ->all(),
            'Hand Carry' => $shipment->handCarryLegs
                ->sortBy('sort_order')
                ->values()
                ->map(fn ($leg) => [
                    'departure_date' => $this->normalizeValue($leg->departure_date),
                    'arrival_date' => $this->normalizeValue($leg->arrival_date),
                    'arrival_time' => $leg->arrival_time,
                    'contact_name' => $leg->contact_name,
                    'contact_phone' => $leg->contact_phone,
                    'onboard_hand_carry' => (bool) $leg->onboard_hand_carry,
                ])
                ->all(),
            'On-board delivery' => $shipment->onBoardLegs
                ->sortBy('sort_order')
                ->values()
                ->map(fn ($leg) => [
                    'departure_date' => $this->normalizeValue($leg->departure_date),
                    'delivery_date' => $this->normalizeValue($leg->delivery_date),
                    'delivery_time' => $leg->delivery_time,
                ])
                ->all(),
            default => [],
        };
    }

    private function normalizeValue(mixed $value): mixed
    {
        if ($value instanceof DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        if (is_bool($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (string) $value;
        }

        return $value;
    }

    private function hash(array $payload): string
    {
        return hash('sha256', json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '');
    }
}
