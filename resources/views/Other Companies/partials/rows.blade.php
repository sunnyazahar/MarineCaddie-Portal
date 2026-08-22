@forelse($companies as $company)
    @php
        $addressSearch = trim(implode(' ', array_filter([
            $company->street_address,
            $company->office_street_address,
            $company->district_state,
            $company->zip_code,
        ])));
        $countryName = $company->country->name ?? '';
        $typeLabel = $company->company_type ? ucfirst(str_replace('_', ' ', $company->company_type)) : '';
    @endphp
    <tr
        data-company-name="{{ $company->company_name }}"
        data-code="{{ $company->code }}"
        data-address="{{ $addressSearch }}"
        data-city="{{ $company->city }}"
        data-country="{{ $countryName }}"
    >
        <td>
            <a href="{{ route('other-companies.edit', $company->id) }}" class="oc-name-link">{{ $company->company_name }}</a>
        </td>
        <td>{{ $company->code ?: '—' }}</td>
        <td>{{ $typeLabel ?: '—' }}</td>
        <td>{{ $company->street_address ? Str::limit($company->street_address, 40) : '—' }}</td>
        <td>{{ $company->city ?: '—' }}</td>
        <td>
            @if ($countryName)
                <span class="oc-country-cell">
                    @if ($company->country?->flag_url)
                        <img src="{{ $company->country->flag_url }}" class="oc-country-flag" alt="">
                    @endif
                    <span class="oc-country-name">{{ $countryName }}</span>
                </span>
            @else
                —
            @endif
        </td>
        <td>{{ $company->phone_number ?: '—' }}</td>
        <td>
            @if ($company->email)
                <a href="mailto:{{ $company->email }}" class="oc-email-link">{{ Str::limit($company->email, 32) }}</a>
            @else
                —
            @endif
        </td>
        <td>
            <span class="oc-status-pill">Active</span>
        </td>
        <td class="text-right">
            <div class="oc-action-icons">
                <a href="{{ route('other-companies.edit', $company->id) }}" class="oc-action-btn" title="Edit company">
                    <i class="ti-pencil"></i>
                </a>
                @if ($canWriteAdministration)
                    <a href="javascript:void(0)" class="oc-action-btn delete-other-company" data-id="{{ $company->id }}" data-name="{{ $company->company_name }}" title="Delete company">
                        <i class="ti-trash"></i>
                    </a>
                @endif
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="10" class="text-center text-muted py-4">No companies found.</td>
    </tr>
@endforelse
