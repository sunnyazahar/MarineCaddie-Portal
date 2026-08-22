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
                    <p class="edit-other-company-sub">Edit company details, billing fields, and contact persons.</p>
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
                        <div class="form-pillar-container">
                            <div class="form-pillar">
                                <div class="form-section-header">Company information</div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="edit_company_name">Company name <span class="text-danger">*</span></label>
                                    <input type="text" id="edit_company_name" name="company_name" class="form-control-custom"
                                        value="{{ old('company_name', $otherCompany->company_name) }}" required>
                                </div>

                                <div class="form-group-custom d-none">
                                    <label class="form-label-custom">Company id</label>
                                    <input type="text" class="form-control-custom form-input-readonly" value="{{ $otherCompany->id }}" readonly>
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="edit_company_type">Company type</label>
                                    <select id="edit_company_type" name="company_type" class="form-control-custom select2-company-type">
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

                                <div class="input-row">
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="edit_code">Code</label>
                                        <input type="text" id="edit_code" name="code" class="form-control-custom"
                                            value="{{ old('code', $otherCompany->code) }}">
                                    </div>
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="edit_code_description">Code description</label>
                                        <input type="text" id="edit_code_description" name="code_description" class="form-control-custom"
                                            value="{{ old('code_description', $otherCompany->code_description) }}">
                                    </div>
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="edit_phone_number">Phone number (with country code)</label>
                                    <input type="text" id="edit_phone_number" name="phone_number" class="form-control-custom"
                                        value="{{ old('phone_number', $otherCompany->phone_number) }}">
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="edit_email">Email</label>
                                    <input type="email" id="edit_email" name="email" class="form-control-custom"
                                        value="{{ old('email', $otherCompany->email) }}">
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="edit_contact_person">Contact person <span class="text-danger">*</span></label>
                                    <input type="text" id="edit_contact_person" name="contact_person" class="form-control-custom"
                                        value="{{ old('contact_person', $otherCompany->contact_person) }}" required>
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

                            <div class="form-pillar">
                                <div class="form-section-header">Company address</div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="edit_street_address">Street address</label>
                                    <textarea id="edit_street_address" name="street_address" class="form-textarea-custom" rows="4">{{ old('street_address', $otherCompany->street_address) }}</textarea>
                                </div>

                                <div class="input-row">
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="edit_city">City</label>
                                        <input type="text" id="edit_city" name="city" class="form-control-custom"
                                            value="{{ old('city', $otherCompany->city) }}">
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
                                    :value="$otherCompany->country_id"
                                    class="form-control-custom"
                                />

                                <x-forms.port-select
                                    name="port_code"
                                    label="Port code"
                                    :value="$otherCompany->port_code"
                                />

                                <div class="form-section-header" style="margin-top: 12px;">Office address (optional)</div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="edit_office_street_address">Street address / post box</label>
                                    <textarea id="edit_office_street_address" name="office_street_address" class="form-textarea-custom" rows="3">{{ old('office_street_address', $otherCompany->office_street_address) }}</textarea>
                                </div>

                                <div class="input-row">
                                    <div class="form-group-custom">
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
                                    :value="$otherCompany->office_country_id"
                                    class="form-control-custom"
                                />
                            </div>

                            <div class="form-pillar">
                                <div class="form-section-header">Company details</div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="edit_vat_number">VAT number</label>
                                    <input type="text" id="edit_vat_number" name="vat_number" class="form-control-custom"
                                        value="{{ old('vat_number', $otherCompany->vat_number) }}">
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="edit_eori_number">EORI number</label>
                                    <input type="text" id="edit_eori_number" name="eori_number" class="form-control-custom"
                                        value="{{ old('eori_number', $otherCompany->eori_number) }}">
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="edit_currency">Currency</label>
                                    <select id="edit_currency" name="currency" class="form-control-custom select2-currency">
                                        <option value=""></option>
                                        @foreach ($currencies as $curr)
                                            <option value="{{ $curr }}" {{ old('currency', $otherCompany->currency) == $curr ? 'selected' : '' }}>{{ $curr }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="edit_un_locode">UN/LOCODE</label>
                                    <input type="text" id="edit_un_locode" name="un_locode" class="form-control-custom"
                                        value="{{ old('un_locode', $otherCompany->un_locode) }}">
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

            $('#edit-company-form').validate({
                rules: {
                    company_name: { required: true },
                    contact_person: { required: true }
                },
                messages: {
                    company_name: { required: 'Please enter the company name' },
                    contact_person: { required: 'Please enter the contact person' }
                },
                errorElement: 'div',
                errorClass: 'error-message',
                errorPlacement: function (error, element) {
                    if (element.hasClass('select2-hidden-accessible')) {
                        error.insertAfter(element.next('.select2'));
                    } else {
                        error.insertAfter(element);
                    }
                },
                highlight: function (element) {
                    $(element).addClass('error');
                },
                unhighlight: function (element) {
                    $(element).removeClass('error');
                }
            });

            $('.select2-company-type').select2({
                placeholder: 'Select company type',
                allowClear: true,
                width: '100%'
            });

            $('.select2-currency').select2({
                placeholder: 'Select currency',
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
