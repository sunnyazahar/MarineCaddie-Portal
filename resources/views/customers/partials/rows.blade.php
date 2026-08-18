@forelse($customers as $customer)
    @php
        $mainContact = $customer->contacts->first();
        $mainContactName = $mainContact?->name ?? '';
        $responsibleOffice = $customer->responsible?->accountManager?->office?->office_short_name ?? '';
        $accountManager = $customer->responsible?->accountManager?->name ?? '';
        $salesManager = $customer->responsible?->salesManager?->name ?? '';
        $countryName = $customer->primaryAddress?->country?->name ?? '';
        $searchText = trim(implode(' ', array_filter([
            $customer->customer_name,
            $customer->customer_number,
            $customer->email,
            $customer->phone,
            $mainContactName,
            $responsibleOffice,
            $accountManager,
            $salesManager,
            $countryName,
        ])));
    @endphp
    <tr
        data-search-text="{{ $searchText }}"
        data-responsible-office="{{ $responsibleOffice }}"
        data-account-manager="{{ $accountManager }}"
        data-sales-manager="{{ $salesManager }}"
        data-country="{{ $countryName }}"
        data-is-inactive="0"
    >
        <td>
            <a href="{{ route('customers.edit', $customer->id) }}"
                style="color: #3b82f6; font-weight: 500;">
                {{ $customer->customer_name }}
            </a>
        </td>
        <td>{{ $customer->responsible->accountManager->office->phone_number ?? '—' }}</td>
        <td>{{ $responsibleOffice ?: '—' }}</td>
        <td>{{ $accountManager ?: '—' }}</td>
        <td>
            <span class="label label-success">Active</span>
        </td>
        <td class="text-right">
            <a href="{{ route('customers.edit', $customer->id) }}"
                style="color: #ccc;"><i class="ti-pencil"></i></a>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="text-center py-4 text-muted">No customers found.</td>
    </tr>
@endforelse
