@extends('layouts.app')

@section('styles')
    @include('Agents.partials.edit-page-styles')
@endsection

@section('content')
    <script>document.body.classList.add('edit-agent-page');</script>

    @include('layouts.partials.pcoded-shell-start', ['pageWrapperClass' => 'p-0'])

    <div class="edit-agent-page">
        <div class="edit-agent-hero">
            <div class="edit-agent-hero-main">
                <span class="edit-agent-hero-icon" aria-hidden="true">
                    <i class="ti-briefcase"></i>
                </span>
                <div>
                    <p class="edit-agent-kicker">Administration</p>
                    <h1 class="edit-agent-title">{{ $agent->agent_name }}</h1>
                    <p class="edit-agent-sub">Edit agent details, billing, documents, users, and scan gun settings.</p>
                </div>
            </div>
            <a href="{{ route('agents.index') }}" class="edit-agent-back">
                <i class="ti-arrow-left"></i> Back to agents
            </a>
        </div>

        <div class="edit-agent-meta">
            <span class="edit-agent-meta-pill">Agent ID <strong>{{ $agent->id }}</strong></span>
            @if ($agent->code)
                <span class="edit-agent-meta-pill">Code <strong>{{ $agent->code }}</strong></span>
            @endif
            @if ($agent->city || $agent->country)
                <span class="edit-agent-meta-pill">
                    Location
                    <strong>{{ trim(($agent->city ? $agent->city . ', ' : '') . ($agent->country?->name ?? '')) }}</strong>
                </span>
            @endif
            @if ($agent->is_active)
                <span class="edit-agent-meta-pill is-active">Status <strong>Active</strong></span>
            @else
                <span class="edit-agent-meta-pill is-hidden">Status <strong>Inactive</strong></span>
            @endif
        </div>

        <div class="tabs-container">
            <a class="tab-item active" data-tab="agent-details"><i class="ti-info-alt"></i> Agent details</a>
            <a class="tab-item" data-tab="billing-details"><i class="ti-receipt"></i> Billing details</a>
            <a class="tab-item" data-tab="sop"><i class="ti-files"></i> SOP</a>
            <a class="tab-item" data-tab="pricing"><i class="ti-money"></i> Pricing</a>
            <a class="tab-item" data-tab="agent-users"><i class="ti-user"></i> Agent users</a>
            <a class="tab-item" data-tab="contacts"><i class="ti-id-badge"></i> Contacts</a>
            <a class="tab-item" data-tab="email-settings"><i class="ti-email"></i> Email settings</a>
            <a class="tab-item" data-tab="scan-gun"><i class="ti-hand-point-right"></i> Scan gun</a>
        </div>

        <div class="edit-agent-card">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show edit-agent-alert" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show edit-agent-alert" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            @endif

            <div class="tab-content-container">
                <form id="agentEditForm" action="{{ route('agents.update', $agent->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="active_tab" id="active_tab" value="{{ old('active_tab', 'agent-details') }}">

<div id="agent-details" class="tab-content-custom active">
    <div class="form-pillar-container">
        <!-- Column 1: Agent information -->
        <div class="form-pillar">
            <div class="form-section-header">Agent information</div>
            <div class="form-group-custom">
                <label class="form-label-custom">Agent name</label>
                <div class="input-group-custom">
                    <input type="text" name="agent_name"
                        class="form-input-custom has-append"
                        value="{{ old('agent_name', $agent->agent_name) }}">
                    <button class="btn-input-append"><i
                            class="ti-more-alt"></i></button>
                </div>
            </div>

            <div class="form-group-custom">
                <label class="form-label-custom">Company id</label>
                <input type="text" name="company_id"
                    class="form-input-custom form-input-readonly"
                    value="{{ old('company_id', $agent->company_id) }}" readonly>
            </div>

            <div class="input-row">
                <div class="form-group-custom">
                    <label class="form-label-custom">Code</label>
                    <input type="text" name="code" class="form-input-custom"
                        value="{{ old('code', $agent->code) }}">
                </div>
                <div class="form-group-custom">
                    <label class="form-label-custom">Code description</label>
                    <input type="text" name="code_description"
                        class="form-input-custom"
                        value="{{ old('code_description', $agent->code_description) }}">
                </div>
            </div>

            <div class="form-group-custom">
                <label class="form-label-custom">Phone number (with country
                    code)</label>
                <input type="text" name="phone" class="form-input-custom"
                    value="{{ old('phone', $agent->phone) }}">
            </div>

            <div class="form-group-custom">
                <label class="form-label-custom">Email</label>
                <input type="text" name="email" class="form-input-custom"
                    value="{{ old('email', $agent->email) }}"
                    placeholder="email@example.com; email2@example.com">
            </div>

            <div class="form-group-custom">
                <label class="form-label-custom">Remarks</label>
                <textarea name="remarks" class="form-textarea-custom"
                    rows="3">{{ old('remarks', $agent->remarks) }}</textarea>
            </div>

            <div class="form-group-custom">
                <label class="form-label-custom">Special considerations for
                    destination</label>
                <textarea name="special_considerations" class="form-textarea-custom"
                    rows="3">{{ old('special_considerations', $agent->special_considerations) }}</textarea>
            </div>

            <div class="form-group-custom"
                style="display: flex; gap: 8px; align-items: flex-start;">
                <input type="checkbox" name="show_pre_alert" value="1"
                    style="margin-top: 3px;" {{ old('show_pre_alert', $agent->show_pre_alert) ? 'checked' : '' }}>
                <label class="form-label-custom">Show pre-alert warning when items
                    in
                    shipment are not scanned</label>
            </div>
            <div class="form-group-custom">
                <label class="form-label-custom">Contact Person <span class="text-danger">*</span></label>
                <input type="text" name="contact_person" class="form-input-custom" value="{{ old('contact_person', $agent->contact_person) }}" required>
            </div>
        </div>

        <!-- Column 2: Agent address & Office address -->
        <div class="form-pillar">
            <div class="form-section-header">Agent address</div>

            <div class="form-group-custom">
                <label class="form-label-custom">Agent address</label>
                <textarea name="agent_address" class="form-textarea-custom"
                    rows="3">{{ old('agent_address', $agent->agent_address) }}</textarea>
            </div>

            <div class="input-row">
                <div class="form-group-custom" style="flex: 2;">
                    <label class="form-label-custom">City</label>
                    <input type="text" name="city" class="form-input-custom"
                        value="{{ old('city', $agent->city) }}">
                </div>
                <div class="form-group-custom">
                    <label class="form-label-custom">District/state</label>
                    <input type="text" name="district_state"
                        class="form-input-custom"
                        value="{{ old('district_state', $agent->district_state) }}">
                </div>
                <div class="form-group-custom">
                    <label class="form-label-custom">Zip code</label>
                    <input type="text" name="zip_code" class="form-input-custom"
                        value="{{ old('zip_code', $agent->zip_code) }}">
                </div>
            </div>

            <x-forms.country-select
                name="country_id"
                label="Country"
                :countries="$countries"
                :value="$agent->country_id"
            />

            <x-forms.port-select
                name="port_code"
                label="Port code"
                :value="$agent->port_code"
            />

            <div class="form-section-header" style="margin-top: 15px;">Office
                address
                (Optional)</div>

            <div class="form-group-custom">
                <label class="form-label-custom">Office address</label>
                <textarea name="office_address" class="form-textarea-custom"
                    rows="3">{{ old('office_address', $agent->office_address) }}</textarea>
            </div>

            <div class="input-row">
                <div class="form-group-custom" style="flex: 2;">
                    <label class="form-label-custom">City</label>
                    <input type="text" name="office_city" class="form-input-custom"
                        value="{{ old('office_city', $agent->office_city) }}">
                </div>
                <div class="form-group-custom">
                    <label class="form-label-custom">District/state</label>
                    <input type="text" name="office_district_state"
                        class="form-input-custom"
                        value="{{ old('office_district_state', $agent->office_district_state) }}">
                </div>
                <div class="form-group-custom">
                    <label class="form-label-custom">Zip code</label>
                    <input type="text" name="office_zip_code"
                        class="form-input-custom"
                        value="{{ old('office_zip_code', $agent->office_zip_code) }}">
                </div>
            </div>

            <x-forms.country-select
                name="office_country_id"
                label="Country"
                :countries="$countries"
                :value="$agent->office_country_id"
            />
        </div>

        <!-- Column 3: Agent details -->
        <div class="form-pillar">
            <div class="form-section-header">Agent details</div>

            <div class="form-group-custom">
                <label class="form-label-custom">EORI number</label>
                <input type="text" name="eori_number" class="form-input-custom"
                    value="{{ old('eori_number', $agent->eori_number) }}">
            </div>

            <div class="input-row">
                <div class="form-group-custom">
                    <label class="form-label-custom">UN/LOCODE</label>
                    <input type="text" name="un_locode" class="form-input-custom"
                        value="{{ old('un_locode', $agent->un_locode) }}"
                        autocomplete="off">
                </div>
                <div class="form-group-custom" style="flex: 1.5;">
                    <label class="form-label-custom">Agent type</label>
                    <select name="agent_type" class="select2-agent-type-edit">
                        <option value=""></option>
                        <option value="contracted_agent" {{ old('agent_type', $agent->agent_type) == 'contracted_agent' ? 'selected' : '' }}>Contracted agent</option>
                        <option value="main_agent" {{ old('agent_type', $agent->agent_type) == 'main_agent' ? 'selected' : '' }}>
                            Main agent</option>
                        <option value="sub_agent" {{ old('agent_type', $agent->agent_type) == 'sub_agent' ? 'selected' : '' }}>
                            Sub agent</option>
                        <option value="3pl_japan_supplier" {{ old('agent_type', $agent->agent_type) == '3pl_japan_supplier' ? 'selected' : '' }}>3PL Japan supplier</option>
                        <option value="3pl_greece_supplier" {{ old('agent_type', $agent->agent_type) == '3pl_greece_supplier' ? 'selected' : '' }}>3PL Greece supplier</option>
                        <option value="mt_bergen_agency" {{ old('agent_type', $agent->agent_type) == 'mt_bergen_agency' ? 'selected' : '' }}>MT Bergen Agency supplier</option>
                        <option value="mt_singapore_projects" {{ old('agent_type', $agent->agent_type) == 'mt_singapore_projects' ? 'selected' : '' }}>MT Singapore Projects supplier
                        </option>
                        <option value="mt_benelux_supplier" {{ old('agent_type', $agent->agent_type) == 'mt_benelux_supplier' ? 'selected' : '' }}>MT Benelux supplier</option>
                        <option value="door_to_deck_agent" {{ old('agent_type', $agent->agent_type) == 'door_to_deck_agent' ? 'selected' : '' }}>Door to Deck agent</option>
                        <option value="mt_singapore_agency" {{ old('agent_type', $agent->agent_type) == 'mt_singapore_agency' ? 'selected' : '' }}>MT Singapore Agency supplier</option>
                        <option value="mt_norway_supplier" {{ old('agent_type', $agent->agent_type) == 'mt_norway_supplier' ? 'selected' : '' }}>MT Norway supplier</option>
                        <option value="3pl_general_supplier" {{ old('agent_type', $agent->agent_type) == '3pl_general_supplier' ? 'selected' : '' }}>3PL General supplier</option>
                        <option value="external_entity" {{ old('agent_type', $agent->agent_type) == 'external_entity' ? 'selected' : '' }}>External entity</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

</div> <!-- End #agent-details -->

<!-- Billing Details Tab -->
<div id="billing-details" class="tab-content-custom">
    <div class="form-pillar-container">
        <!-- Column 1: Invoicing details -->
        <div class="form-pillar">
            <div class="form-section-header">Invoicing details</div>

            <div class="form-group-custom">
                <label class="form-label-custom">Invoicing name</label>
                <input type="text" class="form-input-custom" name="invoicing_name"
                    value="{{ old('invoicing_name', $agent->invoicing_name) }}">
            </div>

            <div class="form-group-custom">
                <label class="form-label-custom">Address</label>
                <textarea class="form-textarea-custom" name="billing_address"
                    rows="3">{{ old('billing_address', $agent->billing_address) }}</textarea>
            </div>

            <div class="input-row">
                <div class="form-group-custom" style="flex: 2;">
                    <label class="form-label-custom">City</label>
                    <div class="input-group-custom">
                        <input type="text" class="form-input-custom has-append"
                            name="billing_city"
                            value="{{ old('billing_city', $agent->billing_city) }}">
                        <button type="button" class="btn-input-append"><i
                                class="ti-more-alt"></i></button>
                    </div>
                </div>
                <div class="form-group-custom">
                    <label class="form-label-custom">District</label>
                    <input type="text" class="form-input-custom"
                        name="billing_district_state"
                        value="{{ old('billing_district_state', $agent->billing_district_state) }}">
                </div>
                <div class="form-group-custom">
                    <label class="form-label-custom">Zip</label>
                    <input type="text" class="form-input-custom"
                        name="billing_zip_code"
                        value="{{ old('billing_zip_code', $agent->billing_zip_code) }}">
                </div>
            </div>

            <x-forms.country-select
                name="billing_country_id"
                label="Country"
                :countries="$countries"
                :value="$agent->billing_country_id"
                class="form-input-custom"
            />

            <div class="form-group-custom">
                <label class="form-label-custom">E-mails for invoicing</label>
                <input type="text" class="form-input-custom" name="invoicing_emails"
                    value="{{ old('invoicing_emails', $agent->invoicing_emails) }}">
            </div>

            <div class="form-group-custom">
                <label class="form-label-custom">E-mails for invoicing (CC)</label>
                <input type="text" class="form-input-custom"
                    name="invoicing_emails_cc"
                    value="{{ old('invoicing_emails_cc', $agent->invoicing_emails_cc) }}">
            </div>

            <div class="input-row">
                <div class="form-group-custom">
                    <label class="form-label-custom">VAT number</label>
                    <input type="text" class="form-input-custom" name="vat_number"
                        value="{{ old('vat_number', $agent->vat_number ?? '01365110996') }}">
                </div>
                <div class="form-group-custom">
                    <label class="form-label-custom">Invoicing Frequency</label>
                    <select class="form-input-custom" name="invoicing_frequency">
                        <option value=""></option>
                        <option value="monthly" {{ old('invoicing_frequency', $agent->invoicing_frequency) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="per shipment" {{ old('invoicing_frequency', $agent->invoicing_frequency) == 'per shipment' ? 'selected' : '' }}>Per Shipment</option>
                    </select>
                </div>
            </div>

            <div class="input-row" style="align-items: flex-end;">
                <div class="form-group-custom"
                    style="display: flex; gap: 8px; align-items: center; margin-bottom: 8px;">
                    <input type="checkbox" name="applies_to_rebate" value="1" {{ old('applies_to_rebate', $agent->applies_to_rebate) ? 'checked' : '' }}>
                    <label class="form-label-custom"
                        style="margin-bottom: 0;">Applies
                        to rebate</label>
                </div>
                <div class="form-group-custom">
                    <label class="form-label-custom">Rebate percentage</label>
                    <input type="text" class="form-input-custom"
                        name="rebate_percentage"
                        value="{{ old('rebate_percentage', $agent->rebate_percentage) }}">
                </div>
            </div>
        </div>

        <!-- Column 2: Other billing sections -->
        <div class="form-pillar">
            <div class="form-section-header">Outgoing invoices to agent</div>
            <div class="input-row">
                <div class="form-group-custom">
                    <label class="form-label-custom">Currency</label>
                    <select class="form-input-custom select2-currency-edit"
                        name="outgoing_currency" style="width: 100%;">
                        <option value=""></option>
                        @foreach($countries->pluck('currency')->filter()->unique()->sort() as $currency)
                            <option value="{{ $currency }}" {{ old('outgoing_currency', $agent->outgoing_currency ?? 'EUR') == $currency ? 'selected' : '' }}>{{ $currency }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group-custom">
                    <label class="form-label-custom">Payment terms</label>
                    <input type="text" class="form-input-custom"
                        name="outgoing_payment_terms"
                        value="{{ old('outgoing_payment_terms', $agent->outgoing_payment_terms) }}">
                </div>
            </div>

            <div class="form-section-header" style="margin-top: 15px;">Incoming
                invoices
                from agent</div>
            <div class="input-row">
                <div class="form-group-custom">
                    <label class="form-label-custom">Currency</label>
                    <select class="form-input-custom select2-currency-edit"
                        name="incoming_currency" style="width: 100%;">
                        <option value=""></option>
                        @foreach($countries->pluck('currency')->filter()->unique()->sort() as $currency)
                            <option value="{{ $currency }}" {{ old('incoming_currency', $agent->incoming_currency ?? 'EUR') == $currency ? 'selected' : '' }}>{{ $currency }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group-custom">
                    <label class="form-label-custom">Payment terms</label>
                    <input type="text" class="form-input-custom"
                        name="incoming_payment_terms"
                        value="{{ old('incoming_payment_terms', $agent->incoming_payment_terms ?? '60') }}">
                </div>
            </div>

            <div class="form-section-header"
                style="margin-top: 15px; margin-bottom: 10px;">Billing exceptions
            </div>

            <div class="billing-exceptions-wrapper">
                <table class="table mb-2" id="billing-exceptions-table"
                    style="font-size: 11px; width: 100%; border-collapse: collapse; {{ $agent->billingExceptions->count() > 0 ? '' : 'display: none;' }}">
                    <thead style="background: #e9ecef;">
                        <tr>
                            <th
                                style="font-weight: 500; border: 1px solid #dee2e6; padding: 6px 10px; color: #1b5e6f;">
                                Billing office</th>
                            <th
                                style="font-weight: 500; border: 1px solid #dee2e6; padding: 6px 10px; color: #1b5e6f;">
                                Invoice to agent</th>
                            <th
                                style="font-weight: 500; border: 1px solid #dee2e6; padding: 6px 10px; color: #1b5e6f;">
                                Currency</th>
                            <th
                                style="font-weight: 500; border: 1px solid #dee2e6; padding: 6px 10px; color: #1b5e6f;">
                                Paym. terms</th>
                            <th
                                style="border: 1px solid #dee2e6; background: #e9ecef; width: 30px;">
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($agent->billingExceptions as $exception)
                            <tr class="billing-exception-row">
                                <td style="padding: 0; border: 1px solid #dee2e6;">
                                    <input type="text"
                                        style="width: 100%; border: none; padding: 6px 10px; font-size: 11px; outline: none; box-sizing: border-box;"
                                        name="billing_exceptions[office][]"
                                        value="{{ $exception->office }}">
                                </td>
                                <td
                                    style="padding: 0; border: 1px solid #dee2e6; position: relative;">
                                    <select
                                        style="width: 100%; border: none; padding: 6px 20px 6px 10px; font-size: 11px; outline: none; box-sizing: border-box; appearance: none; background: transparent; cursor: pointer;"
                                        name="billing_exceptions[invoice_to_agent][]">
                                        <option value="incoming" {{ $exception->invoice_to_agent == 'incoming' ? 'selected' : '' }}>Incoming</option>
                                        <option value="outgoing" {{ $exception->invoice_to_agent == 'outgoing' ? 'selected' : '' }}>Outgoing</option>
                                        <option value="both" {{ $exception->invoice_to_agent == 'both' ? 'selected' : '' }}>Both</option>
                                    </select>
                                    <i class="ti-angle-down"
                                        style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); color: #666; font-size: 9px; pointer-events: none;"></i>
                                </td>
                                <td
                                    style="padding: 0; border: 1px solid #dee2e6; position: relative;">
                                    <select
                                        class="form-input-custom select2-currency-edit"
                                        style="width: 100%; border: none; padding: 6px 20px 6px 10px; font-size: 11px; outline: none; box-sizing: border-box; appearance: none; background: transparent; cursor: pointer;"
                                        name="billing_exceptions[currency][]">
                                        <option value=""></option>
                                        @foreach($countries->pluck('currency')->filter()->unique()->sort() as $currency)
                                            <option value="{{ $currency }}" {{ $exception->currency == $currency ? 'selected' : '' }}>{{ $currency }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td style="padding: 0; border: 1px solid #dee2e6;">
                                    <input type="text"
                                        style="width: 100%; border: none; padding: 6px 10px; font-size: 11px; outline: none; box-sizing: border-box;"
                                        name="billing_exceptions[payment_terms][]"
                                        value="{{ $exception->payment_terms }}">
                                </td>
                                <td
                                    style="padding: 6px; border: 1px solid #dee2e6; text-align: center; vertical-align: middle;">
                                    <button type="button" class="btn-remove-exception"
                                        style="background: none; border: none; color: #1b5e6f; cursor: pointer; padding: 0;"><i
                                            class="ti-trash"
                                            style="font-size: 13px;"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div
                    style="display: flex; justify-content: flex-end; margin-top: 10px;">
                    <button type="button" id="btn-add-billing-exception"
                        style="background: #fff; border: 1px solid #d1d5db; color: #1b5e6f; font-size: 11px; padding: 4px 35px; border-radius: 4px; cursor: pointer;">Add</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- SOP Tab -->
<div id="sop" class="tab-content-custom">
    <div class="form-pillar-container"
        style="grid-template-columns: 1fr 1fr; gap: 80px;">
        <!-- Column 1: SOP details -->
        <div class="form-pillar">
            <div class="form-section-header">SOP details</div>

            <div class="input-row" style="margin-top: 10px; margin-bottom: 15px;">
                <div class="form-group-custom"
                    style="display: flex; gap: 8px; align-items: center;">
                    <input type="checkbox" id="coc_signed" name="coc_signed"
                        value="1" {{ old('coc_signed', $agent->coc_signed) ? 'checked' : '' }}>
                    <label for="coc_signed" class="form-label-custom"
                        style="margin-bottom: 0;">Code of Conduct signed</label>
                </div>
                <div class="form-group-custom"
                    style="display: flex; gap: 8px; align-items: center;">
                    <input type="checkbox" id="sop_implemented"
                        name="sop_implemented" value="1" {{ old('sop_implemented', $agent->sop_implemented) ? 'checked' : '' }}>
                    <label for="sop_implemented" class="form-label-custom"
                        style="margin-bottom: 0;">SOP implemented</label>
                </div>
            </div>

            <div class="input-row">
                <div class="form-group-custom">
                    <label class="form-label-custom">Code of Conduct signed
                        date</label>
                    <input type="date" class="form-input-custom"
                        name="coc_signed_date"
                        value="{{ old('coc_signed_date', $agent->coc_signed_date ? $agent->coc_signed_date->format('Y-m-d') : '') }}">
                </div>
                <div class="form-group-custom">
                    <label class="form-label-custom">Responsible manager</label>
                    <input type="text" class="form-input-custom"
                        name="responsible_manager"
                        value="{{ old('responsible_manager', $agent->responsible_manager) }}">
                </div>
            </div>
        </div>

        <!-- Column 2: Imported documents -->
        <div class="form-pillar">
            <div class="form-section-header">Imported documents</div>

            <div class="document-list">
                @foreach($agent->documents->where('section', 'sop') as $document)
                    <div class="document-item" style="margin-top: 10px;">
                        <div class="doc-info">
                            <span class="doc-name"><a href="{{ $document->fileUrl() }}" target="_blank" style="color: inherit; text-decoration: none;">{{ $document->filename }}</a></span>
                            <span class="doc-meta">Uploaded
                                {{ $document->created_at->format('d.m.Y H:i') }}</span>
                        </div>
                        <button type="button" class="btn-delete-doc"
                            onclick="deleteAgentDocument({{ $document->id }})"><i
                                class="ti-trash"></i></button>
                    </div>
                @endforeach
            </div>

            <div class="drag-drop-area"
                onclick="document.getElementById('sop_file_upload').click()">
                Drag files here or click to browse
                <i class="ti-export"></i>
                <input type="file" id="sop_file_upload" name="sop_documents[]"
                    multiple style="display: none;">
            </div>
        </div>
    </div>
</div>
<!-- Pricing Tab -->
<div id="pricing" class="tab-content-custom">
    <div class="form-pillar-container"
        style="grid-template-columns: 1fr 1fr; gap: 80px;">
        <!-- Column 1: Pricing details -->
        <div class="form-pillar">
            <div class="form-section-header">Pricing details</div>

            <div class="input-row" style="margin-top: 10px; margin-bottom: 15px;">
                <div class="form-group-custom"
                    style="display: flex; gap: 8px; align-items: center;">
                    <input type="checkbox" id="calculate_sell_rates"
                        name="calculate_sell_rates" value="1" {{ old('calculate_sell_rates', $agent->calculate_sell_rates) ? 'checked' : '' }}>
                    <label for="calculate_sell_rates" class="form-label-custom"
                        style="margin-bottom: 0;">Calculate sell rates by
                        formula</label>
                </div>
                <div class="form-group-custom" style="flex: 1.5;">
                    <label class="form-label-custom">Agreement type</label>
                    <select class="form-input-custom" name="agreement_type">
                        <option value="framework" {{ old('agreement_type', $agent->agreement_type) == 'framework' ? 'selected' : '' }}>Framework</option>
                        <option value="price_sheet" {{ old('agreement_type', $agent->agreement_type) == 'price_sheet' ? 'selected' : '' }}>Price Sheet</option>
                    </select>
                </div>
            </div>

            <div class="input-row">
                <div class="form-group-custom">
                    <label class="form-label-custom">Purchase Rate</label>
                    <input type="text" class="form-input-custom"
                        name="purchase_rate"
                        value="{{ old('purchase_rate', $agent->purchase_rate) }}">
                </div>
                <div class="form-group-custom">
                    <label class="form-label-custom">Sell Rate</label>
                    <input type="text" class="form-input-custom" name="sell_rate"
                        value="{{ old('sell_rate', $agent->sell_rate) }}">
                </div>
                <div class="form-group-custom">
                    <label class="form-label-custom">Profit</label>
                    <input type="text" class="form-input-custom" name="profit"
                        value="{{ old('profit', $agent->profit) }}">
                </div>
            </div>
        </div>

        <!-- Column 2: Imported documents -->
        <div class="form-pillar">
            <div class="form-section-header">Imported documents</div>

            <div class="document-list">
                @foreach($agent->documents->where('section', 'pricing') as $document)
                    <div class="document-item" style="margin-top: 10px;">
                        <div class="doc-info">
                            <span class="doc-name"><a href="{{ $document->fileUrl() }}" target="_blank" style="color: inherit; text-decoration: none;">{{ $document->filename }}</a></span>
                            <span class="doc-meta">Uploaded
                                {{ $document->created_at->format('d.m.Y H:i') }}</span>
                        </div>
                        <button type="button" class="btn-delete-doc"
                            onclick="deleteAgentDocument({{ $document->id }})"><i
                                class="ti-trash"></i></button>
                    </div>
                @endforeach
            </div>

            <div class="drag-drop-area"
                onclick="document.getElementById('pricing_file_upload').click()">
                Drag files here or click to browse
                <i class="ti-export"></i>
                <input type="file" id="pricing_file_upload"
                    name="pricing_documents[]" multiple style="display: none;">
            </div>
        </div>
    </div>
</div>

<div id="email-settings" class="tab-content-custom">
    <div class="form-pillar-container">
        <!-- Column 1: Export email settings -->
        <div class="form-pillar">
            <div class="form-section-header">Export email settings</div>
            <div class="form-group-custom">
                <label class="form-label-custom">Select services to add export
                    emails</label>
                <div class="input-group-custom">
                    <div style="position: relative; width: 100%;">
                        <select class="form-input-custom"
                            name="export_email_services"
                            style="padding-right: 30px;">
                            <option value=""></option>
                            <option value="airfreight" {{ old('export_email_services', $agent->export_email_services) == 'airfreight' ? 'selected' : '' }}>Airfreight</option>
                            <option value="seafreight" {{ old('export_email_services', $agent->export_email_services) == 'seafreight' ? 'selected' : '' }}>Seafreight</option>
                            <option value="courier" {{ old('export_email_services', $agent->export_email_services) == 'courier' ? 'selected' : '' }}>Courier</option>
                            <option value="onboarding_delivery" {{ old('export_email_services', $agent->export_email_services) == 'onboarding_delivery' ? 'selected' : '' }}>Onboarding delivery</option>
                            <option value="release" {{ old('export_email_services', $agent->export_email_services) == 'release' ? 'selected' : '' }}>Release</option>
                            <option value="truck" {{ old('export_email_services', $agent->export_email_services) == 'truck' ? 'selected' : '' }}>Truck</option>
                            <option value="hand_carry" {{ old('export_email_services', $agent->export_email_services) == 'hand_carry' ? 'selected' : '' }}>Hand carry</option>
                        </select>
                        <div
                            style="position: absolute; right: 5px; top: 50%; transform: translateY(-50%); display: flex; align-items: center; gap: 5px; pointer-events: none;">
                            <i class="ti-more-alt"
                                style="color: #999; font-size: 10px; background: #eee; padding: 2px 4px; border-radius: 2px;"></i>
                            <i class="ti-angle-down"
                                style="color: #999; font-size: 10px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Column 2: Import email settings -->
        <div class="form-pillar">
            <div class="form-section-header">Import email settings</div>
            <div class="form-group-custom">
                <label class="form-label-custom">Select services to add import
                    emails</label>
                <div style="position: relative; width: 100%;">
                    <select class="form-input-custom" name="import_email_services">
                        <option value=""></option>
                        <option value="airfreight" {{ old('export_email_services', $agent->export_email_services) == 'airfreight' ? 'selected' : '' }}>Airfreight</option>
                        <option value="seafreight" {{ old('export_email_services', $agent->export_email_services) == 'seafreight' ? 'selected' : '' }}>Seafreight</option>
                        <option value="courier" {{ old('export_email_services', $agent->export_email_services) == 'courier' ? 'selected' : '' }}>Courier</option>
                        <option value="onboarding_delivery" {{ old('export_email_services', $agent->export_email_services) == 'onboarding_delivery' ? 'selected' : '' }}>Onboarding delivery</option>
                        <option value="release" {{ old('export_email_services', $agent->export_email_services) == 'release' ? 'selected' : '' }}>Release</option>
                        <option value="truck" {{ old('export_email_services', $agent->export_email_services) == 'truck' ? 'selected' : '' }}>Truck</option>
                        <option value="hand_carry" {{ old('export_email_services', $agent->export_email_services) == 'hand_carry' ? 'selected' : '' }}>Hand carry</option>
                    </select>
                    <i class="ti-angle-down"
                        style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); color: #999; font-size: 10px; pointer-events: none;"></i>
                </div>
            </div>
        </div>

        <!-- Column 3: Other email settings -->
        <div class="form-pillar">
            <div class="form-section-header">Other email settings</div>
            <div class="form-group-custom">
                <label class="form-label-custom">Send "Status changed" emails
                    to</label>
                <input type="text" class="form-input-custom"
                    name="status_changed_emails"
                    value="{{ old('status_changed_emails', $agent->status_changed_emails) }}">
            </div>
            <div class="form-group-custom">
                <label class="form-label-custom">Send "Stock item changed" emails
                    to</label>
                <input type="text" class="form-input-custom"
                    name="stock_item_changed_emails"
                    value="{{ old('stock_item_changed_emails', $agent->stock_item_changed_emails) }}">
            </div>
            <div class="form-group-custom">
                <label class="form-label-custom">Send quote requests emails
                    to</label>
                <input type="text" class="form-input-custom"
                    name="quote_requests_emails"
                    value="{{ old('quote_requests_emails', $agent->quote_requests_emails) }}">
            </div>
        </div>
    </div>
</div>
<!-- Scan gun Tab -->
<div id="scan-gun" class="tab-content-custom">
    <div class="form-pillar-container"
        style="grid-template-columns: 1fr; max-width: 400px;">
        <!-- Credentials Section -->
        <div class="form-pillar">
            <div class="form-section-header">Credentials</div>

            <div class="form-group-custom">
                <label class="form-label-custom">Login</label>
                <div style="position: relative; width: 300px;">
                    <input type="text" class="form-input-custom"
                        name="scangun_login"
                        value="{{ old('scangun_login', $agent->scangun_login) }}"
                        style="padding-right: 35px;">
                    <div
                        style="position: absolute; right: 5px; top: 50%; transform: translateY(-50%); pointer-events: none;">
                        <i class="ti-more-alt"
                            style="color: #999; font-size: 10px; background: #eee; padding: 2px 4px; border-radius: 2px;"></i>
                    </div>
                </div>
            </div>

            <div class="form-group-custom"
                style="display: flex; align-items: center; gap: 8px; margin-top: 15px; margin-bottom: 15px;">
                <input type="checkbox" id="set_new_password" checked>
                <label for="set_new_password" class="form-label-custom"
                    style="margin-bottom: 0;">Set a new password</label>
            </div>

            <div class="form-group-custom">
                <label class="form-label-custom">Password</label>
                <div style="position: relative; width: 300px;">
                    <input type="password" class="form-input-custom"
                        name="scangun_password"
                        value="{{ old('scangun_password', $agent->scangun_password) }}"
                        style="padding-right: 60px;">
                    <div
                        style="position: absolute; right: 5px; top: 50%; transform: translateY(-50%); display: flex; align-items: center; gap: 8px;">
                        <i class="ti-more-alt"
                            style="color: #999; font-size: 10px; background: #eee; padding: 2px 4px; border-radius: 2px; pointer-events: none;"></i>
                        <i class="ti-eye"
                            style="color: #333; font-size: 14px; cursor: pointer; font-weight: bold;"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div class="form-pillar" style="margin-top: 20px;">
            <div class="form-section-header">Features</div>

            <div class="agent-checkbox-stack">
                <div class="checkbox-group">
                    <input type="checkbox" class="checkbox-custom" id="enable_picture_taking"
                        name="scangun_enable_picture" value="1" {{ old('scangun_enable_picture', $agent->scangun_enable_picture) ? 'checked' : '' }}>
                    <label class="checkbox-label" for="enable_picture_taking">Enable picture taking in scangun app</label>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" class="checkbox-custom" id="enable_detailed_shipment"
                        name="scangun_enable_detailed_shipment" value="1" {{ old('scangun_enable_detailed_shipment', $agent->scangun_enable_detailed_shipment) ? 'checked' : '' }}>
                    <label class="checkbox-label" for="enable_detailed_shipment">Enable detailed shipment out</label>
                </div>
            </div>
        </div>
    </div>
</div>
                </form>

                <!-- Agent users / contacts tabs live outside agentEditForm. -->
                <div id="agent-users" class="tab-content-custom">
                    <div class="agent-pane-toolbar">
                        <a href="{{ route('agents.users.create', $agent->id) }}" class="btn-agent-pane-action">Add agent user</a>
                    </div>
                    <div class="agent-table-wrap">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th style="width: 25%;">Name</th>
                                    <th style="width: 25%;">Email</th>
                                    <th style="width: 20%;">Phone number</th>
                                    <th style="width: 20%;">Description</th>
                                    <th style="width: 10%; text-align: right;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($agent->agentUsers as $user)
                                    <tr>
                                        <td>
                                            <a href="{{ route('agents.users.edit', $user->id) }}" class="table-link">{{ $user->name }}</a>
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->phone_number }}</td>
                                        <td>{{ $user->description }}</td>
                                        <td class="text-right">
                                            <a href="{{ route('agents.users.edit', $user->id) }}">
                                                <i class="ti-pencil btn-action-pencil"></i>
                                            </a>
                                            <button type="button" class="btn-action-delete" onclick="deleteAgentUser({{ $user->id }})">
                                                <i class="ti-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" style="text-align: center; padding: 40px; color: #8da2b5;">No agent users found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="contacts" class="tab-content-custom">
                    <div class="agent-pane-toolbar">
                        <a href="{{ route('agents.contacts.create', $agent->id) }}" class="btn-agent-pane-action">Add contact</a>
                    </div>
                    <div class="agent-table-wrap">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th style="width: 25%;">Name</th>
                                    <th style="width: 25%;">Email</th>
                                    <th style="width: 20%;">Phone number</th>
                                    <th style="width: 20%;">Description</th>
                                    <th style="width: 5%; text-align: center;">Main</th>
                                    <th style="width: 5%; text-align: right;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($agent->contacts as $contact)
                                    <tr>
                                        <td>
                                            <a href="{{ route('agents.contacts.edit', $contact->id) }}" class="table-link">{{ $contact->name }}</a>
                                        </td>
                                        <td>{{ $contact->email }}</td>
                                        <td>{{ $contact->phone_number }}</td>
                                        <td>{{ $contact->description }}</td>
                                        <td class="text-center">
                                            @if($contact->is_main_contact)
                                                <i class="ti-check" style="color: #01a9ac;"></i>
                                            @endif
                                        </td>
                                        <td class="text-right">
                                            <a href="{{ route('agents.contacts.edit', $contact->id) }}">
                                                <i class="ti-pencil btn-action-pencil"></i>
                                            </a>
                                            <button type="button" class="btn-action-delete" onclick="deleteAgentContact({{ $contact->id }})">
                                                <i class="ti-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" style="text-align: center; padding: 40px; color: #8da2b5;">No contacts found for this agent.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

        <div class="agent-edit-footer">
            <button type="submit" class="btn-save-custom" form="agentEditForm">Save agent</button>
            <a href="{{ route('agents.index') }}" class="btn-cancel-custom">Cancel</a>
            <div class="audit-info">
                @include('partials.audit-info', ['record' => $agent, 'bold' => true])
            </div>
        </div>
    </div>

    @include('layouts.partials.pcoded-shell-end')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#agentEditForm').validate({
                rules: {
                    agent_name: {
                        required: true,
                        minlength: 3
                    },
                    contact_person: {
                        required: true
                    }
                },
                messages: {
                    agent_name: {
                        required: "Please enter the agent name",
                        minlength: "Agent name must be at least 3 characters"
                    },
                    contact_person: {
                        required: "Please enter the contact person"
                    }
                },
                errorElement: 'div',
                errorClass: 'error-message',
                errorPlacement: function (error, element) {
                    error.insertAfter(element);
                },
                highlight: function (element) {
                    $(element).addClass("error");
                },
                unhighlight: function (element) {
                    $(element).removeClass("error");
                }
            });

            // Initialize Select2 for standard filters
            $('.select2').select2({
                placeholder: "Click here",
                allowClear: false
            });

            // Initialize Select2 for Agent Type
            $('.select2-agent-type-edit').select2({
                placeholder: 'Select agent type',
                allowClear: false,
                width: '100%'
            });

            // Initialize Select2 for Currency
            $('.select2-currency-edit').select2({
                placeholder: 'Select currency',
                allowClear: false,
                width: '100%'
            });

            // Tab switching logic (keeps URL hash + hidden field so update redirects restore the active tab)
            function activateAgentTab(tabId) {
                if (!tabId || !$('#' + tabId).length || !$('.tab-item[data-tab="' + tabId + '"]').length) {
                    return false;
                }
                $('.tab-item').removeClass('active');
                $('.tab-item[data-tab="' + tabId + '"]').addClass('active');
                $('.tab-content-custom').removeClass('active');
                $('#' + tabId).addClass('active');
                $('#active_tab').val(tabId);
                return true;
            }

            $('.tab-item').on('click', function (e) {
                e.preventDefault();
                var tabId = $(this).data('tab');
                activateAgentTab(tabId);
                if (history.replaceState) {
                    history.replaceState(null, '', '#' + tabId);
                } else {
                    window.location.hash = tabId;
                }
            });

            var hashTab = window.location.hash.replace(/^#/, '');
            if (hashTab) {
                activateAgentTab(hashTab);
            }
            // Dynamic Billing Exceptions
            var currencyOptionsHtml = '<option value=""></option>';
            @foreach($countries->pluck('currency')->filter()->unique()->sort() as $currency)
                currencyOptionsHtml += '<option value="{{ $currency }}">{{ $currency }}</option>';
            @endforeach

            $('#btn-add-billing-exception').on('click', function () {
                var table = $('#billing-exceptions-table');
                table.show(); // Ensure table is visible when a row is added

                var rowHtml = `
                                                                                                                                        <tr class="billing-exception-row">
                                                                                                                                            <td style="padding: 0; border: 1px solid #dee2e6;">
                                                                                                                                                <input type="text" style="width: 100%; border: none; padding: 6px 10px; font-size: 11px; outline: none; box-sizing: border-box;" name="billing_exceptions[office][]">
                                                                                                                                            </td>
                                                                                                                                            <td style="padding: 0; border: 1px solid #dee2e6; position: relative;">
                                                                                                                                                <select style="width: 100%; border: none; padding: 6px 20px 6px 10px; font-size: 11px; outline: none; box-sizing: border-box; appearance: none; background: transparent; cursor: pointer;" name="billing_exceptions[invoice_to_agent][]">
                                                                                                                                                    <option value="incoming">Incoming</option>
                                                                                                                                                    <option value="outgoing">Outgoing</option>
                                                                                                                                                    <option value="both">Both</option>
                                                                                                                                                </select>
                                                                                                                                                <i class="ti-angle-down" style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); color: #666; font-size: 9px; pointer-events: none;"></i>
                                                                                                                                            </td>
                                                                                                                                            <td style="padding: 0; border: 1px solid #dee2e6; position: relative;">
                                                                                                                                                <select class="form-input-custom select2-currency-dynamic" style="width: 100%; border: none; padding: 6px 20px 6px 10px; font-size: 11px; outline: none; box-sizing: border-box; appearance: none; background: transparent; cursor: pointer;" name="billing_exceptions[currency][]">
                                                                                                                                                    ${currencyOptionsHtml}
                                                                                                                                                </select>
                                                                                                                                            </td>
                                                                                                                                            <td style="padding: 0; border: 1px solid #dee2e6;">
                                                                                                                                                <input type="text" style="width: 100%; border: none; padding: 6px 10px; font-size: 11px; outline: none; box-sizing: border-box;" name="billing_exceptions[payment_terms][]">
                                                                                                                                            </td>
                                                                                                                                            <td style="padding: 6px; border: 1px solid #dee2e6; text-align: center; vertical-align: middle;">
                                                                                                                                                <button type="button" class="btn-remove-exception" style="background: none; border: none; color: #1b5e6f; cursor: pointer; padding: 0;"><i class="ti-trash" style="font-size: 13px;"></i></button>
                                                                                                                                            </td>
                                                                                                                                        </tr>
                                                                                                                                    `;
                var newRow = $(rowHtml);
                table.find('tbody').append(newRow);

                // Initialize Select2 for the dynamically added dropdown
                newRow.find('.select2-currency-dynamic').select2({
                    placeholder: 'Select currency',
                    allowClear: false,
                    width: '100%'
                });
            });

            $(document).on('click', '.btn-remove-exception', function () {
                $(this).closest('tr').remove();
                if ($('#billing-exceptions-table tbody tr').length === 0) {
                    $('#billing-exceptions-table').hide();
                }
            });

        });

        function postAgentDeleteForm(action) {
            if (typeof window.unsavedChangesGuardAllowLeave === 'function') {
                window.unsavedChangesGuardAllowLeave();
            }

            var form = document.createElement('form');
            form.method = 'POST';
            form.action = action;

            var csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);

            var methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            form.appendChild(methodField);

            document.body.appendChild(form);
            form.submit();
        }

        function confirmAgentDeleteSubmit(action, message) {
            if (typeof swal !== 'function') {
                if (window.confirm(message)) {
                    postAgentDeleteForm(action);
                }
                return;
            }

            swal({
                title: 'Are you sure?',
                text: message,
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#01a9ac',
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel',
                closeOnConfirm: true,
                closeOnCancel: true
            }, function (isConfirm) {
                if (!isConfirm) {
                    return;
                }
                postAgentDeleteForm(action);
            });
        }

        function deleteAgentUser(id) {
            confirmAgentDeleteSubmit(
                '{{ url('/Agents/users/destroy') }}/' + id,
                'Are you sure you want to delete this user?'
            );
        }

        function deleteAgentContact(id) {
            confirmAgentDeleteSubmit(
                '{{ url('/Agents/contacts/destroy') }}/' + id,
                'Are you sure you want to delete this contact?'
            );
        }

        function deleteAgentDocument(id) {
            confirmAgentDeleteSubmit(
                '{{ url('/Agents/documents') }}/' + id,
                'Are you sure you want to delete this document?'
            );
        }
    </script>

@include('partials.unsaved-changes-guard', ['formSelector' => '#agentEditForm', 'fallbackUrl' => route('agents.index')])
@endsection
