@forelse($hubs as $hub)
    @php
        $addressSearch = trim(implode(' ', array_filter([
            $hub->hub_address,
            $hub->office_address,
            $hub->district_state,
            $hub->zip_code,
        ])));
        $isInactive = (bool) $hub->hide_in_portal;
    @endphp
    <tr
        data-hub-name="{{ $hub->hub_name }}"
        data-code="{{ $hub->code }}"
        data-address="{{ $addressSearch }}"
        data-city="{{ $hub->city }}"
        data-country="{{ $hub->country }}"
        data-is-inactive="{{ $isInactive ? '1' : '0' }}"
    >
        <td>
            <a href="{{ route('hub.show', $hub->id) }}" class="hub-name-link">{{ $hub->hub_name }}</a>
        </td>
        <td>{{ $hub->code ?: '—' }}</td>
        <td>{{ $hub->city ?: '—' }}</td>
        <td>
            @if ($hub->country)
                <span class="hub-country-cell">
                    @if (!empty($countryFlags[$hub->country]))
                        <img src="{{ $countryFlags[$hub->country] }}" class="hub-country-flag" alt="">
                    @endif
                    <span class="hub-country-name">{{ $hub->country }}</span>
                </span>
            @else
                —
            @endif
        </td>
        <td>{{ $hub->phone_number ?: '—' }}</td>
        <td>
            @if ($hub->email)
                <a href="mailto:{{ $hub->email }}" class="hub-email-link">{{ $hub->email }}</a>
            @else
                —
            @endif
        </td>
        <td>
            <button type="button"
                class="hub-status-toggle {{ $isInactive ? 'is-inactive' : 'is-active' }}"
                data-id="{{ $hub->id }}"
                data-name="{{ $hub->hub_name }}"
                data-status="{{ $isInactive ? 'inactive' : 'active' }}"
                data-url="{{ route('hub.status.update', $hub->id) }}"
                title="Click to change status">
                {{ $isInactive ? 'Inactive' : 'Active' }}
            </button>
        </td>
        <td class="text-right">
            <div class="hub-action-icons">
                <a href="{{ route('hub.show', $hub->id) }}" class="hub-action-btn" title="Edit hub">
                    <i class="ti-pencil"></i>
                </a>
                @if ($canWriteAdministration)
                    <a href="javascript:void(0)" class="hub-action-btn delete-hub" data-id="{{ $hub->id }}" data-name="{{ $hub->hub_name }}" title="Delete hub">
                        <i class="ti-trash"></i>
                    </a>
                @endif
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="text-center text-muted py-4">No hubs found.</td>
    </tr>
@endforelse
