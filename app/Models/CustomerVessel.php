<?php

namespace App\Models;

use App\Traits\LogsFieldChanges;
use App\Traits\TracksUserAudit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerVessel extends Model
{
    use SoftDeletes, TracksUserAudit, LogsFieldChanges;

    protected $fillable = [
        'customer_id',
        // Vessel information
        'vessel', 'vessel_name_alias', 'vessel_imo',
        'shipyard', 'shipyard_location',
        'not_in_transit', 'inactive_vessel', 'sanction_blocked',
        'financially_blocked', 'pre_payment_only',
        'customer_vessel_code', 'vessel_type_alias', 'po_example',
        'internal_shipment', 'except_from_hubs', 'remarks',
        // Responsible managers
        'manager', 'account_manager', 'receivers_stocklists',
        // Invoice details
        'invoice_vessel_separately', 'title_invoice_recipient', 'yearly_customer_reference',
        // Home ports
        'home_consolidation_port', 'home_delivery_port',
        // Contact details
        'contact_id', 'contact_stocklists', 'contact_pre_alerts',
        'contact_stock_notifications', 'contact_free_storage_notifications', 'contact_offers',
        'created_by', 'updated_by',
    ];

    protected $casts = [
        'not_in_transit' => 'boolean',
        'inactive_vessel' => 'boolean',
        'sanction_blocked' => 'boolean',
        'financially_blocked' => 'boolean',
        'pre_payment_only' => 'boolean',
        'invoice_vessel_separately' => 'boolean',
        'contact_stocklists' => 'boolean',
        'contact_pre_alerts' => 'boolean',
        'contact_stock_notifications' => 'boolean',
        'contact_free_storage_notifications' => 'boolean',
        'contact_offers' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function changeLogSkipFields(): array
    {
        // Logged with contact name in CustomerController::updateVessel.
        return ['contact_id'];
    }
}
