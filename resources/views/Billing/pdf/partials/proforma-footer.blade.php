<div class="page-footer">
    <table class="footer-summary">
        <tr>
            <td class="footer-bank-col">
                <table class="remittance-fields remittance-fields-full">
                    <colgroup>
                        <col class="remittance-key-col">
                        <col class="remittance-value-col">
                    </colgroup>
                    <tr>
                        <td class="remittance-key">A/c Name :</td>
                        <td class="remittance-value">{{ $bank_details['account_name'] }}</td>
                    </tr>
                    <tr>
                        <td class="remittance-key">A/c No :</td>
                        <td class="remittance-value">{{ $bank_details['account_number'] }}</td>
                    </tr>
                    <tr>
                        <td class="remittance-key">IBAN No :</td>
                        <td class="remittance-value">{{ $bank_details['iban'] }}</td>
                    </tr>
                    <tr>
                        <td class="remittance-key">Swift Code :</td>
                        <td class="remittance-value">{{ $bank_details['swift_code'] }}</td>
                    </tr>
                    <tr>
                        <td class="remittance-key">Bank Name :</td>
                        <td class="remittance-value">{{ $bank_details['bank_name'] }}</td>
                    </tr>
                    <tr>
                        <td class="remittance-key">City/Country :</td>
                        <td class="remittance-value">{{ $bank_details['city_country'] }}</td>
                    </tr>
                </table>
            </td>
            <td class="footer-totals-col">
                <table class="totals-table">
                    <colgroup>
                        <col class="totals-label-col">
                        <col class="totals-value-col">
                    </colgroup>
                    <tr>
                        <td class="totals-label">Subtotal Amount</td>
                        <td class="totals-value">{{ $totals['amount'] }}</td>
                    </tr>
                    <tr>
                        <td class="totals-label">Taxable Value</td>
                        <td class="totals-value">{{ $totals['taxable'] }}</td>
                    </tr>
                    <tr>
                        <td class="totals-label">VAT</td>
                        <td class="totals-value">{{ $totals['vat'] }}</td>
                    </tr>
                    <tr>
                        <td class="totals-net-label">Net Payable Amount</td>
                        <td class="totals-net-value">USD {{ $totals['net_payable'] }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="remittance-notes">
        <div class="remittance-notes-title">NOTE</div>
        @foreach ($bank_details['notes'] as $note)
            <div class="remittance-note-line">{{ $note }}</div>
        @endforeach
    </div>
    <div class="footer-page-number"></div>
</div>
