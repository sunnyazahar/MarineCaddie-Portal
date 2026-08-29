<?php

namespace App\Models;

use App\Casts\JsonArrayCast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class Crr extends Model
{
    protected $fillable = [
        'stock_number',
        'registered_by',
        'duplicated_from_crr_id',
        'vessel_name',
        'po_numbers',
        'po_remarks',
        'content',
        'first_mile_updates',
        'first_mile_comment',
        'supplier',
        'is_landed_goods',
        'expected_delivery_date',
        'actual_delivery_date',
        'supplier_reference',
        'deadline_warehouse',
        'internal_shipment',
        'delivery_irregularities',
        'incoterm',
        'hub_agent',
        'location',
        'transit_type',
        'transit_id',
        'is_bonded_goods',
        'customs_doc_type',
        'bonded_date',
        'customs_doc_reference',
        'customs_lot_number',
        'country_of_origin',
        'hs_code',
        'currency',
        'customs_value',
        'priority',
        'status',
        'flags',
        'accept',
        'internal_comments',
        'customs_value_usd',
        'landed_from_vessel',
    ];

    protected $casts = [
        'po_numbers' => JsonArrayCast::class,
        'delivery_irregularities' => JsonArrayCast::class,
        'flags' => JsonArrayCast::class,
        'accept' => 'boolean',
        'is_landed_goods' => 'boolean',
        'is_bonded_goods' => 'boolean',
        'customs_value' => 'decimal:2',
        'customs_value_usd' => 'decimal:2',
        'status' => 'integer',
    ];

    const STATUS_PENDING = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_IN_PROGRESS = 2;
    const STATUS_COMPLETED = 3;
    const STATUS_CANCELLED = 4;
    const STATUS_ARCHIVED = 5;
    const STATUS_NEW = 6;

    public static function getStatusLabels()
    {
        return [
            self::STATUS_NEW => 'New',
            self::STATUS_PENDING => 'Pending',
            self::STATUS_ACTIVE => 'Stock',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_ARCHIVED => 'Archived',
        ];
    }

    public static function statusBadgeClass(int|string|null $status): string
    {
        if ($status === null || $status === '') {
            return 'stock-status-unknown';
        }

        if (is_string($status) && ! is_numeric($status)) {
            $resolvedStatus = array_search(
                strtolower(trim($status)),
                array_map('strtolower', self::getStatusLabels()),
                true
            );

            if ($resolvedStatus === false) {
                return 'stock-status-unknown';
            }

            $status = $resolvedStatus;
        }

        return match ((int) $status) {
            self::STATUS_NEW => 'stock-status-new',
            self::STATUS_ACTIVE => 'stock-status-stock',
            self::STATUS_IN_PROGRESS => 'stock-status-in-progress',
            self::STATUS_PENDING => 'stock-status-pending',
            self::STATUS_CANCELLED => 'stock-status-cancelled',
            self::STATUS_COMPLETED => 'stock-status-completed',
            self::STATUS_ARCHIVED => 'stock-status-archived',
            default => 'stock-status-unknown',
        };
    }

    public static function statusUpdateAttributes(int $status): array
    {
        $attributes = ['status' => $status];

        if ($status === self::STATUS_NEW) {
            $attributes['accept'] = false;
        }

        return $attributes;
    }

    public static function availableFlags(): array
    {
        return [
            'Follow up',
            'Pick up',
            'Un mark pick up',
        ];
    }

    public static function defaultFlags(): array
    {
        return ['Follow up'];
    }

    public function scopeSelectableForShipment($query)
    {
        return $query->whereNotIn('status', [
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED,
        ]);
    }

    public function scopeAttachableToShipment($query)
    {
        return $query->whereNotIn('status', [
            self::STATUS_IN_PROGRESS,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED,
        ]);
    }

    public function shipments()
    {
        return $this->belongsToMany(Shipment::class, 'shipment_crr');
    }

    /**
     * Form "Internal shipment" only posts ETL/KTL/RTL (or empty).
     * Linked stocks store the real shipment number in the same column — empty posts
     * must not wipe that value (or falsely log "Related shipment … to empty").
     */
    public function resolveInternalShipmentFromForm(?string $posted): ?string
    {
        $posted = trim((string) ($posted ?? ''));
        $specialCodes = ['ETL', 'KTL', 'RTL'];

        if ($posted !== '') {
            return $posted;
        }

        $current = trim((string) ($this->internal_shipment ?? ''));
        if ($current !== '' && ! in_array(strtoupper($current), $specialCodes, true)) {
            return $current;
        }

        $this->loadMissing('shipments');
        $linked = $this->shipments
            ->pluck('shipment_number')
            ->filter(fn ($number) => filled($number))
            ->first();

        return $linked ? (string) $linked : null;
    }

    /**
     * Stock list UI: per hub code, In Progress stock shipment info (display only).
     *
     * @return Collection<string, array{number: string, shipment_id: int|null}>
     */
    public static function hubInProgressShipmentInfoByHub(): Collection
    {
        $inProgress = static::query()
            ->with(['shipments' => fn ($query) => $query->select('shipments.id', 'shipments.shipment_number')])
            ->where('status', self::STATUS_IN_PROGRESS)
            ->whereNotNull('internal_shipment')
            ->where('internal_shipment', '!=', '')
            ->orderByDesc('id')
            ->get()
            ->groupBy(fn (self $crr) => (string) $crr->hub_code);

        $infoByHub = $inProgress->map(function (Collection $group) {
            $crr = $group->first();
            $number = trim((string) $crr->internal_shipment);

            return [
                'number' => $number,
                'shipment_id' => $crr->shipments->first()?->id,
            ];
        });

        $numbersMissingId = $infoByHub
            ->filter(fn (array $info) => empty($info['shipment_id']) && $info['number'] !== '')
            ->pluck('number')
            ->unique()
            ->values()
            ->all();

        if ($numbersMissingId === []) {
            return $infoByHub;
        }

        $shipmentIdByNumber = Shipment::query()
            ->whereIn('shipment_number', $numbersMissingId)
            ->pluck('id', 'shipment_number');

        return $infoByHub->map(function (array $info) use ($shipmentIdByNumber) {
            if (empty($info['shipment_id']) && $info['number'] !== '') {
                $info['shipment_id'] = $shipmentIdByNumber->get($info['number']);
            }

            return $info;
        });
    }

    /**
     * Stock list shipment column — inherits hub In Progress shipment for New / Stock rows (not saved).
     *
     * @return array{number: string, inherited: bool, shipment_id: int|null}
     */
    public function stockListShipmentColumn(Collection $hubInProgressShipmentInfo): array
    {
        $empty = ['number' => '', 'inherited' => false, 'shipment_id' => null];

        $own = trim((string) ($this->internal_shipment ?? ''));
        if ($own !== '') {
            return ['number' => $own, 'inherited' => false, 'shipment_id' => null];
        }

        if (! in_array((int) $this->status, [self::STATUS_NEW, self::STATUS_ACTIVE], true)) {
            return $empty;
        }

        $hubKey = (string) $this->hub_code;
        if ($hubKey === '') {
            return $empty;
        }

        $info = $hubInProgressShipmentInfo->get($hubKey);
        if (! is_array($info)) {
            $info = ['number' => trim((string) $info), 'shipment_id' => null];
        }

        $number = trim((string) ($info['number'] ?? ''));
        if ($number === '') {
            return $empty;
        }

        return [
            'number' => $number,
            'inherited' => true,
            'shipment_id' => $info['shipment_id'] ?? null,
        ];
    }

    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    public function scopeStockFollowUp($query)
    {
        return $query
            ->where('status', '!=', self::STATUS_COMPLETED)
            ->where('accept', false);
    }

    public function scopePickupWorkList($query)
    {
        return $query
            ->where('status', '!=', self::STATUS_COMPLETED)
            ->whereJsonContains('flags', 'Pick up');
    }

    public function accountManagerName(): ?string
    {
        return $this->customerVessel?->account_manager
            ?: $this->customerVessel?->customer?->responsible?->accountManager?->name;
    }

    public function packages()
    {
        return $this->hasMany(CrrPackage::class);
    }

    public function costs()
    {
        return $this->hasMany(CrrCost::class);
    }

    public function documents()
    {
        return $this->hasMany(CrrDocument::class);
    }

    public function changeLogs()
    {
        return $this->hasMany(CrrChangeLog::class)->orderByDesc('created_at');
    }

    /**
     * Get the hub associated with this CRR.
     * Tries to match by code first, then by name for legacy data.
     */
    public function hub()
    {
        // We try to match hub_agent against the 'code' column or 'hub_name' column
        // Since Eloquent doesn't natively support multiple keys for belongsTo, 
        // we'll provide a getter that handles the logic.
        return $this->belongsTo(Hub::class, 'hub_agent', 'code')
                    ->orWhere('hub_name', $this->hub_agent);
    }

    /**
     * Get the customer vessel info for this CRR.
     */
    public function customerVessel()
    {
        return $this->belongsTo(CustomerVessel::class, 'vessel_name', 'vessel');
    }

    /**
     * Accessor to get the hub code safely.
     */
    public function getHubCodeAttribute()
    {
        // If hub_agent is already a short code (3-5 chars), return it
        if (strlen($this->hub_agent) <= 5 && $this->hub_agent !== null) {
            return $this->hub_agent;
        }

        // Otherwise, try to find the hub by name
        $hub = Hub::where('hub_name', $this->hub_agent)->first();
        return $hub ? $hub->code : $this->hub_agent;
    }
}
