<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class UserNotification extends Model
{
    public const CATEGORY_COMMENTS = 'comments';
    public const CATEGORY_PICKUPS = 'pickups';
    public const CATEGORY_COSTS = 'costs';
    public const CATEGORY_OTHER = 'other';

    protected $fillable = [
        'user_id',
        'category',
        'title',
        'message',
        'link_label',
        'link_url',
        'icon',
        'is_read',
        'related_type',
        'related_id',
        'occurred_at',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'occurred_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    public function displayTime(): string
    {
        $at = $this->occurred_at ?? $this->created_at;

        return $at ? $at->timezone(config('app.timezone', 'UTC'))->format('d.m.Y H:i') : '';
    }

    public static function categoryOptions(): array
    {
        return [
            self::CATEGORY_COMMENTS => 'Comments',
            self::CATEGORY_PICKUPS => 'Pick ups',
            self::CATEGORY_COSTS => 'Costs',
            self::CATEGORY_OTHER => 'Other',
        ];
    }
}
