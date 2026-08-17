<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentPreAlert extends Model
{
    protected $fillable = [
        'shipment_id',
        'version',
        'file_name',
        'file_path',
        'form_hash',
        'mail_sent_at',
    ];

    protected $casts = [
        'mail_sent_at' => 'datetime',
    ];

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function isMailPending(): bool
    {
        return $this->mail_sent_at === null;
    }

    public function markMailSent(): void
    {
        if ($this->mail_sent_at !== null) {
            return;
        }

        $this->forceFill(['mail_sent_at' => now()])->save();
    }

    public function fileUrl(): string
    {
        return route('shipments.pre-alerts.show', [$this->shipment_id, $this->id], false);
    }

    public function displayLabel(): string
    {
        return self::labelForVersion((int) $this->version);
    }

    public static function labelForVersion(int $version): string
    {
        if ($version <= 1) {
            return 'pre-alert';
        }

        return 'pre-alert ' . ($version - 1);
    }
}
