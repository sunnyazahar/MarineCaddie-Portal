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
                    <p class="create-agent-sub">Add an agent with identity, contact, and location details.</p>
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

                    <input type="hidden" name="company_id" value="{{ old('company_id') }}">

                    <div class="agent-pillars agent-details-grid">
                        <div class="agent-pillar-card">
                            <div class="agent-pillar-head">
                                <span class="agent-pillar-head-icon" aria-hidden="true"><i class="ti-briefcase"></i></span>
                                <div>
                                    <div class="agent-pillar-head-title">Identity &amp; contact</div>
                                    <p class="agent-pillar-head-sub">Core agent profile and how to reach the desk.</p>
                                </div>
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom" for="agent_name">Agent name <span class="text-danger">*</span></label>
                                <input type="text" id="agent_name" name="agent_name" class="form-input-custom"
                                    value="{{ old('agent_name') }}" required autocomplete="organization">
                            </div>

                            <div class="input-row">
                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="code">Code <span class="text-danger">*</span></label>
                                    <input type="text" id="code" name="code" class="form-input-custom" value="{{ old('code') }}" required>
                                </div>
                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="code_description">Code description <span class="text-danger">*</span></label>
                                    <input type="text" id="code_description" name="code_description" class="form-input-custom"
                                        value="{{ old('code_description') }}" required>
                                </div>
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom" for="phone">Phone number (with country code) <span class="text-danger">*</span></label>
                                <input type="text" id="phone" name="phone" class="form-input-custom" value="{{ old('phone') }}" required autocomplete="tel">
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom" for="email">Email <span class="text-danger">*</span></label>
                                <input type="text" id="email" name="email" class="form-input-custom"
                                    value="{{ old('email') }}" placeholder="email@example.com; email2@example.com" required>
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom" for="contact_person">Contact Person <span class="text-danger">*</span></label>
                                <input type="text" id="contact_person" name="contact_person" class="form-input-custom"
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

                        <div class="agent-pillar-card">
                            <div class="agent-pillar-head">
                                <span class="agent-pillar-head-icon is-location" aria-hidden="true"><i class="ti-map-alt"></i></span>
                                <div>
                                    <div class="agent-pillar-head-title">Location</div>
                                    <p class="agent-pillar-head-sub">Physical agent address and port identifiers.</p>
                                </div>
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom" for="agent_address">Agent address <span class="text-danger">*</span></label>
                                <textarea id="agent_address" name="agent_address" class="form-textarea-custom" rows="3" required>{{ old('agent_address') }}</textarea>
                            </div>

                            <div class="input-row">
                                <div class="form-group-custom" style="flex: 2;">
                                    <label class="form-label-custom" for="city">City <span class="text-danger">*</span></label>
                                    <input type="text" id="city" name="city" class="form-input-custom" value="{{ old('city') }}" required>
                                </div>
                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="district_state">District/state</label>
                                    <input type="text" id="district_state" name="district_state" class="form-input-custom"
                                        value="{{ old('district_state') }}">
                                </div>
                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="zip_code">Zip code</label>
                                    <input type="text" id="zip_code" name="zip_code" class="form-input-custom" value="{{ old('zip_code') }}">
                                </div>
                            </div>

                            <x-forms.country-select
                                name="country_id"
                                label="Country"
                                :countries="$countries"
                                :value="old('country_id')"
                                class="form-select-custom select2-flag"
                                :required="true"
                                :allowClear="false"
                            />

                            <x-forms.port-select
                                name="port_code"
                                label="Port code"
                                :value="old('port_code')"
                                :required="true"
                            />

                            <div class="agent-soft-panel">
                                <div class="agent-soft-panel-title">Office address <span>optional</span></div>
                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="office_address">Office address</label>
                                    <textarea id="office_address" name="office_address" class="form-textarea-custom" rows="3">{{ old('office_address') }}</textarea>
                                </div>

                                <div class="input-row">
                                    <div class="form-group-custom" style="flex: 2;">
                                        <label class="form-label-custom" for="office_city">City</label>
                                        <input type="text" id="office_city" name="office_city" class="form-input-custom" value="{{ old('office_city') }}">
                                    </div>
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="office_district_state">District/state</label>
                                        <input type="text" id="office_district_state" name="office_district_state" class="form-input-custom"
                                            value="{{ old('office_district_state') }}">
                                    </div>
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="office_zip_code">Zip code</label>
                                        <input type="text" id="office_zip_code" name="office_zip_code" class="form-input-custom"
                                            value="{{ old('office_zip_code') }}">
                                    </div>
                                </div>

                                <x-forms.country-select
                                    name="office_country_id"
                                    label="Country"
                                    :countries="$countries"
                                    :value="old('office_country_id')"
                                    class="form-select-custom select2-flag"
                                    :allowClear="true"
                                />
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

            function autoResizeAgentTextarea(textarea) {
                if (!textarea) {
                    return;
                }

                var computedStyle = window.getComputedStyle(textarea);
                var minHeight = parseFloat(computedStyle.minHeight) || 0;

                textarea.style.setProperty('height', 'auto', 'important');
                textarea.style.setProperty('overflow-y', 'hidden', 'important');
                textarea.style.setProperty('height', Math.max(textarea.scrollHeight, minHeight) + 'px', 'important');
            }

            function refreshAutoResizeAgentTextareas() {
                $('#agentForm textarea.form-textarea-custom').each(function () {
                    autoResizeAgentTextarea(this);
                });
            }

            $(document).on('input.autoResizeAgentTextarea change.autoResizeAgentTextarea', '#agentForm textarea.form-textarea-custom', function () {
                autoResizeAgentTextarea(this);
            });

            refreshAutoResizeAgentTextareas();

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
                    code: { required: true },
                    code_description: { required: true },
                    phone: { required: true },
                    email: { required: true, multiEmail: true },
                    contact_person: { required: true },
                    agent_address: { required: true },
                    city: { required: true },
                    country_id: { required: true },
                    port_code: { required: true }
                },
                messages: {
                    agent_name: {
                        required: 'Please enter the agent name',
                        minlength: 'Agent name must be at least 3 characters'
                    },
                    code: {
                        required: 'Please enter the code'
                    },
                    code_description: {
                        required: 'Please enter the code description'
                    },
                    phone: {
                        required: 'Please enter the phone number'
                    },
                    email: {
                        required: 'Please enter the email',
                        multiEmail: 'Please enter valid email address(es), separated by comma or semicolon'
                    },
                    contact_person: {
                        required: 'Please enter the contact person'
                    },
                    agent_address: {
                        required: 'Please enter the agent address'
                    },
                    city: {
                        required: 'Please enter the city'
                    },
                    country_id: {
                        required: 'Please select the country'
                    },
                    port_code: {
                        required: 'Please select the port code'
                    }
                },
                errorElement: 'div',
                errorClass: 'error-message',
                highlight: function (element) {
                    $(element).addClass('error');
                    if ($(element).is('[data-country-select]') || $(element).is('[data-port-select]')) {
                        $(element).next('.select2-container').addClass('error');
                    }
                },
                unhighlight: function (element) {
                    $(element).removeClass('error');
                    if ($(element).is('[data-country-select]') || $(element).is('[data-port-select]')) {
                        $(element).next('.select2-container').removeClass('error');
                    }
                },
                errorPlacement: function (error, element) {
                    if (element.is('[data-country-select]') || element.is('[data-port-select]')) {
                        error.insertAfter(element.next('.select2-container'));
                    } else {
                        error.insertAfter(element);
                    }
                }
            });

            $(document).on('select2:open', '.agent-pillar-card select', function () {
                $('.agent-pillar-card').css('z-index', '');
                $(this).closest('.agent-pillar-card').css('z-index', 40);
            });

            $(document).on('select2:close', '.agent-pillar-card select', function () {
                $(this).closest('.agent-pillar-card').css('z-index', '');
            });
        });
    </script>
@endsection
