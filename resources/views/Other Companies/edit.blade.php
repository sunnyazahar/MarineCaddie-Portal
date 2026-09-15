@extends('layouts.app')

@section('styles')
    @include('Other Companies.partials.edit-page-styles')
@endsection

@section('content')
    <script>document.body.classList.add('edit-other-company-page');</script>

    @include('layouts.partials.pcoded-shell-start', ['pageWrapperClass' => 'p-0'])

    <div class="edit-other-company-page">
        <div class="edit-other-company-hero">
            <div class="edit-other-company-hero-main">
                <span class="edit-other-company-hero-icon" aria-hidden="true">
                    <i class="ti-layout-grid2"></i>
                </span>
                <div>
                    <p class="edit-other-company-kicker">Administration</p>
                    <h1 class="edit-other-company-title">{{ $otherCompany->company_name }}</h1>
                    <p class="edit-other-company-sub">Edit company profile, location, and contact persons.</p>
                </div>
            </div>
            <a href="{{ route('other-companies.index') }}" class="edit-other-company-back">
                <i class="ti-arrow-left"></i> Back to other companies
            </a>
        </div>

        <div class="edit-other-company-meta">
            <span class="edit-other-company-meta-pill">Company ID <strong>{{ $otherCompany->id }}</strong></span>
            @if ($otherCompany->code)
                <span class="edit-other-company-meta-pill">Code <strong>{{ $otherCompany->code }}</strong></span>
            @endif
            @if ($otherCompany->city || $otherCompany->country)
                <span class="edit-other-company-meta-pill">
                    Location
                    <strong>{{ trim(($otherCompany->city ? $otherCompany->city . ', ' : '') . ($otherCompany->country?->name ?? '')) }}</strong>
                </span>
            @endif
            <span class="edit-other-company-meta-pill is-active">Status <strong>Active</strong></span>
        </div>

        <div class="tabs-container">
            <a class="tab-item active" data-tab="company-details"><i class="ti-home"></i> Company details</a>
            <a class="tab-item" data-tab="contacts"><i class="ti-id-badge"></i> Contacts</a>
        </div>

        <div class="edit-other-company-card">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show edit-other-company-alert" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            @endif
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show edit-other-company-alert" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            @endif

            <div class="tab-content-container">
                <form id="edit-company-form" action="{{ route('other-companies.update', $otherCompany->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="active_tab" id="active_tab" value="{{ old('active_tab', 'company-details') }}">

                    <div id="company-details" class="tab-content-custom active">
                        <div class="form-pillar-container oc-details-grid">
                            <div class="form-pillar oc-pillar-card">
                                <div class="oc-pillar-head">
                                    <span class="oc-pillar-head-icon" aria-hidden="true"><i class="ti-briefcase"></i></span>
                                    <div>
                                        <div class="oc-pillar-head-title">Identity &amp; contact</div>
                                        <p class="oc-pillar-head-sub">Core company profile and how to reach the desk.</p>
                                    </div>
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="edit_company_name">Company name <span class="text-danger">*</span></label>
                                    <input type="text" id="edit_company_name" name="company_name" class="form-control-custom"
                                        value="{{ old('company_name', $otherCompany->company_name) }}" required autocomplete="organization">
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="edit_company_type">Company type <span class="text-danger">*</span></label>
                                    <select id="edit_company_type" name="company_type" class="form-control-custom select2-company-type" required>
                                        <option value=""></option>
                                        @php
                                            $selectedCompanyType = old('company_type', $otherCompany->company_type);
                                        @endphp
                                        @if ($selectedCompanyType && ! in_array($selectedCompanyType, $companyTypes, true))
                                            <option value="{{ $selectedCompanyType }}" selected>{{ $selectedCompanyType }}</option>
                                        @endif
                                        @foreach ($companyTypes as $type)
                                            <option value="{{ $type }}" {{ $selectedCompanyType == $type ? 'selected' : '' }}>{{ $type }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="input-row oc-details-input-row">
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="edit_code">Code <span class="text-danger">*</span></label>
                                        <input type="text" id="edit_code" name="code" class="form-control-custom"
                                            value="{{ old('code', $otherCompany->code) }}" required>
                                    </div>
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="edit_code_description">Code description <span class="text-danger">*</span></label>
                                        <input type="text" id="edit_code_description" name="code_description" class="form-control-custom"
                                            value="{{ old('code_description', $otherCompany->code_description) }}" required>
                                    </div>
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="edit_phone_number">Phone number (with country code) <span class="text-danger">*</span></label>
                                    <input type="text" id="edit_phone_number" name="phone_number" class="form-control-custom"
                                        value="{{ old('phone_number', $otherCompany->phone_number) }}" required autocomplete="tel">
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="edit_email">Email <span class="text-danger">*</span></label>
                                    <input type="email" id="edit_email" name="email" class="form-control-custom"
                                        value="{{ old('email', $otherCompany->email) }}" required>
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="edit_contact_person">Contact Person <span class="text-danger">*</span></label>
                                    <input type="text" id="edit_contact_person" name="contact_person" class="form-control-custom"
                                        value="{{ old('contact_person', $otherCompany->contact_person) }}" required autocomplete="name">
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="edit_remarks">Remarks</label>
                                    <textarea id="edit_remarks" name="remarks" class="form-textarea-custom" rows="3">{{ old('remarks', $otherCompany->remarks) }}</textarea>
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="edit_special_considerations">Special considerations for destination</label>
                                    <textarea id="edit_special_considerations" name="special_considerations" class="form-textarea-custom" rows="3">{{ old('special_considerations', $otherCompany->special_considerations) }}</textarea>
                                </div>
                            </div>

                            <div class="form-pillar oc-pillar-card">
                                <div class="oc-pillar-head">
                                    <span class="oc-pillar-head-icon is-location" aria-hidden="true"><i class="ti-map-alt"></i></span>
                                    <div>
                                        <div class="oc-pillar-head-title">Location</div>
                                        <p class="oc-pillar-head-sub">Physical company address and port identifiers.</p>
                                    </div>
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="edit_street_address">Street address <span class="text-danger">*</span></label>
                                    <textarea id="edit_street_address" name="street_address" class="form-textarea-custom" rows="3" required>{{ old('street_address', $otherCompany->street_address) }}</textarea>
                                </div>

                                <div class="input-row oc-details-input-row">
                                    <div class="form-group-custom" style="flex: 2;">
                                        <label class="form-label-custom" for="edit_city">City <span class="text-danger">*</span></label>
                                        <input type="text" id="edit_city" name="city" class="form-control-custom"
                                            value="{{ old('city', $otherCompany->city) }}" required>
                                    </div>
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="edit_district_state">District/state</label>
                                        <input type="text" id="edit_district_state" name="district_state" class="form-control-custom"
                                            value="{{ old('district_state', $otherCompany->district_state) }}">
                                    </div>
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="edit_zip_code">Zip code</label>
                                        <input type="text" id="edit_zip_code" name="zip_code" class="form-control-custom"
                                            value="{{ old('zip_code', $otherCompany->zip_code) }}">
                                    </div>
                                </div>

                                <x-forms.country-select
                                    name="country_id"
                                    label="Country"
                                    :countries="$countries"
                                    :value="old('country_id', $otherCompany->country_id)"
                                    class="form-control-custom select2-flag"
                                    :required="true"
                                    :allowClear="false"
                                />

                                <x-forms.port-select
                                    name="port_code"
                                    label="Port code"
                                    :value="old('port_code', $otherCompany->port_code)"
                                    :required="true"
                                />

                                <div class="oc-soft-panel">
                                    <div class="oc-soft-panel-title">Office address <span>optional</span></div>
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="edit_office_street_address">Street address / post box</label>
                                        <textarea id="edit_office_street_address" name="office_street_address" class="form-textarea-custom" rows="3">{{ old('office_street_address', $otherCompany->office_street_address) }}</textarea>
                                    </div>

                                    <div class="input-row oc-details-input-row">
                                        <div class="form-group-custom" style="flex: 2;">
                                            <label class="form-label-custom" for="edit_office_city">City</label>
                                            <input type="text" id="edit_office_city" name="office_city" class="form-control-custom"
                                                value="{{ old('office_city', $otherCompany->office_city) }}">
                                        </div>
                                        <div class="form-group-custom">
                                            <label class="form-label-custom" for="edit_office_district_state">District/state</label>
                                            <input type="text" id="edit_office_district_state" name="office_district_state" class="form-control-custom"
                                                value="{{ old('office_district_state', $otherCompany->office_district_state) }}">
                                        </div>
                                        <div class="form-group-custom">
                                            <label class="form-label-custom" for="edit_office_zip_code">Zip code</label>
                                            <input type="text" id="edit_office_zip_code" name="office_zip_code" class="form-control-custom"
                                                value="{{ old('office_zip_code', $otherCompany->office_zip_code) }}">
                                        </div>
                                    </div>

                                    <x-forms.country-select
                                        name="office_country_id"
                                        label="Country"
                                        :countries="$countries"
                                        :value="old('office_country_id', $otherCompany->office_country_id)"
                                        class="form-control-custom select2-flag"
                                        :allowClear="true"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                {{-- Contacts tab lives outside #edit-company-form so add/delete does not submit company fields --}}
                <div id="contacts" class="tab-content-custom">
                    <div class="oc-pane-toolbar">
                        @if ($canWriteAdministration)
                            <a href="{{ route('other-companies.contacts.create', $otherCompany->id) }}" class="btn-oc-pane-action">Add contact</a>
                        @endif
                    </div>
                    <div class="oc-table-wrap">
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
                                @forelse($otherCompany->contacts as $contact)
                                    <tr>
                                        <td style="text-align: center;">
                                            @if ($contact->is_main_contact)
                                                <i class="ti-check" style="color: #01a9ac;"></i>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('other-companies.contacts.edit', [$otherCompany->id, $contact->id]) }}" class="table-link">{{ $contact->name }}</a>
                                        </td>
                                        <td>{{ $contact->email ?: '—' }}</td>
                                        <td>{{ $contact->phone_number ?: '—' }}</td>
                                        <td>{{ $contact->description ? Str::limit($contact->description, 50) : '—' }}</td>
                                        <td style="text-align: right;">
                                            <a href="{{ route('other-companies.contacts.edit', [$otherCompany->id, $contact->id]) }}">
                                                <i class="ti-pencil btn-action-pencil"></i>
                                            </a>
                                            @if ($canWriteAdministration)
                                                <button type="button" class="btn-action-delete delete-other-company-contact"
                                                    data-url="{{ route('other-companies.contacts.destroy', [$otherCompany->id, $contact->id]) }}"
                                                    data-name="{{ $contact->name }}" title="Delete contact">
                                                    <i class="ti-trash"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" style="text-align: center; padding: 40px; color: #8da2b5;">No contacts found for this company.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="other-company-edit-footer" id="other-company-edit-footer">
            <button type="submit" class="btn-save-custom" id="btn-save" form="edit-company-form">Save company</button>
            <a href="{{ route('other-companies.index') }}" class="btn-cancel-custom">Cancel</a>
            <div class="audit-info">
                @include('partials.audit-info', ['record' => $otherCompany, 'bold' => true])
            </div>
        </div>
    </div>

    @include('layouts.partials.pcoded-shell-end')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function () {
            $('body').addClass('edit-other-company-page');

            function autoResizeOcTextarea(textarea) {
                if (!textarea) {
                    return;
                }

                var computedStyle = window.getComputedStyle(textarea);
                var minHeight = parseFloat(computedStyle.minHeight) || 0;

                textarea.style.setProperty('height', 'auto', 'important');
                textarea.style.setProperty('overflow-y', 'hidden', 'important');
                textarea.style.setProperty('height', Math.max(textarea.scrollHeight, minHeight) + 'px', 'important');
            }

            function refreshAutoResizeOcTextareas() {
                $('#edit-company-form textarea.form-textarea-custom').each(function () {
                    autoResizeOcTextarea(this);
                });
            }

            $(document).on('input.autoResizeOcTextarea change.autoResizeOcTextarea', '#edit-company-form textarea.form-textarea-custom', function () {
                autoResizeOcTextarea(this);
            });

            refreshAutoResizeOcTextareas();

            $('#edit-company-form').validate({
                rules: {
                    company_name: { required: true },
                    company_type: { required: true },
                    code: { required: true },
                    code_description: { required: true },
                    phone_number: { required: true },
                    email: { required: true, email: true },
                    contact_person: { required: true },
                    street_address: { required: true },
                    city: { required: true },
                    country_id: { required: true },
                    port_code: { required: true }
                },
                messages: {
                    company_name: { required: 'Please enter the company name' },
                    company_type: { required: 'Please select the company type' },
                    code: { required: 'Please enter the code' },
                    code_description: { required: 'Please enter the code description' },
                    phone_number: { required: 'Please enter the phone number' },
                    email: { required: 'Please enter the email', email: 'Please enter a valid email' },
                    contact_person: { required: 'Please enter the contact person' },
                    street_address: { required: 'Please enter the street address' },
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

            $('.select2-company-type').select2({
                placeholder: 'Select company type',
                allowClear: true,
                width: '100%'
            });

            function activateOtherCompanyTab(tabId) {
                if (!tabId || !$('#' + tabId).length || !$('.tab-item[data-tab="' + tabId + '"]').length) {
                    return false;
                }
                $('.tab-item').removeClass('active');
                $('.tab-item[data-tab="' + tabId + '"]').addClass('active');
                $('.tab-content-custom').removeClass('active');
                $('#' + tabId).addClass('active');
                $('#active_tab').val(tabId);
                $('#other-company-edit-footer').toggle(tabId === 'company-details');
                if (tabId === 'company-details') {
                    refreshAutoResizeOcTextareas();
                }
                return true;
            }

            $('.tab-item').on('click', function (e) {
                e.preventDefault();
                var tabId = $(this).data('tab');
                activateOtherCompanyTab(tabId);
                if (history.replaceState) {
                    history.replaceState(null, '', '#' + tabId);
                } else {
                    window.location.hash = tabId;
                }
            });

            var hashTab = window.location.hash.replace(/^#/, '');
            if (hashTab) {
                activateOtherCompanyTab(hashTab);
            }

            $(document).on('select2:open', '.oc-pillar-card select', function () {
                $('.oc-pillar-card').css('z-index', '');
                $(this).closest('.oc-pillar-card').css('z-index', 40);
            });

            $(document).on('select2:close', '.oc-pillar-card select', function () {
                $(this).closest('.oc-pillar-card').css('z-index', '');
            });

            $(document).on('click', '.delete-other-company-contact', function () {
                var url = $(this).data('url');
                var name = $(this).data('name') || 'this contact';

                if (typeof swal !== 'function') {
                    if (window.confirm('Are you sure you want to delete ' + name + '?')) {
                        postOtherCompanyContactDelete(url);
                    }
                    return;
                }

                swal({
                    title: 'Are you sure?',
                    text: 'Are you sure you want to delete "' + name + '"?',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#01a9ac',
                    confirmButtonText: 'Yes, delete',
                    cancelButtonText: 'Cancel',
                    closeOnConfirm: true,
                    closeOnCancel: true
                }, function (isConfirm) {
                    if (isConfirm) {
                        postOtherCompanyContactDelete(url);
                    }
                });
            });

            function postOtherCompanyContactDelete(url) {
                if (typeof window.unsavedChangesGuardAllowLeave === 'function') {
                    window.unsavedChangesGuardAllowLeave();
                }

                var form = document.createElement('form');
                form.method = 'POST';
                form.action = url;

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
        });
    </script>

@include('partials.unsaved-changes-guard', [
    'formSelector' => '#edit-company-form',
    'fallbackUrl' => route('other-companies.index'),
    'saveButtonSelector' => '#btn-save',
    'legacySaveLabelSwap' => true,
])
@endsection
