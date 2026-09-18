@extends('layouts.app')

@section('styles')
    @include('Suppliers.partials.create-page-styles')
@endsection

@section('content')
    <script>document.body.classList.add('create-supplier-page');</script>

    @include('layouts.partials.pcoded-shell-start', ['pageWrapperClass' => 'p-0'])

    <div class="create-supplier-page">
        <div class="create-supplier-hero">
            <div class="create-supplier-hero-main">
                <span class="create-supplier-hero-icon" aria-hidden="true">
                    <i class="ti-truck"></i>
                </span>
                <div>
                    <p class="create-supplier-kicker">Administration</p>
                    <h1 class="create-supplier-title">Create supplier</h1>
                    <p class="create-supplier-sub">Add a supplier with identity, contact, and location details.</p>
                </div>
            </div>
            <a href="{{ route('suppliers.index') }}" class="create-supplier-back">
                <i class="ti-arrow-left"></i> Back to suppliers
            </a>
        </div>

        <div class="create-supplier-card">
            <div class="sup-form-container">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show sup-form-alert" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                @endif

                <form id="supplierForm" action="{{ route('suppliers.store') }}" method="POST">
                    @csrf

                    <div class="sup-pillars sup-details-grid">
                        <div class="sup-pillar-card">
                            <div class="sup-pillar-head">
                                <span class="sup-pillar-head-icon" aria-hidden="true"><i class="ti-briefcase"></i></span>
                                <div>
                                    <div class="sup-pillar-head-title">Identity &amp; contact</div>
                                    <p class="sup-pillar-head-sub">Core supplier profile and how to reach the desk.</p>
                                </div>
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom" for="supplier_name">Supplier name <span class="text-danger">*</span></label>
                                <input type="text" id="supplier_name" name="supplier_name" class="form-control-custom"
                                    value="{{ old('supplier_name') }}" required autocomplete="organization">
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
                                <label class="form-label-custom" for="contact_person">Contact person</label>
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

                        <div class="sup-pillar-card">
                            <div class="sup-pillar-head">
                                <span class="sup-pillar-head-icon is-location" aria-hidden="true"><i class="ti-map-alt"></i></span>
                                <div>
                                    <div class="sup-pillar-head-title">Location</div>
                                    <p class="sup-pillar-head-sub">Physical supplier address and port identifiers.</p>
                                </div>
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom" for="supplier_address">Street address</label>
                                <textarea id="supplier_address" name="supplier_address" class="form-textarea-custom" rows="3">{{ old('supplier_address') }}</textarea>
                            </div>

                            <div class="input-row">
                                <div class="form-group-custom" style="flex: 2;">
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
                                :value="old('country_id')"
                                class="form-control-custom select2-flag"
                                :required="false"
                                :allowClear="true"
                            />

                            <x-forms.port-select
                                name="port_code"
                                label="Port code"
                                :value="old('port_code')"
                                :required="false"
                            />

                            <div class="sup-soft-panel">
                                <div class="sup-soft-panel-title">Office address <span>optional</span></div>
                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="office_address">Street address / post box</label>
                                    <textarea id="office_address" name="office_address" class="form-textarea-custom" rows="3">{{ old('office_address') }}</textarea>
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

        <div class="create-supplier-footer">
            <button type="submit" class="btn-save-custom" form="supplierForm">Save supplier</button>
            <a href="{{ route('suppliers.index') }}" class="btn-cancel-custom">Cancel</a>
        </div>
    </div>

    @include('layouts.partials.pcoded-shell-end')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function () {
            $('body').addClass('create-supplier-page');

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
                $('#supplierForm textarea.form-textarea-custom').each(function () {
                    autoResizeSupTextarea(this);
                });
            }

            $(document).on('input.autoResizeSupTextarea change.autoResizeSupTextarea', '#supplierForm textarea.form-textarea-custom', function () {
                autoResizeSupTextarea(this);
            });

            refreshAutoResizeSupTextareas();

            $('#supplierForm').validate({
                rules: {
                    supplier_name: { required: true },
                    email: { email: true }
                },
                messages: {
                    supplier_name: { required: 'Please enter the supplier name' },
                    email: { email: 'Please enter a valid email' }
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

            $(document).on('select2:open', '.sup-pillar-card select', function () {
                $('.sup-pillar-card').css('z-index', '');
                $(this).closest('.sup-pillar-card').css('z-index', 40);
            });

            $(document).on('select2:close', '.sup-pillar-card select', function () {
                $(this).closest('.sup-pillar-card').css('z-index', '');
            });
        });
    </script>
@endsection
