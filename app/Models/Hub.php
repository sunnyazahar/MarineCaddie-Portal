<?php

namespace App\Models;

use App\Traits\LogsFieldChanges;
use App\Traits\TracksUserAudit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hub extends Model
{
    use SoftDeletes, TracksUserAudit, LogsFieldChanges;

    protected $fillable = [
        'hub_name',
        'company_id',
        'customer_number_fm',
        'code',
        'code_description',
        'phone_number',
        'contact_person',
        'email',
        'is_gts_company',
        'remarks',
        'special_considerations',
        'show_pre_alert',
        'hub_address',
        'city',
        'district_state',
        'zip_code',
        'country',
        'port_code',
        'office_address',
        'office_city',
        'office_district_state',
        'office_zip_code',
        'office_country',
        'eori_number',
        'un_locode',
        'hide_in_portal',
        'portal_remarks',
        'portal_email',
        // New Fields
        'invoicing_name',
        'invoicing_address',
        'invoicing_city',
        'invoicing_district',
        'invoicing_zip',
        'billing_country',
        'emails_for_invoicing',
        'emails_for_invoicing_cc',
        'vat_number',
        'invoicing_frequency',
        'billing_currency_outgoing',
        'payment_terms_outgoing',
        'billing_currency_incoming',
        'payment_terms_incoming',
        'agreement_type',
        'rebate_percentage',
        'export_services',
        'import_services',
        'export_emails',
        'import_emails',
        'stock_item_changed_emails',
        'quote_requests_emails',
        'coc_signed',
        'sop_implemented',
        'coc_signed_date',
        'responsible_manager',
        'scan_gun_login',
        'scan_gun_password',
        'agreement_start_date',
        'agreement_expiry_date',
        'minimal_cbm',
        'minimal_weight',
        'free_storage_days',
        'cbm_charge_usd',
        'scangun_photo_taking',
        'scangun_detailed_shipment_out',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_gts_company' => 'boolean',
        'show_pre_alert' => 'boolean',
        'hide_in_portal' => 'boolean',
        'coc_signed' => 'boolean',
        'sop_implemented' => 'boolean',
        'export_services' => 'array',
        'import_services' => 'array',
        'export_emails' => 'array',
        'import_emails' => 'array',
        'rebate_percentage' => 'decimal:2',
        'agreement_start_date' => 'date',
        'agreement_expiry_date' => 'date',
        'minimal_cbm' => 'decimal:2',
        'minimal_weight' => 'decimal:2',
        'cbm_charge_usd' => 'decimal:2',
        'scangun_photo_taking' => 'boolean',
        'scangun_detailed_shipment_out' => 'boolean',
    ];

    public function documents()
    {
        return $this->hasMany(HubDocument::class);
    }

    public function pricingDocuments()
    {
        return $this->hasMany(HubPricingDocument::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function hubUsers()
    {
        return $this->hasMany(HubUser::class);
    }
}
