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
        <td><a href="{{ route('hub.show', $hub->id) }}" style="color: #3b82f6;">{{ $hub->hub_name }}</a></td>
        <td>{{ $hub->code }}</td>
        <td>{{ $hub->city }}</td>
        <td>
            @if($hub->country)
                @if(!empty($countryFlags[$hub->country]))
                    <img src="{{ $countryFlags[$hub->country] }}" class="country-flag" alt="">
                @endif
                {{ $hub->country }}
            @else
                —
            @endif
        </td>
        <td>{{ $hub->phone_number }}</td>
        <td>{{ $hub->email }}</td>
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
            <a href="{{ route('hub.show', $hub->id) }}" style="color: #ccc; margin-right: 8px;"><i class="ti-pencil"></i></a>
            @if($canWriteAdministration)
            <a href="javascript:void(0)" class="delete-hub" data-id="{{ $hub->id }}" data-name="{{ $hub->hub_name }}" style="color: #ccc;" title="Delete hub"><i class="ti-trash"></i></a>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" style="text-align:center; padding:40px; color:#9ca3af;">
            No hubs found.
        </td>
    </tr>
@endforelse
