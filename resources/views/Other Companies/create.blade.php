@extends('layouts.app')

@section('styles')
    @include('Other Companies.partials.create-page-styles')
@endsection

@section('content')
    <script>document.body.classList.add('create-other-company-page');</script>

    @include('layouts.partials.pcoded-shell-start', ['pageWrapperClass' => 'p-0'])

    <div class="create-other-company-page">
        <div class="create-other-company-hero">
            <div class="create-other-company-hero-main">
                <span class="create-other-company-hero-icon" aria-hidden="true">
                    <i class="ti-layout-grid2"></i>
                </span>
                <div>
                    <p class="create-other-company-kicker">Administration</p>
                    <h1 class="create-other-company-title">Create other company</h1>
                    <p class="create-other-company-sub">Add a third-party company with identity, contact, and location details.</p>
                </div>
            </div>
            <a href="{{ route('other-companies.index') }}" class="create-other-company-back">
                <i class="ti-arrow-left"></i> Back to other companies
            </a>
        </div>

        <div class="create-other-company-card">
            <div class="oc-form-container">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show oc-form-alert" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                @endif

                <form id="companyCreateForm" action="{{ route('other-companies.store') }}" method="POST">
                    @csrf

                    <div class="oc-pillars oc-details-grid">
                        <div class="oc-pillar-card">
                            <div class="oc-pillar-head">
                                <span class="oc-pillar-head-icon" aria-hidden="true"><i class="ti-briefcase"></i></span>
                                <div>
                                    <div class="oc-pillar-head-title">Identity &amp; contact</div>
                                    <p class="oc-pillar-head-sub">Core company profile and how to reach the desk.</p>
                                </div>
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom" for="company_name">Company name <span class="text-danger">*</span></label>
                                <input type="text" id="company_name" name="company_name" class="form-control-custom"
                                    value="{{ old('company_name') }}" required autocomplete="organization">
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom" for="company_type">Company type</label>
                                <input type="text" id="company_type" name="company_type" class="form-control-custom"
                                    value="{{ old('company_type') }}">
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom" for="phone_number">Phone number (with country code) <span class="text-danger">*</span></label>
                                <input type="text" id="phone_number" name="phone_number" class="form-control-custom"
                                    value="{{ old('phone_number') }}" required autocomplete="tel">
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom" for="email">Email <span class="text-danger">*</span></label>
                                <input type="email" id="email" name="email" class="form-control-custom" value="{{ old('email') }}" required>
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom" for="contact_person">Contact Person</label>
                                <input type="text" id="contact_person" name="contact_person" class="form-control-custom"
                                    value="{{ old('contact_person') }}" autocomplete="name">
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom" for="remarks">Remarks</label>
                                <textarea id="remarks" name="remarks" class="form-textarea-custom" rows="3">{{ old('remarks') }}</textarea>
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom" for="special_considerations">Notes for consignee</label>
                                <textarea id="special_considerations" name="special_considerations" class="form-textarea-custom" rows="3">{{ old('special_considerations') }}</textarea>
                            </div>
                        </div>

                        <div class="oc-pillar-card">
                            <div class="oc-pillar-head">
                                <span class="oc-pillar-head-icon is-location" aria-hidden="true"><i class="ti-map-alt"></i></span>
                                <div>
                                    <div class="oc-pillar-head-title">Location</div>
                                    <p class="oc-pillar-head-sub">Physical company address and port identifiers.</p>
                                </div>
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom" for="street_address">Street address <span class="text-danger">*</span></label>
                                <textarea id="street_address" name="street_address" class="form-textarea-custom" rows="3" required>{{ old('street_address') }}</textarea>
                            </div>

                            <div class="input-row">
                                <div class="form-group-custom" style="flex: 2;">
                                    <label class="form-label-custom" for="city">City <span class="text-danger">*</span></label>
                                    <input type="text" id="city" name="city" class="form-control-custom" value="{{ old('city') }}" required>
                                </div>
                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="district_state">District/state</label>
                                    <input type="text" id="district_state" name="district_state" class="form-control-custom"
                                        value="{{ old('district_state') }}">
                                </div>
                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="zip_code">Zip code</label>
                                    <input type="text" id="zip_code" name="zip_code" class="form-control-custom" value="{{ old('zip_code') }}">
                                </div>
                            </div>

                            <div class="input-row">
                                <x-forms.country-select
                                    name="country_id"
                                    label="Country"
                                    :countries="$countries"
                                    :value="old('country_id')"
                                    class="form-control-custom select2-flag"
                                    :required="true"
                                    :allowClear="false"
                                />

                                <x-forms.port-select
                                    name="port_code"
                                    label="Port code"
                                    :value="old('port_code')"
                                    :required="true"
                                />
                            </div>

                            <div class="oc-soft-panel">
                                <div class="oc-soft-panel-title">Office address <span>optional</span></div>
                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="office_street_address">Street address / post box</label>
                                    <textarea id="office_street_address" name="office_street_address" class="form-textarea-custom" rows="3">{{ old('office_street_address') }}</textarea>
                                </div>

                                <div class="input-row">
                                    <div class="form-group-custom" style="flex: 2;">
                                        <label class="form-label-custom" for="office_city">City</label>
                                        <input type="text" id="office_city" name="office_city" class="form-control-custom" value="{{ old('office_city') }}">
                                    </div>
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="office_district_state">District/state</label>
                                        <input type="text" id="office_district_state" name="office_district_state" class="form-control-custom"
                                            value="{{ old('office_district_state') }}">
                                    </div>
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="office_zip_code">Zip code</label>
                                        <input type="text" id="office_zip_code" name="office_zip_code" class="form-control-custom"
                                            value="{{ old('office_zip_code') }}">
                                    </div>
                                </div>

                                <x-forms.country-select
                                    name="office_country_id"
                                    label="Country"
                                    :countries="$countries"
                                    :value="old('office_country_id')"
                                    class="form-control-custom select2-flag"
                                    :allowClear="true"
                                />
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="create-other-company-footer">
            <button type="submit" class="btn-save-custom" form="companyCreateForm">Save company</button>
            <a href="{{ route('other-companies.index') }}" class="btn-cancel-custom">Cancel</a>
        </div>
    </div>

    @include('layouts.partials.pcoded-shell-end')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function () {
            $('body').addClass('create-other-company-page');

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
                $('#companyCreateForm textarea.form-textarea-custom').each(function () {
                    autoResizeOcTextarea(this);
                });
            }

            $(document).on('input.autoResizeOcTextarea change.autoResizeOcTextarea', '#companyCreateForm textarea.form-textarea-custom', function () {
                autoResizeOcTextarea(this);
            });

            refreshAutoResizeOcTextareas();

            $('#companyCreateForm').validate({
                rules: {
                    company_name: { required: true },
                    phone_number: { required: true },
                    email: { required: true, email: true },
                    street_address: { required: true },
                    city: { required: true },
                    country_id: { required: true },
                    port_code: { required: true }
                },
                messages: {
                    company_name: { required: 'Please enter the company name' },
                    phone_number: { required: 'Please enter the phone number' },
                    email: {
                        required: 'Please enter the email',
                        email: 'Please enter a valid email'
                    },
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

            $(document).on('select2:open', '.oc-pillar-card select', function () {
                $('.oc-pillar-card').css('z-index', '');
                $(this).closest('.oc-pillar-card').css('z-index', 40);
            });

            $(document).on('select2:close', '.oc-pillar-card select', function () {
                $(this).closest('.oc-pillar-card').css('z-index', '');
            });
        });
    </script>
@endsection
