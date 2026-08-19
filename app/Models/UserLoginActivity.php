<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserLoginActivity extends Model
{
    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'browser_latitude',
        'browser_longitude',
        'browser_location_accuracy',
        'ip_latitude',
        'ip_longitude',
        'ip_city',
        'ip_country',
        'logged_in_at',
        'logged_out_at',
    ];

    protected function casts(): array
    {
        return [
            'browser_latitude' => 'decimal:7',
            'browser_longitude' => 'decimal:7',
            'browser_location_accuracy' => 'decimal:2',
            'ip_latitude' => 'decimal:7',
            'ip_longitude' => 'decimal:7',
            'logged_in_at' => 'datetime',
            'logged_out_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
