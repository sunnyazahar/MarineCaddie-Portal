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
                    <p class="create-other-company-sub">Add a third-party company with address, port, billing details, and contact person.</p>
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

                    <div class="oc-pillars">
                        <div class="oc-pillar-col">
                            <div class="oc-pillar">
                                <div class="oc-pillar__title">Company information</div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="company_name">Company name <span class="text-danger">*</span></label>
                                    <input type="text" id="company_name" name="company_name" class="form-control-custom"
                                        value="{{ old('company_name') }}" required autocomplete="organization">
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="company_type">Company type</label>
                                    <select id="company_type" name="company_type" class="form-control-custom select2-company-type">
                                        <option value=""></option>
                                        @foreach ($companyTypes as $type)
                                            <option value="{{ $type }}" {{ old('company_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="address-sub-grid" style="grid-template-columns: repeat(2, minmax(0, 1fr));">
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="code">Code</label>
                                        <input type="text" id="code" name="code" class="form-control-custom" value="{{ old('code') }}">
                                    </div>
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="code_description">Code description</label>
                                        <input type="text" id="code_description" name="code_description" class="form-control-custom"
                                            value="{{ old('code_description') }}">
                                    </div>
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="phone_number">Phone number (with country code)</label>
                                    <input type="text" id="phone_number" name="phone_number" class="form-control-custom"
                                        value="{{ old('phone_number') }}" autocomplete="tel">
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="email">Email</label>
                                    <input type="email" id="email" name="email" class="form-control-custom" value="{{ old('email') }}">
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="contact_person">Contact person <span class="text-danger">*</span></label>
                                    <input type="text" id="contact_person" name="contact_person" class="form-control-custom"
                                        value="{{ old('contact_person') }}" required autocomplete="name">
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="remarks">Remarks</label>
                                    <textarea id="remarks" name="remarks" class="form-textarea-custom" rows="3">{{ old('remarks') }}</textarea>
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="special_considerations">Special considerations for destination</label>
                                    <textarea id="special_considerations" name="special_considerations" class="form-textarea-custom" rows="3">{{ old('special_considerations') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="oc-pillar-col">
                            <div class="oc-pillar">
                                <div class="oc-pillar__title">Company address</div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="street_address">Street address</label>
                                    <textarea id="street_address" name="street_address" class="form-textarea-custom" rows="3">{{ old('street_address') }}</textarea>
                                </div>

                                <div class="address-sub-grid">
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="city">City</label>
                                        <input type="text" id="city" name="city" class="form-control-custom" value="{{ old('city') }}">
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

                                <x-forms.country-select
                                    name="country_id"
                                    label="Country"
                                    :countries="$countries"
                                    class="form-control-custom"
                                />

                                <x-forms.port-select
                                    name="port_code"
                                    label="Port code"
                                />

                                <div class="oc-pillar__title" style="margin-top: 8px;">Office address (optional)</div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="office_street_address">Street address / post box</label>
                                    <textarea id="office_street_address" name="office_street_address" class="form-textarea-custom" rows="3">{{ old('office_street_address') }}</textarea>
                                </div>

                                <div class="address-sub-grid">
                                    <div class="form-group-custom">
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
                                    class="form-control-custom"
                                />
                            </div>
                        </div>

                        <div class="oc-pillar-col">
                            <div class="oc-pillar">
                                <div class="oc-pillar__title">Company details</div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="vat_number">VAT number</label>
                                    <input type="text" id="vat_number" name="vat_number" class="form-control-custom" value="{{ old('vat_number') }}">
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="eori_number">EORI number</label>
                                    <input type="text" id="eori_number" name="eori_number" class="form-control-custom" value="{{ old('eori_number') }}">
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="currency">Currency</label>
                                    <select id="currency" name="currency" class="form-control-custom select2-currency">
                                        <option value=""></option>
                                        @foreach ($currencies as $curr)
                                            <option value="{{ $curr }}" {{ old('currency') == $curr ? 'selected' : '' }}>{{ $curr }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="un_locode">UN/LOCODE</label>
                                    <input type="text" id="un_locode" name="un_locode" class="form-control-custom" value="{{ old('un_locode') }}">
                                </div>
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

            $('#companyCreateForm').validate({
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
        });
    </script>
@endsection
