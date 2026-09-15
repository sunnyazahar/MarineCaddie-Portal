@extends('layouts.app')

@section('styles')
    @include('Suppliers.partials.edit-page-styles')
@endsection

@section('content')
    <script>document.body.classList.add('edit-supplier-page');</script>

    @include('layouts.partials.pcoded-shell-start', ['pageWrapperClass' => 'p-0'])

    <div class="edit-supplier-page">
        <div class="edit-supplier-hero">
            <div class="edit-supplier-hero-main">
                <span class="edit-supplier-hero-icon" aria-hidden="true">
                    <i class="ti-truck"></i>
                </span>
                <div>
                    <p class="edit-supplier-kicker">Administration</p>
                    <h1 class="edit-supplier-title">{{ $supplier->supplier_name }}</h1>
                    <p class="edit-supplier-sub">Edit supplier profile, location, and contact persons.</p>
                </div>
            </div>
            <a href="{{ route('suppliers.index') }}" class="edit-supplier-back">
                <i class="ti-arrow-left"></i> Back to suppliers
            </a>
        </div>

        <div class="edit-supplier-meta">
            <span class="edit-supplier-meta-pill">Supplier ID <strong>{{ $supplier->id }}</strong></span>
            @if ($supplier->city || $supplier->country)
                <span class="edit-supplier-meta-pill">
                    Location
                    <strong>{{ trim(($supplier->city ? $supplier->city . ', ' : '') . ($supplier->country?->name ?? '')) }}</strong>
                </span>
            @endif
            <span class="edit-supplier-meta-pill is-active">Status <strong>Active</strong></span>
        </div>

        <div class="tabs-container">
            <a class="tab-item active" data-tab="supplier-details"><i class="ti-home"></i> Supplier details</a>
            <a class="tab-item" data-tab="contacts"><i class="ti-id-badge"></i> Contacts</a>
        </div>

        <div class="edit-supplier-card">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show edit-supplier-alert" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            @endif
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show edit-supplier-alert" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            @endif

            <div class="tab-content-container">
                <form id="edit-supplier-form" action="{{ route('suppliers.update', $supplier->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="active_tab" id="active_tab" value="{{ old('active_tab', 'supplier-details') }}">

                    <div id="supplier-details" class="tab-content-custom active">
                        <div class="form-pillar-container sup-details-grid">
                            <div class="form-pillar sup-pillar-card">
                                <div class="sup-pillar-head">
                                    <span class="sup-pillar-head-icon" aria-hidden="true"><i class="ti-briefcase"></i></span>
                                    <div>
                                        <div class="sup-pillar-head-title">Identity &amp; contact</div>
                                        <p class="sup-pillar-head-sub">Core supplier profile and how to reach the desk.</p>
                                    </div>
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="edit_supplier_name">Supplier name <span class="text-danger">*</span></label>
                                    <input type="text" id="edit_supplier_name" name="supplier_name" class="form-control-custom"
                                        value="{{ old('supplier_name', $supplier->supplier_name) }}" required autocomplete="organization">
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="edit_phone_number">Phone number (with country code) <span class="text-danger">*</span></label>
                                    <input type="text" id="edit_phone_number" name="phone_number" class="form-control-custom"
                                        value="{{ old('phone_number', $supplier->phone_number) }}" required autocomplete="tel">
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="edit_email">Email <span class="text-danger">*</span></label>
                                    <input type="email" id="edit_email" name="email" class="form-control-custom"
                                        value="{{ old('email', $supplier->email) }}" required>
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="edit_contact_person">Contact person <span class="text-danger">*</span></label>
                                    <input type="text" id="edit_contact_person" name="contact_person" class="form-control-custom"
                                        value="{{ old('contact_person', $supplier->contact_person) }}" required autocomplete="name">
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="edit_remarks">Remarks</label>
                                    <textarea id="edit_remarks" name="remarks" class="form-textarea-custom" rows="3">{{ old('remarks', $supplier->remarks) }}</textarea>
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="edit_special_considerations">Special considerations for destination</label>
                                    <textarea id="edit_special_considerations" name="special_considerations" class="form-textarea-custom" rows="3">{{ old('special_considerations', $supplier->special_considerations) }}</textarea>
                                </div>
                            </div>

                            <div class="form-pillar sup-pillar-card">
                                <div class="sup-pillar-head">
                                    <span class="sup-pillar-head-icon is-location" aria-hidden="true"><i class="ti-map-alt"></i></span>
                                    <div>
                                        <div class="sup-pillar-head-title">Location</div>
                                        <p class="sup-pillar-head-sub">Physical supplier address and port identifiers.</p>
                                    </div>
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="edit_supplier_address">Street address <span class="text-danger">*</span></label>
                                    <textarea id="edit_supplier_address" name="supplier_address" class="form-textarea-custom" rows="3" required>{{ old('supplier_address', $supplier->supplier_address) }}</textarea>
                                </div>

                                <div class="input-row sup-details-input-row">
                                    <div class="form-group-custom" style="flex: 2;">
                                        <label class="form-label-custom" for="edit_city">City <span class="text-danger">*</span></label>
                                        <input type="text" id="edit_city" name="city" class="form-control-custom"
                                            value="{{ old('city', $supplier->city) }}" required>
                                    </div>
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="edit_district_state">District/state</label>
                                        <input type="text" id="edit_district_state" name="district_state" class="form-control-custom"
                                            value="{{ old('district_state', $supplier->district_state) }}">
                                    </div>
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="edit_zip_code">Zip code</label>
                                        <input type="text" id="edit_zip_code" name="zip_code" class="form-control-custom"
                                            value="{{ old('zip_code', $supplier->zip_code) }}">
                                    </div>
                                </div>

                                <x-forms.country-select
                                    name="country_id"
                                    label="Country"
                                    :countries="$countries"
                                    :value="old('country_id', $supplier->country_id)"
                                    class="form-control-custom select2-flag"
                                    :required="true"
                                    :allowClear="false"
                                />

                                <x-forms.port-select
                                    name="port_code"
                                    label="Port code"
                                    :value="old('port_code', $supplier->port_code)"
                                    :required="true"
                                />

                                <div class="sup-soft-panel">
                                    <div class="sup-soft-panel-title">Office address <span>optional</span></div>
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="edit_office_address">Street address / post box</label>
                                        <textarea id="edit_office_address" name="office_address" class="form-textarea-custom" rows="3">{{ old('office_address', $supplier->office_address) }}</textarea>
                                    </div>

                                    <div class="input-row sup-details-input-row">
                                        <div class="form-group-custom" style="flex: 2;">
                                            <label class="form-label-custom" for="edit_office_city">City</label>
                                            <input type="text" id="edit_office_city" name="office_city" class="form-control-custom"
                                                value="{{ old('office_city', $supplier->office_city) }}">
                                        </div>
                                        <div class="form-group-custom">
                                            <label class="form-label-custom" for="edit_office_district_state">District/state</label>
                                            <input type="text" id="edit_office_district_state" name="office_district_state" class="form-control-custom"
                                                value="{{ old('office_district_state', $supplier->office_district_state) }}">
                                        </div>
                                        <div class="form-group-custom">
                                            <label class="form-label-custom" for="edit_office_zip_code">Zip code</label>
                                            <input type="text" id="edit_office_zip_code" name="office_zip_code" class="form-control-custom"
                                                value="{{ old('office_zip_code', $supplier->office_zip_code) }}">
                                        </div>
                                    </div>

                                    <x-forms.country-select
                                        name="office_country_id"
                                        label="Country"
                                        :countries="$countries"
                                        :value="old('office_country_id', $supplier->office_country_id)"
                                        class="form-control-custom select2-flag"
                                        :allowClear="true"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                {{-- Contacts tab lives outside #edit-supplier-form so add/edit contact does not submit supplier fields --}}
                <div id="contacts" class="tab-content-custom">
                    <div class="sup-pane-toolbar">
                        @if ($canWriteAdministration)
                            <a href="{{ route('suppliers.contacts.create', $supplier->id) }}" class="btn-sup-pane-action">Add contact</a>
                        @endif
                    </div>
                    <div class="sup-table-wrap">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th style="width: 5%; text-align: center;">Main</th>
                                    <th style="width: 22%;">Name</th>
                                    <th style="width: 22%;">Email</th>
                                    <th style="width: 18%;">Phone number</th>
                                    <th style="width: 23%;">Description</th>
                                    <th style="width: 10%; text-align: right;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($supplier->contacts as $contact)
                                    <tr>
                                        <td style="text-align: center;">
                                            @if ($contact->is_main_contact)
                                                <i class="ti-check" style="color: #01a9ac;"></i>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('suppliers.contacts.edit', [$supplier->id, $contact->id]) }}" class="table-link">{{ $contact->name }}</a>
                                        </td>
                                        <td>{{ $contact->email ?: '—' }}</td>
                                        <td>{{ $contact->phone_number ?: '—' }}</td>
                                        <td>{{ $contact->description ? Str::limit($contact->description, 50) : '—' }}</td>
                                        <td style="text-align: right;">
                                            <a href="{{ route('suppliers.contacts.edit', [$supplier->id, $contact->id]) }}">
                                                <i class="ti-pencil btn-action-pencil"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" style="text-align: center; padding: 40px; color: #8da2b5;">No contacts found for this supplier.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="supplier-edit-footer" id="supplier-edit-footer">
            <button type="submit" class="btn-save-custom" id="btn-save" form="edit-supplier-form">Save supplier</button>
            <a href="{{ route('suppliers.index') }}" class="btn-cancel-custom">Cancel</a>
            <div class="audit-info">
                @include('partials.audit-info', ['record' => $supplier, 'bold' => true])
            </div>
        </div>
    </div>

    @include('layouts.partials.pcoded-shell-end')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function () {
            $('body').addClass('edit-supplier-page');

            function autoResizeSupTextarea(textarea) {
                if (!textarea) {
                    return;
                }

                var computedStyle = window.getComputedStyle(textarea);
                var minHeight = parseFloat(computedStyle.minHeight) || 0;

                textarea.style.setProperty('height', 'auto', 'important');
                textarea.style.setProperty('overflow-y', 'hidden', 'important');
                textarea.style.setProperty('height', Math.max(textarea.scrollHeight, minHeight) + 'px', 'important');
            }

            function refreshAutoResizeSupTextareas() {
                $('#edit-supplier-form textarea.form-textarea-custom').each(function () {
                    autoResizeSupTextarea(this);
                });
            }

            $(document).on('input.autoResizeSupTextarea change.autoResizeSupTextarea', '#edit-supplier-form textarea.form-textarea-custom', function () {
                autoResizeSupTextarea(this);
            });

            refreshAutoResizeSupTextareas();

            $('#edit-supplier-form').validate({
                rules: {
                    supplier_name: { required: true },
                    phone_number: { required: true },
                    email: { required: true, email: true },
                    contact_person: { required: true },
                    supplier_address: { required: true },
                    city: { required: true },
                    country_id: { required: true },
                    port_code: { required: true }
                },
                messages: {
                    supplier_name: { required: 'Please enter the supplier name' },
                    phone_number: { required: 'Please enter the phone number' },
                    email: { required: 'Please enter the email', email: 'Please enter a valid email' },
                    contact_person: { required: 'Please enter the contact person' },
                    supplier_address: { required: 'Please enter the street address' },
                    city: { required: 'Please enter the city' },
                    country_id: { required: 'Please select the country' },
                    port_code: { required: 'Please select the port code' }
                },
                errorElement: 'div',
                errorClass: 'error-message',
                errorPlacement: function (error, element) {
                    if (element.hasClass('select2-hidden-accessible') || element.is('[data-country-select]') || element.is('[data-port-select]')) {
                        error.insertAfter(element.next('.select2-container'));
                    } else {
                        error.insertAfter(element);
                    }
                },
                highlight: function (element) {
                    $(element).addClass('error');
                    if ($(element).hasClass('select2-hidden-accessible') || $(element).is('[data-country-select]') || $(element).is('[data-port-select]')) {
                        $(element).next('.select2-container').addClass('error');
                    }
                },
                unhighlight: function (element) {
                    $(element).removeClass('error');
                    if ($(element).hasClass('select2-hidden-accessible') || $(element).is('[data-country-select]') || $(element).is('[data-port-select]')) {
                        $(element).next('.select2-container').removeClass('error');
                    }
                }
            });

            function activateSupplierTab(tabId) {
                if (!tabId || !$('#' + tabId).length || !$('.tab-item[data-tab="' + tabId + '"]').length) {
                    return false;
                }
                $('.tab-item').removeClass('active');
                $('.tab-item[data-tab="' + tabId + '"]').addClass('active');
                $('.tab-content-custom').removeClass('active');
                $('#' + tabId).addClass('active');
                $('#active_tab').val(tabId);
                $('#supplier-edit-footer').toggle(tabId === 'supplier-details');
                if (tabId === 'supplier-details') {
                    refreshAutoResizeSupTextareas();
                }
                return true;
            }

            $('.tab-item').on('click', function (e) {
                e.preventDefault();
                var tabId = $(this).data('tab');
                activateSupplierTab(tabId);
                if (history.replaceState) {
                    history.replaceState(null, '', '#' + tabId);
                } else {
                    window.location.hash = tabId;
                }
            });

            var hashTab = window.location.hash.replace(/^#/, '');
            if (hashTab) {
                activateSupplierTab(hashTab);
            }

            $(document).on('select2:open', '.sup-pillar-card select', function () {
                $('.sup-pillar-card').css('z-index', '');
                $(this).closest('.sup-pillar-card').css('z-index', 40);
            });

            $(document).on('select2:close', '.sup-pillar-card select', function () {
                $(this).closest('.sup-pillar-card').css('z-index', '');
            });
        });
    </script>

@include('partials.unsaved-changes-guard', [
    'formSelector' => '#edit-supplier-form',
    'fallbackUrl' => route('suppliers.index'),
    'saveButtonSelector' => '#btn-save',
    'legacySaveLabelSwap' => true,
])
@endsection
