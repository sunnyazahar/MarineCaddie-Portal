@extends('layouts.app')

@section('styles')
    @include('customers.partials.edit-page-styles')
@endsection

@section('content')
    <script>document.body.classList.add('edit-customer-page');</script>

    @include('layouts.partials.pcoded-shell-start', ['pageWrapperClass' => 'p-0'])

    <div class="edit-customer-page">
        <div class="edit-customer-hero">
            <div class="edit-customer-hero-main">
                <span class="edit-customer-hero-icon" aria-hidden="true">
                    <i class="ti-user"></i>
                </span>
                <div>
                    <p class="edit-customer-kicker">Administration</p>
                    <h1 class="edit-customer-title">{{ $customer->customer_name }}</h1>
                    <p class="edit-customer-sub">Edit customer details, contacts, SOP, vessels, and notification settings.</p>
                </div>
            </div>
            <a href="{{ route('customers.index') }}" class="edit-customer-back">
                <i class="ti-arrow-left"></i> Back to customers
            </a>
        </div>

        <div class="edit-customer-meta">
            <span class="edit-customer-meta-pill">FM Number <strong>{{ $customer->customer_number ?? 'N/A' }}</strong></span>
            <span class="edit-customer-meta-pill">
                Account manager
                <strong>{{ $customer->responsible->accountManager->name ?? 'N/A' }}</strong>
            </span>
            <span class="edit-customer-meta-pill is-active">Status <strong>Active</strong></span>
        </div>

        <div class="tabs-container">
            <a class="tab-item active" data-tab="customer-details"><i class="ti-home"></i> Customer details</a>
            <a class="tab-item" data-tab="contacts"><i class="ti-id-badge"></i> Contacts</a>
            <a class="tab-item" data-tab="sop"><i class="ti-files"></i> SOP</a>
            <a class="tab-item" data-tab="vessels"><i class="ti-anchor"></i> Vessels</a>
            <a class="tab-item" data-tab="notification-settings"><i class="ti-bell"></i> Notification settings</a>
        </div>

        <div class="edit-customer-card">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show edit-customer-alert" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            @endif
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show edit-customer-alert" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            @endif

            <div class="tab-content-container">
                <form id="customerForm" action="{{ route('customers.update', $customer->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="active_tab" id="active_tab" value="{{ old('active_tab', 'customer-details') }}">

                    <div id="customer-details" class="tab-content-custom active">
                        <div class="form-pillar-container form-pillar-container--4">
                            <div class="form-pillar">
                                <div class="form-section-header">Customer information</div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="customer_name">Customer name <span class="text-danger">*</span></label>
                                    <input type="text" id="customer_name" name="customer_name" class="form-control-custom"
                                        value="{{ old('customer_name', $customer->customer_name) }}" required>
                                </div>

                                <div class="form-group-custom d-none">
                                    <label class="form-label-custom" for="customer_number_fm">Customer number from FM</label>
                                    <input type="text" id="customer_number_fm" name="customer_number_fm" class="form-control-custom"
                                        value="{{ old('customer_number_fm', $customer->customer_number) }}">
                                </div>

                                <div class="form-group-custom d-none">
                                    <label class="form-label-custom" for="customer_group">Customer group</label>
                                    <select id="customer_group" name="customer_group" class="form-control-custom select2-field">
                                        <option value="N/A" {{ ! $customer->customer_group_id ? 'selected' : '' }}>N/A</option>
                                        @foreach ($groups as $group)
                                            <option value="{{ $group->id }}" {{ (string) old('customer_group', $customer->customer_group_id) === (string) $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="phone_number">Phone number (with country code)</label>
                                    <input type="text" id="phone_number" name="phone_number" class="form-control-custom"
                                        value="{{ old('phone_number', $customer->phone) }}">
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="email">E-mail <span class="text-danger">*</span></label>
                                    <input type="text" id="email" name="email" class="form-control-custom"
                                        value="{{ old('email', $customer->email) }}"
                                        placeholder="email@example.com; email2@example.com" required>
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="internal_shipment">Internal shipment</label>
                                    <select id="internal_shipment" name="internal_shipment" class="form-control-custom select2-field">
                                        <option value="1" {{ (string) old('internal_shipment', $customer->internal_shipment) === '1' ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ (string) old('internal_shipment', $customer->internal_shipment) === '0' ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="remarks">Remarks</label>
                                    <textarea id="remarks" name="remarks" class="form-textarea-custom" rows="3">{{ old('remarks', $customer->remarks) }}</textarea>
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="special_considerations">Special considerations for destination</label>
                                    <textarea id="special_considerations" name="special_considerations" class="form-textarea-custom" rows="3">{{ old('special_considerations', $customer->special_considerations) }}</textarea>
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="contact_person">Contact person <span class="text-danger">*</span></label>
                                    <input type="text" id="contact_person" name="contact_person" class="form-control-custom"
                                        value="{{ old('contact_person', $customer->contact_person) }}" required>
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="un_locode">UN / LOCODE</label>
                                    <input type="text" id="un_locode" name="un_locode" class="form-control-custom"
                                        value="{{ old('un_locode', $customer->un_locode) }}">
                                </div>

                                <div class="checkbox-group d-none">
                                    <input type="checkbox" id="show_transport_details" name="show_transport_details" class="checkbox-custom"
                                        {{ old('show_transport_details', $customer->show_transport_details) ? 'checked' : '' }}>
                                    <label class="checkbox-label" for="show_transport_details">Show transport details on customer portal</label>
                                    </div>

                                <div class="checkbox-group d-none">
                                    <input type="checkbox" id="esea_store_stock_only" name="esea_store_stock_only" class="checkbox-custom"
                                        {{ old('esea_store_stock_only', $customer->esea_store_stock_only) ? 'checked' : '' }}>
                                    <label class="checkbox-label" for="esea_store_stock_only">eSea store stock only</label>
                                </div>
                            </div>

                            <div class="form-pillar">
                                <div class="form-section-header">Customer address</div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="street_address">Street address <span class="text-danger">*</span></label>
                                    <textarea id="street_address" name="street_address" class="form-textarea-custom" rows="3" required>{{ old('street_address', $customer->primaryAddress->street ?? '') }}</textarea>
                                                        </div>

                                <div class="input-row">
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="city">City <span class="text-danger">*</span></label>
                                        <input type="text" id="city" name="city" class="form-control-custom"
                                            value="{{ old('city', $customer->primaryAddress->city ?? '') }}" required>
                                                    </div>
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="district_state">District/state</label>
                                        <input type="text" id="district_state" name="district_state" class="form-control-custom"
                                            value="{{ old('district_state', $customer->primaryAddress->state ?? '') }}">
                                                    </div>
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="zip_code">Zip code</label>
                                        <input type="text" id="zip_code" name="zip_code" class="form-control-custom"
                                            value="{{ old('zip_code', $customer->primaryAddress->zip_code ?? '') }}">
                                                    </div>
                                                    </div>

                                <x-forms.country-select
                                    name="country"
                                    label="Country"
                                    :countries="$countries"
                                    :value="$customer->primaryAddress->country_id ?? null"
                                    class="form-control-custom"
                                    :allowClear="false"
                                />

                                <x-forms.port-select
                                    name="port_code"
                                    label="Port code"
                                    :value="$customer->primaryAddress->port_code ?? ''"
                                    class="form-control-custom"
                                />

                                <div class="form-section-header" style="margin-top: 12px;">Postal address (optional)</div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="postal_street_address">Street address / post box</label>
                                    <textarea id="postal_street_address" name="postal_street_address" class="form-textarea-custom" rows="3">{{ old('postal_street_address', $customer->postalAddress->street ?? '') }}</textarea>
                                                    </div>

                                <div class="input-row">
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="postal_city">City</label>
                                        <input type="text" id="postal_city" name="postal_city" class="form-control-custom"
                                            value="{{ old('postal_city', $customer->postalAddress->city ?? '') }}">
                                                    </div>
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="postal_district_state">District/state</label>
                                        <input type="text" id="postal_district_state" name="postal_district_state" class="form-control-custom"
                                            value="{{ old('postal_district_state', $customer->postalAddress->state ?? '') }}">
                                                    </div>
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="postal_zip_code">Zip code</label>
                                        <input type="text" id="postal_zip_code" name="postal_zip_code" class="form-control-custom"
                                            value="{{ old('postal_zip_code', $customer->postalAddress->zip_code ?? '') }}">
                                                    </div>
                                                    </div>
                                                    
                                <x-forms.country-select
                                    name="postal_country"
                                    label="Country"
                                    :countries="$countries"
                                    :value="$customer->postalAddress->country_id ?? null"
                                    class="form-control-custom"
                                    :allowClear="false"
                                />
                                                    </div>

                            <div class="form-pillar">
                                <div class="form-section-header">Invoice details</div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="invoice_recipient_name">Invoice recipient name <span class="text-danger">*</span></label>
                                    <input type="text" id="invoice_recipient_name" name="invoice_recipient_name" class="form-control-custom"
                                        value="{{ old('invoice_recipient_name', $customer->invoiceDetail->invoice_recipient_name ?? '') }}" required>
                                                    </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="invoice_recipient_address">Invoice recipient address <span class="text-danger">*</span></label>
                                    <textarea id="invoice_recipient_address" name="invoice_recipient_address" class="form-textarea-custom" rows="3" required>{{ old('invoice_recipient_address', $customer->invoiceAddress->street ?? '') }}</textarea>
                                                </div>

                                <div class="input-row">
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="invoice_city">City <span class="text-danger">*</span></label>
                                        <input type="text" id="invoice_city" name="invoice_city" class="form-control-custom"
                                            value="{{ old('invoice_city', $customer->invoiceAddress->city ?? '') }}" required>
                                                </div>
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="invoice_district_state">District/state</label>
                                        <input type="text" id="invoice_district_state" name="invoice_district_state" class="form-control-custom"
                                            value="{{ old('invoice_district_state', $customer->invoiceAddress->state ?? '') }}">
                                                    </div>
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="invoice_zip_code">Zip code</label>
                                        <input type="text" id="invoice_zip_code" name="invoice_zip_code" class="form-control-custom"
                                            value="{{ old('invoice_zip_code', $customer->invoiceAddress->zip_code ?? '') }}">
                                                    </div>
                                                    </div>

                                <x-forms.country-select
                                    name="invoice_country"
                                    label="Country"
                                    :countries="$countries"
                                    :value="$customer->invoiceAddress->country_id ?? null"
                                    class="form-control-custom"
                                    :allowClear="false"
                                />

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="currency">Currency <span class="text-danger">*</span></label>
                                    <select id="currency" name="currency" class="form-control-custom select2-field" required>
                                        <option value="USD" {{ old('currency', $customer->invoiceDetail->currency_code ?? '') == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                        <option value="EUR" {{ old('currency', $customer->invoiceDetail->currency_code ?? '') == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                        <option value="GBP" {{ old('currency', $customer->invoiceDetail->currency_code ?? '') == 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                                        <option value="INR" {{ old('currency', $customer->invoiceDetail->currency_code ?? '') == 'INR' ? 'selected' : '' }}>INR - Indian Rupee</option>
                                    </select>
                                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="invoicing_email">E-mails for invoicing <span class="text-danger">*</span></label>
                                    <input type="text" id="invoicing_email" name="invoicing_email" class="form-control-custom"
                                        value="{{ old('invoicing_email', $customer->invoiceDetail->invoice_email ?? '') }}" required>
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="invoicing_email_cc">E-mails for invoicing (CC)</label>
                                    <input type="text" id="invoicing_email_cc" name="invoicing_email_cc" class="form-control-custom"
                                        value="{{ old('invoicing_email_cc', $customer->invoiceDetail->invoice_email_cc ?? '') }}">
                                </div>

                                <div class="input-row">
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="payment_terms">Payment terms (days)</label>
                                        <input type="text" id="payment_terms" name="payment_terms" class="form-control-custom"
                                            value="{{ old('payment_terms', $customer->invoiceDetail->payment_terms_days ?? 30) }}">
                                    </div>
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="invoice_frequency">Invoice frequency</label>
                                        <select id="invoice_frequency" name="invoice_frequency" class="form-control-custom select2-field">
                                            <option value="Daily" {{ old('invoice_frequency', $customer->invoiceDetail->invoice_frequency ?? '') == 'Daily' ? 'selected' : '' }}>Daily</option>
                                            <option value="Weekly" {{ old('invoice_frequency', $customer->invoiceDetail->invoice_frequency ?? '') == 'Weekly' ? 'selected' : '' }}>Weekly</option>
                                            <option value="Monthly" {{ old('invoice_frequency', $customer->invoiceDetail->invoice_frequency ?? '') == 'Monthly' ? 'selected' : '' }}>Monthly</option>
                                                    </select>
                                                </div>
                                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="invoicing_remarks">Remarks regarding invoicing</label>
                                    <textarea id="invoicing_remarks" name="invoicing_remarks" class="form-textarea-custom" rows="3">{{ old('invoicing_remarks', $customer->invoiceDetail->invoice_remarks ?? '') }}</textarea>
                                                </div>

                                <div class="input-row">
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="vat_number">VAT number</label>
                                        <input type="text" id="vat_number" name="vat_number" class="form-control-custom"
                                            value="{{ old('vat_number', $customer->invoiceDetail->vat_number ?? '') }}">
                                                    </div>
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="eori_number">EORI number</label>
                                        <input type="text" id="eori_number" name="eori_number" class="form-control-custom"
                                            value="{{ old('eori_number', $customer->invoiceDetail->eori_number ?? '') }}">
                                                    </div>
                                                    </div>
                                                </div>

                            <div class="form-pillar">
                                @php
                                    $selectedMainAccountManagerId = old('main_account_manager', $customer->responsible->account_manager_id ?? null);
                                    $selectedMainAccountManager = $selectedMainAccountManagerId
                                        ? \App\Models\Contact::find($selectedMainAccountManagerId)
                                        : null;
                                    $selectedAccountingUserId = old('responsible_accounting_users', $customer->responsible->accounting_user_id ?? null);
                                    $selectedAccountingUser = $selectedAccountingUserId
                                        ? (
                                            ($customer->responsible?->accountingUser?->id ?? null) == $selectedAccountingUserId
                                                ? $customer->responsible->accountingUser
                                                : \App\Models\Contact::with('office')->find($selectedAccountingUserId)
                                        )
                                        : null;
                                    $responsibleOfficeShortName = old(
                                        'responsible_office',
                                        $selectedAccountingUser?->office?->office_short_name ?? ''
                                    );
                                @endphp

                                <div class="form-section-header">Responsible office/person</div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="sales_manager">Sales manager <span class="text-danger">*</span></label>
                                    <select id="sales_manager" name="sales_manager" class="form-control-custom select2-field" required>
                                        <option value=""></option>
                                        @foreach ($salesManagers as $manager)
                                            <option value="{{ $manager->id }}" {{ (string) old('sales_manager', $customer->responsible->sales_manager_id ?? '') === (string) $manager->id ? 'selected' : '' }}>{{ $manager->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="main-account-manager-select">Main account manager <span class="text-danger">*</span></label>
                                    <select id="main-account-manager-select" name="main_account_manager" class="form-control-custom select2-office-user" required>
                                        <option value=""></option>
                                        @if ($selectedMainAccountManager)
                                            <option value="{{ $selectedMainAccountManager->id }}" selected>{{ $selectedMainAccountManager->name }}</option>
                                        @endif
                                    </select>
                                            </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="responsible-accounting-users-select">Responsible accounting users</label>
                                    <select id="responsible-accounting-users-select" name="responsible_accounting_users" class="form-control-custom select2-office-user">
                                        <option value=""></option>
                                        @if ($selectedAccountingUser)
                                            <option value="{{ $selectedAccountingUser->id }}" selected
                                                data-office-short-name="{{ $selectedAccountingUser->office?->office_short_name ?? '' }}">{{ $selectedAccountingUser->name }}</option>
                                        @endif
                                    </select>
                                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="responsible-office-input">Responsible office</label>
                                    <input type="text" id="responsible-office-input" name="responsible_office"
                                        class="form-control-custom" value="{{ $responsibleOfficeShortName }}" readonly>
                                                </div>

                                <div class="form-section-header">Company's logo</div>
                                <div class="logo-placeholder" id="logo_drop_zone" style="cursor: pointer; position: relative;">
                                    @if ($customer->logo)
                                        <img src="{{ route('customers.logo.show', $customer->id) }}" id="logo_preview" alt="Customer logo"
                                            style="max-width: 100%; max-height: 80px; margin-bottom: 8px; display: block; margin-left: auto; margin-right: auto;">
                                    @else
                                        <img src="" id="logo_preview" alt=""
                                            style="max-width: 100%; max-height: 80px; margin-bottom: 8px; display: none; margin-left: auto; margin-right: auto;">
                                    @endif
                                    <p id="logo_text" style="font-size: 13px; margin: 0;">Drag image file here or click to browse</p>
                                    <i class="ti-camera" id="logo_icon"></i>
                                                    </div>
                                                    </div>
                                                    </div>
                                                </div>

                    <div id="sop" class="tab-content-custom">
                        <div class="form-pillar-container">
                            <div class="form-pillar sop-pillar">
                                <div class="form-section-header" style="margin-top: 0;">General procedures</div>
                                <div class="input-row input-row--2">
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="send_stocklist">Send stocklist?</label>
                                        <select id="send_stocklist" name="send_stocklist" class="form-control-custom select2-field">
                                            <option value=""></option>
                                            <option value="Yes" {{ old('send_stocklist', $customer->sop->send_stocklist ?? '') == 'Yes' ? 'selected' : '' }}>Yes</option>
                                            <option value="No" {{ old('send_stocklist', $customer->sop->send_stocklist ?? '') == 'No' ? 'selected' : '' }}>No</option>
                                                    </select>
                                                </div>
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="onboard_delivery">Onboard delivery?</label>
                                        <select id="onboard_delivery" name="onboard_delivery" class="form-control-custom select2-field">
                                            <option value=""></option>
                                            <option value="Yes" {{ old('onboard_delivery', $customer->sop->onboard_delivery ?? '') == 'Yes' ? 'selected' : '' }}>Yes</option>
                                            <option value="No" {{ old('onboard_delivery', $customer->sop->onboard_delivery ?? '') == 'No' ? 'selected' : '' }}>No</option>
                                                    </select>
                                                </div>
                                                </div>
                                <div class="input-row input-row--2">
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="quotes_prior_to_instructions">Quotes prior to instructions?</label>
                                        <select id="quotes_prior_to_instructions" name="quotes_prior_to_instructions" class="form-control-custom select2-field">
                                            <option value=""></option>
                                            <option value="Yes" {{ old('quotes_prior_to_instructions', $customer->sop->quotes_prior_to_instructions ?? '') == 'Yes' ? 'selected' : '' }}>Yes</option>
                                            <option value="No" {{ old('quotes_prior_to_instructions', $customer->sop->quotes_prior_to_instructions ?? '') == 'No' ? 'selected' : '' }}>No</option>
                                        </select>
                                                </div>
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="agreed_rate">Agreed rate?</label>
                                        <select id="agreed_rate" name="agreed_rate" class="form-control-custom select2-field">
                                            <option value=""></option>
                                            <option value="Yes" {{ old('agreed_rate', $customer->sop->agreed_rate ?? '') == 'Yes' ? 'selected' : '' }}>Yes</option>
                                            <option value="No" {{ old('agreed_rate', $customer->sop->agreed_rate ?? '') == 'No' ? 'selected' : '' }}>No</option>
                                                        </select>
                                                    </div>
                                                </div>
                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="invoicing_procedure">Invoicing</label>
                                    <input type="text" id="invoicing_procedure" name="invoicing_procedure" class="form-control-custom"
                                        value="{{ old('invoicing_procedure', $customer->sop->invoicing_procedure ?? '') }}">
                                                </div>
                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="pending_entry">Pending entry?</label>
                                    <select id="pending_entry" name="pending_entry" class="form-control-custom select2-field">
                                        <option value=""></option>
                                        <option value="Yes" {{ old('pending_entry', $customer->sop->pending_entry ?? '') == 'Yes' ? 'selected' : '' }}>Yes</option>
                                        <option value="No" {{ old('pending_entry', $customer->sop->pending_entry ?? '') == 'No' ? 'selected' : '' }}>No</option>
                                    </select>
                                                    </div>
                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="special_pending_routines">Special pending routines</label>
                                    <input type="text" id="special_pending_routines" name="special_pending_routines" class="form-control-custom"
                                        value="{{ old('special_pending_routines', $customer->sop->special_pending_routines ?? '') }}">
                                                    </div>
                            </div>

                            <div class="form-pillar sop-pillar">
                                <div class="form-section-header" style="margin-top: 0;">Other procedures</div>
                                <div class="form-group-custom" style="flex: 1; display: flex; flex-direction: column;">
                                    <label class="form-label-custom" for="other_procedures_comments">Other procedures/comments</label>
                                    <textarea id="other_procedures_comments" name="other_procedures_comments" class="form-textarea-custom sop-comments-textarea" rows="10">{{ old('other_procedures_comments', $customer->sop->other_procedures_comments ?? '') }}</textarea>
                                                </div>
                                            </div>

                            <div class="form-pillar sop-pillar">
                                <div class="form-section-header" style="margin-top: 0;">Imported documents</div>
                                <div class="upload-area sop-upload-area" id="sop_drop_zone" style="cursor: pointer; position: relative;">
                                    <p id="sop_text" class="upload-text">Drag files here or click to browse</p>
                                    <i class="ti-upload upload-icon" id="sop_icon"></i>
                                                </div>
                                <div id="sop_file_list" style="margin-top: 10px;">
                                    @foreach ($customer->documents as $doc)
                                        <div class="file-item">
                                            <i class="ti-file"></i>
                                            <a href="{{ $doc->fileUrl() }}" target="_blank" class="file-name">{{ $doc->file_name }}</a>
                                            <span class="remove-file" data-id="{{ $doc->id }}"><i class="ti-trash"></i></span>
                                                </div>
                                                        @endforeach
                                                </div>
                                <input type="hidden" name="removed_documents" id="removed_documents" value="">
                            </div>
                        </div>
                                                 </div>

                    <div id="notification-settings" class="tab-content-custom">
                        <div class="form-pillar-container">
                            <div class="form-pillar notification-pillar">
                                <div class="form-section-header" style="margin-top: 0;">Stock items to stock</div>
                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="notify_stock_items">Notify when stock items come to stock</label>
                                    <input type="text" id="notify_stock_items" name="notify_stock_items" class="form-control-custom"
                                        value="{{ old('notify_stock_items', $customer->notificationSetting->notify_stock_items ?? '') }}">
                                                </div>
                                            </div>
                            <div class="form-pillar notification-pillar">
                                <div class="form-section-header" style="margin-top: 0;">First mile management</div>
                                <div class="checkbox-group">
                                    <input type="checkbox" id="send_automatic_first_mile_email" name="send_automatic_first_mile_email" class="checkbox-custom"
                                        {{ old('send_automatic_first_mile_email', $customer->notificationSetting->send_automatic_first_mile_email ?? false) ? 'checked' : '' }}>
                                    <label class="checkbox-label" for="send_automatic_first_mile_email">Send automatic first-mile email to supplier</label>
                                        </div>
                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="notify_first_mile_email_sent">Notify when first mile email is sent to supplier</label>
                                    <input type="text" id="notify_first_mile_email_sent" name="notify_first_mile_email_sent" class="form-control-custom"
                                        value="{{ old('notify_first_mile_email_sent', $customer->notificationSetting->notify_first_mile_email_sent ?? '') }}">
                                    </div>
                            </div>
                            <div class="form-pillar notification-pillar">
                                <div class="form-section-header" style="margin-top: 0;">Free storage period</div>
                                <div class="input-row">
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="shipping_free_storage_days">Days limit</label>
                                        <input type="text" id="shipping_free_storage_days" name="shipping_free_storage_days" class="form-control-custom"
                                            value="{{ old('shipping_free_storage_days', $customer->notificationSetting->shipping_free_storage_days ?? '') }}">
                                    </div>
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="shipping_free_storage_weight">Weight limit (kg)</label>
                                        <input type="text" id="shipping_free_storage_weight" name="shipping_free_storage_weight" class="form-control-custom"
                                            value="{{ old('shipping_free_storage_weight', $customer->notificationSetting->shipping_free_storage_weight ?? '') }}">
                                    </div>
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="shipping_free_storage_volume">Volume limit (CBM)</label>
                                        <input type="text" id="shipping_free_storage_volume" name="shipping_free_storage_volume" class="form-control-custom"
                                            value="{{ old('shipping_free_storage_volume', $customer->notificationSetting->shipping_free_storage_volume ?? '') }}">
                                    </div>
                                </div>
                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="notify_free_storage_exceeded">Notify when free storage period exceeds</label>
                                    <input type="text" id="notify_free_storage_exceeded" name="notify_free_storage_exceeded" class="form-control-custom"
                                        value="{{ old('notify_free_storage_exceeded', $customer->notificationSetting->notify_free_storage_exceeded ?? '') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                {{-- Contacts / Vessels tabs live outside #customerForm --}}
                <div id="contacts" class="tab-content-custom">
                                        <div class="vessels-header">
                                            <div class="vessels-search-container">
                                                <span class="search-label">Search</span>
                                                <input type="text" id="contactSearchInput" placeholder="type here">
                                            </div>
                                            <div style="display: flex; gap: 10px;">
                                                <a href="{{ route('contacts.create', $customer->id) }}" class="btn-vessel-add">Add contact</a>
                                            </div>
                                        </div>
                    <div class="cust-table-wrap">
                                            <table class="custom-table" id="contactsTable">
                                                <thead>
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Email</th>
                                                        <th>Phone number</th>
                                                        <th>Description</th>
                                                        <th style="text-align: center;">Main contact</th>
                                    <th style="text-align: right;"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                @forelse ($customer->contacts as $contact)
                                    <tr>
                                        <td>
                                            <a href="{{ route('contacts.edit', $contact->id) }}" class="table-link">{{ $contact->name }}</a>
                                        </td>
                                        <td>{{ $contact->email ?: '—' }}</td>
                                        <td>{{ $contact->phone_number ?: '—' }}</td>
                                        <td>{{ $contact->description ? Str::limit($contact->description, 50) : '—' }}</td>
                                                            <td style="text-align: center;">
                                            @if ($contact->is_main_contact)
                                                <i class="ti-check" style="color: #008080; font-weight: bold;"></i>
                                                                @endif
                                                            </td>
                                        <td style="text-align: right; white-space: nowrap;">
                                                                <a href="{{ route('contacts.edit', $contact->id) }}">
                                                <i class="ti-pencil btn-action-pencil"></i>
                                                                </a>
                                            <button type="button" class="btn-action-delete delete-contact" data-id="{{ $contact->id }}" aria-label="Delete contact">
                                                <i class="ti-trash"></i>
                                            </button>
                                                            </td>
                                                        </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" style="text-align: center; padding: 40px; color: #94a3b8;">No contacts found for this customer.</td>
                                    </tr>
                                @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    
                <div id="vessels" class="tab-content-custom">
                                        <div class="vessels-header">
                                            <div class="vessels-search-container">
                                                <span class="search-label">Search</span>
                                                <input type="text" id="vesselSearchInput" placeholder="type here">
                                            </div>
                                            <div style="display: flex; gap: 10px;">
                            <button type="button" class="btn-vessel-export" aria-label="Export vessels"><i class="ti-download"></i></button>
                                                <a href="{{ route('customers.vessels.create', $customer->id) }}" class="btn-vessel-add">Add vessel</a>
                                            </div>
                                        </div>
                    <div class="cust-table-wrap">
                        <table class="custom-table vessels-table" id="vesselsTable">
                                                <thead>
                                                    <tr>
                                                        <th>Vessel name</th>
                                                        <th>Customer vessel code</th>
                                                        <th>IMO</th>
                                                        <th>Manager</th>
                                                        <th>Account manager</th>
                                                        <th>Status</th>
                                                        <th style="width: 50px;"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                @forelse ($customer->vessels as $vessel)
                                                    <tr>
                                        <td>
                                            <a href="{{ route('customers.vessels.edit', $vessel->id) }}" class="vessel-link">{{ $vessel->vessel }}</a>
                                        </td>
                                                        <td>{{ $vessel->customer_vessel_code }}</td>
                                                        <td>{{ $vessel->vessel_imo }}</td>
                                                        <td>{{ $vessel->manager }}</td>
                                                        <td>{{ $vessel->account_manager }}</td>
                                                        <td>
                                            @if ($vessel->inactive_vessel)
                                                                <span class="label label-danger">Inactive</span>
                                            @elseif ($vessel->sanction_blocked || $vessel->financially_blocked)
                                                                <span class="label label-warning">Blocked</span>
                                                            @else
                                                                <span class="label label-success">Active</span>
                                                            @endif
                                                        </td>
                                        <td style="text-align: right;">
                                            <a href="{{ route('customers.vessels.edit', $vessel->id) }}">
                                                <i class="ti-pencil btn-action-pencil"></i>
                                            </a>
                                        </td>
                                                    </tr>
                                                    @empty
                                                    <tr>
                                        <td colspan="7" style="text-align: center; padding: 40px; color: #94a3b8;">No vessels found.</td>
                                                    </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                                    </div>
                                                </div>

        <div class="customer-edit-footer" id="customer-edit-footer">
            <button type="submit" class="btn-save-custom" id="btn-save" form="customerForm">Save customer</button>
            <a href="{{ route('customers.index') }}" class="btn-cancel-custom">Cancel</a>
            <div class="audit-info">
                @include('partials.audit-info', ['record' => $customer, 'bold' => true])
                                            </div>
                                                </div>
                                                </div>

    @include('layouts.partials.pcoded-shell-end')

                            <input type="file" name="logo" id="logo_input" accept="image/*" style="display:none;" form="customerForm">
                            <input type="file" name="sop_documents[]" id="sop_documents_input" multiple style="display:none;" form="customerForm">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function () {
            $('body').addClass('edit-customer-page');

            var formTabsWithFooter = ['customer-details', 'sop', 'notification-settings'];

            function activateCustomerTab(tabId) {
                if (!tabId || !$('#' + tabId).length || !$('.tab-item[data-tab="' + tabId + '"]').length) {
                    return false;
                }
                $('.tab-item').removeClass('active');
                $('.tab-item[data-tab="' + tabId + '"]').addClass('active');
                $('.tab-content-custom').removeClass('active');
                $('#' + tabId).addClass('active');
                $('#active_tab').val(tabId);
                $('#customer-edit-footer').toggle(formTabsWithFooter.indexOf(tabId) !== -1);
                return true;
            }

            $('.tab-item').on('click', function (e) {
                e.preventDefault();
                var tabId = $(this).data('tab');
                activateCustomerTab(tabId);
                if (history.replaceState) {
                    history.replaceState(null, '', '#' + tabId);
                } else {
                    window.location.hash = tabId;
                }
            });

            var hashTab = window.location.hash.replace(/^#/, '');
            if (hashTab) {
                activateCustomerTab(hashTab);
            }

            $('.select2-field').not('[data-country-select]').not('[data-port-select]').select2({
                placeholder: 'Select an option',
                allowClear: false,
                width: '100%'
            });

            function formatOfficeUser(item) {
                if (!item.id) {
                    return item.text;
                }

                var subtitleParts = [];
                if (item.type_label) {
                    subtitleParts.push(item.type_label);
                }
                if (item.subtitle) {
                    subtitleParts.push(item.subtitle);
                }

                var subtitle = subtitleParts.join(' · ');
                return $(
                    '<div style="line-height:1.1;"><div style="font-weight:600;">' + item.text + '</div>' +
                    (subtitle ? '<div style="font-size:11px;color:#6b7280;">' + subtitle + '</div>' : '') +
                    '</div>'
                );
            }

            function formatOfficeUserSelection(item) {
                return item.text || item.id;
            }

            function initOfficeUserSelect(selector, placeholder) {
                $(selector).select2({
                    placeholder: placeholder,
                    allowClear: false,
                width: '100%',
                    minimumInputLength: 0,
                    ajax: {
                        url: @json(url('/api/account-managers')),
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params.term || '',
                                categories: 'operations,account,manager'
                            };
                        },
                        processResults: function (data) {
                            return { results: data };
                        }
                    },
                    templateResult: formatOfficeUser,
                    templateSelection: formatOfficeUserSelection
                });
            }

            initOfficeUserSelect('#main-account-manager-select', 'Select account manager');
            initOfficeUserSelect('#responsible-accounting-users-select', 'Select accounting user');

            function setResponsibleOfficeFromAccountingUser(officeShortName) {
                $('#responsible-office-input').val(officeShortName || '');
            }

            $('#responsible-accounting-users-select').on('select2:select', function (e) {
                setResponsibleOfficeFromAccountingUser(e.params.data.office_short_name);
            });

            $('#responsible-accounting-users-select').on('select2:clear', function () {
                setResponsibleOfficeFromAccountingUser('');
            });

            var $preselectedAccountingUser = $('#responsible-accounting-users-select option:selected');
            if ($preselectedAccountingUser.length && $preselectedAccountingUser.val()) {
                setResponsibleOfficeFromAccountingUser(
                    $preselectedAccountingUser.attr('data-office-short-name') || $('#responsible-office-input').val()
                );
            }

            $('.select2-field, .select2-office-user').on('change', function () {
                $(this).valid();
                if ($(this).hasClass('error')) {
                    $(this).next('.select2-container').addClass('error');
                } else {
                    $(this).next('.select2-container').removeClass('error');
                }
            });

            $.validator.addMethod('multiEmail', function (value, element) {
                if (this.optional(element)) {
                    return true;
                }

                var emails = value.split(/[;,]+/).map(function (part) {
                    return $.trim(part);
                }).filter(Boolean);

                if (!emails.length) {
                    return false;
                }

                return emails.every(function (email) {
                    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
                });
            }, 'Please enter valid email address(es), separated by comma or semicolon');

            $('#customerForm').validate({
                rules: {
                    customer_name: 'required',
                    contact_person: 'required',
                    email: {
                        required: true,
                        multiEmail: true
                    },
                    street_address: 'required',
                    city: 'required',
                    country: 'required',
                    invoice_recipient_name: 'required',
                    invoice_recipient_address: 'required',
                    invoice_city: 'required',
                    invoice_country: 'required',
                    currency: 'required',
                    invoicing_email: {
                        required: true,
                        email: true
                    },
                    invoicing_email_cc: {
                        email: true
                    },
                    sales_manager: 'required',
                    main_account_manager: 'required'
                },
                messages: {
                    customer_name: 'Please enter customer name',
                    contact_person: 'Please enter the contact person',
                    email: 'Please enter valid email address(es), separated by comma or semicolon',
                    street_address: 'Please enter street address',
                    city: 'Please enter city',
                    country: 'Please select country',
                    invoice_recipient_name: 'Please enter recipient name',
                    invoice_recipient_address: 'Please enter recipient address',
                    invoice_city: 'Please enter city',
                    invoice_country: 'Please select country',
                    currency: 'Please select currency',
                    invoicing_email: 'Please enter a valid invoicing email',
                    sales_manager: 'Please select sales manager',
                    main_account_manager: 'Please select account manager'
                },
                errorElement: 'div',
                errorClass: 'error-message',
                errorPlacement: function (error, element) {
                    if (element.hasClass('select2-field') || element.hasClass('select2-office-user') || element.is('[data-country-select]') || element.is('[data-port-select]')) {
                        error.insertAfter(element.next('.select2-container'));
                    } else {
                        error.insertAfter(element);
                    }
                },
                highlight: function (element) {
                    $(element).addClass('error');
                    if ($(element).hasClass('select2-field') || $(element).hasClass('select2-office-user') || $(element).is('[data-country-select]') || $(element).is('[data-port-select]')) {
                        $(element).next('.select2-container').addClass('error');
                    }
                },
                unhighlight: function (element) {
                    $(element).removeClass('error');
                    if ($(element).hasClass('select2-field') || $(element).hasClass('select2-office-user') || $(element).is('[data-country-select]') || $(element).is('[data-port-select]')) {
                        $(element).next('.select2-container').removeClass('error');
                    }
                },
                invalidHandler: function () {
                    if (typeof window.unsavedChangesGuardClearAllowLeave === 'function') {
                        window.unsavedChangesGuardClearAllowLeave();
                    }
                },
                submitHandler: function (form) {
                    if (typeof window.unsavedChangesGuardAllowLeave === 'function') {
                        window.unsavedChangesGuardAllowLeave();
                    }
                    form.submit();
                }
            });

            $('#vesselSearchInput').on('keyup', function () {
                var value = $(this).val().toLowerCase();
                $('#vesselsTable tbody tr').filter(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });

            $('#contactSearchInput').on('keyup', function () {
                var value = $(this).val().toLowerCase();
                $('#contactsTable tbody tr').filter(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });

            $(document).on('click', '.delete-contact', function () {
                var contactId = $(this).data('id');
                var row = $(this).closest('tr');

                if (confirm('Are you sure you want to delete this contact?')) {
                    $.ajax({
                        url: '/contacts/' + contactId,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            if (response.success) {
                                row.fadeOut(function () {
                                    $(this).remove();
                                });
                            }
                        },
                        error: function () {
                            alert('An error occurred while deleting the contact.');
                        }
                    });
                }
            });

            var $logoZone = $('#logo_drop_zone');
            var $logoInput = $('#logo_input');
            var $logoPreview = $('#logo_preview');
            var $logoText = $('#logo_text');
            var $logoIcon = $('#logo_icon');

            $logoZone.on('click', function () { $logoInput.trigger('click'); });
            $logoInput.on('click', function (e) { e.stopPropagation(); });

            $logoInput.on('change', function () {
                var file = this.files[0];
                if (!file) {
                    return;
                }
                var reader = new FileReader();
                reader.onload = function (e) {
                    $logoPreview.attr('src', e.target.result).show();
                    $logoIcon.hide();
                    $logoText.text('Click to change logo');
                };
                reader.readAsDataURL(file);
            });

            $logoZone.on('dragover dragenter', function (e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).addClass('dragover');
            }).on('dragleave drop', function (e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass('dragover');
            }).on('drop', function (e) {
                var file = e.originalEvent.dataTransfer.files[0];
                if (!file || !file.type.startsWith('image/')) {
                    return;
                }
                var dt = new DataTransfer();
                dt.items.add(file);
                $logoInput[0].files = dt.files;
                $logoInput.trigger('change');
            });

            var $sopZone = $('#sop_drop_zone');
            var $sopInput = $('#sop_documents_input');
            var $sopList = $('#sop_file_list');
            var customerId = {{ $customer->id }};
            var uploadUrl = '/customers/' + customerId + '/documents';
            var csrfToken = $('meta[name="csrf-token"]').attr('content') ||
                             $('input[name="_token"]').first().val();

            function uploadFile(file) {
                var fd = new FormData();
                fd.append('_token', csrfToken);
                fd.append('file', file);

                var $item = $('<div class="file-item uploading">' +
                    '<i class="ti-file"></i>' +
                    '<span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:#999;">Uploading ' + file.name + '...</span>' +
                    '</div>');
                $sopList.append($item);

                $.ajax({
                    url: uploadUrl,
                    type: 'POST',
                    data: fd,
                    processData: false,
                    contentType: false,
                    success: function (res) {
                        $item.replaceWith(
                            '<div class="file-item" data-doc-id="' + res.id + '">' +
                            '<i class="ti-file"></i>' +
                            '<a href="' + res.file_url + '" target="_blank" class="file-name">' + res.file_name + '</a>' +
                            '<span class="remove-file" data-id="' + res.id + '"><i class="ti-trash"></i></span>' +
                            '</div>'
                        );
                    },
                    error: function () {
                        $item.replaceWith(
                            '<div class="file-item" style="color:#dc2626;">' +
                            '<i class="ti-alert"></i><span>Failed to upload ' + file.name + '</span>' +
                            '</div>'
                        );
                    }
                });
            }

            $sopZone.on('click', function () { $sopInput.trigger('click'); });
            $sopInput.on('click', function (e) { e.stopPropagation(); });

            $sopInput.on('change', function () {
                $.each(this.files, function (i, file) { uploadFile(file); });
                this.value = '';
            });

            $sopZone.on('dragover dragenter', function (e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).addClass('dragover');
            }).on('dragleave drop', function (e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass('dragover');
            }).on('drop', function (e) {
                var files = e.originalEvent.dataTransfer.files;
                $.each(files, function (i, file) { uploadFile(file); });
            });

            $sopList.on('click', '.remove-file', function () {
                var $btn = $(this);
                var docId = $btn.data('id');
                if (!confirm('Delete this document?')) {
                    return;
                }
                $.ajax({
                    url: '/customers/documents/' + docId,
                    type: 'POST',
                    data: { _token: csrfToken, _method: 'DELETE' },
                    success: function () { $btn.closest('.file-item').remove(); },
                    error: function () { alert('Failed to delete document.'); }
                });
            });
        });
    </script>

@include('partials.unsaved-changes-guard', [
    'formSelector' => '#customerForm',
    'saveButtonSelector' => '#btn-save',
    'legacySaveLabelSwap' => true,
    'fallbackUrl' => route('customers.index'),
])
@endsection
