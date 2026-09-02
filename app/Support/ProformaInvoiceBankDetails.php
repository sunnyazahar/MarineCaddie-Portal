<?php

namespace App\Support;

class ProformaInvoiceBankDetails
{
    public const CURRENCY = 'USD';

    public const ACCOUNT_NAME = 'MARINECADDIE SHIPPING L.L.C';

    public const ACCOUNT_NUMBER = '1025974210902';

    public const IBAN = 'AE670260001025974210902';

    public const SWIFT_CODE = 'EBILAEAD';

    public const BANK_NAME = 'Emirates NBD';

    public const CITY_COUNTRY = 'Dubai, UAE';

    /**
     * @var list<string>
     */
    public const NOTES = [
        'Any Discrepancies in this invoice should be lodged within 7 days from the date of invoice, otherwise the invoice amount will be treated as true & correct.',
        'Interest of 2% per month will be levied on all account beyond 30 days.',
        'This is a computer generated invoice. No signature is required.',
        'All business is transacted in accordance with Standard Trading Conditions and Copy is available upon request.',
    ];

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
        return [
            'account_name' => self::ACCOUNT_NAME,
            'account_number' => self::ACCOUNT_NUMBER,
            'iban' => self::IBAN,
            'swift_code' => self::SWIFT_CODE,
            'bank_name' => self::BANK_NAME,
            'city_country' => self::CITY_COUNTRY,
            'notes' => self::NOTES,
        ];
    }
}
