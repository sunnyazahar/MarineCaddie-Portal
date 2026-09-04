@forelse ($shipments as $shipment)
    @php
        $consigneeDisplay = $shipment->partyDisplay($shipment->consignee, $partyNames);
        $consigneeType = explode(':', (string) $shipment->consignee, 2)[0];
        $customerNames = $shipment->customerNamesFromVessels($vesselCustomerMap ?? []);
        $customerDisplay = $shipment->formatNamesDisplay($customerNames);
        $customerDisplayShort = $shipment->formatNamesDisplayShort($customerNames);
        $serviceReferenceDisplay = $shipment->service_reference_display;
        $portCities = $portCities ?? [];
        $departureCity = $shipment->departureCityDisplay($portCities);
        $destinationCity = $shipment->destinationCityDisplay($portCities);
    @endphp
    <tr
        data-customers="{{ $customerNames->implode(',') }}"
        data-vessels="{{ $shipment->vessel_names->implode(',') }}"
        data-shipment-number="{{ $shipment->shipment_number }}"
        data-service-reference="{{ $serviceReferenceDisplay }}"
        data-consignee="{{ $consigneeDisplay }}"
        data-departure-port-code="{{ $shipment->departure_port_code ?? '' }}"
        data-destination="{{ $destinationCity }}"
        data-service="{{ $shipment->service ?? '' }}"
        data-po-numbers="{{ $shipment->po_numbers_display }}"
        data-account-manager="{{ $shipment->accountManager?->name ?? '' }}"
        data-created-by="{{ $shipment->creator?->name ?? '' }}"
        data-office="{{ $shipment->accountManager?->office?->office_name ?? '' }}"
        data-creation-date="{{ $shipment->created_at?->format('Y-m-d') ?? '' }}"
        data-status="{{ $shipment->status ?? '' }}"
    >
        <td title="{{ $shipment->shipment_number }}">
            <a href="{{ route('shipments.edit', $shipment->id) }}" class="text-primary">{{ $shipment->shipment_number }}</a>
            @if ($shipment->hasOpenIrregularities())
                <i class="ti-alert text-danger ml-2" title="Open irregularities"></i>
            @endif
        </td>
        <td title="{{ $customerDisplay }}">
            @if ($customerNames->count() > 2)
                <span class="cell-ellipsis" title="{{ $customerDisplay }}" style="cursor: help;">{{ $customerDisplayShort }}</span>
            @else
                <span class="cell-ellipsis">{{ $customerDisplay }}</span>
            @endif
        </td>
        <td title="{{ $shipment->vessel_display }}">
            @if ($shipment->vessel_names->count() > 2)
                <span title="{{ $shipment->vessel_display }}" style="cursor: help;">{{ $shipment->vessel_display_short }}</span>
            @else
                {{ $shipment->vessel_display }}
            @endif
        </td>
        <td title="{{ $shipment->service ?? '—' }}">{{ $shipment->service ?? '—' }}</td>
        <td title="{{ $serviceReferenceDisplay }}">
            @if ($shipment->service_reference_values->count() > 2)
                <span title="{{ $shipment->service_reference_display }}" style="cursor: help;">{{ $shipment->service_reference_display_short }}</span>
            @else
                {{ $shipment->service_reference_display }}
            @endif
        </td>
        <td class="consignee-cell">
            @if ($consigneeType === 'hub')
                <span class="consignee-row consignee-hub-agent">
                    <span class="consignee-icon-slot"><i class="ti-home" title="Hub"></i></span>
                    <span class="consignee-hub-agent-text" title="{{ $consigneeDisplay }}">{{ $consigneeDisplay }}</span>
                </span>
            @elseif ($consigneeType === 'agent')
                <span class="consignee-row consignee-hub-agent">
                    <span class="consignee-icon-slot"><i class="ti-user" title="Agent"></i></span>
                    <span class="consignee-hub-agent-text" title="{{ $consigneeDisplay }}">{{ $consigneeDisplay }}</span>
                </span>
            @else
                <span class="consignee-row">
                    <span class="consignee-icon-slot"></span>
                    <span class="consignee-hub-agent-text" title="{{ $consigneeDisplay }}">{{ $consigneeDisplay }}</span>
                </span>
            @endif
        </td>
        <td title="{{ $departureCity }}">{{ $departureCity }}</td>
        <td title="{{ $destinationCity }}">{{ $destinationCity }}</td>
        <td>{{ $shipment->deadline_arrival?->format('d.m.Y') ?? '—' }}</td>
        <td>{{ $shipment->pre_alert_reminder?->format('d.m.Y') ?? '—' }}</td>
        <td><label class="{{ $shipment->statusBadgeClass() }}">{{ $shipment->status }}</label></td>
    </tr>
@empty
    <tr>
        <td colspan="11" class="text-center py-4 text-muted">No shipments found.</td>
    </tr>
@endforelse
