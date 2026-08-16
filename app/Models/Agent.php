<?php

namespace App\Models;

use App\Traits\LogsFieldChanges;
use App\Traits\TracksUserAudit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agent extends Model
{
    use HasFactory, SoftDeletes, TracksUserAudit, LogsFieldChanges;

    protected $fillable = [
        'agent_name',
        'company_id',
        'code',
        'code_description',
        'phone',
        'contact_person',
        'email',
        'remarks',
        'special_considerations',
        'show_pre_alert',
        'agent_address',
        'city',
        'district_state',
        'zip_code',
        'country_id',
        'port_code',
        'office_address',
        'office_city',
        'office_district_state',
        'office_zip_code',
        'office_country_id',
        'eori_number',
        'un_locode',
        'agent_type',
        'is_active',
        // Billing
        'invoicing_name', 'billing_address', 'billing_city', 'billing_district_state', 'billing_zip_code', 'billing_country_id',
        'invoicing_emails', 'invoicing_emails_cc', 'vat_number', 'invoicing_frequency', 'applies_to_rebate', 'rebate_percentage',
        'outgoing_currency', 'outgoing_payment_terms', 'incoming_currency', 'incoming_payment_terms',
        // SOP
        'coc_signed', 'sop_implemented', 'coc_signed_date', 'responsible_manager',
        // Pricing
        'calculate_sell_rates', 'purchase_rate', 'sell_rate', 'profit',
        // Email Settings
        'export_email_services', 'import_email_services', 'status_changed_emails', 'stock_item_changed_emails', 'quote_requests_emails',
        // Scan gun
        'scangun_login', 'scangun_password', 'scangun_enable_picture', 'scangun_enable_detailed_shipment',
        'created_by', 'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_pre_alert' => 'boolean',
        'applies_to_rebate' => 'boolean',
        'coc_signed' => 'boolean',
        'sop_implemented' => 'boolean',
        'calculate_sell_rates' => 'boolean',
        'scangun_enable_picture' => 'boolean',
        'scangun_enable_detailed_shipment' => 'boolean',
        'coc_signed_date' => 'date',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function officeCountry()
    {
        return $this->belongsTo(Country::class, 'office_country_id');
    }

    public function billingCountry()
    {
        return $this->belongsTo(Country::class, 'billing_country_id');
    }

    public function billingExceptions()
    {
        return $this->hasMany(AgentBillingException::class);
    }

    public function documents()
    {
        return $this->hasMany(AgentDocument::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function agentUsers()
    {
        return $this->hasMany(AgentUser::class);
    }
}
