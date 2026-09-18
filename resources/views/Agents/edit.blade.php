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
                    <p class="edit-agent-sub">Edit agent details, users, and contacts.</p>
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
            <a class="tab-item" data-tab="agent-users"><i class="ti-user"></i> Agent users</a>
            <a class="tab-item" data-tab="contacts"><i class="ti-id-badge"></i> Contacts</a>
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
                <form id="agentEditForm" action="{{ route('agents.update', $agent->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="active_tab" id="active_tab" value="{{ old('active_tab', 'agent-details') }}">

<div id="agent-details" class="tab-content-custom active">
    <div class="form-pillar-container agent-details-grid">
        <div class="form-pillar agent-pillar-card">
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
                    value="{{ old('agent_name', $agent->agent_name) }}" required autocomplete="organization">
            </div>

            <input type="hidden" name="company_id" value="{{ old('company_id', $agent->company_id) }}">

            <div class="input-row agent-details-input-row">
                <div class="form-group-custom">
                    <label class="form-label-custom" for="code">Code <span class="text-danger">*</span></label>
                    <input type="text" id="code" name="code" class="form-input-custom"
                        value="{{ old('code', $agent->code) }}" required>
                </div>
                <div class="form-group-custom">
                    <label class="form-label-custom" for="code_description">Code description <span class="text-danger">*</span></label>
                    <input type="text" id="code_description" name="code_description" class="form-input-custom"
                        value="{{ old('code_description', $agent->code_description) }}" required>
                </div>
            </div>

            <div class="form-group-custom">
                <label class="form-label-custom" for="phone">Phone number (with country code) <span class="text-danger">*</span></label>
                <input type="text" id="phone" name="phone" class="form-input-custom"
                    value="{{ old('phone', $agent->phone) }}" required autocomplete="tel">
            </div>

            <div class="form-group-custom">
                <label class="form-label-custom" for="email">Email <span class="text-danger">*</span></label>
                <input type="text" id="email" name="email" class="form-input-custom"
                    value="{{ old('email', $agent->email) }}"
                    placeholder="email@example.com; email2@example.com" required>
            </div>

            <div class="form-group-custom">
                <label class="form-label-custom" for="contact_person">Contact Person <span class="text-danger">*</span></label>
                <input type="text" id="contact_person" name="contact_person" class="form-input-custom"
                    value="{{ old('contact_person', $agent->contact_person) }}" required autocomplete="name">
            </div>

            <div class="form-group-custom">
                <label class="form-label-custom" for="remarks">Remarks</label>
                <textarea id="remarks" name="remarks" class="form-textarea-custom"
                    rows="3">{{ old('remarks', $agent->remarks) }}</textarea>
            </div>

            <div class="form-group-custom">
                <label class="form-label-custom" for="special_considerations">Notes for consignee</label>
                <textarea id="special_considerations" name="special_considerations" class="form-textarea-custom"
                    rows="3">{{ old('special_considerations', $agent->special_considerations) }}</textarea>
            </div>
        </div>

        <div class="form-pillar agent-pillar-card">
            <div class="agent-pillar-head">
                <span class="agent-pillar-head-icon is-location" aria-hidden="true"><i class="ti-map-alt"></i></span>
                <div>
                    <div class="agent-pillar-head-title">Location</div>
                    <p class="agent-pillar-head-sub">Physical agent address and port identifiers.</p>
                </div>
            </div>

            <div class="form-group-custom">
                <label class="form-label-custom" for="agent_address">Agent address <span class="text-danger">*</span></label>
                <textarea id="agent_address" name="agent_address" class="form-textarea-custom" rows="3"
                    required>{{ old('agent_address', $agent->agent_address) }}</textarea>
            </div>

            <div class="input-row agent-details-input-row">
                <div class="form-group-custom" style="flex: 2;">
                    <label class="form-label-custom" for="city">City <span class="text-danger">*</span></label>
                    <input type="text" id="city" name="city" class="form-input-custom"
                        value="{{ old('city', $agent->city) }}" required>
                </div>
                <div class="form-group-custom">
                    <label class="form-label-custom" for="district_state">District/state</label>
                    <input type="text" id="district_state" name="district_state" class="form-input-custom"
                        value="{{ old('district_state', $agent->district_state) }}">
                </div>
                <div class="form-group-custom">
                    <label class="form-label-custom" for="zip_code">Zip code</label>
                    <input type="text" id="zip_code" name="zip_code" class="form-input-custom"
                        value="{{ old('zip_code', $agent->zip_code) }}">
                </div>
            </div>

            <x-forms.country-select
                name="country_id"
                label="Country"
                :countries="$countries"
                :value="old('country_id', $agent->country_id)"
                class="form-select-custom select2-flag"
                :required="true"
                :allowClear="false"
            />

            <x-forms.port-select
                name="port_code"
                label="Port code"
                :value="old('port_code', $agent->port_code)"
                :required="true"
            />

            <div class="agent-soft-panel">
                <div class="agent-soft-panel-title">Office address <span>optional</span></div>
                <div class="form-group-custom">
                    <label class="form-label-custom" for="office_address">Office address</label>
                    <textarea id="office_address" name="office_address" class="form-textarea-custom"
                        rows="3">{{ old('office_address', $agent->office_address) }}</textarea>
                </div>

                <div class="input-row agent-details-input-row">
                    <div class="form-group-custom" style="flex: 2;">
                        <label class="form-label-custom" for="office_city">City</label>
                        <input type="text" id="office_city" name="office_city" class="form-input-custom"
                            value="{{ old('office_city', $agent->office_city) }}">
                    </div>
                    <div class="form-group-custom">
                        <label class="form-label-custom" for="office_district_state">District/state</label>
                        <input type="text" id="office_district_state" name="office_district_state" class="form-input-custom"
                            value="{{ old('office_district_state', $agent->office_district_state) }}">
                    </div>
                    <div class="form-group-custom">
                        <label class="form-label-custom" for="office_zip_code">Zip code</label>
                        <input type="text" id="office_zip_code" name="office_zip_code" class="form-input-custom"
                            value="{{ old('office_zip_code', $agent->office_zip_code) }}">
                    </div>
                </div>

                <x-forms.country-select
                    name="office_country_id"
                    label="Country"
                    :countries="$countries"
                    :value="old('office_country_id', $agent->office_country_id)"
                    class="form-select-custom select2-flag"
                    :allowClear="true"
                />
            </div>
        </div>
    </div>
</div> <!-- End #agent-details -->

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
                $('#agent-details textarea.form-textarea-custom').each(function () {
                    autoResizeAgentTextarea(this);
                });
            }

            $(document).on('input.autoResizeAgentTextarea change.autoResizeAgentTextarea', '#agent-details textarea.form-textarea-custom', function () {
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

            $('#agentEditForm').validate({
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
                    code: { required: 'Please enter the code' },
                    code_description: { required: 'Please enter the code description' },
                    phone: { required: 'Please enter the phone number' },
                    email: {
                        required: 'Please enter the email',
                        multiEmail: 'Please enter valid email address(es), separated by comma or semicolon'
                    },
                    contact_person: { required: 'Please enter the contact person' },
                    agent_address: { required: 'Please enter the agent address' },
                    city: { required: 'Please enter the city' },
                    country_id: { required: 'Please select the country' },
                    port_code: { required: 'Please select the port code' }
                },
                errorElement: 'div',
                errorClass: 'error-message',
                errorPlacement: function (error, element) {
                    if (element.is('[data-country-select]') || element.is('[data-port-select]')) {
                        error.insertAfter(element.next('.select2-container'));
                    } else {
                        error.insertAfter(element);
                    }
                },
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
                }
            });

            // Initialize Select2 for standard filters
            $('.select2').select2({
                placeholder: "Click here",
                allowClear: false
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
                if (tabId === 'agent-details') {
                    refreshAutoResizeAgentTextareas();
                }
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
            csrfToken.value = (typeof window.mcCsrfToken === 'function' ? window.mcCsrfToken() : null)
                || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                || '{{ csrf_token() }}';
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
