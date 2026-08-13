<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\Crr;
use App\Models\Customer;
use App\Models\Hub;
use App\Models\Office;
use App\Models\OtherCompany;
use App\Models\Shipment;
use App\Models\Supplier;
use Carbon\Carbon;

class ShipmentManifestPdfBuilder
{
    public function __construct(
        private ShipmentStockSnapshotService $stockSnapshotService,
    ) {}

    public function build(Shipment $shipment, ?int $manifestVersion = null): array
    {
        $shipment->loadMissing([
            'accountManager.office',
            'creator',
            'manifests',
        ]);

        $resolvedManifestVersion = $manifestVersion;
        if ($resolvedManifestVersion === null) {
            $latestVersion = (int) ($shipment->manifests->max('version') ?? 0);
            $resolvedManifestVersion = $latestVersion > 0 ? $latestVersion : null;
        }

        $crrs = $this->stockSnapshotService->applyResolvedStockCrrs($shipment);
        $partyNames = Shipment::batchResolvePartyNames(collect([$shipment]));
        $departureParty = $this->resolvePartyContact($shipment->departure, $partyNames);
        $consigneeParty = $this->resolvePartyContact($shipment->consignee, $partyNames);

        $packages = $crrs->flatMap(fn (Crr $crr) => $crr->packages);
        $totalPackages = $packages->count();
        $totalWeight = round((float) $packages->sum('weight'), 2);
        $totalCbm = round((float) $packages->sum('cbm'), 2);
        $totalCustomsValue = round((float) $crrs->sum('customs_value'), 2);
        $currency = $crrs->first()?->currency ?? 'USD';
        $volumeWeight = round($totalCbm * 167, 2);
        $totalCbft = round($totalCbm * 35.315, 2);

        $primaryVessel = $crrs->pluck('vessel_name')->filter()->first();
        $vesselInfo = $crrs
            ->map(fn (Crr $crr) => $crr->customerVessel)
            ->filter()
            ->first();

        $vesselLine = $primaryVessel ?? '—';
        if ($vesselInfo?->vessel_imo) {
            $vesselLine .= ' (IMO: ' . $vesselInfo->vessel_imo . ')';
        }
        $vesselLine .= $vesselInfo?->not_in_transit ? '' : ' in transit';

        $customerName = $crrs
            ->map(fn (Crr $crr) => $crr->customerVessel?->customer?->customer_name)
            ->filter()
            ->unique()
            ->implode(', ');

        $manifestRows = $crrs->map(function (Crr $crr) {
            $poNumbers = is_array($crr->po_numbers)
                ? implode(', ', $crr->po_numbers)
                : ($crr->po_numbers ?? '—');

            return [
                'vessel' => $crr->vessel_name ?? '—',
                'supplier' => $crr->supplier ?? '—',
                'po_number' => $poNumbers ?: '—',
                'items' => $crr->packages->count(),
                'weight' => round((float) $crr->packages->sum('weight'), 2),
                'cbm' => round((float) $crr->packages->sum('cbm'), 2),
                'customs_value' => number_format((float) ($crr->customs_value ?? 0), 2),
                'currency' => $crr->currency ?? 'USD',
                'description' => $crr->content ?? 'Shipspares',
                'stock_number' => $crr->stock_number ?? '—',
                'transit_id' => $crr->transit_id ?? '',
                'location' => $crr->location ?: '—',
            ];
        });

        $packingRows = [];
        $packageIndex = 0;
        foreach ($crrs as $crr) {
            $poNumbers = is_array($crr->po_numbers)
                ? implode(', ', $crr->po_numbers)
                : ($crr->po_numbers ?? '—');

            if ($crr->packages->isEmpty()) {
                $packageIndex++;
                $packingRows[] = [
                    'stock_number' => $crr->stock_number ?? '—',
                    'position' => $crr->location ?: ($crr->hub_code ?? '—'),
                    'supplier' => $crr->supplier ?? '—',
                    'po_number' => $poNumbers ?: '—',
                    'item_label' => $packageIndex . ' of ' . max($totalPackages, 1),
                    'weight_label' => '0 of ' . round($totalWeight, 0) . ' kg',
                    'dimensions' => '—',
                    'transit_id' => $crr->transit_id ?? '',
                    'pending_eta' => $crr->expected_delivery_date
                        ? Carbon::parse($crr->expected_delivery_date)->format('d.m.Y')
                        : null,
                    'label_code' => strtoupper($crr->hub_code ?? 'MTL') . '-' . ($crr->stock_number ?? $packageIndex) . '-1',
                ];
                continue;
            }

            foreach ($crr->packages as $package) {
                $packageIndex++;
                $packingRows[] = [
                    'stock_number' => $crr->stock_number ?? '—',
                    'position' => $package->warehouse_location ?: ($crr->location ?: ($crr->hub_code ?? '—')),
                    'supplier' => $crr->supplier ?? '—',
                    'po_number' => $poNumbers ?: '—',
                    'item_label' => $packageIndex . ' of ' . max($totalPackages, 1),
                    'weight_label' => round((float) $package->weight, 0) . ' of ' . round($totalWeight, 0) . ' kg',
                    'dimensions' => $this->formatDimensions($package->length, $package->width, $package->height),
                    'transit_id' => $crr->transit_id ?? '',
                    'pending_eta' => $crr->expected_delivery_date
                        ? Carbon::parse($crr->expected_delivery_date)->format('d.m.Y')
                        : null,
                    'label_code' => strtoupper($crr->hub_code ?? 'MTL') . '-' . ($crr->stock_number ?? $packageIndex) . '-' . $packageIndex,
                ];
            }
        }

        $createdAt = Carbon::now('Asia/Kolkata')->format('d.m.Y H:i') . ' IST';
        $handledBy = trim(($shipment->creator?->name ?? 'System') . ' on ' . Carbon::now()->format('d.m.Y H:i'));

        $companyName = $departureParty['name'] ?: 'Marinetrans';
        $companyAddress = $departureParty['address_line'] ?: '—';
        $companyPhone = $departureParty['phone'] ?: '—';
        $companyEmail = $departureParty['email'] ?: '—';

        $invoiceEmail = $departureParty['invoice_email'] ?? $companyEmail;

        return [
            'shipment' => $shipment,
            'titleLine' => 'Ref No. :' . $shipment->shipment_number,
            'manifestRevisionLabel' => $this->formatManifestRevisionLabel($resolvedManifestVersion),
            'companyName' => $companyName,
            'companyAddress' => $companyAddress,
            'companyPhone' => $companyPhone,
            'companyEmail' => $companyEmail,
            'createdAt' => $createdAt,
            'shippedThrough' => $this->formatContactLine($departureParty),
            'invoiceTo' => $this->formatContactLine(array_merge($departureParty, [
                'email' => $invoiceEmail,
            ])),
            'consigneeName' => $consigneeParty['name'] ?: ($shipment->consignee_att ?? '—'),
            'consigneeAddress' => $this->formatShipmentAddress($shipment),
            'consigneePhone' => $consigneeParty['phone'] ?: '—',
            'consigneeEmail' => $shipment->consignee_email ?? '',
            'consigneeContact' => $shipment->consignee_att ?? '—',
            'consigneeContactEmail' => $shipment->consignee_email ?? '—',
            'consigneeContactPhone' => $consigneeParty['phone'] ?: '—',
            'departurePort' => $this->formatPortLabel(
                $shipment->departure_port_code,
                null,
                $departureParty,
                null
            ),
            'destinationPort' => $this->formatPortLabel(
                $shipment->consignee_port_code,
                $shipment->consignee_city,
                $consigneeParty,
                $shipment->consignee_country
            ),
            'agentName' => $departureParty['name'] ?: '—',
            'consigneeAgentName' => $consigneeParty['name'] ?: ($shipment->consignee_att ?? '—'),
            'vesselLine' => $vesselLine,
            'shipmentLocation' => $shipment->location ?: '—',
            'special_considerations_destination' => $shipment->special_considerations_destination ?? '',
            'documentHandledBy' => $handledBy,
            'serviceLabel' => $shipment->service ?? '—',
            'additionalServiceLabel' => $shipment->additional_service ?: '—',
            'isOnBoardDelivery' => ($shipment->service ?? '') === 'On-board delivery',
            'onBoardSignatory' => $this->formatOnBoardSignatory($primaryVessel),
            'pcsSummary' => $totalPackages
                . ' / '
                . (filled($shipment->repacked_items) ? $shipment->repacked_items : $totalPackages)
                . ' / '
                . number_format(filled($shipment->repacked_weight) ? (float) $shipment->repacked_weight : $totalWeight, 2)
                . ' kg',
            'preferredShipmentDate' => $shipment->preferred_shipment_date?->format('d.m.y') ?? '—',
            'deadlineArrival' => $shipment->deadline_arrival?->format('d.m.y') ?? '—',
            'commentsHub' => $shipment->comments_departure_hub ?? '',
            'combinedPoReference' => '*' . $shipment->shipment_number . '*',
            'combinedPoUrl' => route('shipments.combined-po-documents', $shipment->id),
            'barcodeHtml' => \App\Support\Code39Barcode::html($shipment->shipment_number),
            'customerName' => $customerName ?: '—',
            'manifestRows' => $manifestRows,
            'packingRows' => $packingRows,
            'totals' => [
                'packages' => $totalPackages,
                'weight' => $totalWeight,
                'volume_weight' => $volumeWeight,
                'customs_value' => number_format($totalCustomsValue, 2),
                'currency' => $currency,
                'cbm' => $totalCbm,
                'cbft' => $totalCbft,
                'repacked_items' => filled($shipment->repacked_items) ? (int) $shipment->repacked_items : '—',
                'repacked_weight' => filled($shipment->repacked_weight)
                    ? number_format((float) $shipment->repacked_weight, 2)
                    : '—',
            ],
            'shipperLine' => $this->formatContactLine($departureParty),
            'consigneeLine' => $this->formatConsigneeLine(
                $consigneeParty['name'] ?: ($shipment->consignee_att ?? null),
                $this->formatShipmentAddress($shipment, true)
            ),
        ];
    }

    private function formatConsigneeLine(?string $name, string $addressLine): string
    {
        $name = trim((string) $name);
        $addressLine = trim($addressLine);

        if ($name !== '' && $name !== '—' && $addressLine !== '' && $addressLine !== '—') {
            return $name . ', ' . $addressLine;
        }

        if ($name !== '' && $name !== '—') {
            return $name;
        }

        return $addressLine !== '' ? $addressLine : '—';
    }

    private function resolvePartyContact(?string $composite, array $partyNames): array
    {
        $result = [
            'name' => '',
            'address_line' => '',
            'phone' => '',
            'email' => '',
            'invoice_email' => '',
            'port_code' => '',
        ];

        if (!$composite) {
            return $result;
        }

        if (!str_contains($composite, ':')) {
            $result['name'] = $partyNames[$composite] ?? $composite;

            return $result;
        }

        [$type, $id] = explode(':', $composite, 2);
        $id = (int) $id;
        $result['name'] = $partyNames[$composite] ?? $composite;

        switch ($type) {
            case 'hub':
                $hub = Hub::find($id);
                if ($hub) {
                    $result['name'] = $hub->hub_name;
                    $result['address_line'] = $this->joinParts([
                        $hub->hub_address,
                        $hub->city,
                        $hub->zip_code,
                        $hub->country,
                    ]);
                    $result['phone'] = $hub->phone_number;
                    $result['email'] = $hub->email;
                    $result['invoice_email'] = $hub->emails_for_invoicing ?: $hub->email;
                    $result['port_code'] = $hub->port_code;
                }
                break;
            case 'agent':
                $agent = Agent::with('country')->find($id);
                if ($agent) {
                    $result['name'] = $agent->agent_name;
                    $result['address_line'] = $this->joinParts([
                        $agent->agent_address,
                        $agent->city,
                        $agent->zip_code,
                        $agent->country?->name,
                    ]);
                    $result['phone'] = $agent->phone;
                    $result['email'] = $agent->email;
                    $result['port_code'] = $agent->port_code;
                }
                break;
            case 'office':
                $office = Office::with('country')->find($id);
                if ($office) {
                    $result['name'] = $office->office_name;
                    $result['address_line'] = $this->joinParts([
                        $office->address,
                        $office->city,
                        $office->zip_code,
                        $office->country?->name,
                    ]);
                    $result['phone'] = $office->phone_number;
                    $result['email'] = $office->email;
                    $result['port_code'] = $office->port_code ?? '';
                }
                break;
            case 'customer':
                $customer = Customer::find($id);
                if ($customer) {
                    $result['name'] = $customer->customer_name;
                    $result['phone'] = $customer->phone;
                    $result['email'] = $customer->email;
                }
                break;
            case 'supplier':
                $supplier = Supplier::find($id);
                if ($supplier) {
                    $result['name'] = $supplier->supplier_name;
                    $result['address_line'] = $this->joinParts([
                        $supplier->street_address,
                        $supplier->city,
                        $supplier->zip_code,
                    ]);
                    $result['phone'] = $supplier->phone_number;
                    $result['email'] = $supplier->email;
                }
                break;
            case 'other_company':
                $company = OtherCompany::with('country')->find($id);
                if ($company) {
                    $result['name'] = $company->company_name;
                    $result['address_line'] = $this->joinParts([
                        $company->street_address,
                        $company->city,
                        $company->zip_code,
                        $company->country?->name,
                    ]);
                    $result['phone'] = $company->phone_number;
                    $result['email'] = $company->email;
                    $result['port_code'] = $company->port_code;
                }
                break;
        }

        return $result;
    }

    private function formatShipmentAddress(Shipment $shipment, bool $includePhone = false): string
    {
        $parts = array_filter([
            $shipment->consignee_address,
            $shipment->consignee_city,
            $shipment->consignee_district,
            $shipment->consignee_zip,
            $shipment->consignee_country,
        ]);

        $line = $this->joinParts($parts);

        return $line ?: '—';
    }

    private function formatContactLine(array $party): string
    {
        $segments = array_filter([
            $party['name'] ?? null,
            $party['address_line'] ?? null,
            trim(($party['phone'] ?? '') . ($party['email'] ? ', ' . $party['email'] : '')),
        ]);

        return $this->joinParts($segments) ?: '—';
    }

    private function formatPortLabel(?string $portCode, ?string $city, ?array $party = null, ?string $country = null): string
    {
        $parts = array_filter([
            $city ?: ($party['name'] ?? null),
            $portCode,
            $country,
        ]);

        return $this->joinParts($parts, ' / ') ?: '—';
    }

    private function formatDimensions($length, $width, $height): string
    {
        if (!$length && !$width && !$height) {
            return '—';
        }

        return implode(' / ', [
            $this->formatNumber($length),
            $this->formatNumber($width),
            $this->formatNumber($height),
        ]);
    }

    private function formatNumber($value): string
    {
        return $value !== null && $value !== '' ? (string) round((float) $value, 0) : '—';
    }

    private function formatOnBoardSignatory(?string $vesselName): string
    {
        $vessel = trim((string) $vesselName);

        if ($vessel === '') {
            $vessel = '—';
        } elseif (! preg_match('/^MV\b/i', $vessel)) {
            $vessel = 'MV ' . $vessel;
        }

        return 'Master / Chief Engineer of ' . $vessel;
    }

    private function joinParts(array $parts, string $separator = ', '): string
    {
        return collect($parts)
            ->map(fn ($part) => trim((string) $part))
            ->filter()
            ->implode($separator);
    }

    private function formatManifestRevisionLabel(?int $manifestVersion): ?string
    {
        if ($manifestVersion === null || $manifestVersion <= 1) {
            return null;
        }

        return 'Revision ' . ($manifestVersion - 1);
    }
}
