<?php

namespace App\Http\Requests\Billing;

use Illuminate\Foundation\Http\FormRequest;

class StoreProformaInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'shipment_id' => ['required', 'integer', 'exists:shipments,id'],
            'invoice_type' => ['nullable', 'string', 'max:20'],
            'shipper' => ['nullable', 'string', 'max:255'],
            'consignee' => ['nullable', 'string', 'max:255'],
            'billing_party' => ['nullable', 'string', 'max:255'],
            'bill_to_pos' => ['nullable', 'integer', 'exists:countries,id'],
            'airport_of_loading' => ['nullable', 'string', 'max:20'],
            'airport_of_destination' => ['nullable', 'string', 'max:20'],
            'loading_date' => ['nullable', 'string', 'max:20'],
            'destination_date' => ['nullable', 'string', 'max:20'],
            'due_date' => ['nullable', 'string', 'max:20'],
            'proforma_date' => ['nullable', 'string', 'max:20'],
            'client_ref_no' => ['nullable', 'string', 'max:255'],
            'job_no' => ['nullable', 'string', 'max:255'],
            'job_date' => ['nullable', 'string', 'max:20'],
            'hawb_no' => ['nullable', 'string', 'max:255'],
            'hawb_date' => ['nullable', 'string', 'max:20'],
            'mawb_no' => ['nullable', 'string', 'max:255'],
            'mawb_date' => ['nullable', 'string', 'max:20'],
            'packages' => ['nullable', 'string', 'max:50'],
            'chargeable_wt' => ['nullable', 'string', 'max:50'],
            'gross_wt' => ['nullable', 'string', 'max:50'],
            'commodity' => ['nullable', 'string', 'max:255'],
            'type_of_supply' => ['nullable', 'string', 'max:50'],
            'sb_be_no' => ['nullable', 'string', 'max:255'],
            'sb_be_date' => ['nullable', 'string', 'max:20'],
            'flight_no' => ['nullable', 'string', 'max:255'],
            'flight_date' => ['nullable', 'string', 'max:20'],
            'vessel_name' => ['nullable', 'string', 'max:255'],
            'currency' => ['nullable', 'string', 'max:8'],
            'einvoice_status' => ['nullable', 'string', 'max:50'],
            'payment_type' => ['required', 'string', 'in:partial_payment,full_payment'],
            'paid_amount' => ['nullable', 'string', 'max:50'],
            'due_amount' => ['nullable', 'string', 'max:50'],
            'line_items' => ['nullable', 'array'],
            'line_items.*.description' => ['nullable', 'string', 'max:255'],
            'line_items.*.hsn' => ['nullable', 'string', 'max:50'],
            'line_items.*.remarks' => ['nullable', 'string', 'max:255'],
            'line_items.*.qty' => ['nullable', 'string', 'max:50'],
            'line_items.*.qty_type' => ['nullable', 'string', 'max:32'],
            'line_items.*.rate' => ['nullable', 'string', 'max:50'],
            'line_items.*.currency' => ['nullable', 'string', 'max:8'],
            'line_items.*.amount' => ['nullable', 'string', 'max:50'],
            'line_items.*.exchange_rate' => ['nullable', 'string', 'max:50'],
            'line_items.*.tax_type' => ['nullable', 'string', 'max:4'],
            'line_items.*.non_taxable' => ['nullable', 'string', 'max:50'],
            'line_items.*.taxable' => ['nullable', 'string', 'max:50'],
            'line_items.*.igst_pct' => ['nullable', 'string', 'max:10'],
            'line_items.*.igst_amt' => ['nullable', 'string', 'max:50'],
            'line_items.*.cgst_pct' => ['nullable', 'string', 'max:10'],
            'line_items.*.cgst_amt' => ['nullable', 'string', 'max:50'],
            'line_items.*.sgst_pct' => ['nullable', 'string', 'max:10'],
            'line_items.*.sgst_amt' => ['nullable', 'string', 'max:50'],
        ];
    }
}
