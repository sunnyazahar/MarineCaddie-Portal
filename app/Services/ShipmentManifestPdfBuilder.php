<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\Crr;
use App\Models\Customer;
use App\Models\Hub;
use App\Models\Office;
use App\Models\OtherCompany;
use App\Models\Port;
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

        $primaryVessel = $this->formatMotorVesselName($crrs->pluck('vessel_name')->filter()->first());
        $vesselInfo = $crrs
            ->map(fn (Crr $crr) => $crr->customerVessel)
            ->filter()
            ->first();

        $vesselLine = $primaryVessel;
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
                'vessel' => $this->formatMotorVesselName($crr->vessel_name),
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
            'titleLine' => 'Ref No. ' . $shipment->shipment_number,
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
            'consigneeContactLine' => $this->joinParts([
                $shipment->consignee_att,
                $shipment->consignee_email,
                $consigneeParty['phone'] ?? null,
            ]) ?: '—',
            'departurePort' => $this->formatPortCodeCityCountry($shipment->departure_port_code),
            'destinationPort' => $this->formatPortCodeCityCountry(
                $shipment->consignee_port_code,
                $shipment->consignee_city,
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
            'shipperLine' => $this->formatPartyBlock($departureParty),
            'consigneeLine' => $this->formatPartyDetails(
                $consigneeParty['name'] ?: ($shipment->consignee_att ?? ''),
                $this->formatShipmentAddress($shipment),
                $shipment->consignee_email ?: ($consigneeParty['email'] ?? ''),
                $consigneeParty['phone'] ?? ''
            ),
        ];
    }

    private function formatPartyBlock(array $party): string
    {
        return $this->formatPartyDetails(
            $party['name'] ?? '',
            $party['address_line'] ?? '',
            $party['email'] ?? '',
            $party['phone'] ?? ''
        );
    }

    private function formatPartyDetails(?string $name, ?string $address, ?string $email, ?string $phone): string
    {
        $name = trim((string) $name);
        $address = trim((string) $address);
        $email = trim((string) $email);
        $phone = trim((string) $phone);

        $lines = array_values(array_filter([
            ($name !== '' && $name !== '—') ? $name : null,
            ($address !== '' && $address !== '—') ? $address : null,
            ($email !== '' && $email !== '—') ? 'Email: ' . $email : null,
            ($phone !== '' && $phone !== '—') ? 'Phone: ' . $phone : null,
        ]));

        return $lines !== [] ? implode("\n", $lines) : '—';
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
                $hub = Hub::with('contacts')->find($id);
                if ($hub) {
                    $result['name'] = $hub->hub_name;
                    $result['address_line'] = $this->joinParts([
                        $hub->hub_address,
                        $hub->city,
                        $hub->zip_code,
                        $hub->country,
                    ]);
                    $result = $this->applyContactDetails($result, $hub, $hub->phone_number, $hub->email);
                    $result['invoice_email'] = $hub->emails_for_invoicing ?: $result['email'];
                    $result['port_code'] = $hub->port_code;
                }
                break;
            case 'agent':
                $agent = Agent::with(['country', 'contacts'])->find($id);
                if ($agent) {
                    $result['name'] = $agent->agent_name;
                    $result['address_line'] = $this->joinParts([
                        $agent->agent_address,
                        $agent->city,
                        $agent->zip_code,
                        $agent->country?->name,
                    ]);
                    $result = $this->applyContactDetails($result, $agent, $agent->phone, $agent->email);
                    $result['port_code'] = $agent->port_code;
                }
                break;
            case 'office':
                $office = Office::with(['country', 'contacts'])->find($id);
                if ($office) {
                    $result['name'] = $office->office_name;
                    $result['address_line'] = $this->joinParts([
                        $office->address,
                        $office->city,
                        $office->zip_code,
                        $office->country?->name,
                    ]);
                    $result = $this->applyContactDetails($result, $office, $office->phone_number, $office->email);
                    $result['port_code'] = $office->port_code ?? '';
                }
                break;
            case 'customer':
                $customer = Customer::with('contacts')->find($id);
                if ($customer) {
                    $result['name'] = $customer->customer_name;
                    $result = $this->applyContactDetails($result, $customer, $customer->phone, $customer->email);
                }
                break;
            case 'supplier':
                $supplier = Supplier::with('contacts')->find($id);
                if ($supplier) {
                    $result['name'] = $supplier->supplier_name;
                    $result['address_line'] = $this->joinParts([
                        $supplier->street_address,
                        $supplier->city,
                        $supplier->zip_code,
                    ]);
                    $result = $this->applyContactDetails($result, $supplier, $supplier->phone_number, $supplier->email);
                }
                break;
            case 'other_company':
                $company = OtherCompany::with(['country', 'contacts'])->find($id);
                if ($company) {
                    $result['name'] = $company->company_name;
                    $result['address_line'] = $this->joinParts([
                        $company->street_address,
                        $company->city,
                        $company->zip_code,
                        $company->country?->name,
                    ]);
                    $result = $this->applyContactDetails($result, $company, $company->phone_number, $company->email);
                    $result['port_code'] = $company->port_code;
                }
                break;
        }

        return $result;
    }

    private function applyContactDetails(array $result, object $model, ?string $phone, ?string $email): array
    {
        $result['phone'] = trim((string) $phone);
        $result['email'] = trim((string) $email);

        $needsPhone = $result['phone'] === '';
        $needsEmail = $result['email'] === '' || ! filter_var($result['email'], FILTER_VALIDATE_EMAIL);

        if (! ($needsPhone || $needsEmail) || ! method_exists($model, 'contacts')) {
            return $result;
        }

        $contacts = $model->relationLoaded('contacts')
            ? $model->contacts
            : $model->contacts()->get();

        $main = $contacts->firstWhere('is_main_contact', true)
            ?? $contacts->first();

        if (! $main) {
            return $result;
        }

        if ($needsPhone) {
            $result['phone'] = trim((string) ($main->phone_number ?? ''));
        }

        if ($needsEmail) {
            $contactEmail = trim((string) ($main->email ?? ''));
            if ($contactEmail !== '') {
                $result['email'] = $contactEmail;
            }
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

    public function formatPortCodeCityCountry(?string $portCode, ?string $fallbackCity = null, ?string $fallbackCountry = null): string
    {
        $parts = $this->resolvePortCodeParts($portCode);
        $city = trim((string) $fallbackCity);
        if ($city === '' || $this->looksLikePortCode($city)) {
            $city = $parts['city'];
        }
        if ($city === '' || $this->looksLikePortCode($city)) {
            $city = $this->portCityAlias($parts['code']) ?: $this->portCityAlias($portCode);
        }

        $country = $parts['country'] !== '' ? $parts['country'] : trim((string) $fallbackCountry);

        return $this->joinParts([
            $parts['code'] ?: null,
            ($city !== '' && ! $this->looksLikePortCode($city)) ? $city : null,
            $country ?: null,
        ], ' | ') ?: '—';
    }

    public function formatPortCity(?string $portCode, ?string $fallbackCity = null): string
    {
        $parts = $this->resolvePortCodeParts($portCode);
        $candidates = [
            trim((string) $fallbackCity),
            $parts['city'],
            $this->portCityAlias($parts['code']),
            $this->portCityAlias($portCode),
        ];

        foreach ($candidates as $candidate) {
            $city = trim((string) $candidate);
            if ($city !== '' && ! $this->looksLikePortCode($city)) {
                return $city;
            }
        }

        return '—';
    }

    private function looksLikePortCode(string $value): bool
    {
        return (bool) preg_match('/^[A-Z0-9]{3,8}$/', $value);
    }

    private function portCityAlias(?string $code): string
    {
        $key = strtoupper(preg_replace('/\d+$/', '', trim((string) $code)) ?: '');
        $aliases = [
            'JNPT' => 'Nhava Sheva',
            'INNSA' => 'Nhava Sheva',
        ];

        if (isset($aliases[strtoupper(trim((string) $code))])) {
            return $aliases[strtoupper(trim((string) $code))];
        }

        return $aliases[$key] ?? '';
    }

    /**
     * @return array{code: string, city: string, country: string}
     */
    private function resolvePortCodeParts(?string $portCode): array
    {
        $code = trim((string) $portCode);
        $city = '';
        $country = '';

        if ($code === '') {
            return ['code' => '', 'city' => '', 'country' => ''];
        }

        $port = Port::query()
            ->with('country')
            ->where(function ($query) use ($code) {
                $query->where('iata_code', $code)
                    ->orWhere('un_locode', $code)
                    ->orWhere('port_name', $code);
            })
            ->first();

        if (! $port) {
            $locodePrefix = preg_replace('/\d+$/', '', $code);
            if (is_string($locodePrefix) && $locodePrefix !== '' && $locodePrefix !== $code) {
                $port = Port::query()
                    ->with('country')
                    ->where('un_locode', 'like', $locodePrefix . '%')
                    ->first();
            }
        }

        if ($port) {
            $code = $port->displayCode() ?: $code;
            $city = trim((string) ($port->city ?: $port->port_name ?: ''));
            $country = trim((string) ($port->country_name ?: $port->country?->name ?: ''));
        } else {
            $hub = Hub::query()->where('port_code', $code)->first();

            if ($hub) {
                $city = trim((string) ($hub->city ?? ''));
                $country = trim((string) ($hub->country ?? ''));
            } else {
                $agent = Agent::query()
                    ->with('country')
                    ->where('port_code', $code)
                    ->first();

                if ($agent) {
                    $city = trim((string) ($agent->city ?? ''));
                    $country = trim((string) ($agent->country?->name ?? ''));
                }
            }
        }

        return ['code' => $code, 'city' => $city, 'country' => $country];
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

    public function formatMotorVesselName(?string $vesselName): string
    {
        $vessel = trim((string) $vesselName);
        if ($vessel === '' || $vessel === '—') {
            return '—';
        }

        if (preg_match('/^(M\/V|M\.V\.|MV)\b/i', $vessel)) {
            return (string) preg_replace('/^(M\/V|M\.V\.|MV)\b/i', 'M/V', $vessel, 1);
        }

        return 'M/V ' . $vessel;
    }

    private function formatOnBoardSignatory(?string $vesselName): string
    {
        return 'Master / Chief Engineer of ' . $this->formatMotorVesselName($vesselName);
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
