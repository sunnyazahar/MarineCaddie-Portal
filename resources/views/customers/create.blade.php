@extends('layouts.app')

@section('styles')
    @include('customers.partials.create-page-styles')
@endsection

@section('content')
    <script>document.body.classList.add('create-customer-page');</script>

    @include('layouts.partials.pcoded-shell-start', ['pageWrapperClass' => 'p-0'])

    <div class="create-customer-page">
        <div class="create-customer-hero">
            <div class="create-customer-hero-main">
                <span class="create-customer-hero-icon" aria-hidden="true">
                    <i class="ti-user"></i>
                </span>
                <div>
                    <p class="create-customer-kicker">Administration</p>
                    <h1 class="create-customer-title">Create customer</h1>
                    <p class="create-customer-sub">Add a customer with addresses, invoice details, and responsible office contacts.</p>
                </div>
            </div>
            <a href="{{ route('customers.index') }}" class="create-customer-back">
                <i class="ti-arrow-left"></i> Back to customers
            </a>
        </div>

        <div class="create-customer-card">
            <div class="cust-form-container">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show cust-form-alert" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                @endif

                <form id="customerForm" action="{{ route('customers.store') }}" method="POST">
                    @csrf

                    <div class="cust-pillars">
                        <div class="cust-pillar-col">
                            <div class="cust-pillar">
                                <div class="cust-pillar__title">Customer information</div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="customer_name">Customer name <span class="text-danger">*</span></label>
                                    <input type="text" id="customer_name" name="customer_name" class="form-control-custom"
                                        value="{{ old('customer_name') }}" required autocomplete="organization">
                                </div>

                                <div class="form-group-custom d-none">
                                    <label class="form-label-custom" for="customer_number_fm">Customer number from FM</label>
                                    <input type="text" id="customer_number_fm" name="customer_number_fm" class="form-control-custom"
                                        value="{{ old('customer_number_fm') }}">
                                </div>

                                <div class="form-group-custom d-none">
                                    <label class="form-label-custom" for="customer_group">Customer group</label>
                                    <select id="customer_group" name="customer_group" class="form-control-custom select2-field">
                                        <option></option>
                                        <option value="N/A" {{ old('customer_group') === 'N/A' ? 'selected' : '' }}>N/A</option>
                                        @foreach ($groups as $group)
                                            <option value="{{ $group->id }}" {{ (string) old('customer_group') === (string) $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="phone_number">Phone number (with country code)</label>
                                    <input type="text" id="phone_number" name="phone_number" class="form-control-custom"
                                        value="{{ old('phone_number') }}" autocomplete="tel">
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="email">E-mail <span class="text-danger">*</span></label>
                                    <input type="text" id="email" name="email" class="form-control-custom"
                                        value="{{ old('email') }}" placeholder="email@example.com; email2@example.com" required>
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="internal_shipment">Internal shipment</label>
                                    <select id="internal_shipment" name="internal_shipment" class="form-control-custom select2-field">
                                        <option></option>
                                        <option value="1" {{ old('internal_shipment') === '1' ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ old('internal_shipment') === '0' ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="remarks">Remarks</label>
                                    <textarea id="remarks" name="remarks" class="form-textarea-custom" rows="3">{{ old('remarks') }}</textarea>
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="special_considerations">Special considerations for destination</label>
                                    <textarea id="special_considerations" name="special_considerations" class="form-textarea-custom" rows="3">{{ old('special_considerations') }}</textarea>
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="contact_person">Contact person <span class="text-danger">*</span></label>
                                    <input type="text" id="contact_person" name="contact_person" class="form-control-custom"
                                        value="{{ old('contact_person') }}" required autocomplete="name">
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="un_locode">UN / LOCODE</label>
                                    <input type="text" id="un_locode" name="un_locode" class="form-control-custom"
                                        value="{{ old('un_locode') }}" autocomplete="off">
                                </div>

                                <div class="checkbox-group d-none">
                                    <input type="checkbox" id="show_transport_details" name="show_transport_details" class="checkbox-custom"
                                        {{ old('show_transport_details') ? 'checked' : '' }}>
                                    <label class="checkbox-label" for="show_transport_details">Show transport details on customer portal</label>
                                </div>

                                <div class="checkbox-group d-none">
                                    <input type="checkbox" id="esea_store_stock_only" name="esea_store_stock_only" class="checkbox-custom"
                                        {{ old('esea_store_stock_only') ? 'checked' : '' }}>
                                    <label class="checkbox-label" for="esea_store_stock_only">eSea store stock only</label>
                                </div>
                            </div>
                        </div>

                        <div class="cust-pillar-col">
                            <div class="cust-pillar">
                                <div class="cust-pillar__title">Customer address</div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="street_address">Street address <span class="text-danger">*</span></label>
                                    <textarea id="street_address" name="street_address" class="form-textarea-custom" rows="3" required>{{ old('street_address') }}</textarea>
                                </div>

                                <div class="address-sub-grid">
                                    <div class="form-group-custom">
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

                                <x-forms.country-select
                                    name="country"
                                    label="Country"
                                    :countries="$countries"
                                    class="form-control-custom"
                                    :allowClear="false"
                                />

                                <x-forms.port-select
                                    name="port_code"
                                    label="Port code"
                                />

                                <div class="cust-pillar__title" style="margin-top: 8px;">Postal address (optional)</div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="postal_street_address">Street address / post box</label>
                                    <textarea id="postal_street_address" name="postal_street_address" class="form-textarea-custom" rows="3">{{ old('postal_street_address') }}</textarea>
                                </div>

                                <div class="address-sub-grid">
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="postal_city">City</label>
                                        <input type="text" id="postal_city" name="postal_city" class="form-control-custom"
                                            value="{{ old('postal_city') }}">
                                    </div>
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="postal_district_state">District/state</label>
                                        <input type="text" id="postal_district_state" name="postal_district_state" class="form-control-custom"
                                            value="{{ old('postal_district_state') }}">
                                    </div>
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="postal_zip_code">Zip code</label>
                                        <input type="text" id="postal_zip_code" name="postal_zip_code" class="form-control-custom"
                                            value="{{ old('postal_zip_code') }}">
                                    </div>
                                </div>

                                <x-forms.country-select
                                    name="postal_country"
                                    label="Country"
                                    :countries="$countries"
                                    class="form-control-custom"
                                    :allowClear="false"
                                />
                            </div>
                        </div>

                        <div class="cust-pillar-col">
                            <div class="cust-pillar">
                                <div class="cust-pillar__title">Invoice details</div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="invoice_recipient_name">Invoice recipient name <span class="text-danger">*</span></label>
                                    <input type="text" id="invoice_recipient_name" name="invoice_recipient_name" class="form-control-custom"
                                        value="{{ old('invoice_recipient_name') }}" required>
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="invoice_recipient_address">Invoice recipient address <span class="text-danger">*</span></label>
                                    <textarea id="invoice_recipient_address" name="invoice_recipient_address" class="form-textarea-custom" rows="3" required>{{ old('invoice_recipient_address') }}</textarea>
                                </div>

                                <div class="address-sub-grid">
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="invoice_city">City <span class="text-danger">*</span></label>
                                        <input type="text" id="invoice_city" name="invoice_city" class="form-control-custom"
                                            value="{{ old('invoice_city') }}" required>
                                    </div>
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="invoice_district_state">District/state</label>
                                        <input type="text" id="invoice_district_state" name="invoice_district_state" class="form-control-custom"
                                            value="{{ old('invoice_district_state') }}">
                                    </div>
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="invoice_zip_code">Zip code</label>
                                        <input type="text" id="invoice_zip_code" name="invoice_zip_code" class="form-control-custom"
                                            value="{{ old('invoice_zip_code') }}">
                                    </div>
                                </div>

                                <x-forms.country-select
                                    name="invoice_country"
                                    label="Country"
                                    :countries="$countries"
                                    class="form-control-custom"
                                    :allowClear="false"
                                />

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="currency">Currency <span class="text-danger">*</span></label>
                                    <select id="currency" name="currency" class="form-control-custom select2-field" required>
                                        <option></option>
                                        <option value="USD" {{ old('currency') === 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                        <option value="EUR" {{ old('currency') === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                        <option value="GBP" {{ old('currency') === 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                                        <option value="INR" {{ old('currency') === 'INR' ? 'selected' : '' }}>INR - Indian Rupee</option>
                                    </select>
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="invoicing_email">E-mails for invoicing <span class="text-danger">*</span></label>
                                    <input type="text" id="invoicing_email" name="invoicing_email" class="form-control-custom"
                                        value="{{ old('invoicing_email') }}" required>
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="invoicing_email_cc">E-mails for invoicing (CC)</label>
                                    <input type="text" id="invoicing_email_cc" name="invoicing_email_cc" class="form-control-custom"
                                        value="{{ old('invoicing_email_cc') }}">
                                </div>

                                <div class="address-sub-grid" style="grid-template-columns: 1fr 1fr;">
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="payment_terms">Payment terms (days)</label>
                                        <input type="text" id="payment_terms" name="payment_terms" class="form-control-custom"
                                            value="{{ old('payment_terms', 30) }}">
                                    </div>
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="invoice_frequency">Invoice frequency</label>
                                        <select id="invoice_frequency" name="invoice_frequency" class="form-control-custom">
                                            <option></option>
                                            <option value="Daily" {{ old('invoice_frequency') === 'Daily' ? 'selected' : '' }}>Daily</option>
                                            <option value="Weekly" {{ old('invoice_frequency') === 'Weekly' ? 'selected' : '' }}>Weekly</option>
                                            <option value="Monthly" {{ old('invoice_frequency') === 'Monthly' ? 'selected' : '' }}>Monthly</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="invoicing_remarks">Remarks regarding invoicing</label>
                                    <textarea id="invoicing_remarks" name="invoicing_remarks" class="form-textarea-custom" rows="2">{{ old('invoicing_remarks') }}</textarea>
                                </div>

                                <div class="address-sub-grid" style="grid-template-columns: 1fr 1fr;">
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="vat_number">VAT number</label>
                                        <input type="text" id="vat_number" name="vat_number" class="form-control-custom" value="{{ old('vat_number') }}">
                                    </div>
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="eori_number">EORI number</label>
                                        <input type="text" id="eori_number" name="eori_number" class="form-control-custom" value="{{ old('eori_number') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="cust-pillar-col">
                            <div class="cust-pillar">
                                <div class="cust-pillar__title">Responsible office / person</div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="sales_manager">Sales manager</label>
                                    <select id="sales_manager" name="sales_manager" class="form-control-custom select2-field">
                                        <option></option>
                                        @foreach ($salesManagers as $manager)
                                            <option value="{{ $manager->id }}" {{ (string) old('sales_manager') === (string) $manager->id ? 'selected' : '' }}>{{ $manager->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="main-account-manager-select">Main account manager <span class="text-danger">*</span></label>
                                    <select id="main-account-manager-select" name="main_account_manager" class="form-control-custom select2-office-user" required>
                                        <option value=""></option>
                                        @php
                                            $selectedMainAccountManager = old('main_account_manager')
                                                ? \App\Models\Contact::find(old('main_account_manager'))
                                                : null;
                                        @endphp
                                        @if ($selectedMainAccountManager)
                                            <option value="{{ $selectedMainAccountManager->id }}" selected>{{ $selectedMainAccountManager->name }}</option>
                                        @endif
                                    </select>
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="responsible-accounting-users-select">Responsible accounting users</label>
                                    <select id="responsible-accounting-users-select" name="responsible_accounting_users" class="form-control-custom select2-office-user">
                                        <option value=""></option>
                                        @php
                                            $selectedAccountingUser = old('responsible_accounting_users')
                                                ? \App\Models\Contact::with('office')->find(old('responsible_accounting_users'))
                                                : null;
                                            $responsibleOfficeShortName = old(
                                                'responsible_office',
                                                $selectedAccountingUser?->office?->office_short_name ?? ''
                                            );
                                        @endphp
                                        @if ($selectedAccountingUser)
                                            <option value="{{ $selectedAccountingUser->id }}" selected
                                                data-office-short-name="{{ $selectedAccountingUser->office?->office_short_name ?? '' }}">{{ $selectedAccountingUser->name }}</option>
                                        @endif
                                    </select>
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="responsible-office-input">Responsible office</label>
                                    <input type="text" id="responsible-office-input" name="responsible_office" class="form-control-custom"
                                        value="{{ $responsibleOfficeShortName }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="create-customer-footer">
            <button type="submit" class="btn-save-custom" form="customerForm">Save customer</button>
            <a href="{{ route('customers.index') }}" class="btn-cancel-custom">Cancel</a>
        </div>
    </div>

    @include('layouts.partials.pcoded-shell-end')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function () {
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

            $('.select2-field').not('[data-country-select]').select2({
                placeholder: 'Select an option',
                allowClear: false,
                width: '100%'
            });

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
                    email: { required: true, multiEmail: true },
                    street_address: 'required',
                    city: 'required',
                    country: 'required',
                    invoice_recipient_name: 'required',
                    invoice_recipient_address: 'required',
                    invoice_city: 'required',
                    invoice_country: 'required',
                    currency: 'required',
                    invoicing_email: { required: true, email: true },
                    invoicing_email_cc: { email: true },
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
                    main_account_manager: 'Please select account manager'
                },
                errorElement: 'div',
                errorClass: 'error-message',
                errorPlacement: function (error, element) {
                    if (element.hasClass('select2-field') || element.hasClass('select2-office-user')) {
                        error.insertAfter(element.next('.select2-container'));
                    } else {
                        error.insertAfter(element);
                    }
                },
                highlight: function (element) {
                    $(element).addClass('error');
                    if ($(element).hasClass('select2-field') || $(element).hasClass('select2-office-user')) {
                        $(element).next('.select2-container').addClass('error');
                    }
                },
                unhighlight: function (element) {
                    $(element).removeClass('error');
                    if ($(element).hasClass('select2-field') || $(element).hasClass('select2-office-user')) {
                        $(element).next('.select2-container').removeClass('error');
                    }
                }
            });
        });
    </script>
@endsection
