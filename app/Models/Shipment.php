<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Shipment extends Model
{
    protected $fillable = [
        'shipment_number',
        'departure',
        'departure_port_code',
        'service',
        'additional_service',
        'preferred_shipment_date',
        'deadline_arrival',
        'vessel_eta',
        'vessel_etd',
        'pre_alert_reminder',
        'customer_reference',
        'not_applicable_for_consolidation',
        'consignee',
        'consignee_address',
        'consignee_city',
        'consignee_district',
        'consignee_zip',
        'consignee_country',
        'consignee_port_code',
        'location',
        'consignee_att',
        'consignee_email',
        'account_manager_id',
        'special_considerations_destination',
        'skip_instruction_dest',
        'comments_departure_hub',
        'skip_instruction_hub',
        'comments_consignee',
        'skip_prealert',
        'project_logistics',
        'port_agency',
        'status',
        'arrived_at',
        'repacked_items',
        'repacked_weight',
        'stock_repacked_items',
        'stock_repacked_weight',
        'flags',
        'created_by',
    ];

    protected $casts = [
        'preferred_shipment_date' => 'date',
        'deadline_arrival' => 'date',
        'vessel_eta' => 'date',
        'vessel_etd' => 'date',
        'pre_alert_reminder' => 'date',
        'arrived_at' => 'datetime',
        'flags' => 'array',
        'not_applicable_for_consolidation' => 'boolean',
        'skip_instruction_dest' => 'boolean',
        'skip_instruction_hub' => 'boolean',
        'skip_prealert' => 'boolean',
        'project_logistics' => 'boolean',
        'port_agency' => 'boolean',
        'repacked_items' => 'integer',
        'repacked_weight' => 'decimal:2',
        'stock_repacked_items' => 'integer',
        'stock_repacked_weight' => 'decimal:2',
    ];

    public function crrs(): BelongsToMany
    {
        return $this->belongsToMany(Crr::class, 'shipment_crr');
    }

    public function proformaInvoice(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ProformaInvoice::class);
    }

    public function irregularities(): HasMany
    {
        return $this->hasMany(ShipmentIrregularity::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ShipmentDocument::class);
    }

    public function manifests(): HasMany
    {
        return $this->hasMany(ShipmentManifest::class)->orderBy('version');
    }

    public function latestManifest(): ?ShipmentManifest
    {
        if (! $this->exists) {
            return null;
        }

        if ($this->relationLoaded('manifests') && $this->manifests->isNotEmpty()) {
            return $this->manifests
                ->sort(fn ($a, $b) => [(int) $b->version, (int) $b->id] <=> [(int) $a->version, (int) $a->id])
                ->first();
        }

        return $this->manifests()
            ->reorder()
            ->orderByDesc('version')
            ->orderByDesc('id')
            ->first();
    }

    public function needsManifestMailSend(): bool
    {
        $latest = $this->latestManifest();

        return $latest !== null && $latest->isMailPending();
    }

    public function preAlerts(): HasMany
    {
        return $this->hasMany(ShipmentPreAlert::class)->orderBy('version');
    }

    public function latestPreAlert(): ?ShipmentPreAlert
    {
        if (! $this->exists) {
            return null;
        }

        // reorder() clears the relationship's ASC version order so DESC actually wins.
        return $this->preAlerts()
            ->reorder()
            ->orderByDesc('version')
            ->orderByDesc('id')
            ->first();
    }

    public function needsPreAlertMailSend(): bool
    {
        $latest = $this->latestPreAlert();

        return $latest !== null && $latest->isMailPending();
    }

    public function preAlertReminderSends(): HasMany
    {
        return $this->hasMany(ShipmentPreAlertReminderSend::class);
    }

    public function stockSnapshots(): HasMany
    {
        return $this->hasMany(ShipmentStockSnapshot::class)->orderBy('sort_order');
    }

    public function flights(): HasMany
    {
        return $this->hasMany(ShipmentFlight::class)->orderBy('sort_order');
    }

    public function seaLegs(): HasMany
    {
        return $this->hasMany(ShipmentSeaLeg::class)->orderBy('sort_order');
    }

    public function truckLegs(): HasMany
    {
        return $this->hasMany(ShipmentTruckLeg::class)->orderBy('sort_order');
    }

    public function courierLegs(): HasMany
    {
        return $this->hasMany(ShipmentCourierLeg::class)->orderBy('sort_order');
    }

    public function releaseLegs(): HasMany
    {
        return $this->hasMany(ShipmentReleaseLeg::class)->orderBy('sort_order');
    }

    public function handCarryLegs(): HasMany
    {
        return $this->hasMany(ShipmentHandCarryLeg::class)->orderBy('sort_order');
    }

    public function onBoardLegs(): HasMany
    {
        return $this->hasMany(ShipmentOnBoardLeg::class)->orderBy('sort_order');
    }

    public function changeLogs(): HasMany
    {
        return $this->hasMany(ShipmentChangeLog::class)->orderByDesc('created_at');
    }

    public function accountManager(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'account_manager_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getCustomerNamesAttribute(): Collection
    {
        return $this->crrs
            ->map(fn (Crr $crr) => $crr->customerVessel?->customer?->customer_name)
            ->filter()
            ->unique()
            ->values();
    }

    public function customerNamesFromVessels(array $vesselCustomerMap = []): Collection
    {
        return $this->crrs
            ->map(function (Crr $crr) use ($vesselCustomerMap) {
                return $crr->customerVessel?->customer?->customer_name
                    ?? ($crr->vessel_name ? ($vesselCustomerMap[$crr->vessel_name] ?? null) : null);
            })
            ->filter()
            ->unique()
            ->values();
    }

    public function getCustomerDisplayAttribute(): string
    {
        $names = $this->customer_names;

        return $names->isNotEmpty() ? $names->implode(', ') : '—';
    }

    public function getCustomerDisplayShortAttribute(): string
    {
        return $this->formatNamesDisplayShort($this->customer_names);
    }

    public function formatNamesDisplay(Collection $names): string
    {
        return $names->isNotEmpty() ? $names->implode(', ') : '—';
    }

    public function formatNamesDisplayShort(Collection $names): string
    {
        if ($names->isEmpty()) {
            return '—';
        }

        if ($names->count() <= 2) {
            return $names->implode(', ');
        }

        return $names->take(2)->implode(', ') . ', ...';
    }

    public function getVesselNamesAttribute(): Collection
    {
        return $this->crrs
            ->pluck('vessel_name')
            ->filter()
            ->unique()
            ->values();
    }

    public function getVesselDisplayAttribute(): string
    {
        $names = $this->vessel_names;

        return $names->isNotEmpty() ? $names->implode(', ') : '—';
    }

    public function getVesselDisplayShortAttribute(): string
    {
        return $this->formatNamesDisplayShort($this->vessel_names);
    }

    public function getPoNumbersDisplayAttribute(): string
    {
        $numbers = $this->crrs
            ->flatMap(function (Crr $crr) {
                if (!is_array($crr->po_numbers)) {
                    return $crr->po_numbers ? [$crr->po_numbers] : [];
                }

                return $crr->po_numbers;
            })
            ->filter()
            ->unique()
            ->values();

        return $numbers->implode(', ');
    }

    public function getServiceReferenceValuesAttribute(): Collection
    {
        return collect()
            ->merge($this->flights->pluck('leg_reference'))
            ->merge($this->courierLegs->pluck('airway_bill'))
            ->merge($this->seaLegs->pluck('bill_of_lading'))
            ->merge($this->truckLegs->pluck('cmr'))
            ->merge($this->truckLegs->pluck('freight_company'))
            ->merge($this->releaseLegs->pluck('freight_company'))
            ->filter(fn ($value) => filled($value))
            ->unique()
            ->values();
    }

    public function getServiceReferenceDisplayAttribute(): string
    {
        $values = $this->service_reference_values;

        return $values->isNotEmpty() ? $values->implode(', ') : '—';
    }

    public function getServiceReferenceDisplayShortAttribute(): string
    {
        return $this->formatNamesDisplayShort($this->service_reference_values);
    }

    public function getDestinationDisplayAttribute(): string
    {
        if ($this->consignee_port_code) {
            return $this->consignee_port_code;
        }

        $parts = array_filter([$this->consignee_city, $this->consignee_country]);

        return $parts ? implode(', ', $parts) : '—';
    }

    /**
     * List display: city for a port code (from batchResolvePortCities map).
     */
    public function portCityLabel(?string $portCode, array $portCities, ?string $fallbackCity = null): string
    {
        $code = trim((string) ($portCode ?? ''));
        if ($code !== '') {
            $city = $portCities[$code]
                ?? $portCities[strtoupper($code)]
                ?? null;
            if (is_string($city) && trim($city) !== '') {
                return self::normalizePortCityLabel($city);
            }
        }

        $fallback = self::normalizePortCityLabel((string) ($fallbackCity ?? ''));

        return $fallback !== '' ? $fallback : '—';
    }

    /**
     * Clean messy port/city source strings for list display.
     * e.g. "Bombay (Mumbai)" → "Mumbai", "Incheon, Incheon International Airport" → "Incheon"
     */
    public static function normalizePortCityLabel(?string $city): string
    {
        $city = trim((string) ($city ?? ''));
        if ($city === '') {
            return '';
        }

        if (preg_match('/\(([^)]+)\)/', $city, $matches)) {
            $inside = trim((string) $matches[1]);
            if ($inside !== '') {
                return $inside;
            }
        }

        if (str_contains($city, ',')) {
            $city = trim(explode(',', $city, 2)[0]);
        }

        if (str_contains($city, ' - ')) {
            $city = trim(explode(' - ', $city, 2)[0]);
        }

        return $city;
    }

    public function departureCityDisplay(array $portCities): string
    {
        return $this->portCityLabel($this->departure_port_code, $portCities);
    }

    public function destinationCityDisplay(array $portCities): string
    {
        return $this->portCityLabel(
            $this->consignee_port_code,
            $portCities,
            $this->consignee_city
        );
    }

    /**
     * @return array<string, string> port code => city name
     */
    public static function batchResolvePortCities(Collection $shipments): array
    {
        $codes = $shipments
            ->flatMap(fn (self $shipment) => [
                $shipment->departure_port_code,
                $shipment->consignee_port_code,
            ])
            ->map(fn ($code) => trim((string) $code))
            ->filter()
            ->unique()
            ->values();

        if ($codes->isEmpty()) {
            return [];
        }

        $aliases = [
            'JNPT' => 'Nhava Sheva',
            'INNSA' => 'Nhava Sheva',
        ];

        $map = [];
        foreach ($codes as $code) {
            $upper = strtoupper($code);
            $prefix = strtoupper((string) (preg_replace('/\d+$/', '', $upper) ?: ''));
            if (isset($aliases[$upper])) {
                $map[$code] = self::normalizePortCityLabel($aliases[$upper]);
            } elseif ($prefix !== '' && isset($aliases[$prefix])) {
                $map[$code] = self::normalizePortCityLabel($aliases[$prefix]);
            }
        }

        $unresolved = $codes->reject(fn ($code) => isset($map[$code]))->values();
        if ($unresolved->isEmpty()) {
            return $map;
        }

        $lookupKeys = $unresolved
            ->flatMap(function (string $code) {
                $upper = strtoupper($code);
                $prefix = (string) (preg_replace('/\d+$/', '', $upper) ?: '');

                return array_values(array_unique(array_filter([$code, $upper, $prefix])));
            })
            ->unique()
            ->values()
            ->all();

        $ports = Port::query()
            ->where(function ($query) use ($lookupKeys) {
                $query->whereIn('iata_code', $lookupKeys)
                    ->orWhereIn('un_locode', $lookupKeys);
            })
            ->get();

        $portByCode = [];
        foreach ($ports as $port) {
            $city = self::normalizePortCityLabel((string) ($port->city ?: $port->port_name ?: ''));
            if ($city === '') {
                continue;
            }
            foreach ([$port->iata_code, $port->un_locode] as $key) {
                $key = trim((string) $key);
                if ($key !== '') {
                    $portByCode[strtoupper($key)] = $city;
                }
            }
        }

        foreach ($unresolved as $code) {
            $upper = strtoupper($code);
            $prefix = strtoupper((string) (preg_replace('/\d+$/', '', $upper) ?: ''));
            if (isset($portByCode[$upper])) {
                $map[$code] = $portByCode[$upper];
            } elseif ($prefix !== '' && isset($portByCode[$prefix])) {
                $map[$code] = $portByCode[$prefix];
            }
        }

        $stillUnresolved = $unresolved->reject(fn ($code) => isset($map[$code]))->values();
        if ($stillUnresolved->isEmpty() || ! \Illuminate\Support\Facades\Schema::hasColumn('hubs', 'port_code')) {
            return $map;
        }

        $hubs = Hub::query()
            ->whereIn('port_code', $stillUnresolved->all())
            ->get(['port_code', 'city', 'office_city']);

        foreach ($hubs as $hub) {
            $city = self::normalizePortCityLabel((string) ($hub->city ?: $hub->office_city ?: ''));
            if ($city === '') {
                continue;
            }
            $hubCode = trim((string) $hub->port_code);
            foreach ($stillUnresolved as $code) {
                if (strcasecmp($code, $hubCode) === 0) {
                    $map[$code] = $city;
                }
            }
        }

        return $map;
    }

    public function getServiceEtdAttribute(): ?\Illuminate\Support\Carbon
    {
        return match ($this->service) {
            'Airfreight' => $this->flights->last()?->departure_date,
            'Sea freight' => $this->seaLegs->last()?->etd,
            'Truck' => $this->truckLegs->last()?->departure_date,
            'Courier' => $this->courierLegs->last()?->departure_date,
            'Hand Carry' => $this->handCarryLegs->last()?->departure_date,
            'On-board delivery' => $this->onBoardLegs->last()?->departure_date,
            default => null,
        };
    }

    public function getServiceEtaAttribute(): ?\Illuminate\Support\Carbon
    {
        return match ($this->service) {
            'Airfreight' => $this->flights->last()?->arrival_date,
            'Sea freight' => $this->seaLegs->last()?->eta,
            'Truck' => $this->truckLegs->last()?->arrival_date,
            'Courier' => $this->courierLegs->last()?->arrival_date,
            'Release' => $this->releaseLegs->last()?->delivery_date,
            'Hand Carry' => $this->handCarryLegs->last()?->arrival_date,
            'On-board delivery' => $this->onBoardLegs->last()?->delivery_date,
            default => null,
        };
    }

    public function hasOpenIrregularities(): bool
    {
        return $this->irregularities->contains(fn ($item) => $item->status !== 'Closed');
    }

    public function hasEtlStock(): bool
    {
        return $this->crrs->contains(
            fn (Crr $crr) => strtoupper((string) $crr->internal_shipment) === 'ETL'
        );
    }

    public function getTotalPcsAttribute(): int
    {
        return (int) $this->crrs->sum(
            fn (Crr $crr) => $crr->packages->count()
        );
    }

    public function getTotalPcsDisplayAttribute(): string
    {
        $pcs = $this->total_pcs;

        return $pcs > 0 ? (string) $pcs : '—';
    }

    public function getTotalWeightDisplayAttribute(): string
    {
        $weight = $this->crrs->sum(
            fn (Crr $crr) => (float) $crr->packages->sum('weight')
        );

        return $weight > 0 ? (string) round($weight, 2) : '—';
    }

    public function statusBadgeClass(): string
    {
        return 'stock-status-badge ' . self::statusColorClass($this->status);
    }

    public static function statusColorClass(?string $status): string
    {
        return match (strtolower(trim((string) $status))) {
            'draft', 'new' => 'stock-status-new',
            'in transit' => 'shipment-status-in-transit',
            'stock' => 'stock-status-stock',
            'in process', 'in progress' => 'stock-status-in-progress',
            'pending' => 'stock-status-pending',
            'cancelled', 'canceled' => 'stock-status-cancelled',
            'delivered', 'completed' => 'stock-status-completed',
            'archived' => 'stock-status-archived',
            default => 'stock-status-unknown',
        };
    }

    public static function availableFlags(): array
    {
        return [
            'Pick up',
            'Un mark pick up',
        ];
    }

    public static function defaultFlags(): array
    {
        return [];
    }

    public static function batchResolvePartyNames(Collection $shipments): array
    {
        $compositeKeys = $shipments
            ->flatMap(fn (self $shipment) => [$shipment->departure, $shipment->consignee])
            ->filter(fn ($value) => is_string($value) && str_contains($value, ':'))
            ->unique()
            ->values();

        if ($compositeKeys->isEmpty()) {
            return [];
        }

        $idsByType = [];
        foreach ($compositeKeys as $key) {
            [$type, $id] = explode(':', $key, 2);
            $idsByType[$type][] = (int) $id;
        }

        $lookup = [];

        if (!empty($idsByType['hub'])) {
            Hub::whereIn('id', $idsByType['hub'])->get()->each(function (Hub $hub) use (&$lookup) {
                $lookup['hub:' . $hub->id] = $hub->hub_name;
            });
        }

        if (!empty($idsByType['agent'])) {
            Agent::whereIn('id', $idsByType['agent'])->get()->each(function (Agent $agent) use (&$lookup) {
                $lookup['agent:' . $agent->id] = $agent->agent_name;
            });
        }

        if (!empty($idsByType['customer'])) {
            Customer::whereIn('id', $idsByType['customer'])->get()->each(function (Customer $customer) use (&$lookup) {
                $lookup['customer:' . $customer->id] = $customer->customer_name;
            });
        }

        if (!empty($idsByType['office'])) {
            Office::whereIn('id', $idsByType['office'])->get()->each(function (Office $office) use (&$lookup) {
                $lookup['office:' . $office->id] = $office->office_name;
            });
        }

        if (!empty($idsByType['supplier'])) {
            Supplier::whereIn('id', $idsByType['supplier'])->get()->each(function (Supplier $supplier) use (&$lookup) {
                $lookup['supplier:' . $supplier->id] = $supplier->supplier_name;
            });
        }

        if (!empty($idsByType['other_company'])) {
            OtherCompany::whereIn('id', $idsByType['other_company'])->get()->each(function (OtherCompany $company) use (&$lookup) {
                $lookup['other_company:' . $company->id] = $company->company_name;
            });
        }

        return $lookup;
    }

    public function partyDisplay(?string $composite, array $partyNames): string
    {
        if (!$composite) {
            return '—';
        }

        return $partyNames[$composite] ?? $composite;
    }

    public static function batchResolveVesselCustomerNames(Collection $shipments): array
    {
        $vesselNames = $shipments
            ->flatMap(fn (self $shipment) => $shipment->crrs->pluck('vessel_name'))
            ->filter()
            ->unique()
            ->values();

        if ($vesselNames->isEmpty()) {
            return [];
        }

        $lookup = [];

        CustomerVessel::with('customer')
            ->where(function ($query) use ($vesselNames) {
                $query->whereIn('vessel', $vesselNames)
                    ->orWhereIn('vessel_name_alias', $vesselNames);
            })
            ->get()
            ->each(function (CustomerVessel $customerVessel) use (&$lookup) {
                $customerName = $customerVessel->customer?->customer_name;

                if (!$customerName) {
                    return;
                }

                if ($customerVessel->vessel) {
                    $lookup[$customerVessel->vessel] = $customerName;
                }

                if ($customerVessel->vessel_name_alias) {
                    $lookup[$customerVessel->vessel_name_alias] = $customerName;
                }
            });

        return $lookup;
    }
}
