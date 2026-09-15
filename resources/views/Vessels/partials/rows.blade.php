@forelse ($vessels as $vessel)
    @php
        $alias = trim((string) ($vessel->vessel_name_alias ?? ''));
        $type = trim((string) ($vessel->vessel_type_alias ?? ''));
        $imo = trim((string) ($vessel->vessel_imo ?? ''));
        $isInactive = (bool) ($vessel->inactive_vessel ?? false);
        $isBlocked = (bool) (($vessel->sanction_blocked ?? false) || ($vessel->financially_blocked ?? false));
        if ($isInactive) {
            $statusLabel = 'Inactive';
            $statusClass = 'is-inactive';
        } elseif ($isBlocked) {
            $statusLabel = 'Blocked';
            $statusClass = 'is-blocked';
        } else {
            $statusLabel = 'Active';
            $statusClass = 'is-active';
        }
    @endphp
    <tr>
        <td>
            <div class="vessel-name-cell">
                <a href="{{ route('customers.vessels.edit', $vessel->id) }}" class="vessel-name-link">{{ $vessel->vessel }}</a>
                @if ($alias !== '' && strcasecmp($alias, (string) $vessel->vessel) !== 0)
                    <span class="vessel-alias-meta">{{ $alias }}</span>
                @endif
            </div>
        </td>
        <td>
            @if ($imo !== '')
                <span class="vessel-imo">{{ $imo }}</span>
            @else
                <span class="vessel-muted">—</span>
            @endif
        </td>
        <td>
            @if ($type !== '')
                <span class="vessel-type-chip">{{ $type }}</span>
            @else
                <span class="vessel-muted">—</span>
            @endif
        </td>
        <td>
            @if ($vessel->customer)
                <a href="{{ route('customers.edit', $vessel->customer_id) }}#vessels" class="vessel-customer-link">{{ $vessel->customer->customer_name }}</a>
            @else
                <span class="vessel-muted">—</span>
            @endif
        </td>
        <td>
            <span class="vessel-status-pill {{ $statusClass }}">{{ $statusLabel }}</span>
        </td>
        <td class="text-right">
            <a href="{{ route('customers.vessels.edit', $vessel->id) }}" class="vessel-action-btn" aria-label="Edit vessel">
                <i class="ti-pencil"></i>
            </a>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="text-center py-4 text-muted">No vessels found.</td>
    </tr>
@endforelse
