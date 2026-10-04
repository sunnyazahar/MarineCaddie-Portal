<?php

namespace App\Support;

/**
 * Bank details + terms printed on proforma invoices, read from
 * Admin > Company Settings via {@see Branding}.
 */
class ProformaInvoiceBankDetails
{
    /**
     * @return array{
     *     account_name: string,
     *     account_number: string,
     *     iban: string,
     *     swift_code: string,
     *     bank_name: string,
     *     city_country: string,
     *     notes: list<string>
     * }
     */
    public static function toArray(): array
    {
        return Branding::bankDetails() + ['notes' => Branding::invoiceNotes()];
    }
}
