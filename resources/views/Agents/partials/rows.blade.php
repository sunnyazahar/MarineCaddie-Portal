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
            <a href="{{ route('agents.edit', ['id' => $agent->id]) }}" class="agent-name-link">
                {{ $agent->agent_name }}
            </a>
        </td>
        <td>{{ $agent->code ?: '—' }}</td>
        <td>{{ $agent->city ?: '—' }}</td>
        <td>
            @if ($countryName)
                <span class="agent-country-cell">
                    @if (!empty($agent->country->flag_url))
                        <img src="{{ $agent->country->flag_url }}" class="agent-country-flag" alt="">
                    @endif
                    <span class="agent-country-name">{{ $countryName }}</span>
                </span>
            @else
                —
            @endif
        </td>
        <td>{{ $agent->phone ?: '—' }}</td>
        <td>
            @if ($agent->email)
                <a href="mailto:{{ $agent->email }}" class="agent-email-link">{{ $agent->email }}</a>
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
        <td class="text-right">
            <div class="agent-action-icons">
                <a href="{{ route('agents.edit', ['id' => $agent->id]) }}" class="agent-action-btn" title="Edit agent">
                    <i class="ti-pencil"></i>
                </a>
                @if ($canWriteAdministration)
                    <a href="javascript:void(0)" class="agent-action-btn delete-agent" data-id="{{ $agent->id }}" data-name="{{ $agent->agent_name }}" title="Delete agent">
                        <i class="ti-trash"></i>
                    </a>
                @endif
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="9" class="text-center text-muted py-4">No agents found.</td>
    </tr>
@endforelse
