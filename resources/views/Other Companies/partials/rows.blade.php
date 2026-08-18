@forelse($companies as $company)
                                                        @php
                                                            $addressSearch = trim(implode(' ', array_filter([
                                                                $company->street_address,
                                                                $company->office_street_address,
                                                                $company->district_state,
                                                                $company->zip_code,
                                                            ])));
                                                            $countryName = $company->country->name ?? '';
                                                        @endphp
                                                        <tr
                                                            data-company-name="{{ $company->company_name }}"
                                                            data-code="{{ $company->code }}"
                                                            data-address="{{ $addressSearch }}"
                                                            data-city="{{ $company->city }}"
                                                            data-country="{{ $countryName }}"
                                                            data-is-inactive="0"
                                                        >
                                                            <td><a href="{{ route('other-companies.edit', $company->id) }}" class="company-link">{{ $company->company_name }}</a></td>
                                                            <td>{{ $company->code }}</td>
                                                            <td>{{ $company->company_type }}</td>
                                                            <td>{{ Str::limit($company->street_address, 25) }}</td>
                                                            <td>{{ $company->city }}</td>
                                                            <td>
                                                                @if($company->country)
                                                                    @if($company->country->flag_url)
                                                                        <img src="{{ $company->country->flag_url }}" class="country-flag" alt="">
                                                                    @endif
                                                                    {{ $countryName }}
                                                                @endif
                                                            </td>
                                                            <td>{{ $company->phone_number }}</td>
                                                            <td>
                                                                @if($company->email)
                                                                    <a href="mailto:{{ $company->email }}" class="company-link">{{ Str::limit($company->email, 28) }}</a>
                                                                @endif
                                                            </td>
                                                            <td><span class="ti-check" style="color: green;"></span></td>
                                                            <td>
                                                                <div class="action-icons">
                                                                    <a href="{{ route('other-companies.edit', $company->id) }}"><i class="ti-pencil"></i></a>
                                                                    <a href="javascript:void(0)" class="delete-other-company" data-id="{{ $company->id }}" data-name="{{ $company->company_name }}" title="Delete company">
                                                                        <i class="ti-trash"></i>
                                                                    </a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        @empty
                                                        <tr>
                                                            <td colspan="10" style="text-align:center; padding: 30px; color: #999;">No companies found. <a href="{{ route('other-companies.create') }}">Add one</a>.</td>
                                                        </tr>
                                                        @endforelse
