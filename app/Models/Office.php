<?php

namespace App\Models;

use App\Traits\LogsFieldChanges;
use App\Traits\TracksUserAudit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Office extends Model
{
    use HasFactory, SoftDeletes, TracksUserAudit, LogsFieldChanges;

    protected $fillable = [
        'office_name', 'office_short_name', 'phone_number', 'email', 'eori_number',
        'address', 'city', 'district_state', 'zip_code', 'country_id',
        'postal_address', 'postal_city', 'postal_district_state', 'postal_zip_code', 'office_country_id',
        'invoicing_currency', 'reporting_currency', 'vat_rates', 'vat_country_specific_name', 'vat_number',
        'invoicing_emails', 'heading_invoice', 'information_invoice',
        'use_vat_check', 'show_imo', 'enable_reader', 'status',
        'created_by', 'updated_by',
    ];

    /**
     * Active offices only (status = 1). Soft-deleted rows are already excluded.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 1);
    }

    public function bankAccounts()
    {
        return $this->hasMany(OfficeBankAccount::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_office_assignments');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
}
