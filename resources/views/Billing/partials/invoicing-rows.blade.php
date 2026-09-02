@forelse ($invoices as $row)
    <tr>
        <td class="invoicing-checkbox-cell">
            <input type="checkbox"
                class="table-checkbox invoicing-row-checkbox"
                value="{{ $row['job_no'] }}"
                data-po-no="{{ $row['client_ref_no'] }}"
                data-party-name="{{ $row['party_name'] }}"
                aria-label="Select row">
        </td>
        <td title="{{ $row['service_type'] }}">{{ $row['service_type'] }}</td>
        <td title="{{ $row['proforma_date'] }}">{{ $row['proforma_date'] }}</td>
        <td title="{{ $row['proforma_no'] }}">{{ $row['proforma_no'] }}</td>
        <td title="{{ $row['job_no'] }}">
            @if (! empty($row['shipment_id']))
                <a href="{{ route('shipments.edit', $row['shipment_id']) }}" class="text-primary">{{ $row['job_no'] }}</a>
            @else
                {{ $row['job_no'] }}
            @endif
        </td>
        <td title="{{ $row['job_date'] }}">{{ $row['job_date'] }}</td>
        <td title="{{ $row['shipper_name'] }}">{{ $row['shipper_name'] }}</td>
        <td title="{{ $row['consignee_name'] }}">{{ $row['consignee_name'] }}</td>
        <td title="{{ $row['party_name'] }}">{{ $row['party_name'] }}</td>
        <td title="{{ $row['port_of_loading'] }}">{{ $row['port_of_loading'] }}</td>
        <td title="{{ $row['port_of_discharge'] }}">{{ $row['port_of_discharge'] }}</td>
        <td title="{{ $row['client_ref_no'] }}">{{ $row['client_ref_no'] }}</td>
        <td title="{{ $row['mbl_no'] }}">{{ $row['mbl_no'] }}</td>
        <td title="{{ $row['gross_wt'] }}">{{ $row['gross_wt'] }}</td>
        <td title="{{ $row['chargeable_wt'] }}">{{ $row['chargeable_wt'] }}</td>
        <td title="{{ $row['currency'] }}">{{ $row['currency'] }}</td>
        <td title="{{ $row['net_invoice_amount'] }}">{{ $row['net_invoice_amount'] }}</td>
        <td class="invoicing-status-cell" title="{{ $row['status'] }}">
            @php
                $status = $row['status'] ?? '';
                $statusBadgeClass = match ($status) {
                    'Billed' => 'invoicing-status-badge--billed',
                    'Partially paid' => 'invoicing-status-badge--partial',
                    default => 'invoicing-status-badge--ready',
                };
            @endphp
            <span class="invoicing-status-badge {{ $statusBadgeClass }}">
                {{ $status }}
            </span>
        </td>
        <td class="invoicing-action-cell">
            <div class="invoicing-row-actions" role="group" aria-label="Invoice actions">
                <a href="{{ route('billing.invoicing.edit', ['proformaNo' => $row['job_no']]) }}" class="invoicing-row-action invoicing-row-action--edit" title="Edit invoice" aria-label="Edit invoice">
                    <i class="feather icon-edit"></i>
                </a>
                <a href="{{ route('billing.invoicing.print', ['proformaNo' => $row['job_no']]) }}" class="invoicing-row-action invoicing-row-action--print" title="Print invoice" aria-label="Print invoice" target="_blank" rel="noopener noreferrer">
                    <i class="feather icon-printer"></i>
                </a>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="19" class="dataTables_empty">No shipments available for invoicing.</td>
    </tr>
@endforelse
