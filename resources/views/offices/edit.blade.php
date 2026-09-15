@extends('layouts.app')

@section('styles')
    @include('offices.partials.edit-page-styles')
@endsection

@section('content')
    <script>document.body.classList.add('edit-office-page');</script>

    @include('layouts.partials.pcoded-shell-start', ['pageWrapperClass' => 'p-0'])

    <div class="edit-office-page">
        <div class="edit-office-hero">
            <div class="edit-office-hero-main">
                <span class="edit-office-hero-icon" aria-hidden="true">
                    <i class="ti-home"></i>
                </span>
                <div>
                    <p class="edit-office-kicker">Administration</p>
                    <h1 class="edit-office-title">{{ $office->office_name }}</h1>
                    <p class="edit-office-sub">Edit office details, users, billing settings, and bank accounts.</p>
                </div>
            </div>
            <a href="{{ route('offices.index') }}" class="edit-office-back">
                <i class="ti-arrow-left"></i> Back to offices
            </a>
        </div>

        <div class="edit-office-meta">
            <span class="edit-office-meta-pill">Office ID <strong>{{ $office->id }}</strong></span>
            @if ((int) $office->status === 1)
                <span class="edit-office-meta-pill is-active">Status <strong>Active</strong></span>
            @else
                <span class="edit-office-meta-pill is-inactive">Status <strong>Inactive</strong></span>
            @endif
            @if ($office->office_short_name)
                <span class="edit-office-meta-pill">Short name <strong>{{ $office->office_short_name }}</strong></span>
            @endif
        </div>

        <div class="edit-office-tabs">
            <a href="javascript:void(0)" class="edit-office-tab active" data-tab="office-details">Office details</a>
            <a href="javascript:void(0)" class="edit-office-tab" data-tab="operations-users">Operations users</a>
            <a href="javascript:void(0)" class="edit-office-tab" data-tab="accounting-users">Accounting users</a>
            <a href="javascript:void(0)" class="edit-office-tab" data-tab="manager-users">Manager users</a>
        </div>

        <div class="edit-office-card">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show edit-office-alert" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show edit-office-alert" role="alert">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show edit-office-alert" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            @endif

            <div id="office-details" class="tab-pane active">
                <form id="officeEditForm" action="{{ route('offices.update', $office->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="{{ old('status', (string) $office->status) }}">
                    <input type="hidden" name="eori_number" value="{{ old('eori_number', $office->eori_number) }}">
                    <input type="hidden" name="postal_address" value="{{ old('postal_address', $office->postal_address) }}">
                    <input type="hidden" name="postal_city" value="{{ old('postal_city', $office->postal_city) }}">
                    <input type="hidden" name="postal_district_state" value="{{ old('postal_district_state', $office->postal_district_state) }}">
                    <input type="hidden" name="postal_zip_code" value="{{ old('postal_zip_code', $office->postal_zip_code) }}">
                    <input type="hidden" name="office_country_id" value="{{ old('office_country_id', $office->office_country_id) }}">
                    <input type="hidden" name="vat_country_specific_name" value="{{ old('vat_country_specific_name', $office->vat_country_specific_name) }}">
                    <input type="hidden" name="use_vat_check" value="{{ old('use_vat_check', $office->use_vat_check ? '1' : '0') }}">
                    <input type="hidden" name="show_imo" value="{{ old('show_imo', $office->show_imo ? '1' : '0') }}">
                    <input type="hidden" name="enable_reader" value="{{ old('enable_reader', $office->enable_reader ? '1' : '0') }}">

                    <div class="office-form-container">
                        <div class="office-pillars">
                            <div class="office-pillar">
                                <div class="office-pillar__title">
                                    <span class="office-pillar__title-text">Office information</span>
                                </div>

                                <div class="office-primary-row">
                                    <div class="form-group-custom">
                                        <label class="form-label-custom">Legal office name</label>
                                        <input type="text" name="office_name" class="form-control-custom"
                                            value="{{ old('office_name', $office->office_name) }}" required>
                                    </div>

                                    <div class="form-group-custom">
                                        <label class="form-label-custom">Office short name</label>
                                        <input type="text" name="office_short_name" class="form-control-custom"
                                            value="{{ old('office_short_name', $office->office_short_name) }}">
                                    </div>
                                </div>

                                <div class="office-primary-row">
                                    <div class="form-group-custom">
                                        <label class="form-label-custom">Office phone number</label>
                                        <input type="text" name="phone_number" class="form-control-custom"
                                            value="{{ old('phone_number', $office->phone_number) }}">
                                    </div>

                                    <div class="form-group-custom">
                                        <label class="form-label-custom">Office email address</label>
                                        <input type="email" name="email" class="form-control-custom"
                                            value="{{ old('email', $office->email) }}">
                                    </div>
                                </div>

                                <div class="form-group-custom d-none">
                                    <label class="form-label-custom">Company id</label>
                                    <input type="text" class="form-control-custom" value="{{ $office->id }}" readonly>
                                </div>

                                <div class="form-group-custom d-none">
                                    <input type="text" name="customer_fm_number" class="form-control-custom"
                                        value="{{ old('customer_fm_number') }}">
                                </div>

                                <div class="office-section-shell">
                                    <p class="office-section-shell__title">Office address</p>

                                    <div class="form-group-custom">
                                        <label class="form-label-custom">Street address</label>
                                        <textarea name="address" class="form-textarea-custom" rows="3">{{ old('address', $office->address) }}</textarea>
                                    </div>

                                    <div class="address-sub-grid">
                                        <div class="form-group-custom">
                                            <label class="form-label-custom">City / Town</label>
                                            <input type="text" name="city" class="form-control-custom"
                                                value="{{ old('city', $office->city) }}">
                                        </div>
                                        <div class="form-group-custom">
                                            <label class="form-label-custom">State / Region</label>
                                            <input type="text" name="district_state" class="form-control-custom"
                                                value="{{ old('district_state', $office->district_state) }}">
                                        </div>
                                        <div class="form-group-custom">
                                            <label class="form-label-custom">Postal code</label>
                                            <input type="text" name="zip_code" class="form-control-custom"
                                                value="{{ old('zip_code', $office->zip_code) }}">
                                        </div>
                                    </div>

                                    <x-forms.country-select
                                        name="country_id"
                                        label="Country / Region"
                                        :countries="$countries"
                                        :value="$office->country_id"
                                        wrapperClass="form-group-custom"
                                        dropdownParent=".edit-office-card"
                                    />
                                </div>
                            </div>

                            <div class="office-pillar">
                                <div class="office-pillar__title">
                                    <span class="office-pillar__title-text">Billing details &amp; accounts</span>
                                </div>

                                <div class="billing-primary-row">
                                    <div class="form-group-custom">
                                        <label class="form-label-custom">Invoice currency</label>
                                        <select name="invoicing_currency" class="form-control-custom select2-simple">
                                            <option value="">Select currency</option>
                                            @foreach ($countries->pluck('currency')->unique()->filter()->sort() as $curr)
                                                <option value="{{ $curr }}" {{ old('invoicing_currency', $office->invoicing_currency) == $curr ? 'selected' : '' }}>{{ $curr }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group-custom">
                                        <label class="form-label-custom">Reporting currency</label>
                                        <select name="reporting_currency" class="form-control-custom select2-simple">
                                            <option value="">Select currency</option>
                                            @foreach ($countries->pluck('currency')->unique()->filter()->sort() as $curr)
                                                <option value="{{ $curr }}" {{ old('reporting_currency', $office->reporting_currency) == $curr ? 'selected' : '' }}>{{ $curr }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group-custom">
                                        <label class="form-label-custom">VAT treatment</label>
                                        <select name="vat_rates" class="form-control-custom">
                                            <option value="standard" {{ old('vat_rates', $office->vat_rates) == 'standard' ? 'selected' : '' }}>Standard</option>
                                            <option value="zero" {{ old('vat_rates', $office->vat_rates) == 'zero' ? 'selected' : '' }}>Zero</option>
                                            <option value="exempt" {{ old('vat_rates', $office->vat_rates) == 'exempt' ? 'selected' : '' }}>Exempt</option>
                                            <option value="Out of scope" {{ old('vat_rates', $office->vat_rates) == 'Out of scope' ? 'selected' : '' }}>Out of scope</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="billing-primary-row">
                                    <div class="form-group-custom">
                                        <label class="form-label-custom">VAT registration number</label>
                                        <input type="text" name="vat_number" class="form-control-custom"
                                            value="{{ old('vat_number', $office->vat_number) }}">
                                    </div>

                                    <div class="form-group-custom">
                                        <label class="form-label-custom">Billing email address</label>
                                        <input type="email" name="invoicing_emails" class="form-control-custom"
                                            value="{{ old('invoicing_emails', $office->invoicing_emails) }}">
                                    </div>

                                    <div class="form-group-custom">
                                        <label class="form-label-custom">Invoice heading</label>
                                        <input type="text" name="heading_invoice" class="form-control-custom"
                                            value="{{ old('heading_invoice', $office->heading_invoice) }}">
                                    </div>
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom">Invoice notes</label>
                                    <textarea name="information_invoice" class="form-textarea-custom" rows="4">{{ old('information_invoice', $office->information_invoice) }}</textarea>
                                </div>

                                <div class="office-section-shell">
                                    <div class="office-section-toolbar">
                                        <p class="office-section-shell__title">Bank accounts</p>
                                        <button type="button" class="btn-add-account">
                                            <i class="ti-plus"></i> Add bank account
                                        </button>
                                    </div>

                                    <div id="accounts-container">
                                        @foreach ($office->bankAccounts as $account)
                                            <div class="account-block">
                                                <div style="display:flex;justify-content:space-between;align-items:center;margin-top:4px;">
                                                    <label class="form-label-custom" style="margin-bottom:0;">Bank details</label>
                                                    <button type="button" class="remove-account-btn" title="Remove bank account">
                                                        <i class="feather icon-trash-2" style="font-size:16px;"></i>
                                                    </button>
                                                </div>
                                                <textarea name="bank[]" class="form-textarea-custom" rows="3" style="margin-top:8px;">{{ $account->bank }}</textarea>
                                                <div class="account-row-grid">
                                                    <div class="form-group-custom">
                                                        <label class="form-label-custom">Account currency</label>
                                                        <select name="currency[]" class="select-custom">
                                                            <option value="">Select currency</option>
                                                            @foreach ($countries->pluck('currency')->unique()->filter()->sort() as $curr)
                                                                <option value="{{ $curr }}" {{ $account->currency == $curr ? 'selected' : '' }}>{{ $curr }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group-custom">
                                                        <label class="form-label-custom">Bank account number</label>
                                                        <input type="text" name="account_number[]" class="form-control-custom" value="{{ $account->account_number }}">
                                                    </div>
                                                </div>
                                                <div class="account-row-grid">
                                                    <div class="form-group-custom">
                                                        <label class="form-label-custom">IBAN</label>
                                                        <input type="text" name="iban[]" class="form-control-custom" value="{{ $account->iban }}">
                                                    </div>
                                                    <div class="form-group-custom">
                                                        <label class="form-label-custom">SWIFT / BIC</label>
                                                        <input type="text" name="swift[]" class="form-control-custom" value="{{ $account->swift }}">
                                                    </div>
                                                </div>
                                                <div class="checkbox-group" style="margin-top:12px;">
                                                    <input type="checkbox" class="checkbox-custom main-account-checkbox" {{ $account->is_main_account ? 'checked' : '' }}>
                                                    <input type="hidden" name="is_main_account_status[]" class="main-account-hidden" value="{{ $account->is_main_account ? '1' : '0' }}">
                                                    <label class="checkbox-label">Set as primary account</label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>
            </div>

            <div id="operations-users" class="tab-pane edit-office-tab-pane">
                <div class="pane-header-actions">
                    <a href="{{ route('offices.operations_users.create', $office->id) }}" class="btn-pane-action">
                        Add operation user
                    </a>
                </div>
                                    <div class="ops-table-wrap">
                                    <table class="ops-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 40%;">Name</th>
                                                <th style="width: 30%;">Email</th>
                                                <th style="width: 15%;">Phone number</th>
                                                <th style="width: 10%;">Activated</th>
                                                <th style="width: 8%;"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($office->contacts->where('category', 'operations')->filter(fn ($contact) => (bool) $contact->status) as $contact)
                                                <tr>
                                                    <td><a href="{{ route('offices.operations_users.edit', ['office' => $office->id, 'contact' => $contact->id]) }}"
                                                            class="ops-name-link">{{ $contact->name }}</a></td>
                                                    <td><a href="mailto:{{ $contact->email }}"
                                                            class="ops-email-link">{{ $contact->email }}</a></td>
                                                    <td>{{ $contact->phone_number }}</td>
                                                    <td style="text-align: center;">
                                                        @if($contact->status)
                                                            <i class="fa fa-check activated-icon"></i>
                                                        @endif
                                                    </td>
                                                    <td style="text-align: right;">
                                                        <div class="ops-action-icons">
                                                            <a href="{{ route('offices.operations_users.edit', ['office' => $office->id, 'contact' => $contact->id]) }}" title="Edit user">
                                                                <i class="ti-pencil ops-action-icon"></i>
                                                            </a>
                                                            @if ($canWriteAdministration)
                                                                <button type="button"
                                                                    class="ops-action-delete delete-office-user"
                                                                    data-url="{{ route('offices.operations_users.destroy', ['office' => $office->id, 'contact' => $contact->id]) }}"
                                                                    data-name="{{ $contact->name }}"
                                                                    title="Delete user">
                                                                    <i class="ti-trash ops-action-icon"></i>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    </div>
                                </div>
            <div id="manager-users" class="tab-pane edit-office-tab-pane">
                <div class="pane-header-actions">
                    <a href="{{ route('offices.manager_users.create', $office->id) }}" class="btn-pane-action">
                        Add manager user
                    </a>
                </div>
                                    <div class="ops-table-wrap">
                                    <table class="ops-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 40%;">Name</th>
                                                <th style="width: 30%;">Email</th>
                                                <th style="width: 15%;">Phone number</th>
                                                <th style="width: 10%;">Activated</th>
                                                <th style="width: 8%;"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($office->contacts->where('category', 'manager')->filter(fn ($contact) => (bool) $contact->status) as $contact)
                                                <tr>
                                                    <td><a href="{{ route('offices.manager_users.edit', ['office' => $office->id, 'contact' => $contact->id]) }}"
                                                            class="ops-name-link">{{ $contact->name }}</a></td>
                                                    <td><a href="mailto:{{ $contact->email }}"
                                                            class="ops-email-link">{{ $contact->email }}</a></td>
                                                    <td>{{ $contact->phone_number }}</td>
                                                    <td style="text-align: center;">
                                                        @if($contact->status)
                                                            <i class="fa fa-check activated-icon"></i>
                                                        @endif
                                                    </td>
                                                    <td style="text-align: right;">
                                                        <div class="ops-action-icons">
                                                            <a href="{{ route('offices.manager_users.edit', ['office' => $office->id, 'contact' => $contact->id]) }}" title="Edit user">
                                                                <i class="ti-pencil ops-action-icon"></i>
                                                            </a>
                                                            @if ($canWriteAdministration)
                                                                <button type="button"
                                                                    class="ops-action-delete delete-office-user"
                                                                    data-url="{{ route('offices.manager_users.destroy', ['office' => $office->id, 'contact' => $contact->id]) }}"
                                                                    data-name="{{ $contact->name }}"
                                                                    title="Delete user">
                                                                    <i class="ti-trash ops-action-icon"></i>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    </div>
                                    @if($office->contacts->where('category', 'manager')->filter(fn ($contact) => (bool) $contact->status)->isEmpty())
                                        <div class="no-data-wrapper">
                                            <i class="fa fa-info-circle no-data-icon"></i>
                                            <div class="no-data-text">No data to show</div>
                                        </div>
                                    @endif
                                </div>
            <div id="accounting-users" class="tab-pane edit-office-tab-pane">
                <div class="pane-header-actions">
                    <a href="{{ route('offices.account_users.create', $office->id) }}" class="btn-pane-action">
                        Add account user
                    </a>
                </div>
                                    <div class="ops-table-wrap">
                                    <table class="ops-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 40%;">Name</th>
                                                <th style="width: 30%;">Email</th>
                                                <th style="width: 15%;">Phone number</th>
                                                <th style="width: 10%;">Activated</th>
                                                <th style="width: 8%;"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($office->contacts->where('category', 'account')->filter(fn ($contact) => (bool) $contact->status) as $contact)
                                                <tr>
                                                    <td><a href="{{ route('offices.account_users.edit', ['office' => $office->id, 'contact' => $contact->id]) }}"
                                                            class="ops-name-link">{{ $contact->name }}</a></td>
                                                    <td><a href="mailto:{{ $contact->email }}"
                                                            class="ops-email-link">{{ $contact->email }}</a></td>
                                                    <td>{{ $contact->phone_number }}</td>
                                                    <td style="text-align: center;">
                                                        @if($contact->status)
                                                            <i class="fa fa-check activated-icon"></i>
                                                        @endif
                                                    </td>
                                                    <td style="text-align: right;">
                                                        <div class="ops-action-icons">
                                                            <a href="{{ route('offices.account_users.edit', ['office' => $office->id, 'contact' => $contact->id]) }}" title="Edit user">
                                                                <i class="ti-pencil ops-action-icon"></i>
                                                            </a>
                                                            @if ($canWriteAdministration)
                                                                <button type="button"
                                                                    class="ops-action-delete delete-office-user"
                                                                    data-url="{{ route('offices.account_users.destroy', ['office' => $office->id, 'contact' => $contact->id]) }}"
                                                                    data-name="{{ $contact->name }}"
                                                                    title="Delete user">
                                                                    <i class="ti-trash ops-action-icon"></i>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    </div>
                                    @if($office->contacts->where('category', 'account')->filter(fn ($contact) => (bool) $contact->status)->isEmpty())
                                        <div class="no-data-wrapper">
                                            <i class="fa fa-info-circle no-data-icon"></i>
                                            <div class="no-data-text">No data to show</div>
                                        </div>
                                    @endif
                                </div>
        </div>{{-- .edit-office-card --}}

        <div class="edit-footer" id="office-edit-footer">
            <button type="submit" class="btn-save-custom" form="officeEditForm">Save office</button>
            <a href="{{ route('offices.index') }}" class="btn-cancel-custom">Cancel</a>
            <div class="audit-info">
                @include('partials.audit-info', ['record' => $office, 'bold' => true])
            </div>
        </div>
    </div>{{-- .edit-office-page --}}

    @include('layouts.partials.pcoded-shell-end')

<script>
        $(document).ready(function () {
            $('body').addClass('edit-office-page');

            function initSelect2() {
                $('.select2-simple, .select-custom').each(function () {
                    if (!$(this).hasClass('select2-hidden-accessible')) {
                        var $parent = $(this).closest('.office-pillar');
                        $(this).select2({
                            width: '100%',
                            dropdownParent: $parent.length ? $parent : $(this).parent()
                        });
                    }
                });
            }

            initSelect2();

            function activateOfficeTab(tabId) {
                if (!tabId || !$('#' + tabId).length || !$('.edit-office-tab[data-tab="' + tabId + '"]').length) {
                    return;
                }
                $('.edit-office-tab').removeClass('active');
                $('.edit-office-tab[data-tab="' + tabId + '"]').addClass('active');
                $('.tab-pane').removeClass('active');
                $('#' + tabId).addClass('active');
                $('#office-edit-footer').toggle(tabId === 'office-details');
            }

            $('.edit-office-tab').on('click', function () {
                var tabId = $(this).data('tab');
                activateOfficeTab(tabId);
                if (history.replaceState) {
                    history.replaceState(null, '', '#' + tabId);
                } else {
                    window.location.hash = tabId;
                }
            });

            var hashTab = window.location.hash.replace(/^#/, '');
            if (hashTab) {
                activateOfficeTab(hashTab);
            }

            var accountCount = {{ $office->bankAccounts->count() }};

            $('.btn-add-account').click(function () {
                accountCount++;
                var currencyOptions = `
                    <option value="">Select currency</option>
                    @foreach ($countries->pluck('currency')->unique()->filter()->sort() as $curr)
                        <option value="{{ $curr }}">{{ $curr }}</option>
                    @endforeach
                `;

                var $newAccount = $(`
                    <div class="account-block">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-top:4px;">
                            <label class="form-label-custom" style="margin-bottom:0;">Bank details</label>
                            <button type="button" class="remove-account-btn" title="Remove bank account">
                                <i class="feather icon-trash-2" style="font-size:16px;"></i>
                            </button>
                        </div>
                        <div class="form-group-custom">
                            <textarea name="bank[]" class="form-textarea-custom" rows="3" style="margin-top:8px;"></textarea>
                        </div>
                        <div class="account-row-grid">
                            <div class="form-group-custom">
                                <label class="form-label-custom">Account currency</label>
                                <select name="currency[]" class="select-custom">${currencyOptions}</select>
                            </div>
                            <div class="form-group-custom">
                                <label class="form-label-custom">Bank account number</label>
                                <input type="text" name="account_number[]" class="form-control-custom">
                            </div>
                        </div>
                        <div class="account-row-grid">
                            <div class="form-group-custom">
                                <label class="form-label-custom">IBAN</label>
                                <input type="text" name="iban[]" class="form-control-custom">
                            </div>
                            <div class="form-group-custom">
                                <label class="form-label-custom">SWIFT / BIC</label>
                                <input type="text" name="swift[]" class="form-control-custom">
                            </div>
                        </div>
                        <div class="checkbox-group" style="margin-top:12px;">
                            <input type="checkbox" class="checkbox-custom main-account-checkbox">
                            <input type="hidden" name="is_main_account_status[]" class="main-account-hidden" value="0">
                            <label class="checkbox-label">Set as primary account</label>
                        </div>
                    </div>
                `);

                $('#accounts-container').append($newAccount);

                $newAccount.find('.select-custom').select2({
                    width: '100%',
                    dropdownParent: $newAccount.closest('.office-pillar')
                });
            });

            $(document).on('click', '.remove-account-btn', function () {
                $(this).closest('.account-block').remove();
            });

            $(document).on('change', '.main-account-checkbox', function () {
                $('.main-account-checkbox').not(this).prop('checked', false);
                $('.main-account-hidden').val('0');
                if ($(this).is(':checked')) {
                    $(this).siblings('.main-account-hidden').val('1');
                }
            });

            $(document).on('select2:open', '.office-pillar select', function () {
                $('.office-pillar').css('z-index', '');
                $(this).closest('.office-pillar').css('z-index', 40);
            });

            $(document).on('select2:close', '.office-pillar select', function () {
                $(this).closest('.office-pillar').css('z-index', '');
            });

            $(document).on('click', '.delete-office-user', function () {
                var url = $(this).data('url');
                var name = $(this).data('name') || 'this user';
                var $row = $(this).closest('tr');

                swal({
                    title: 'Delete user?',
                    text: 'Are you sure you want to delete "' + name + '"?',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete',
                    cancelButtonText: 'Cancel',
                    closeOnConfirm: false,
                    closeOnCancel: true,
                    showLoaderOnConfirm: true
                }, function (isConfirm) {
                    if (!isConfirm) {
                        return;
                    }

                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            _method: 'DELETE'
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            if (response.success) {
                                swal({
                                    title: 'Deleted',
                                    text: response.message || 'User deleted successfully.',
                                    type: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                $row.remove();
                            } else {
                                swal('Error', response.message || 'Error deleting user.', 'error');
                            }
                        },
                        error: function (xhr) {
                            var message = (xhr.responseJSON && xhr.responseJSON.message)
                                ? xhr.responseJSON.message
                                : 'Error deleting user.';
                            swal('Error', message, 'error');
                        }
                    });
                });
            });

        });
    </script>
@include('partials.unsaved-changes-guard', ['formSelector' => '#officeEditForm', 'fallbackUrl' => route('offices.index')])
@endsection
