<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProformaInvoiceLineItem extends Model
{
    protected $fillable = [
        'proforma_invoice_id',
        'sort_order',
        'description',
        'hsn',
        'remarks',
        'qty',
        'qty_type',
        'rate',
        'currency',
        'amount',
        'exchange_rate',
        'tax_type',
        'non_taxable',
        'taxable',
        'igst_pct',
        'igst_amt',
        'cgst_pct',
        'cgst_amt',
        'sgst_pct',
        'sgst_amt',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'decimal:2',
            'rate' => 'decimal:2',
            'amount' => 'decimal:2',
            'exchange_rate' => 'decimal:4',
            'non_taxable' => 'decimal:2',
            'taxable' => 'decimal:2',
            'igst_pct' => 'decimal:2',
            'igst_amt' => 'decimal:2',
            'cgst_pct' => 'decimal:2',
            'cgst_amt' => 'decimal:2',
            'sgst_pct' => 'decimal:2',
            'sgst_amt' => 'decimal:2',
            'sort_order' => 'integer',
        ];
    }

    public function proformaInvoice(): BelongsTo
    {
        return $this->belongsTo(ProformaInvoice::class);
    }
}
