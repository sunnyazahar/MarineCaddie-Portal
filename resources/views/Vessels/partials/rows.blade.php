@forelse ($vessels as $vessel)
    <tr>
        <td>
            <a href="{{ route('customers.vessels.edit', $vessel->id) }}" class="vessel-name-link">{{ $vessel->vessel }}</a>
        </td>
        <td>{{ $vessel->vessel_imo ?: '—' }}</td>
        <td>{{ $vessel->vessel_type_alias ?: '—' }}</td>
        <td>
            @if ($vessel->customer)
                <a href="{{ route('customers.edit', $vessel->customer_id) }}#vessels" class="vessel-customer-link">{{ $vessel->customer->customer_name }}</a>
            @else
                —
            @endif
        </td>
        <td class="text-right">
            <a href="{{ route('customers.vessels.edit', $vessel->id) }}" class="vessel-action-btn" aria-label="Edit vessel">
                <i class="ti-pencil"></i>
            </a>
        </td>
    @empty
    <tr>
        <td colspan="5" class="text-center py-4 text-muted">No vessels found.</td>
    </tr>
@endforelse
