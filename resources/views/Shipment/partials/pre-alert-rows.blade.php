@forelse ($shipments as $shipment)
@php
    $portCities = $portCities ?? [];
    $departureCity = $shipment->departureCityDisplay($portCities);
    $destinationCity = $shipment->destinationCityDisplay($portCities);
    $consigneeDisplay = $shipment->partyDisplay($shipment->consignee, $partyNames);
    $consigneeType = explode(':', (string) $shipment->consignee, 2)[0];
    $paReminder = $shipment->pre_alert_reminder;
    $paReminderOverdue = $paReminder && $paReminder->startOfDay()->lt(now()->startOfDay());
@endphp
<tr
    data-customers="{{ $shipment->customer_names->implode(',') }}"
    data-vessels="{{ $shipment->vessel_names->implode(',') }}"
    data-shipment-number="{{ $shipment->shipment_number }}"
    data-destination="{{ $destinationCity }}"
    data-account-manager="{{ $shipment->accountManager?->name ?? '' }}"
    data-created-by="{{ $shipment->creator?->name ?? '' }}"
    data-status="{{ $shipment->status ?? '' }}"
    data-has-etl="{{ $shipment->hasEtlStock() ? '1' : '0' }}"
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
    <td>
        @if ($consigneeType === 'hub')
            <span class="consignee-row consignee-hub-agent"><i class="ti-home consignee-hub-icon" title="Hub"></i><span class="consignee-hub-agent-text">{{ $consigneeDisplay }}</span></span>
        @elseif ($consigneeType === 'agent')
            <span class="consignee-row consignee-hub-agent"><i class="ti-user consignee-hub-icon" title="Agent"></i><span class="consignee-hub-agent-text">{{ $consigneeDisplay }}</span></span>
        @else
            <span class="consignee-row"><span class="consignee-hub-icon consignee-hub-icon-spacer"></span><span class="consignee-hub-agent-text">{{ $consigneeDisplay }}</span></span>
        @endif
    </td>
    <td title="{{ $departureCity }}">{{ $departureCity }}</td>
    <td title="{{ $destinationCity }}">{{ $destinationCity }}</td>
    <td>{{ $shipment->total_weight_display }}</td>
    <td>{{ $shipment->deadline_arrival?->format('d.m.Y') ?? '—' }}</td>
    <td>
        <span class="{{ $shipment->statusBadgeClass() }}" style="padding: 4px 8px; font-weight: 500;">
            {{ $shipment->status ?? '—' }}
        </span>
    </td>
    <td @if($paReminderOverdue) style="color: #ff5252;" @endif>{{ $paReminder?->format('d.m.Y') ?? '—' }}</td>
    <td>{{ $shipment->accountManager?->name ?? '—' }}</td>
    <td class="reminder-sent-count" data-shipment-id="{{ $shipment->id }}">{{ $shipment->reminder_sent_count }}</td>
    <td>
        <button type="button"
            class="btn btn-outline-teal py-1 pl-2 pr-2 send-reminder-btn"
            style="font-size: 10px; height: 24px;"
            data-shipment-id="{{ $shipment->id }}"
            data-preview-url="{{ route('shipments.pre-alert-reminder-mail.preview', $shipment->id) }}"
            data-send-url="{{ route('shipments.pre-alert-reminder-mail.dispatch', $shipment->id) }}"
            data-eml-url="{{ route('shipments.pre-alert-reminder-mail', $shipment->id) }}"
            data-eml-filename="pre-alert-reminder-{{ $shipment->shipment_number }}.eml">Send reminder</button>
    </td>
</tr>
@empty
<tr>
    <td colspan="14" class="text-center py-4 text-muted">No shipments found.</td>
</tr>
@endforelse
