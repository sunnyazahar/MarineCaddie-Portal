<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentDocument extends Model
{
    protected $fillable = [
        'shipment_id',
        'file_name',
        'file_path',
        'file_type',
        'is_internal',
    ];

    protected function casts(): array
    {
        return [
            'is_internal' => 'boolean',
        ];
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function fileUrl(): string
    {
        return route('shipments.documents.show', [$this->shipment_id, $this->id], false);
    }

    public static function fileTypeOptions(): array
    {
        return [
            'Agent invoice',
            'Agent quote',
            'Airway bill',
            'B/L',
            'Booking details',
            'Client instruction',
            'Client quote',
            'CMR',
            'Commercial invoice',
            'Delivery picture',
            'DG docs',
            'Export declaration',
            'Landing',
            'LSP',
            'Manifest',
            'MSDS',
            'Packing list',
            'Pickup picture',
            'Picture',
            'PO',
            'POD',
            'Pre-alert',
            'Quote',
            'Scanned',
            'Shipment certificates',
            'Transport document',
            'Unspecified',
        ];
    }

    public static function fileTypeOptionsWithCustom(): array
    {
        $defaults = self::fileTypeOptions();

        $custom = self::query()
            ->select('file_type')
            ->distinct()
            ->whereNotNull('file_type')
            ->pluck('file_type')
            ->filter(function ($type) use ($defaults) {
                return $type !== '' && ! in_array($type, $defaults, true);
            })
            ->sort(SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->all();

        return array_merge($defaults, $custom);
    }
}
