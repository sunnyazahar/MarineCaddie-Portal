@extends('layouts.app')

@section('styles')
    @include('hub.partials.show-page-styles')
@endsection

@section('content')
    <script>document.body.classList.add('edit-hub-page');</script>

    @include('layouts.partials.pcoded-shell-start', ['pageWrapperClass' => 'p-0'])

    <div class="edit-hub-page">
        <div class="edit-hub-hero">
            <div class="edit-hub-hero-main">
                <span class="edit-hub-hero-icon" aria-hidden="true">
                    <i class="ti-location-pin"></i>
                </span>
                <div>
                    <p class="edit-hub-kicker">Administration</p>
                    <h1 class="edit-hub-title">{{ $hub->hub_name }}</h1>
                    <p class="edit-hub-sub">Edit hub details, users, and contacts.</p>
                </div>
            </div>
            <a href="{{ route('hub.index') }}" class="edit-hub-back">
                <i class="ti-arrow-left"></i> Back to hubs
            </a>
        </div>

        <div class="edit-hub-meta">
            <span class="edit-hub-meta-pill">Hub ID <strong>{{ $hub->id }}</strong></span>
            @if ($hub->code)
                <span class="edit-hub-meta-pill">Code <strong>{{ $hub->code }}</strong></span>
            @endif
            @if ($hub->city || $hub->country)
                <span class="edit-hub-meta-pill">
                    Location
                    <strong>{{ trim(($hub->city ? $hub->city . ', ' : '') . ($hub->country ?? '')) }}</strong>
                </span>
            @endif
            @if ($hub->hide_in_portal)
                <span class="edit-hub-meta-pill is-hidden">Portal <strong>Hidden</strong></span>
            @else
                <span class="edit-hub-meta-pill is-active">Portal <strong>Visible</strong></span>
            @endif
        </div>

        <div class="tabs-container">
            <a class="tab-item active" data-tab="hub-details"><i class="ti-info-alt"></i> Hub Details</a>
            <a class="tab-item" data-tab="hub-users"><i class="ti-user"></i> Hub Users</a>
            <a class="tab-item" data-tab="contacts"><i class="ti-id-badge"></i> Contacts</a>
        </div>

        <div class="edit-hub-card">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show edit-hub-alert" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show edit-hub-alert" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            @endif

                                                            <div class="tab-content-container">
                                                            <form id="hubEditForm" action="{{ route('hub.update', $hub->id) }}" method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="hidden" name="active_tab" id="active_tab" value="{{ old('active_tab', 'hub-details') }}">
                                                                 <!-- Hub Details Tab -->
                                                                 <div id="hub-details" class="tab-content-custom active">
                                                                    <div class="form-pillar-container hub-details-grid">
                                                                        <div class="form-pillar hub-pillar-card">
                                                                            <div class="hub-pillar-head">
                                                                                <span class="hub-pillar-head-icon" aria-hidden="true"><i class="ti-briefcase"></i></span>
                                                                                <div>
                                                                                    <div class="hub-pillar-head-title">Identity &amp; contact</div>
                                                                                    <p class="hub-pillar-head-sub">Core hub profile and how to reach the desk.</p>
                                                                                </div>
                                                                            </div>

                                                                            <div class="form-group-custom">
                                                                                <label class="form-label-custom">Hub name <span class="text-danger">*</span></label>
                                                                                <input type="text" name="hub_name" class="form-input-custom" value="{{ $hub->hub_name }}" required>
                                                                            </div>

                                                                            <div class="form-group-custom d-none">
                                                                                <label class="form-label-custom">Company id</label>
                                                                                <input type="text" name="company_id" class="form-input-custom form-input-readonly" value="{{ $hub->company_id }}" readonly>
                                                                            </div>

                                                                            <div class="form-group-custom d-none">
                                                                                <label class="form-label-custom">Customer number from FM</label>
                                                                                <input type="text" name="customer_number_fm" class="form-input-custom" value="{{ $hub->customer_number_fm }}">
                                                                            </div>

                                                                            <div class="input-row">
                                                                                <div class="form-group-custom">
                                                                                    <label class="form-label-custom">Code <span class="text-danger">*</span></label>
                                                                                    <input type="text" name="code" class="form-input-custom" value="{{ $hub->code }}" required>
                                                                                </div>
                                                                                <div class="form-group-custom">
                                                                                    <label class="form-label-custom">Code description <span class="text-danger">*</span></label>
                                                                                    <input type="text" name="code_description" class="form-input-custom" value="{{ $hub->code_description }}" required>
                                                                                </div>
                                                                            </div>

                                                                            <div class="form-group-custom">
                                                                                <label class="form-label-custom">Phone number (with country code)</label>
                                                                                <input type="text" name="phone_number" class="form-input-custom" value="{{ $hub->phone_number }}">
                                                                            </div>

                                                                            <div class="form-group-custom">
                                                                                <label class="form-label-custom">Email <span class="text-danger">*</span></label>
                                                                                <input type="text" name="email" class="form-input-custom" value="{{ $hub->email }}"
                                                                                    placeholder="email@example.com; email2@example.com" required>
                                                                            </div>

                                                                            <div class="form-group-custom">
                                                                                <label class="form-label-custom">Contact Person <span class="text-danger">*</span></label>
                                                                                <input type="text" name="contact_person" class="form-input-custom" value="{{ old('contact_person', $hub->contact_person) }}" required>
                                                                            </div>

                                                                            <div class="form-group-custom d-none" style="flex-direction: row; gap: 8px; align-items: center; margin-top: 5px;">
                                                                                <input type="checkbox" name="is_gts_company" id="is_gts_company" value="1" {{ $hub->is_gts_company ? 'checked' : '' }}>
                                                                                <label class="form-label-custom" for="is_gts_company">This hub is part of GTS company</label>
                                                                            </div>

                                                                            <div class="input-row">
                                                                                <div class="form-group-custom">
                                                                                    <label class="form-label-custom">EORI number</label>
                                                                                    <input type="text" name="eori_number" class="form-input-custom" value="{{ $hub->eori_number }}">
                                                                                </div>
                                                                                <div class="form-group-custom">
                                                                                    <label class="form-label-custom">UN/LOCODE</label>
                                                                                    <input type="text" name="un_locode" class="form-input-custom" value="{{ $hub->un_locode }}">
                                                                                </div>
                                                                            </div>

                                                                            <div class="form-group-custom">
                                                                                <label class="form-label-custom">Remarks</label>
                                                                                <textarea name="remarks" class="form-textarea-custom" rows="3">{{ $hub->remarks }}</textarea>
                                                                            </div>

                                                                            <div class="form-group-custom">
                                                                                <label class="form-label-custom">Special considerations for destination</label>
                                                                                <textarea name="special_considerations" class="form-textarea-custom" rows="3">{{ $hub->special_considerations }}</textarea>
                                                                            </div>
                                                                        </div>

                                                                        <div class="form-pillar hub-pillar-card">
                                                                            <div class="hub-pillar-head">
                                                                                <span class="hub-pillar-head-icon is-location" aria-hidden="true"><i class="ti-map-alt"></i></span>
                                                                                <div>
                                                                                    <div class="hub-pillar-head-title">Location</div>
                                                                                    <p class="hub-pillar-head-sub">Physical hub address and port identifiers.</p>
                                                                                </div>
                                                                            </div>

                                                                            <div class="form-group-custom">
                                                                                <label class="form-label-custom">Hub address <span class="text-danger">*</span></label>
                                                                                <textarea name="hub_address" class="form-textarea-custom" rows="3" required>{{ $hub->hub_address }}</textarea>
                                                                            </div>

                                                                            <div class="input-row">
                                                                                <div class="form-group-custom" style="flex: 2;">
                                                                                    <label class="form-label-custom">City <span class="text-danger">*</span></label>
                                                                                    <input type="text" name="city" class="form-input-custom" value="{{ $hub->city }}" required>
                                                                                </div>
                                                                                <div class="form-group-custom">
                                                                                    <label class="form-label-custom">District/state</label>
                                                                                    <input type="text" name="district_state" class="form-input-custom" value="{{ $hub->district_state }}">
                                                                                </div>
                                                                                <div class="form-group-custom">
                                                                                    <label class="form-label-custom">Zip code</label>
                                                                                    <input type="text" name="zip_code" class="form-input-custom" value="{{ $hub->zip_code }}">
                                                                                </div>
                                                                            </div>

                                                                            <x-forms.country-select
                                                                                name="country"
                                                                                label="Country"
                                                                                :countries="$countries"
                                                                                valueKey="name"
                                                                                :value="$hub->country"
                                                                                class="form-select-custom select2-flag"
                                                                                :required="true"
                                                                                :allowClear="false"
                                                                            />

                                                                            <x-forms.port-select
                                                                                name="port_code"
                                                                                label="Port code"
                                                                                :value="old('port_code', $hub->port_code)"
                                                                                :required="true"
                                                                            />

                                                                            <div class="hub-soft-panel">
                                                                                <div class="hub-soft-panel-title">Office address <span>optional</span></div>
                                                                                <div class="form-group-custom">
                                                                                    <label class="form-label-custom">Office address</label>
                                                                                    <textarea name="office_address" class="form-textarea-custom" rows="3">{{ $hub->office_address }}</textarea>
                                                                                </div>

                                                                                <div class="input-row">
                                                                                    <div class="form-group-custom" style="flex: 2;">
                                                                                        <label class="form-label-custom">City</label>
                                                                                        <input type="text" name="office_city" class="form-input-custom" value="{{ $hub->office_city }}">
                                                                                    </div>
                                                                                    <div class="form-group-custom">
                                                                                        <label class="form-label-custom">District/state</label>
                                                                                        <input type="text" name="office_district_state" class="form-input-custom" value="{{ $hub->office_district_state }}">
                                                                                    </div>
                                                                                    <div class="form-group-custom">
                                                                                        <label class="form-label-custom">Zip code</label>
                                                                                        <input type="text" name="office_zip_code" class="form-input-custom" value="{{ $hub->office_zip_code }}">
                                                                                    </div>
                                                                                </div>

                                                                                <x-forms.country-select
                                                                                    name="office_country"
                                                                                    label="Country"
                                                                                    :countries="$countries"
                                                                                    valueKey="name"
                                                                                    :value="$hub->office_country"
                                                                                    class="form-select-custom select2-flag"
                                                                                    :allowClear="true"
                                                                                />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                 </div>

            </form>

            <!-- Hub Users / Contacts tabs live outside hubEditForm (separate create/edit pages). -->
            <div id="hub-users" class="tab-content-custom">
                <div class="hub-pane-toolbar">
                    <a href="{{ route('hub.users.create', $hub->id) }}" class="btn-hub-pane-action">Add hub user</a>
                </div>
                <div class="hub-table-wrap">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th style="width: 35%;">Name</th>
                                <th style="width: 25%;">Email</th>
                                <th style="width: 20%;">Phone number</th>
                                <th style="width: 15%; text-align: center;">Scan Gun</th>
                                <th style="width: 10%; text-align: right;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($hub->hubUsers as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone_number }}</td>
                                    <td style="text-align: center;">@if($user->show_in_scan_gun)<i class="ti-check" style="color: #01a9ac;"></i>@endif</td>
                                    <td style="text-align: right;">
                                        <div class="hub-row-actions">
                                            <a href="{{ route('hub.users.edit', [$hub->id, $user->id]) }}" title="Edit hub user">
                                                <i class="ti-pencil btn-action-pencil"></i>
                                            </a>
                                            @if ($canWriteAdministration)
                                                <button type="button"
                                                    class="btn-action-delete delete-hub-user"
                                                    data-url="{{ route('hub.users.destroy', [$hub->id, $user->id]) }}"
                                                    data-name="{{ $user->name }}"
                                                    title="Delete hub user">
                                                    <i class="ti-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 40px; color: #8da2b5;">No hub users found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="contacts" class="tab-content-custom">
                <div class="hub-pane-toolbar">
                    <a href="{{ route('hub.contacts.create', $hub->id) }}" class="btn-hub-pane-action">Add contact</a>
                </div>
                <div class="hub-table-wrap">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th style="width: 25%;">Name</th>
                                <th style="width: 25%;">Email</th>
                                <th style="width: 20%;">Phone number</th>
                                <th style="width: 20%;">Description</th>
                                <th style="width: 5%;">Main</th>
                                <th style="width: 10%; text-align: right;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($hub->contacts as $contact)
                                <tr>
                                    <td>
                                        <a href="{{ route('hub.contacts.edit', [$hub->id, $contact->id]) }}" class="table-link">{{ $contact->name }}</a>
                                    </td>
                                    <td>{{ $contact->email }}</td>
                                    <td>{{ $contact->phone_number }}</td>
                                    <td>{{ $contact->description }}</td>
                                    <td style="text-align: center;">@if($contact->is_main_contact)<i class="ti-check" style="color: #01a9ac;"></i>@endif</td>
                                    <td style="text-align: right;">
                                        <div class="hub-row-actions">
                                            <a href="{{ route('hub.contacts.edit', [$hub->id, $contact->id]) }}" title="Edit contact">
                                                <i class="ti-pencil btn-action-pencil"></i>
                                            </a>
                                            @if ($canWriteAdministration)
                                                <button type="button"
                                                    class="btn-action-delete delete-hub-contact"
                                                    data-url="{{ route('hub.contacts.destroy', [$hub->id, $contact->id]) }}"
                                                    data-name="{{ $contact->name }}"
                                                    title="Delete contact">
                                                    <i class="ti-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 40px; color: #9ca3af;">No contacts found for this hub.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
                                                            </div>
        </div>

        <div class="hub-edit-footer">
            <button type="submit" class="btn-save-custom" form="hubEditForm">Save hub</button>
            <a href="{{ route('hub.index') }}" class="btn-cancel-custom">Cancel</a>
            <div class="audit-info">
                @include('partials.audit-info', ['record' => $hub, 'bold' => true])
            </div>
        </div>
    </div>

    @include('layouts.partials.pcoded-shell-end')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>

    <script>
        $(document).ready(function() {
            $('body').addClass('edit-hub-page');

            function activateHubTab(tabId) {
                if (!tabId || !$('#' + tabId).length || !$('.tab-item[data-tab="' + tabId + '"]').length) {
                    return false;
                }
                $('.tab-item').removeClass('active');
                $('.tab-item[data-tab="' + tabId + '"]').addClass('active');
                $('.tab-content-custom').removeClass('active');
                $('#' + tabId).addClass('active');
                $('#active_tab').val(tabId);
                return true;
            }

            $('.tab-item').on('click', function() {
                var tabId = $(this).data('tab');
                activateHubTab(tabId);
                if (tabId === 'hub-details') {
                    refreshAutoResizeHubTextareas();
                }
                if (history.replaceState) {
                    history.replaceState(null, '', '#' + tabId);
                } else {
                    window.location.hash = tabId;
                }
            });

            var hashTab = window.location.hash.replace(/^#/, '');
            if (hashTab) {
                activateHubTab(hashTab);
            }

            $('.select2-single').select2({
                placeholder: 'Select an option',
                allowClear: true,
                width: '100%'
            });

            function autoResizeHubTextarea(textarea) {
                if (!textarea) {
                    return;
                }

                var computedStyle = window.getComputedStyle(textarea);
                var minHeight = parseFloat(computedStyle.minHeight) || 0;

                textarea.style.setProperty('height', 'auto', 'important');
                textarea.style.setProperty('overflow-y', 'hidden', 'important');
                textarea.style.setProperty('height', Math.max(textarea.scrollHeight, minHeight) + 'px', 'important');
            }

            function refreshAutoResizeHubTextareas() {
                $('#hubEditForm textarea.form-textarea-custom').each(function() {
                    autoResizeHubTextarea(this);
                });
            }

            $(document).on('input.autoResizeHubTextarea change.autoResizeHubTextarea', '#hubEditForm textarea.form-textarea-custom', function() {
                autoResizeHubTextarea(this);
            });

            refreshAutoResizeHubTextareas();

            function deleteHubRelatedRow($button, title, successFallback) {
                var url = $button.data('url');
                var name = $button.data('name') || 'this record';
                var $row = $button.closest('tr');

                swal({
                    title: title,
                    text: 'Are you sure you want to delete "' + name + '"?',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete',
                    cancelButtonText: 'Cancel',
                    closeOnConfirm: false,
                    closeOnCancel: true,
                    showLoaderOnConfirm: true
                }, function (isConfirm) {
                    if (!isConfirm) {
                        return;
                    }

                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            if (response.success) {
                                swal({
                                    title: 'Deleted',
                                    text: response.message || successFallback,
                                    type: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                $row.remove();
                            } else {
                                swal('Error', response.message || successFallback.replace('successfully', 'failed'), 'error');
                            }
                        },
                        error: function (xhr) {
                            var message = (xhr.responseJSON && xhr.responseJSON.message)
                                ? xhr.responseJSON.message
                                : 'Error deleting record.';
                            swal('Error', message, 'error');
                        }
                    });
                });
            }

            $(document).on('click', '.delete-hub-user', function () {
                deleteHubRelatedRow($(this), 'Delete hub user?', 'Hub user deleted successfully.');
            });

            $(document).on('click', '.delete-hub-contact', function () {
                deleteHubRelatedRow($(this), 'Delete contact?', 'Contact deleted successfully.');
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

            $('#hubEditForm').validate({
                rules: {
                    hub_name: {
                        required: true,
                        minlength: 3
                    },
                    code: {
                        required: true
                    },
                    code_description: {
                        required: true
                    },
                    email: {
                        required: true,
                        multiEmail: true
                    },
                    contact_person: {
                        required: true
                    },
                    hub_address: {
                        required: true
                    },
                    city: {
                        required: true
                    },
                    country: {
                        required: true
                    },
                    port_code: {
                        required: true
                    }
                },
                messages: {
                    hub_name: {
                        required: "Please enter the hub name",
                        minlength: "Hub name must be at least 3 characters"
                    },
                    code: {
                        required: "Please enter the code"
                    },
                    code_description: {
                        required: "Please enter the code description"
                    },
                    email: {
                        required: "Please enter the email",
                        multiEmail: "Please enter valid email address(es), separated by comma or semicolon"
                    },
                    contact_person: {
                        required: "Please enter the contact person"
                    },
                    hub_address: {
                        required: "Please enter the hub address"
                    },
                    city: {
                        required: "Please enter the city"
                    },
                    country: {
                        required: "Please select the country"
                    },
                    port_code: {
                        required: "Please select the port code"
                    }
                },
                errorElement: 'div',
                errorClass: 'error-message',
                errorPlacement: function(error, element) {
                    if (element.hasClass('select2-flag') || element.is('[data-country-select]') || element.is('[data-port-select]')) {
                        error.insertAfter(element.next('.select2-container'));
                    } else if (element.parent('.input-group-custom').length) {
                        error.insertAfter(element.parent());
                    } else {
                        error.insertAfter(element);
                    }
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass("error");
                    if ($(element).hasClass('select2-flag') || $(element).is('[data-country-select]') || $(element).is('[data-port-select]')) {
                        $(element).next('.select2-container').addClass('error');
                    }
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass("error");
                    if ($(element).hasClass('select2-flag') || $(element).is('[data-country-select]') || $(element).is('[data-port-select]')) {
                        $(element).next('.select2-container').removeClass('error');
                    }
                }
            });
        });
    </script>

    @include('partials.unsaved-changes-guard', ['formSelector' => '#hubEditForm', 'fallbackUrl' => route('hub.index')])
@endsection
