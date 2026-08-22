@forelse($customers as $customer)
    @php
        $mainContact = $customer->contacts->first();
        $mainContactName = $mainContact?->name ?? '';
        $responsibleOffice = $customer->responsible?->accountManager?->office?->office_short_name ?? '';
        $accountManager = $customer->responsible?->accountManager?->name ?? '';
        $salesManager = $customer->responsible?->salesManager?->name ?? '';
        $countryName = $customer->primaryAddress?->country?->name ?? '';
    @endphp
    <tr
        data-search-text="{{ trim(implode(' ', array_filter([
            $customer->customer_name,
            $customer->customer_number,
            $customer->email,
            $customer->phone,
            $mainContactName,
            $responsibleOffice,
            $accountManager,
            $salesManager,
            $countryName,
        ]))) }}"
        data-responsible-office="{{ $responsibleOffice }}"
        data-account-manager="{{ $accountManager }}"
        data-sales-manager="{{ $salesManager }}"
        data-country="{{ $countryName }}"
    >
        <td>
            <a href="{{ route('customers.edit', $customer->id) }}" class="cust-name-link">{{ $customer->customer_name }}</a>
        </td>
        <td>{{ $mainContactName ?: '—' }}</td>
        <td>{{ $responsibleOffice ?: '—' }}</td>
        <td>{{ $accountManager ?: '—' }}</td>
        <td>
            <span class="cust-status-pill">Active</span>
        </td>
        <td class="text-right">
            <div class="cust-action-icons">
                <a href="{{ route('customers.edit', $customer->id) }}" class="cust-action-btn" title="Edit customer">
                    <i class="ti-pencil"></i>
                </a>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="text-center text-muted py-4">No customers found.</td>
    </tr>
@endforelse
