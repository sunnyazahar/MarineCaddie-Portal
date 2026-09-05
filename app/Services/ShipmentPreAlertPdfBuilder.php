<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\Crr;
use App\Models\Hub;
use App\Models\Port;
use App\Models\Shipment;
use App\Models\ShipmentCourierLeg;
use App\Models\ShipmentFlight;
use App\Models\ShipmentHandCarryLeg;
use App\Models\ShipmentOnBoardLeg;
use App\Models\ShipmentReleaseLeg;
use App\Models\ShipmentSeaLeg;
use App\Models\ShipmentTruckLeg;
use App\Repositories\Contracts\PartyLookupRepositoryInterface;
use App\Repositories\Contracts\PortRepositoryInterface;

class ShipmentPreAlertPdfBuilder
{
    public function __construct(
        private ShipmentManifestPdfBuilder $manifestPdfBuilder,
        private CombinedPoPdfService $combinedPoPdfService,
        private PortRepositoryInterface $portRepository,
        private PartyLookupRepositoryInterface $partyLookupRepository,
    ) {}

    public function build(Shipment $shipment): array
    {
        $shipment->loadMissing([
            'crrs.packages',
            'crrs.customerVessel.customer',
            'accountManager.office.country',
            'creator',
            'flights',
            'seaLegs',
            'truckLegs',
            'courierLegs',
            'releaseLegs',
            'handCarryLegs',
            'onBoardLegs',
        ]);

        $base = $this->manifestPdfBuilder->build($shipment);
        $serviceDetails = $this->buildServiceDetails($shipment);
        $serviceDetailRows = $this->buildServiceDetailRows($shipment);

        $primaryVessel = $this->formatMotorVesselName($shipment->crrs->pluck('vessel_name')->filter()->first());
        // Pre-alert PDF/email: vessel name + transit suffix only (no IMO).
        $vesselLine = $this->formatMailVesselLine($shipment);
        $awb = $serviceDetails['awb'] ?? '—';
        $flightNumber = $serviceDetails['flight_number'] ?? '—';
        $arrivalDate = $serviceDetails['arrival_date'] ?? ($shipment->deadline_arrival?->format('d.m.Y') ?? '—');
        $arrivalTime = $serviceDetails['arrival_time'] ?? '—';
        $arrivalLabel = trim($arrivalDate . ($arrivalTime && $arrivalTime !== '—' ? ' ' . $arrivalTime : ''));

        if ($serviceDetailRows !== []) {
            $lastRow = $serviceDetailRows[array_key_last($serviceDetailRows)];
            $arrivalDate = $lastRow['arrival_date'] ?? $arrivalDate;
            $arrivalTime = $lastRow['arrival_time'] ?? $arrivalTime;
            $arrivalLabel = trim($arrivalDate . ($arrivalTime && $arrivalTime !== '—' ? ' ' . $arrivalTime : ''));
            $flightNumber = $lastRow['flight'] ?? $flightNumber;
        }

        $departurePort = $this->simplePortLabel(
            $shipment->departure_port_code,
            null,
            $base['departurePort'] ?? null
        );
        $destinationPort = $this->simplePortLabel(
            $shipment->consignee_port_code,
            $shipment->consignee_city,
            $base['destinationPort'] ?? null,
            $shipment->consignee_country
        );

        $serviceLabel = filled($shipment->additional_service)
            ? trim(($shipment->service ?? '—') . ', ' . $shipment->additional_service)
            : ($shipment->service ?? '—');

        $ownerName = $shipment->crrs
            ->map(fn (Crr $crr) => $crr->customerVessel?->customer?->customer_name)
            ->filter()
            ->unique()
            ->implode(', ') ?: '—';

        $accountManager = $shipment->accountManager;
        $accountHandledBy = $accountManager
            ? trim($accountManager->name . ($accountManager->email ? ', ' . $accountManager->email : ''))
            : trim(($shipment->creator?->name ?? '—') . ($shipment->creator?->email ? ', ' . $shipment->creator->email : ''));

        $office = $accountManager?->office;
        $issuedByName = $office?->office_name ?? $base['companyName'] ?? 'Marinetrans';
        $issuedByAddress = $this->formatOfficeAddress($office) ?: ($base['companyAddress'] ?? '—');

        $currencyRates = $this->manifestPdfBuilder->currencyRatesByCode();
        $preAlertRows = $shipment->crrs->map(function (Crr $crr) use ($currencyRates) {
            $poNumbers = is_array($crr->po_numbers)
                ? implode(', ', $crr->po_numbers)
                : ($crr->po_numbers ?? '—');

            $description = $crr->content ?? 'Shipspares';
            if ($crr->hs_code) {
                $description .= "\nHsCode: " . $crr->hs_code;
            }

            $customsUsd = $this->manifestPdfBuilder->convertCustomsValueToUsd($crr, $currencyRates);

            return [
                'supplier' => $crr->supplier ?? '—',
                'po_number' => $poNumbers ?: '—',
                'items' => $crr->packages->count(),
                'weight' => round((float) $crr->packages->sum('weight'), 2),
                'cbm' => round((float) $crr->packages->sum('cbm'), 2),
                'customs_value' => number_format($customsUsd, 2) . ' USD',
                'description' => $description,
                'stock_number' => $crr->stock_number ?? '—',
            ];
        });

        $totals = $base['totals'] ?? [];
        $totalPackages = (int) ($totals['packages'] ?? 0);
        $totalWeight = $totals['weight'] ?? 0;

        $resolvedServiceDetailRows = $serviceDetailRows !== []
            ? $serviceDetailRows
            : [[
                'service' => $serviceLabel,
                'additional_service' => $shipment->additional_service ?: '—',
                'departure_port' => $serviceDetails['departure_port'] ?? $departurePort,
                'departure_date' => '—',
                'flight' => $flightNumber,
                'arrival_date' => $arrivalDate,
                'arrival_time' => $arrivalTime,
                'reference' => $awb,
            ]];

        $shippersReference = $shipment->shipment_number;
        $ownersReference = $shipment->customer_reference ?? '—';
        $referenceColumnLabel = $this->referenceColumnLabel($shipment->service);
        $showReferenceColumn = $this->serviceHasReferenceColumn($shipment->service);

        $freightDetailRows = [[
            'departure_port' => $departurePort,
            'destination_port' => $destinationPort,
            'shippers_reference' => $shippersReference,
            'reference' => $resolvedServiceDetailRows[0]['reference'] ?? $awb,
            'owners_reference' => $ownersReference,
        ]];

        $awbLabels = collect($resolvedServiceDetailRows)
            ->pluck('reference')
            ->filter(fn ($value) => filled($value) && $value !== '—')
            ->unique()
            ->values();
        $awb = $awbLabels->isNotEmpty() ? $awbLabels->first() : $awb;

        $packedItems = filled($shipment->repacked_items) ? (int) $shipment->repacked_items : $totalPackages;
        $packedWeight = filled($shipment->repacked_weight)
            ? number_format((float) $shipment->repacked_weight, 2, '.', '')
            : number_format((float) $totalWeight, 2, '.', '');

        return array_merge($base, [
            'headerSubtitle' => $serviceLabel,
            'expectedLine' => sprintf(
                'Shipment is expected on %s in %s with the below details',
                $arrivalDate,
                $destinationPort
            ),
            'departurePortSimple' => $departurePort,
            'destinationPortSimple' => $destinationPort,
            'shippersReference' => $shippersReference,
            'ownersReference' => $ownersReference,
            'awb' => $awb,
            'referenceColumnLabel' => $referenceColumnLabel,
            'showReferenceColumn' => $showReferenceColumn,
            'freightDetailRows' => $freightDetailRows,
            'flightNumber' => $flightNumber,
            'serviceDeparturePort' => $serviceDetails['departure_port'] ?? $departurePort,
            'arrivalDate' => $arrivalDate,
            'arrivalTime' => $arrivalTime,
            'arrivalLabel' => $arrivalLabel,
            'serviceDetailRows' => $resolvedServiceDetailRows,
            'accountHandledBy' => $accountHandledBy,
            'issuedByName' => $issuedByName,
            'issuedByAddress' => $issuedByAddress,
            'primaryVessel' => $primaryVessel,
            'vesselLine' => $vesselLine !== '' ? $vesselLine : ($primaryVessel ?: '—'),
            'serviceLabel' => $serviceLabel,
            'customerReference' => $shipment->customer_reference ?? '—',
            'customerName' => $ownerName,
            'consigneeName' => $base['consigneeName'] ?? '—',
            'consigneeAddressBlock' => $this->formatShippedToBlock($shipment, $base),
            'combinedPoUrl' => $this->combinedPoPdfService->documentsForShipment($shipment)->isNotEmpty()
                ? route('shipments.combined-po-documents', $shipment->id)
                : null,
            'preAlertRows' => $preAlertRows,
            'totalPiecesLabel' => $totalPackages . ' pcs',
            'packedAsLabel' => $packedItems . ' item(s) / ' . $packedWeight . ' kg',
            'customsValueLabel' => ($totals['customs_value'] ?? '0.00') . ' ' . ($totals['currency'] ?? 'USD'),
            'totalSummaryLabel' => sprintf(
                '%s pcs %s kg %s CBM %s %s',
                $totalPackages,
                $totalWeight,
                number_format((float) ($totals['cbm'] ?? 0), 2),
                $totals['customs_value'] ?? '0.00',
                $totals['currency'] ?? 'USD'
            ),
        ]);
    }

    /**
     * @return array{awb?: string, flight_number?: string, arrival?: string, arrival_date?: string, arrival_time?: string, departure_port?: string, service_suffix?: string, rows: array<int, array{label: string, value: string}>}
     */
    private function buildServiceDetails(Shipment $shipment): array
    {
        $result = ['rows' => []];
        $departurePort = $this->simplePortLabel($shipment->departure_port_code, null, null);

        switch ($shipment->service) {
            case 'Airfreight':
                /** @var ShipmentFlight|null $flight */
                $flight = $shipment->flights->first();
                if (!$flight) {
                    break;
                }

                $result['awb'] = $flight->leg_reference ?: '—';
                $result['flight_number'] = $flight->flight_number ?: '—';
                $result['arrival_date'] = $this->formatDate($flight->arrival_date) ?? '—';
                $result['arrival_time'] = $flight->arrival_time ?: '—';
                $result['arrival'] = $this->formatArrival($flight->arrival_date, $flight->arrival_time);
                $result['departure_port'] = $departurePort;
                if (filled($shipment->additional_service)) {
                    $result['service_suffix'] = ', ' . $shipment->additional_service;
                }
                break;

            case 'Sea freight':
                /** @var ShipmentSeaLeg|null $leg */
                $leg = $shipment->seaLegs->sortBy('sort_order')->first();
                if (!$leg) {
                    break;
                }

                $result['awb'] = $leg->bill_of_lading ?: '—';
                $result['arrival_date'] = $this->formatDate($leg->eta) ?? '—';
                $result['arrival_time'] = $leg->arrival_time ?: '—';
                $result['arrival'] = $this->formatArrival($leg->eta, $leg->arrival_time);
                $result['departure_port'] = $departurePort;
                $result['flight_number'] = $leg->transport_vessel_name ?: '—';
                if (filled($shipment->additional_service)) {
                    $result['service_suffix'] = ', ' . $shipment->additional_service;
                }
                break;

            case 'Truck':
                /** @var ShipmentTruckLeg|null $leg */
                $leg = $shipment->truckLegs->first();
                if (!$leg) {
                    break;
                }

                $result['awb'] = $leg->cmr ?: '—';
                $result['arrival_date'] = $this->formatDate($leg->arrival_date) ?? '—';
                $result['arrival_time'] = $leg->arrival_time ?: '—';
                $result['arrival'] = $this->formatArrival($leg->arrival_date, $leg->arrival_time);
                $result['departure_port'] = $departurePort;
                $result['flight_number'] = $leg->freight_company ?: '—';
                break;

            case 'Courier':
                /** @var ShipmentCourierLeg|null $leg */
                $leg = $shipment->courierLegs->first();
                if (!$leg) {
                    break;
                }

                $result['awb'] = $leg->airway_bill ?: '—';
                $result['arrival_date'] = $this->formatDate($leg->arrival_date) ?? '—';
                $result['arrival_time'] = $leg->arrival_time ?: '—';
                $result['arrival'] = $this->formatArrival($leg->arrival_date, $leg->arrival_time);
                $result['departure_port'] = $departurePort;
                $result['flight_number'] = $leg->carrier ?: '—';
                break;

            case 'Release':
                /** @var ShipmentReleaseLeg|null $leg */
                $leg = $shipment->releaseLegs->first();
                if (!$leg) {
                    break;
                }

                $result['arrival_date'] = $this->formatDate($leg->delivery_date) ?? '—';
                $result['arrival_time'] = $leg->delivery_time ?: '—';
                $result['arrival'] = $this->formatArrival($leg->delivery_date, $leg->delivery_time);
                $result['departure_port'] = $departurePort;
                $result['flight_number'] = $leg->freight_company ?: '—';
                break;

            case 'Hand Carry':
                /** @var ShipmentHandCarryLeg|null $leg */
                $leg = $shipment->handCarryLegs->first();
                if (!$leg) {
                    break;
                }

                $result['arrival_date'] = $this->formatDate($leg->arrival_date) ?? '—';
                $result['arrival_time'] = $leg->arrival_time ?: '—';
                $result['arrival'] = $this->formatArrival($leg->arrival_date, $leg->arrival_time);
                $result['departure_port'] = $departurePort;
                $result['flight_number'] = $leg->contact_name ?: '—';
                break;

            case 'On-board delivery':
                /** @var ShipmentOnBoardLeg|null $leg */
                $leg = $shipment->onBoardLegs->first();
                if (!$leg) {
                    break;
                }

                $result['arrival_date'] = $this->formatDate($leg->delivery_date) ?? '—';
                $result['arrival_time'] = $leg->delivery_time ?: '—';
                $result['arrival'] = $this->formatArrival($leg->delivery_date, $leg->delivery_time);
                $result['departure_port'] = $departurePort;
                break;
        }

        return $result;
    }

    /**
     * Public rows for compose/pre-alert mail service table (same shape as PDF).
     *
     * @return list<array{service: string, additional_service: string, departure_port: string, departure_date: string, flight: string, arrival_date: string, arrival_time: string, reference: string}>
     */
    public function serviceDetailRowsForMail(Shipment $shipment): array
    {
        $shipment->loadMissing([
            'flights',
            'seaLegs',
            'truckLegs',
            'courierLegs',
            'releaseLegs',
            'handCarryLegs',
            'onBoardLegs',
        ]);

        return $this->buildServiceDetailRows($shipment);
    }

    public function flightColumnLabel(?string $service): string
    {
        return match ($service) {
            'Sea freight' => 'Vessel',
            'Truck' => 'Freight company',
            'Courier' => 'Carrier',
            'Release' => 'Freight company',
            'Hand Carry' => 'Contact',
            'On-board delivery' => 'Delivery',
            default => 'Flight',
        };
    }

    /**
     * @return list<array{service: string, additional_service: string, departure_port: string, departure_date: string, flight: string, arrival_date: string, arrival_time: string, reference: string}>
     */
    private function buildServiceDetailRows(Shipment $shipment): array
    {
        $departurePort = $this->simplePortLabel($shipment->departure_port_code, null, null) ?: '—';
        $additionalService = $shipment->additional_service ?: '—';
        $serviceBase = $shipment->service ?? '—';
        $serviceWithAdditional = filled($shipment->additional_service)
            ? trim($serviceBase . ', ' . $shipment->additional_service)
            : $serviceBase;

        return match ($shipment->service) {
            'Airfreight' => $shipment->flights
                ->sortBy('sort_order')
                ->values()
                ->map(function (ShipmentFlight $flight, int $index) use ($departurePort, $additionalService, $serviceWithAdditional) {
                    $isFirstLeg = $index === 0;
                    $legDeparturePort = $isFirstLeg
                        ? $departurePort
                        : $this->simplePortLabel($flight->leg_reference);

                    return [
                        'service' => $serviceWithAdditional,
                        'additional_service' => $additionalService,
                        'departure_port' => $legDeparturePort,
                        'departure_date' => $this->formatDate($flight->departure_date) ?? '—',
                        'flight' => $flight->flight_number ?: '—',
                        'arrival_date' => $this->formatDate($flight->arrival_date) ?? '—',
                        'arrival_time' => $flight->arrival_time ?: '—',
                        // First leg stores AWB; later legs store next departure port in leg_reference.
                        'reference' => $isFirstLeg ? ($flight->leg_reference ?: '—') : '—',
                    ];
                })
                ->all(),
            'Sea freight' => $shipment->seaLegs
                ->sortBy('sort_order')
                ->values()
                ->map(function (ShipmentSeaLeg $leg, int $index) use ($departurePort, $additionalService, $serviceWithAdditional) {
                    $isFirstLeg = $index === 0;
                    $legDeparturePort = $isFirstLeg
                        ? $departurePort
                        : $this->simplePortLabel($leg->bill_of_lading);

                    return [
                        'service' => $serviceWithAdditional,
                        'additional_service' => $additionalService,
                        'departure_port' => $legDeparturePort,
                        'departure_date' => $this->formatDate($leg->etd) ?? '—',
                        'flight' => $leg->transport_vessel_name ?: '—',
                        'arrival_date' => $this->formatDate($leg->eta) ?? '—',
                        'arrival_time' => $leg->arrival_time ?: '—',
                        // First leg stores B/L; later legs store next departure port in bill_of_lading.
                        'reference' => $isFirstLeg ? ($leg->bill_of_lading ?: '—') : '—',
                    ];
                })
                ->all(),
            'Truck' => $shipment->truckLegs
                ->sortBy('sort_order')
                ->values()
                ->map(fn (ShipmentTruckLeg $leg) => [
                    'service' => $serviceWithAdditional,
                    'additional_service' => $additionalService,
                    'departure_port' => $departurePort,
                    'departure_date' => $this->formatDate($leg->departure_date) ?? '—',
                    'flight' => $leg->freight_company ?: '—',
                    'arrival_date' => $this->formatDate($leg->arrival_date) ?? '—',
                    'arrival_time' => $leg->arrival_time ?: '—',
                    'reference' => $leg->cmr ?: '—',
                ])
                ->all(),
            'Courier' => $shipment->courierLegs
                ->sortBy('sort_order')
                ->values()
                ->map(fn (ShipmentCourierLeg $leg) => [
                    'service' => $serviceWithAdditional,
                    'additional_service' => $additionalService,
                    'departure_port' => $departurePort,
                    'departure_date' => $this->formatDate($leg->departure_date) ?? '—',
                    'flight' => $leg->carrier ?: '—',
                    'arrival_date' => $this->formatDate($leg->arrival_date) ?? '—',
                    'arrival_time' => $leg->arrival_time ?: '—',
                    'reference' => $leg->airway_bill ?: '—',
                ])
                ->all(),
            'Release' => $shipment->releaseLegs
                ->sortBy('sort_order')
                ->values()
                ->map(fn (ShipmentReleaseLeg $leg) => [
                    'service' => $serviceWithAdditional,
                    'additional_service' => $additionalService,
                    'departure_port' => $departurePort,
                    'departure_date' => '—',
                    'flight' => $leg->freight_company ?: '—',
                    'arrival_date' => $this->formatDate($leg->delivery_date) ?? '—',
                    'arrival_time' => $leg->delivery_time ?: '—',
                    'reference' => '—',
                ])
                ->all(),
            'Hand Carry' => $shipment->handCarryLegs
                ->sortBy('sort_order')
                ->values()
                ->map(fn (ShipmentHandCarryLeg $leg) => [
                    'service' => $serviceWithAdditional,
                    'additional_service' => $additionalService,
                    'departure_port' => $departurePort,
                    'departure_date' => $this->formatDate($leg->departure_date) ?? '—',
                    'flight' => $leg->contact_name ?: '—',
                    'arrival_date' => $this->formatDate($leg->arrival_date) ?? '—',
                    'arrival_time' => $leg->arrival_time ?: '—',
                    'reference' => '—',
                ])
                ->all(),
            'On-board delivery' => $shipment->onBoardLegs
                ->sortBy('sort_order')
                ->values()
                ->map(fn (ShipmentOnBoardLeg $leg) => [
                    'service' => $serviceWithAdditional,
                    'additional_service' => $additionalService,
                    'departure_port' => $departurePort,
                    'departure_date' => $this->formatDate($leg->departure_date) ?? '—',
                    'flight' => '—',
                    'arrival_date' => $this->formatDate($leg->delivery_date) ?? '—',
                    'arrival_time' => $leg->delivery_time ?: '—',
                    'reference' => '—',
                ])
                ->all(),
            default => [],
        };
    }

    private function referenceColumnLabel(?string $service): string
    {
        return match ($service) {
            'Sea freight' => 'B/L',
            'Truck' => 'CMR',
            'Courier' => 'AWB',
            default => 'AWB',
        };
    }

    private function serviceHasReferenceColumn(?string $service): bool
    {
        return in_array($service, ['Airfreight', 'Sea freight', 'Truck', 'Courier'], true);
    }

    /**
     * Subject token like "AWB:AS244444" / "B/L:MBL123" from the first service leg.
     */
    public function composeSubjectServiceReference(Shipment $shipment): ?string
    {
        if (! $this->serviceHasReferenceColumn($shipment->service)) {
            return null;
        }

        $shipment->loadMissing([
            'flights',
            'seaLegs',
            'truckLegs',
            'courierLegs',
        ]);

        $value = match ($shipment->service) {
            'Airfreight' => $shipment->flights->sortBy('sort_order')->first()?->leg_reference,
            'Sea freight' => $shipment->seaLegs->sortBy('sort_order')->first()?->bill_of_lading,
            'Truck' => $shipment->truckLegs->sortBy('sort_order')->first()?->cmr,
            'Courier' => $shipment->courierLegs->sortBy('sort_order')->first()?->airway_bill,
            default => null,
        };

        $value = trim((string) $value);
        if ($value === '' || $value === '—') {
            return null;
        }

        return $this->referenceColumnLabel($shipment->service) . ':' . $value;
    }

    /**
     * @return array<int, string>
     */
    public function reminderMailServiceDetailLines(Shipment $shipment): array
    {
        $shipment->loadMissing([
            'crrs',
            'flights',
            'seaLegs',
            'truckLegs',
            'courierLegs',
            'releaseLegs',
            'handCarryLegs',
            'onBoardLegs',
        ]);

        $vessel = $shipment->crrs->pluck('vessel_name')->filter()->first() ?? '—';
        $serviceLabel = filled($shipment->additional_service)
            ? trim(($shipment->service ?? '—') . ', ' . $shipment->additional_service)
            : ($shipment->service ?? '—');

        $lines = [
            'Vessel: ' . $this->formatMotorVesselName($vessel),
            'Service: ' . $serviceLabel,
        ];

        $detailLines = match ($shipment->service) {
            'Airfreight' => $this->airfreightReminderLines($shipment),
            'Sea freight' => $this->seaFreightReminderLines($shipment),
            'Truck' => $this->truckReminderLines($shipment),
            'Courier' => $this->courierReminderLines($shipment),
            'Release' => $this->releaseReminderLines($shipment),
            'Hand Carry' => $this->handCarryReminderLines($shipment),
            'On-board delivery' => $this->onBoardReminderLines($shipment),
            default => [],
        };

        return array_merge($lines, $detailLines);
    }

    /**
     * Compose/pre-alert body: Service + every leg for this shipment (Vessel already in Shipment Details).
     *
     * @return array<int, string>
     */
    public function composeMailServiceDetailLines(Shipment $shipment): array
    {
        return array_values(array_slice($this->reminderMailServiceDetailLines($shipment), 1));
    }

    /**
     * @return array<int, string>
     */
    private function airfreightReminderLines(Shipment $shipment): array
    {
        $flights = $shipment->flights->sortBy('sort_order')->values();
        $departurePort = $this->simplePortLabel($shipment->departure_port_code, null, null);

        if ($flights->isEmpty()) {
            return [
                'Departure port: ' . $this->displayValue($departurePort),
                'Airway bill: ',
                'Flight number: ',
                'Departure date: ',
                'Arrival date: ',
            ];
        }

        if ($flights->count() === 1) {
            $flight = $flights->first();

            return [
                'Departure port: ' . $this->displayValue($departurePort),
                'Airway bill: ' . $this->displayValue($flight->leg_reference),
                'Flight number: ' . $this->displayValue($flight->flight_number),
                'Departure date: ' . $this->displayValue($this->formatDate($flight->departure_date)),
                'Arrival date: ' . $this->displayArrivalDate($flight->arrival_date, $flight->arrival_time),
            ];
        }

        $lines = [];
        foreach ($flights as $index => $flight) {
            $isFirst = $index === 0;
            $lines[] = 'Flight ' . ($index + 1) . ':';
            $lines[] = 'Departure port: ' . $this->displayValue(
                $isFirst ? $departurePort : $this->simplePortLabel($flight->leg_reference)
            );
            if ($isFirst) {
                $lines[] = 'Airway bill: ' . $this->displayValue($flight->leg_reference);
            }
            $lines[] = 'Flight number: ' . $this->displayValue($flight->flight_number);
            $lines[] = 'Departure date: ' . $this->displayValue($this->formatDate($flight->departure_date));
            $lines[] = 'Arrival date: ' . $this->displayArrivalDate($flight->arrival_date, $flight->arrival_time);
            if ($index < $flights->count() - 1) {
                $lines[] = '';
            }
        }

        return $lines;
    }

    /**
     * @return array<int, string>
     */
    private function seaFreightReminderLines(Shipment $shipment): array
    {
        $legs = $shipment->seaLegs->sortBy('sort_order')->values();
        $departurePort = $this->simplePortLabel($shipment->departure_port_code, null, null);

        if ($legs->isEmpty()) {
            return [
                'Departure port: ' . $this->displayValue($departurePort),
                'B/L: ',
                'Container number: ',
                'Vessel Name: ',
                'Departure date: ',
                'Arrival date: ',
            ];
        }

        if ($legs->count() === 1) {
            $leg = $legs->first();

            return [
                'Departure port: ' . $this->displayValue($departurePort),
                'B/L: ' . $this->displayValue($leg->bill_of_lading),
                'Container number: ' . $this->displayValue($leg->container_number),
                'Vessel Name: ' . $this->displayValue($leg->transport_vessel_name),
                'Departure date: ' . $this->displayValue($this->formatDate($leg->etd)),
                'Arrival date: ' . $this->displayArrivalDate($leg->eta, $leg->arrival_time),
            ];
        }

        $lines = [];
        foreach ($legs as $index => $leg) {
            $isFirst = $index === 0;
            $lines[] = 'Sea leg ' . ($index + 1) . ':';
            $lines[] = 'Departure port: ' . $this->displayValue(
                $isFirst ? $departurePort : $this->simplePortLabel($leg->bill_of_lading)
            );
            if ($isFirst) {
                $lines[] = 'B/L: ' . $this->displayValue($leg->bill_of_lading);
            }
            $lines[] = 'Container number: ' . $this->displayValue($leg->container_number);
            $lines[] = 'Vessel Name: ' . $this->displayValue($leg->transport_vessel_name);
            $lines[] = 'Departure date: ' . $this->displayValue($this->formatDate($leg->etd));
            $lines[] = 'Arrival date: ' . $this->displayArrivalDate($leg->eta, $leg->arrival_time);
            if ($index < $legs->count() - 1) {
                $lines[] = '';
            }
        }

        return $lines;
    }

    /**
     * @return array<int, string>
     */
    private function truckReminderLines(Shipment $shipment): array
    {
        $legs = $shipment->truckLegs->sortBy('sort_order')->values();
        $departurePort = $this->simplePortLabel($shipment->departure_port_code, null, null);

        if ($legs->isEmpty()) {
            return [
                'Departure port: ' . $this->displayValue($departurePort),
                'CMR: ',
                'Freight company: ',
                'Departure date: ',
                'Arrival date: ',
            ];
        }

        if ($legs->count() === 1) {
            $leg = $legs->first();

            return [
                'Departure port: ' . $this->displayValue($departurePort),
                'CMR: ' . $this->displayValue($leg->cmr),
                'Freight company: ' . $this->displayValue($leg->freight_company),
                'Departure date: ' . $this->displayValue($this->formatDate($leg->departure_date)),
                'Arrival date: ' . $this->displayArrivalDate($leg->arrival_date, $leg->arrival_time),
            ];
        }

        $lines = [];
        foreach ($legs as $index => $leg) {
            $lines[] = 'Truck ' . ($index + 1) . ':';
            $lines[] = 'Departure port: ' . $this->displayValue($departurePort);
            $lines[] = 'CMR: ' . $this->displayValue($leg->cmr);
            $lines[] = 'Freight company: ' . $this->displayValue($leg->freight_company);
            $lines[] = 'Departure date: ' . $this->displayValue($this->formatDate($leg->departure_date));
            $lines[] = 'Arrival date: ' . $this->displayArrivalDate($leg->arrival_date, $leg->arrival_time);
            if ($index < $legs->count() - 1) {
                $lines[] = '';
            }
        }

        return $lines;
    }

    /**
     * @return array<int, string>
     */
    private function courierReminderLines(Shipment $shipment): array
    {
        $legs = $shipment->courierLegs->sortBy('sort_order')->values();
        $departurePort = $this->simplePortLabel($shipment->departure_port_code, null, null);

        if ($legs->isEmpty()) {
            return [
                'Departure port: ' . $this->displayValue($departurePort),
                'Airway bill: ',
                'Carrier: ',
                'Departure date: ',
                'Arrival date: ',
            ];
        }

        if ($legs->count() === 1) {
            $leg = $legs->first();

            return [
                'Departure port: ' . $this->displayValue($departurePort),
                'Airway bill: ' . $this->displayValue($leg->airway_bill),
                'Carrier: ' . $this->displayValue($leg->carrier),
                'Departure date: ' . $this->displayValue($this->formatDate($leg->departure_date)),
                'Arrival date: ' . $this->displayArrivalDate($leg->arrival_date, $leg->arrival_time),
            ];
        }

        $lines = [];
        foreach ($legs as $index => $leg) {
            $lines[] = 'Courier ' . ($index + 1) . ':';
            $lines[] = 'Departure port: ' . $this->displayValue($departurePort);
            $lines[] = 'Airway bill: ' . $this->displayValue($leg->airway_bill);
            $lines[] = 'Carrier: ' . $this->displayValue($leg->carrier);
            $lines[] = 'Departure date: ' . $this->displayValue($this->formatDate($leg->departure_date));
            $lines[] = 'Arrival date: ' . $this->displayArrivalDate($leg->arrival_date, $leg->arrival_time);
            if ($index < $legs->count() - 1) {
                $lines[] = '';
            }
        }

        return $lines;
    }

    /**
     * @return array<int, string>
     */
    private function releaseReminderLines(Shipment $shipment): array
    {
        $legs = $shipment->releaseLegs->sortBy('sort_order')->values();
        $departurePort = $this->simplePortLabel($shipment->departure_port_code, null, null);

        if ($legs->isEmpty()) {
            return [
                'Departure port: ' . $this->displayValue($departurePort),
                'Freight company: ',
                'Departure date: ',
                'Arrival date: ',
            ];
        }

        if ($legs->count() === 1) {
            $leg = $legs->first();

            return [
                'Departure port: ' . $this->displayValue($departurePort),
                'Freight company: ' . $this->displayValue($leg->freight_company),
                'Departure date: ',
                'Arrival date: ' . $this->displayArrivalDate($leg->delivery_date, $leg->delivery_time),
            ];
        }

        $lines = [];
        foreach ($legs as $index => $leg) {
            $lines[] = 'Release ' . ($index + 1) . ':';
            $lines[] = 'Departure port: ' . $this->displayValue($departurePort);
            $lines[] = 'Freight company: ' . $this->displayValue($leg->freight_company);
            $lines[] = 'Departure date: ';
            $lines[] = 'Arrival date: ' . $this->displayArrivalDate($leg->delivery_date, $leg->delivery_time);
            if ($index < $legs->count() - 1) {
                $lines[] = '';
            }
        }

        return $lines;
    }

    /**
     * @return array<int, string>
     */
    private function handCarryReminderLines(Shipment $shipment): array
    {
        $legs = $shipment->handCarryLegs->sortBy('sort_order')->values();
        $departurePort = $this->simplePortLabel($shipment->departure_port_code, null, null);

        if ($legs->isEmpty()) {
            return [
                'Departure port: ' . $this->displayValue($departurePort),
                'Contact: ',
                'Contact phone: ',
                'Departure date: ',
                'Arrival date: ',
            ];
        }

        if ($legs->count() === 1) {
            $leg = $legs->first();

            return [
                'Departure port: ' . $this->displayValue($departurePort),
                'Contact: ' . $this->displayValue($leg->contact_name),
                'Contact phone: ' . $this->displayValue($leg->contact_phone),
                'Departure date: ' . $this->displayValue($this->formatDate($leg->departure_date)),
                'Arrival date: ' . $this->displayArrivalDate($leg->arrival_date, $leg->arrival_time),
            ];
        }

        $lines = [];
        foreach ($legs as $index => $leg) {
            $lines[] = 'Hand carry ' . ($index + 1) . ':';
            $lines[] = 'Departure port: ' . $this->displayValue($departurePort);
            $lines[] = 'Contact: ' . $this->displayValue($leg->contact_name);
            $lines[] = 'Contact phone: ' . $this->displayValue($leg->contact_phone);
            $lines[] = 'Departure date: ' . $this->displayValue($this->formatDate($leg->departure_date));
            $lines[] = 'Arrival date: ' . $this->displayArrivalDate($leg->arrival_date, $leg->arrival_time);
            if ($index < $legs->count() - 1) {
                $lines[] = '';
            }
        }

        return $lines;
    }

    /**
     * @return array<int, string>
     */
    private function onBoardReminderLines(Shipment $shipment): array
    {
        $legs = $shipment->onBoardLegs->sortBy('sort_order')->values();

        if ($legs->isEmpty()) {
            return [
                'Departure date: ',
                'Delivery date: ',
                'Delivery time: ',
            ];
        }

        if ($legs->count() === 1) {
            /** @var ShipmentOnBoardLeg $leg */
            $leg = $legs->first();

            return [
                'Departure date: ' . $this->displayValue($this->formatDate($leg->departure_date)),
                'Delivery date: ' . $this->displayValue($this->formatDate($leg->delivery_date)),
                'Delivery time: ' . $this->displayValue($leg->delivery_time),
            ];
        }

        $lines = [];
        foreach ($legs as $index => $leg) {
            $lines[] = 'On-board delivery ' . ($index + 1) . ':';
            $lines[] = 'Departure date: ' . $this->displayValue($this->formatDate($leg->departure_date));
            $lines[] = 'Delivery date: ' . $this->displayValue($this->formatDate($leg->delivery_date));
            $lines[] = 'Delivery time: ' . $this->displayValue($leg->delivery_time);
            if ($index < $legs->count() - 1) {
                $lines[] = '';
            }
        }

        return $lines;
    }

    public function formatMotorVesselName(?string $vesselName): string
    {
        return $this->manifestPdfBuilder->formatMotorVesselName($vesselName);
    }

    /**
     * Compose/pre-alert vessel line without IMO, e.g. "M/V ANGEL in transit".
     */
    public function formatMailVesselLine(Shipment $shipment): string
    {
        $shipment->loadMissing('crrs.customerVessel');

        $primaryVessel = $this->formatMotorVesselName(
            $shipment->crrs->pluck('vessel_name')->filter()->first()
        );
        $vesselInfo = $shipment->crrs
            ->map(fn (Crr $crr) => $crr->customerVessel)
            ->filter()
            ->first();

        if ($primaryVessel === '' || $primaryVessel === '—') {
            return '—';
        }

        return $primaryVessel . ($vesselInfo?->not_in_transit ? '' : ' in transit');
    }

    private function displayValue(mixed $value): string
    {
        if ($value === null || $value === '' || $value === '—') {
            return '';
        }

        return (string) $value;
    }

    private function displayArrivalDate(mixed $date, ?string $time): string
    {
        $dateLabel = $date ? ($this->formatDate($date) ?? '') : '';

        if ($time) {
            return trim($dateLabel . ' ' . $time);
        }

        return $dateLabel;
    }

    private function simplePortLabel(
        ?string $portCode,
        ?string $city = null,
        ?string $fullLabel = null,
        ?string $country = null
    ): string {
        $code = trim((string) $portCode);
        $cityPart = trim((string) $city);
        $countryPart = trim((string) $country);

        if ($code !== '') {
            $port = $this->portRepository->findByCodeWithCountry($code);

            if ($port) {
                $code = $port->iata_code ?: $code;
                if ($cityPart === '') {
                    $cityPart = trim((string) ($port->city ?? ''));
                }
                if ($countryPart === '') {
                    $countryPart = trim((string) ($port->country_name ?: $port->country?->name ?: ''));
                }
            } else {
                $hub = $this->partyLookupRepository->findHubByPortCode($code);

                if ($hub) {
                    if ($cityPart === '') {
                        $cityPart = trim((string) ($hub->city ?? ''));
                    }
                    if ($countryPart === '') {
                        $countryPart = trim((string) ($hub->country ?? ''));
                    }
                } else {
                    $agent = $this->partyLookupRepository->findAgentByPortCodeWithCountry($code);

                    if ($agent) {
                        if ($cityPart === '') {
                            $cityPart = trim((string) ($agent->city ?? ''));
                        }
                        if ($countryPart === '') {
                            $countryPart = trim((string) ($agent->country?->name ?? ''));
                        }
                    }
                }
            }
        } elseif (filled($fullLabel)) {
            $parts = array_values(array_filter(array_map(
                static fn ($part) => trim((string) $part),
                preg_split('/\s*\/\s*/', (string) $fullLabel) ?: []
            )));

            if (count($parts) >= 3) {
                // Manifest format is often: city / code / country
                $cityPart = $cityPart !== '' ? $cityPart : $parts[0];
                $code = $parts[1];
                $countryPart = $countryPart !== '' ? $countryPart : $parts[2];
            } elseif (count($parts) === 2) {
                $code = $code !== '' ? $code : $parts[0];
                $cityPart = $cityPart !== '' ? $cityPart : $parts[1];
            } elseif (count($parts) === 1) {
                $code = $code !== '' ? $code : $parts[0];
            }
        }

        $label = implode(' | ', array_filter([$code ?: null, $cityPart ?: null, $countryPart ?: null]));

        return $label !== '' ? $label : '—';
    }

    private function formatOfficeAddress($office): string
    {
        if (!$office) {
            return '';
        }

        $lines = array_filter([
            $office->address,
            trim(implode(', ', array_filter([
                $office->zip_code,
                $office->city,
            ]))),
            $office->country?->name ?? null,
        ]);

        return implode("\n", $lines);
    }

    private function formatArrival(mixed $date, ?string $time): ?string
    {
        $dateLabel = $this->formatDate($date);
        if (!$dateLabel) {
            return null;
        }

        return trim($dateLabel . ($time ? ' ' . $time : ''));
    }

    private function formatDate(mixed $date): ?string
    {
        if ($date === null) {
            return null;
        }

        if ($date instanceof \DateTimeInterface) {
            return $date->format('d.m.Y');
        }

        return (string) $date;
    }

    /**
     * @param  array<string, mixed>  $base
     */
    private function formatShippedToBlock(Shipment $shipment, array $base): string
    {
        $lines = array_filter([
            $base['consigneeName'] ?? null,
            $shipment->consignee_address,
            trim(implode(', ', array_filter([
                $shipment->consignee_zip,
                $shipment->consignee_city,
            ]))),
            $shipment->consignee_country,
        ]);

        return implode("\n", $lines) ?: '—';
    }

    public static function shipmentHasServiceDetails(Shipment $shipment): bool
    {
        return match ($shipment->service) {
            'Airfreight' => $shipment->flights->isNotEmpty(),
            'Sea freight' => $shipment->seaLegs->isNotEmpty(),
            'Truck' => $shipment->truckLegs->isNotEmpty(),
            'Courier' => $shipment->courierLegs->isNotEmpty(),
            'Release' => $shipment->releaseLegs->isNotEmpty(),
            'Hand Carry' => $shipment->handCarryLegs->isNotEmpty(),
            'On-board delivery' => $shipment->onBoardLegs->isNotEmpty(),
            default => false,
        };
    }
}

