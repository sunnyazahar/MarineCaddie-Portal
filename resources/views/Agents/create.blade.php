@extends('layouts.app')

@section('styles')
    @include('Agents.partials.create-page-styles')
@endsection

@section('content')
    <script>document.body.classList.add('create-agent-page');</script>

    @include('layouts.partials.pcoded-shell-start', ['pageWrapperClass' => 'p-0'])

    <div class="create-agent-page">
        <div class="create-agent-hero">
            <div class="create-agent-hero-main">
                <span class="create-agent-hero-icon" aria-hidden="true">
                    <i class="ti-briefcase"></i>
                </span>
                <div>
                    <p class="create-agent-kicker">Administration</p>
                    <h1 class="create-agent-title">Create agent</h1>
                    <p class="create-agent-sub">Add an agent company with address, port, type, and contact details.</p>
                </div>
            </div>
            <a href="{{ route('agents.index') }}" class="create-agent-back">
                <i class="ti-arrow-left"></i> Back to agents
            </a>
        </div>

        <div class="create-agent-card">
            <div class="agent-form-container">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show agent-form-alert" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                @endif

                <form id="agentForm" action="{{ route('agents.store') }}" method="POST">
                    @csrf

                    <div class="agent-pillars">
                        <div class="agent-pillar-col">
                            <div class="agent-pillar">
                                <div class="agent-pillar__title">Agent information</div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="agent_name">Agent name <span class="text-danger">*</span></label>
                                    <input type="text" id="agent_name" name="agent_name" class="form-control-custom"
                                        value="{{ old('agent_name') }}" required autocomplete="organization">
                                </div>

                                <input type="hidden" name="company_id" value="{{ old('company_id') }}">

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
                                    <label class="form-label-custom" for="phone">Phone number (with country code)</label>
                                    <input type="text" id="phone" name="phone" class="form-control-custom" value="{{ old('phone') }}" autocomplete="tel">
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="email">Email</label>
                                    <input type="text" id="email" name="email" class="form-control-custom"
                                        value="{{ old('email') }}" placeholder="email@example.com; email2@example.com">
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

                                <div class="checkbox-group">
                                    <input type="checkbox" class="checkbox-custom" name="show_pre_alert" id="show_pre_alert" value="1"
                                        {{ old('show_pre_alert') ? 'checked' : '' }}>
                                    <label class="checkbox-label" for="show_pre_alert">Show pre-alert warning when items in shipment are not scanned</label>
                                </div>
                            </div>
                        </div>

                        <div class="agent-pillar-col">
                            <div class="agent-pillar">
                                <div class="agent-pillar__title">Agent address</div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="agent_address">Agent address</label>
                                    <textarea id="agent_address" name="agent_address" class="form-textarea-custom" rows="3">{{ old('agent_address') }}</textarea>
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
                                    :value="old('country_id')"
                                    wrapperClass="form-group-custom"
                                    :allowClear="true"
                                />

                                <x-forms.port-select
                                    name="port_code"
                                    label="Port code"
                                    :value="old('port_code')"
                                    wrapperClass="form-group-custom"
                                />

                                <div class="agent-pillar__title" style="margin-top: 8px;">Office address (optional)</div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="office_address">Office address</label>
                                    <textarea id="office_address" name="office_address" class="form-textarea-custom" rows="3">{{ old('office_address') }}</textarea>
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
                                    label="Office country"
                                    :countries="$countries"
                                    :value="old('office_country_id')"
                                    wrapperClass="form-group-custom"
                                    :allowClear="true"
                                />
                            </div>
                        </div>

                        <div class="agent-pillar-col">
                            <div class="agent-pillar">
                                <div class="agent-pillar__title">Agent details</div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="eori_number">EORI number</label>
                                    <input type="text" id="eori_number" name="eori_number" class="form-control-custom" value="{{ old('eori_number') }}">
                                </div>

                                <div class="address-sub-grid" style="grid-template-columns: 1fr 1.4fr;">
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="un_locode">UN/LOCODE</label>
                                        <input type="text" id="un_locode" name="un_locode" class="form-control-custom" value="{{ old('un_locode') }}">
                                    </div>
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="agent_type">Agent type</label>
                                        <select name="agent_type" id="agent_type" class="form-control-custom select2-agent-type">
                                            <option value=""></option>
                                            @foreach ([
                                                'contracted_agent' => 'Contracted agent',
                                                'main_agent' => 'Main agent',
                                                'sub_agent' => 'Sub agent',
                                                '3pl_japan_supplier' => '3PL Japan supplier',
                                                '3pl_greece_supplier' => '3PL Greece supplier',
                                                'mt_bergen_agency' => 'MT Bergen Agency supplier',
                                                'mt_singapore_projects' => 'MT Singapore Projects supplier',
                                                'mt_benelux_supplier' => 'MT Benelux supplier',
                                                'door_to_deck_agent' => 'Door to Deck agent',
                                                'mt_singapore_agency' => 'MT Singapore Agency supplier',
                                                'mt_norway_supplier' => 'MT Norway supplier',
                                                '3pl_general_supplier' => '3PL General supplier',
                                                'external_entity' => 'External entity',
                                            ] as $value => $label)
                                                <option value="{{ $value }}" {{ old('agent_type') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="create-agent-footer">
            <button type="submit" class="btn-save-custom" form="agentForm">Save agent</button>
            <a href="{{ route('agents.index') }}" class="btn-cancel-custom">Cancel</a>
        </div>
    </div>

    @include('layouts.partials.pcoded-shell-end')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function () {
            $('body').addClass('create-agent-page');

            $('.select2-agent-type').select2({
                placeholder: 'Select agent type',
                allowClear: true,
                width: '100%'
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

            $('#agentForm').validate({
                rules: {
                    agent_name: { required: true, minlength: 3 },
                    contact_person: { required: true },
                    email: { multiEmail: true }
                },
                messages: {
                    agent_name: {
                        required: 'Please enter the agent name',
                        minlength: 'Agent name must be at least 3 characters'
                    },
                    contact_person: {
                        required: 'Please enter the contact person'
                    }
                },
                errorElement: 'div',
                errorClass: 'error-message',
                highlight: function (element) {
                    $(element).addClass('error');
                    if ($(element).hasClass('select2-agent-type')) {
                        $(element).next('.select2-container').addClass('error');
                    }
                },
                unhighlight: function (element) {
                    $(element).removeClass('error');
                    if ($(element).hasClass('select2-agent-type')) {
                        $(element).next('.select2-container').removeClass('error');
                    }
                },
                errorPlacement: function (error, element) {
                    if (element.hasClass('select2-agent-type')) {
                        error.insertAfter(element.next('.select2-container'));
                    } else {
                        error.insertAfter(element);
                    }
                }
            });
        });
    </script>
@endsection
