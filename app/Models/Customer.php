<?php

namespace App\Models;

use App\Traits\LogsFieldChanges;
use App\Traits\TracksUserAudit;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use TracksUserAudit, LogsFieldChanges;

    protected $fillable = [
        'customer_name', 'customer_number', 'customer_group_id', 'phone', 'contact_person', 'email',
        'internal_shipment', 'remarks', 'special_considerations', 'un_locode',
        'show_transport_details', 'esea_store_stock_only', 'logo',
        'created_by', 'updated_by',
    ];

    public function documents()
    {
        return $this->hasMany(CustomerDocument::class);
    }

    public function addresses()
    {
        return $this->hasMany(CustomerAddress::class);
    }

    public function primaryAddress()
    {
        return $this->hasOne(CustomerAddress::class)->where('type', 'primary');
    }

    public function postalAddress()
    {
        return $this->hasOne(CustomerAddress::class)->where('type', 'postal');
    }

    public function invoiceAddress()
    {
        return $this->hasOne(CustomerAddress::class)->where('type', 'invoice');
    }

    public function invoiceDetail()
    {
        return $this->hasOne(CustomerInvoiceDetail::class);
    }

    public function responsible()
    {
        return $this->hasOne(CustomerResponsible::class);
    }

    public function group()
    {
        return $this->belongsTo(CustomerGroup::class, 'customer_group_id');
    }

    public function sop()
    {
        return $this->hasOne(CustomerSop::class);
    }

    public function notificationSetting()
    {
        return $this->hasOne(CustomerNotificationSetting::class);
    }

    public function vessels()
    {
        return $this->hasMany(CustomerVessel::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function changeLogSkipFields(): array
    {
        // Logged via related snapshot with human-readable group name.
        return ['customer_group_id'];
    }
}
