@forelse($agents as $agent)
    @php
        $addressSearch = trim(implode(' ', array_filter([
            $agent->agent_address,
            $agent->office_address,
            $agent->district_state,
            $agent->zip_code,
        ])));
        $countryName = $agent->country->name ?? '';
        $typeLabel = $agent->agent_type ? ucfirst(str_replace('_', ' ', $agent->agent_type)) : '';
        $isInactive = ! ($agent->is_active ?? true);
    @endphp
    <tr
        data-agent-name="{{ $agent->agent_name }}"
        data-code="{{ $agent->code }}"
        data-address="{{ $addressSearch }}"
        data-city="{{ $agent->city }}"
        data-country="{{ $countryName }}"
        data-agent-type="{{ $agent->agent_type }}"
        data-is-inactive="{{ $isInactive ? '1' : '0' }}"
    >
        <td>
            <a href="{{ route('agents.edit', ['id' => $agent->id]) }}" class="agent-link">
                {{ $agent->agent_name }}
            </a>
        </td>
        <td>{{ $agent->code }}</td>
        <td>{{ $agent->city }}</td>
        <td>
            @if($countryName)
                @if(!empty($agent->country->flag_url))
                    <img src="{{ $agent->country->flag_url }}" class="country-flag" alt="">
                @endif
                {{ $countryName }}
            @else
                —
            @endif
        </td>
        <td>{{ $agent->phone }}</td>
        <td>
            @if($agent->email)
                <a href="mailto:{{ $agent->email }}" class="company-link">{{ $agent->email }}</a>
            @else
                —
            @endif
        </td>
        <td>{{ $typeLabel ?: '—' }}</td>
        <td>
            <button type="button"
                class="agent-status-toggle {{ $isInactive ? 'is-inactive' : 'is-active' }}"
                data-id="{{ $agent->id }}"
                data-name="{{ $agent->agent_name }}"
                data-status="{{ $isInactive ? 'inactive' : 'active' }}"
                data-url="{{ route('agents.status.update', $agent->id) }}"
                title="Click to change status">
                {{ $isInactive ? 'Inactive' : 'Active' }}
            </button>
        </td>
        <td>
            <div class="action-icons">
                <a href="{{ route('agents.edit', ['id' => $agent->id]) }}">
                    <i class="ti-pencil"></i>
                </a>
                @if($canWriteAdministration)
                <a href="javascript:void(0)" class="delete-agent" data-id="{{ $agent->id }}" data-name="{{ $agent->agent_name }}" title="Delete agent">
                    <i class="ti-trash"></i>
                </a>
                @endif
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="9" style="text-align:center; padding:40px; color:#9ca3af;">
            No agents found.
        </td>
    </tr>
@endforelse
