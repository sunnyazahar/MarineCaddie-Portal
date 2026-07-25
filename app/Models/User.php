<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'role',
        'is_active',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'login_otp_hash',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_active' => 'boolean',
            'password' => 'hashed',
            'login_otp_expires_at' => 'datetime',
            'login_otp_sent_at' => 'datetime',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'Admin';
    }

    public function offices(): BelongsToMany
    {
        return $this->belongsToMany(Office::class, 'user_office_assignments');
    }

    public function hubs(): BelongsToMany
    {
        return $this->belongsToMany(Hub::class, 'user_hub_assignments');
    }

    public function agents(): BelongsToMany
    {
        return $this->belongsToMany(Agent::class, 'user_agent_assignments');
    }

    public function suppliers(): BelongsToMany
    {
        return $this->belongsToMany(Supplier::class, 'user_supplier_assignments');
    }

    public function notifications()
    {
        return $this->hasMany(UserNotification::class)->orderByDesc('occurred_at')->orderByDesc('id');
    }
}
