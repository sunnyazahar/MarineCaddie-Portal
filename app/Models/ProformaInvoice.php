<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProformaInvoice extends Model
{
    protected $fillable = [
        'shipment_id',
        'proforma_no',
        'financial_year_label',
        'sequence_no',
        'invoice_type',
        'shipper',
        'consignee',
        'billing_party',
        'bill_to_pos',
        'airport_of_loading',
        'airport_of_destination',
        'loading_date',
        'destination_date',
        'due_date',
        'proforma_date',
        'client_ref_no',
        'job_no',
        'job_date',
        'hawb_no',
        'hawb_date',
        'mawb_no',
        'mawb_date',
        'packages',
        'chargeable_wt',
        'gross_wt',
        'commodity',
        'type_of_supply',
        'sb_be_no',
        'sb_be_date',
        'flight_no',
        'flight_date',
        'vessel_name',
        'currency',
        'einvoice_status',
        'payment_type',
        'paid_amount',
        'due_amount',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'loading_date' => 'date',
            'destination_date' => 'date',
            'due_date' => 'date',
            'proforma_date' => 'date',
            'job_date' => 'date',
            'hawb_date' => 'date',
            'mawb_date' => 'date',
            'sb_be_date' => 'date',
            'flight_date' => 'date',
            'chargeable_wt' => 'decimal:2',
            'gross_wt' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'due_amount' => 'decimal:2',
            'sequence_no' => 'integer',
        ];
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function lineItems(): HasMany
    {
        return $this->hasMany(ProformaInvoiceLineItem::class)->orderBy('sort_order');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
