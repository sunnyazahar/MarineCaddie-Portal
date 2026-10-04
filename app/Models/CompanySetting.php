<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    protected $fillable = [
        'company_name',
        'legal_name',
        'logo_path',
        'website',
        'address_line_1',
        'address_line_2',
        'phone',
        'email',
        'bank_account_name',
        'bank_account_number',
        'bank_iban',
        'bank_swift',
        'bank_name',
        'bank_city_country',
        'invoice_notes',
        'proforma_prefix',
        'otp_enabled',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'invoice_notes' => 'array',
            'otp_enabled' => 'boolean',
        ];
    }
}
