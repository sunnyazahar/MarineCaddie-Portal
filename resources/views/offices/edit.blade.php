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
            <a href="javascript:void(0)" class="edit-office-tab" data-tab="sales-users">Sales users</a>
            <a href="javascript:void(0)" class="edit-office-tab" data-tab="manager-users">Manager users</a>
            <a href="javascript:void(0)" class="edit-office-tab" data-tab="invoice-templates">Invoice templates</a>
            <a href="javascript:void(0)" class="edit-office-tab" data-tab="accounting-systems">Accounting systems</a>
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

                    <div class="office-form-container">
                        <div class="office-pillars">
                            <div class="office-pillar-col">
                                <div class="office-pillar">
                                    <div class="office-pillar__title">
                                        <span class="office-pillar__title-text">Office information</span>
                                    </div>

                                    <div class="form-group-custom">
                                        <label class="form-label-custom">Office name</label>
                                        <input type="text" name="office_name" class="form-control-custom"
                                            value="{{ old('office_name', $office->office_name) }}" required>
                                    </div>

                                    <div class="form-group-custom d-none">
                                        <label class="form-label-custom">Company id</label>
                                        <input type="text" class="form-control-custom" value="{{ $office->id }}" readonly>
                                    </div>

                                    <div class="form-group-custom">
                                        <label class="form-label-custom">Office short name</label>
                                        <input type="text" name="office_short_name" class="form-control-custom"
                                            value="{{ old('office_short_name', $office->office_short_name) }}">
                                    </div>

                                    <div class="form-group-custom d-none">
                                        <input type="text" name="customer_fm_number" class="form-control-custom"
                                            value="{{ old('customer_fm_number') }}">
                                    </div>

                                    <div class="form-group-custom">
                                        <label class="form-label-custom">Phone number (with country code)</label>
                                        <input type="text" name="phone_number" class="form-control-custom"
                                            value="{{ old('phone_number', $office->phone_number) }}">
                                    </div>

                                    <div class="form-group-custom">
                                        <label class="form-label-custom">Email</label>
                                        <input type="email" name="email" class="form-control-custom"
                                            value="{{ old('email', $office->email) }}">
                                    </div>

                                    <div class="form-group-custom">
                                        <label class="form-label-custom">EORI number</label>
                                        <input type="text" name="eori_number" class="form-control-custom"
                                            value="{{ old('eori_number', $office->eori_number) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="office-pillar-col">
                                <div class="office-pillar">
                                    <div class="office-pillar__title">
                                        <span class="office-pillar__title-text">Main address</span>
                                    </div>

                                    <div class="form-group-custom">
                                        <label class="form-label-custom">Office address</label>
                                        <textarea name="address" class="form-textarea-custom" rows="3">{{ old('address', $office->address) }}</textarea>
                                    </div>

                                    <div class="address-sub-grid">
                                        <div class="form-group-custom">
                                            <label class="form-label-custom">City</label>
                                            <input type="text" name="city" class="form-control-custom"
                                                value="{{ old('city', $office->city) }}">
                                        </div>
                                        <div class="form-group-custom">
                                            <label class="form-label-custom">District/state</label>
                                            <input type="text" name="district_state" class="form-control-custom"
                                                value="{{ old('district_state', $office->district_state) }}">
                                        </div>
                                        <div class="form-group-custom">
                                            <label class="form-label-custom">Zip code</label>
                                            <input type="text" name="zip_code" class="form-control-custom"
                                                value="{{ old('zip_code', $office->zip_code) }}">
                                        </div>
                                    </div>

                                    <x-forms.country-select
                                        name="country_id"
                                        label="Country"
                                        :countries="$countries"
                                        :value="$office->country_id"
                                        wrapperClass="form-group-custom"
                                        dropdownParent=".edit-office-card"
                                    />

                                    <div class="office-section-shell">
                                        <p class="office-section-shell__title">Office address (optional)</p>

                                        <div class="form-group-custom">
                                            <label class="form-label-custom">Postal address (optional)</label>
                                            <textarea name="postal_address" class="form-textarea-custom" rows="3">{{ old('postal_address', $office->postal_address) }}</textarea>
                                        </div>

                                        <div class="address-sub-grid">
                                            <div class="form-group-custom">
                                                <label class="form-label-custom">City</label>
                                                <input type="text" name="postal_city" class="form-control-custom"
                                                    value="{{ old('postal_city', $office->postal_city) }}">
                                            </div>
                                            <div class="form-group-custom">
                                                <label class="form-label-custom">District/state</label>
                                                <input type="text" name="postal_district_state" class="form-control-custom"
                                                    value="{{ old('postal_district_state', $office->postal_district_state) }}">
                                            </div>
                                            <div class="form-group-custom">
                                                <label class="form-label-custom">Zip code</label>
                                                <input type="text" name="postal_zip_code" class="form-control-custom"
                                                    value="{{ old('postal_zip_code', $office->postal_zip_code) }}">
                                            </div>
                                        </div>

                                        <x-forms.country-select
                                            name="office_country_id"
                                            label="Country"
                                            :countries="$countries"
                                            :value="$office->office_country_id"
                                            wrapperClass="form-group-custom"
                                            dropdownParent=".edit-office-card"
                                        />
                                    </div>
                                </div>
                            </div>

                            <div class="office-pillar-col">
                                <div class="office-pillar">
                                    <div class="office-pillar__title">
                                        <span class="office-pillar__title-text">Billing details</span>
                                    </div>

                                    <div class="address-sub-grid is-two-col">
                                        <div class="form-group-custom">
                                            <label class="form-label-custom">Invoicing currency</label>
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
                                    </div>

                                    <div class="form-group-custom">
                                        <label class="form-label-custom">VAT rates</label>
                                        <select name="vat_rates" class="form-control-custom">
                                            <option value="Out of scope" {{ old('vat_rates', $office->vat_rates) == 'Out of scope' ? 'selected' : '' }}>Out of scope</option>
                                        </select>
                                    </div>

                                    <div class="form-group-custom">
                                        <label class="form-label-custom">VAT country specific name</label>
                                        <input type="text" name="vat_country_specific_name" class="form-control-custom"
                                            value="{{ old('vat_country_specific_name', $office->vat_country_specific_name) }}">
                                    </div>

                                    <div class="form-group-custom">
                                        <label class="form-label-custom">VAT number</label>
                                        <input type="text" name="vat_number" class="form-control-custom"
                                            value="{{ old('vat_number', $office->vat_number) }}">
                                    </div>

                                    <div class="form-group-custom">
                                        <label class="form-label-custom">Invoicing e-mails</label>
                                        <input type="email" name="invoicing_emails" class="form-control-custom"
                                            value="{{ old('invoicing_emails', $office->invoicing_emails) }}">
                                    </div>

                                    <div class="form-group-custom">
                                        <label class="form-label-custom">Heading of invoice</label>
                                        <input type="text" name="heading_invoice" class="form-control-custom"
                                            value="{{ old('heading_invoice', $office->heading_invoice) }}">
                                    </div>

                                    <div class="form-group-custom">
                                        <label class="form-label-custom">Information on invoice</label>
                                        <textarea name="information_invoice" class="form-textarea-custom" rows="4">{{ old('information_invoice', $office->information_invoice) }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="office-pillar-col">
                                <div class="office-pillar">
                                    <div class="office-pillar__title">
                                        <span class="office-pillar__title-text">Accounts</span>
                                        <button type="button" class="btn-add-account">
                                            <i class="ti-plus"></i> Add account
                                        </button>
                                    </div>

                                    <div id="accounts-container">
                                        @foreach ($office->bankAccounts as $index => $account)
                                            <div class="account-block">
                                                <div class="account-block__header">
                                                    <p class="account-block__title">Account #{{ $index + 1 }}</p>
                                                    <button type="button" class="remove-account-btn" title="Delete account">
                                                        <i class="feather icon-trash-2"></i> Delete
                                                    </button>
                                                </div>

                                                <div class="form-group-custom">
                                                    <label class="form-label-custom">Bank</label>
                                                    <input type="text" name="bank[]" class="form-control-custom" value="{{ $account->bank }}">
                                                </div>
                                                <div class="form-group-custom">
                                                    <label class="form-label-custom">Currency</label>
                                                    <select name="currency[]" class="select-custom">
                                                        <option value="">Select currency</option>
                                                        @foreach ($countries->pluck('currency')->unique()->filter()->sort() as $curr)
                                                            <option value="{{ $curr }}" {{ $account->currency == $curr ? 'selected' : '' }}>{{ $curr }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group-custom">
                                                    <label class="form-label-custom">Account number</label>
                                                    <input type="text" name="account_number[]" class="form-control-custom" value="{{ $account->account_number }}">
                                                </div>
                                                <div class="form-group-custom">
                                                    <label class="form-label-custom">IBAN</label>
                                                    <input type="text" name="iban[]" class="form-control-custom" value="{{ $account->iban }}">
                                                </div>
                                                <div class="form-group-custom">
                                                    <label class="form-label-custom">SWIFT</label>
                                                    <input type="text" name="swift[]" class="form-control-custom" value="{{ $account->swift }}">
                                                </div>
                                                <div class="checkbox-group">
                                                    <input type="checkbox" class="checkbox-custom main-account-checkbox" {{ $account->is_main_account ? 'checked' : '' }}>
                                                    <input type="hidden" name="is_main_account_status[]" class="main-account-hidden" value="{{ $account->is_main_account ? '1' : '0' }}">
                                                    <label class="checkbox-label">Set as main account</label>
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
                                                <th style="width: 5%;"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($office->contacts->where('category', 'operations') as $contact)
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
                                                    <td style="text-align: right;"><a
                                                            href="{{ route('offices.operations_users.edit', ['office' => $office->id, 'contact' => $contact->id]) }}"><i
                                                                class="ti-pencil ops-action-icon"></i></a></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    </div>
                                </div>
            <div id="sales-users" class="tab-pane edit-office-tab-pane">
                <div class="pane-header-actions">
                    <a href="{{ route('offices.sales_users.create', $office->id) }}" class="btn-pane-action">
                        Add sales user
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
                                                <th style="width: 5%;"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($office->contacts->where('category', 'sales') as $contact)
                                                <tr>
                                                    <td><a href="{{ route('offices.sales_users.edit', ['office' => $office->id, 'contact' => $contact->id]) }}"
                                                            class="ops-name-link">{{ $contact->name }}</a></td>
                                                    <td><a href="mailto:{{ $contact->email }}"
                                                            class="ops-email-link">{{ $contact->email }}</a></td>
                                                    <td>{{ $contact->phone_number }}</td>
                                                    <td style="text-align: center;">
                                                        @if($contact->status)
                                                            <i class="fa fa-check activated-icon"></i>
                                                        @endif
                                                    </td>
                                                    <td style="text-align: right;"><a
                                                            href="{{ route('offices.sales_users.edit', ['office' => $office->id, 'contact' => $contact->id]) }}"><i
                                                                class="ti-pencil ops-action-icon"></i></a></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    </div>
                                    @if($office->contacts->where('category', 'sales')->isEmpty())
                                        <div class="no-data-wrapper">
                                            <i class="fa fa-info-circle no-data-icon"></i>
                                            <div class="no-data-text">No data to show</div>
                                        </div>
                                    @endif
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
                                                <th style="width: 5%;"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($office->contacts->where('category', 'manager') as $contact)
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
                                                    <td style="text-align: right;"><a
                                                            href="{{ route('offices.manager_users.edit', ['office' => $office->id, 'contact' => $contact->id]) }}"><i
                                                                class="ti-pencil ops-action-icon"></i></a></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    </div>
                                    @if($office->contacts->where('category', 'manager')->isEmpty())
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
                                                <th style="width: 5%;"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($office->contacts->where('category', 'account') as $contact)
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
                                                    <td style="text-align: right;"><a
                                                            href="{{ route('offices.account_users.edit', ['office' => $office->id, 'contact' => $contact->id]) }}"><i
                                                                class="ti-pencil ops-action-icon"></i></a></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    </div>
                                    @if($office->contacts->where('category', 'account')->isEmpty())
                                        <div class="no-data-wrapper">
                                            <i class="fa fa-info-circle no-data-icon"></i>
                                            <div class="no-data-text">No data to show</div>
                                        </div>
                                    @endif
                                </div>
            <div id="invoice-templates" class="tab-pane edit-office-tab-pane">
                                    <div class="pane-header-actions">
                                        <button type="button" class="btn-pane-action" id="btn-add-template">Add
                                            template</button>
                                    </div>
                                    <div class="ops-table-wrap">
                                    <table class="ops-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 50%;">Template name</th>
                                                <th style="width: 25%;">Last updated</th>
                                                <th style="width: 20%;">Updated by</th>
                                                <th style="width: 5%;"></th>
                                            </tr>
                                        </thead>
                                    </table>
                                    </div>
                                    <div class="no-data-wrapper">
                                        <i class="fa fa-info-circle no-data-icon"></i>
                                        <div class="no-data-text">No data to show</div>
                                    </div>
                                </div>
            <div id="accounting-systems" class="tab-pane edit-office-tab-pane">
                                    <div class="pane-header-actions">
                                        <button type="button" class="btn-pane-action" id="btn-add-accounting">Add accounting
                                            system configuration</button>
                                    </div>
                                    <div class="ops-table-wrap">
                                    <table class="ops-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 45%;">Name</th>
                                                <th style="width: 35%;">Accounting system</th>
                                                <th style="width: 15%;">Is current</th>
                                                <th style="width: 5%;"></th>
                                            </tr>
                                        </thead>
                                    </table>
                                    </div>
                                    <div class="no-data-wrapper">
                                        <i class="fa fa-info-circle no-data-icon"></i>
                                        <div class="no-data-text">No data to show</div>
                                    </div>
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

    <!-- Accounting System Modal -->
    <div class="custom-modal-overlay" id="modal-overlay">
        <div class="custom-modal">
            <div class="modal-body">
                <div class="modal-field">
                    <label class="modal-label">Name</label>
                    <input type="text" class="modal-input" placeholder="">
                </div>
                <div class="modal-field">
                    <label class="modal-label">Accounting system</label>
                    <select class="modal-input">
                        <option value="" selected disabled></option>
                        <option value="visma">Visma</option>
                        <option value="exact">Exact</option>
                        <option value="softone">Softone</option>
                    </select>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-modal-save">Save</button>
                    <a href="javascript:void(0)" class="btn-modal-cancel" id="btn-close-modal">Cancel</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Invoice Template Modal -->
    <div class="custom-modal-overlay" id="template-modal-overlay">
        <div class="custom-modal" style="width: 500px; max-height: 90vh; overflow-y: auto;">
            <div class="modal-body">
                <div class="modal-field">
                    <label class="modal-label" style="color: #ef4444;">Template name</label>
                    <input type="text" class="modal-input error" value="">
                    <div class="modal-error-msg">Provide value</div>
                </div>

                <div class="modal-section">
                    <div class="modal-section-title">Price charge</div>
                    <div id="price-charge-container">
                        <div class="dynamic-row">
                            <select class="modal-input">
                                <option value="" selected disabled></option>
                            </select>
                            <i class="ti-trash btn-remove-row"></i>
                        </div>
                        <div class="dynamic-row">
                            <select class="modal-input">
                                <option value="" selected disabled></option>
                            </select>
                            <i class="ti-trash btn-remove-row"></i>
                        </div>
                    </div>
                    <button type="button" class="btn-add-row" id="btn-add-price">Add price charge</button>
                </div>

                <div class="modal-section">
                    <div class="modal-section-title">Cost charge</div>
                    <div id="cost-charge-container">
                        <div class="dynamic-row">
                            <select class="modal-input">
                                <option value="" selected disabled></option>
                            </select>
                            <i class="ti-trash btn-remove-row"></i>
                        </div>
                        <div class="dynamic-row">
                            <select class="modal-input">
                                <option value="" selected disabled></option>
                            </select>
                            <i class="ti-trash btn-remove-row"></i>
                        </div>
                    </div>
                    <button type="button" class="btn-add-row" id="btn-add-cost">Add cost charge</button>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-modal-save">Save</button>
                    <a href="javascript:void(0)" class="btn-modal-cancel" id="btn-close-template-modal">Cancel</a>
                </div>
            </div>
        </div>
    </div>

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
                        <div class="account-block__header">
                            <p class="account-block__title">Account #${accountCount}</p>
                            <button type="button" class="remove-account-btn" title="Delete account">
                                <i class="feather icon-trash-2"></i> Delete
                            </button>
                        </div>
                        <div class="form-group-custom">
                            <label class="form-label-custom">Bank</label>
                            <input type="text" name="bank[]" class="form-control-custom">
                        </div>
                        <div class="form-group-custom">
                            <label class="form-label-custom">Currency</label>
                            <select name="currency[]" class="select-custom">${currencyOptions}</select>
                        </div>
                        <div class="form-group-custom">
                            <label class="form-label-custom">Account number</label>
                            <input type="text" name="account_number[]" class="form-control-custom">
                        </div>
                        <div class="form-group-custom">
                            <label class="form-label-custom">IBAN</label>
                            <input type="text" name="iban[]" class="form-control-custom">
                        </div>
                        <div class="form-group-custom">
                            <label class="form-label-custom">SWIFT</label>
                            <input type="text" name="swift[]" class="form-control-custom">
                        </div>
                        <div class="checkbox-group">
                            <input type="checkbox" class="checkbox-custom main-account-checkbox">
                            <input type="hidden" name="is_main_account_status[]" class="main-account-hidden" value="0">
                            <label class="checkbox-label">Set as main account</label>
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

            $('#btn-add-accounting').on('click', function () {
                $('#modal-overlay').css('display', 'flex');
            });

            $('#btn-close-modal, #modal-overlay').on('click', function (e) {
                if (e.target === this) $('#modal-overlay').hide();
            });
        });
    </script>
@include('partials.unsaved-changes-guard', ['formSelector' => '#officeEditForm', 'fallbackUrl' => route('offices.index')])
@endsection