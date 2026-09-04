@forelse ($shipments as $shipment)
@php
    $portCities = $portCities ?? [];
    $departureCity = $shipment->departureCityDisplay($portCities);
    $destinationCity = $shipment->destinationCityDisplay($portCities);
    $consigneeDisplay = $shipment->partyDisplay($shipment->consignee, $partyNames);
    $consigneeType = explode(':', (string) $shipment->consignee, 2)[0];
    $etd = $shipment->service_etd;
    $eta = $shipment->service_eta;
    $etaOverdue = $eta && $eta->startOfDay()->lte(now()->startOfDay());
    $lastReminderSent = $shipment->last_reminder_sent_at
        ? \Carbon\Carbon::parse($shipment->last_reminder_sent_at)->format('d.m.Y')
        : '';
@endphp
<tr
    data-customers="{{ $shipment->customer_names->implode(',') }}"
    data-vessels="{{ $shipment->vessel_names->implode(',') }}"
    data-shipment-number="{{ $shipment->shipment_number }}"
    data-destination="{{ $destinationCity }}"
    data-account-manager="{{ $shipment->accountManager?->name ?? '' }}"
    data-created-by="{{ $shipment->creator?->name ?? '' }}"
    data-status="{{ $shipment->status ?? '' }}"
>
    <td>
        <div class="d-flex align-items-center">
            <a href="{{ route('shipments.edit', $shipment->id) }}">{{ $shipment->shipment_number }}</a>
            @if ($shipment->hasOpenIrregularities())
                <i class="ti-alert text-danger ml-2" title="Open irregularities"></i>
            @endif
        </div>
    </td>
    <td>{{ $shipment->customer_display }}</td>
    <td>{{ $shipment->vessel_display }}</td>
    <td>{{ $shipment->service ?? '—' }}</td>
    <td class="consignee-cell" title="{{ $consigneeDisplay }}">
        @if ($consigneeType === 'hub')
            <span class="consignee-row consignee-hub-agent"><i class="ti-home consignee-hub-icon" title="Hub"></i><span class="consignee-hub-agent-text" title="{{ $consigneeDisplay }}">{{ $consigneeDisplay }}</span></span>
        @elseif ($consigneeType === 'agent')
            <span class="consignee-row consignee-hub-agent"><i class="ti-user consignee-hub-icon" title="Agent"></i><span class="consignee-hub-agent-text" title="{{ $consigneeDisplay }}">{{ $consigneeDisplay }}</span></span>
        @else
            <span class="consignee-row"><span class="consignee-hub-agent-text" title="{{ $consigneeDisplay }}">{{ $consigneeDisplay }}</span></span>
        @endif
    </td>
    <td title="{{ $departureCity }}">{{ $departureCity }}</td>
    <td title="{{ $destinationCity }}">{{ $destinationCity }}</td>
    <td>{{ $etd?->format('d.m.Y') ?? '—' }}</td>
    <td @if($etaOverdue) style="color: #ff5252; font-weight: 500;" @endif>{{ $eta?->format('d.m.Y') ?? '—' }}</td>
    <td>
        <span class="{{ $shipment->statusBadgeClass() }}" style="padding: 4px 8px; font-weight: 500;">{{ $shipment->status ?? '—' }}</span>
    </td>
    <td>
        <button type="button"
            class="btn btn-outline-teal py-1 px-2 mark-arrived-btn"
            style="font-size: 11px; height: 26px; border-color: #ddd; background: #fff;"
            data-shipment-id="{{ $shipment->id }}"
            data-shipment-number="{{ $shipment->shipment_number }}"
            data-mark-arrived-url="{{ route('shipments.mark-as-arrived', $shipment->id) }}">Mark as arrived</button>
    </td>
    <td class="reminder-sent-date" data-shipment-id="{{ $shipment->id }}">{{ $lastReminderSent }}</td>
    <td>
        <button type="button"
            class="btn btn-outline-teal py-1 pl-2 pr-2 send-reminder-btn"
            style="font-size: 10px; height: 24px;"
            data-shipment-id="{{ $shipment->id }}"
            data-preview-url="{{ route('shipments.delivery-status-reminder-mail.preview', $shipment->id) }}"
            data-send-url="{{ route('shipments.delivery-status-reminder-mail.send', $shipment->id) }}"
            data-eml-url="{{ route('shipments.delivery-status-reminder-mail', $shipment->id) }}"
            data-eml-filename="delivery-status-request-{{ $shipment->shipment_number }}.eml">Send reminder</button>
    </td>
</tr>
@empty
<tr>
    <td colspan="13" class="text-center py-4 text-muted">No shipments found.</td>
</tr>
@endforelse
