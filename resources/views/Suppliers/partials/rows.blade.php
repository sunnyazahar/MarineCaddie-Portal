@forelse($suppliers as $supplier)
    @php
        $countryName = $supplier->country->name ?? '';
    @endphp
    <tr>
        <td>
            <a href="{{ route('suppliers.edit', $supplier->id) }}" class="sup-name-link">{{ $supplier->supplier_name }}</a>
        </td>
        <td title="{{ $supplier->supplier_address }}">
            {{ $supplier->supplier_address ? Str::limit($supplier->supplier_address, 48) : '—' }}
        </td>
        <td>{{ $supplier->city ?: '—' }}</td>
        <td>
            @if ($countryName)
                <span class="sup-country-cell">
                    @if ($supplier->country?->flag_url)
                        <img src="{{ $supplier->country->flag_url }}" class="sup-country-flag" alt="">
                    @endif
                    <span class="sup-country-name">{{ $countryName }}</span>
                </span>
            @else
                —
            @endif
        </td>
        <td>{{ $supplier->phone_number ?: '—' }}</td>
        <td>
            @if ($supplier->email)
                <a href="mailto:{{ $supplier->email }}" class="sup-email-link">{{ Str::limit($supplier->email, 32) }}</a>
            @else
                —
            @endif
        </td>
        <td class="text-right">
            <div class="sup-action-icons">
                <a href="{{ route('suppliers.edit', $supplier->id) }}" class="sup-action-btn" title="Edit supplier">
                    <i class="ti-pencil"></i>
                </a>
                @if ($canWriteAdministration)
                    <a href="javascript:void(0)" class="sup-action-btn delete-supplier" data-id="{{ $supplier->id }}" data-name="{{ $supplier->supplier_name }}" title="Delete supplier">
                        <i class="ti-trash"></i>
                    </a>
                @endif
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center text-muted py-4">No suppliers found.</td>
    </tr>
@endforelse
