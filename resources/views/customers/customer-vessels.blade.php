@extends('layouts.app')

@section('styles')
    @include('customers.partials.vessel-form-styles')
@endsection

@section('content')
    <script>document.body.classList.add('edit-vessel-page');</script>

    @include('layouts.partials.pcoded-shell-start', ['pageWrapperClass' => 'p-0'])

    @php
        $vesselStatusClass = 'is-active';
        $vesselStatusLabel = 'Active';
        if ($vessel->sanction_blocked || $vessel->financially_blocked) {
            $vesselStatusClass = 'is-warning';
            $vesselStatusLabel = 'Blocked';
        } elseif ($vessel->inactive_vessel) {
            $vesselStatusClass = 'is-muted';
            $vesselStatusLabel = 'Inactive';
        }
    @endphp

    <div class="edit-vessel-page">
        <div class="edit-vessel-hero">
            <div class="edit-vessel-hero-main">
                <span class="edit-vessel-hero-icon" aria-hidden="true">
                    <i class="ti-anchor"></i>
                </span>
                <div>
                    <p class="edit-vessel-kicker">Administration</p>
                    <h1 class="edit-vessel-title">{{ $vessel->vessel }}</h1>
                    <p class="edit-vessel-sub">
                        Vessel for customer <strong>{{ $vessel->customer->customer_name }}</strong>
                    </p>
                </div>
            </div>
            <a href="{{ route('customers.edit', $vessel->customer_id) }}#vessels" class="edit-vessel-back">
                <i class="ti-arrow-left"></i> Back to customer
            </a>
        </div>

        <div class="edit-vessel-meta">
            <span class="edit-vessel-meta-pill">
                IMO <strong>{{ $vessel->vessel_imo ?: 'N/A' }}</strong>
            </span>
            <span class="edit-vessel-meta-pill">
                Customer vessel code <strong>{{ $vessel->customer_vessel_code ?: 'N/A' }}</strong>
            </span>
            <span class="edit-vessel-meta-pill {{ $vesselStatusClass }}">
                Status <strong>{{ $vesselStatusLabel }}</strong>
            </span>
        </div>

        <div class="tabs-container">
            <a class="tab-item active" data-tab="vessel-details"><i class="ti-list"></i> Vessel details</a>
            <a class="tab-item" data-tab="vessel-location"><i class="ti-location-pin"></i> Vessel location</a>
            <a class="tab-item" data-tab="change-log"><i class="ti-time"></i> Change log</a>
        </div>

        <div class="edit-vessel-card">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin: 16px 16px 0;">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            @endif
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin: 16px 16px 0;">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            @endif

            <div class="tab-content-container">
                <form id="vesselForm" method="POST" action="{{ route('customers.vessels.update', $vessel->id) }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="active_tab" id="active_tab" value="{{ old('active_tab', 'vessel-details') }}">

                    <div id="vessel-details" class="tab-content-custom active">
                        <div class="form-pillar-container form-pillar-container--4">
                            <div class="form-pillar">
                                <div class="form-section-header">Vessel information</div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="vessel">Vessel</label>
                                    <input type="text" id="vessel" name="vessel" class="form-control-custom" value="{{ $vessel->vessel }}">
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="vessel_name_alias">Vessel name alias</label>
                                    <input type="text" id="vessel_name_alias" name="vessel_name_alias" class="form-control-custom" value="{{ $vessel->vessel_name_alias }}">
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="vessel_imo">Vessel IMO</label>
                                    <input type="text" id="vessel_imo" name="vessel_imo" class="form-control-custom" value="{{ $vessel->vessel_imo }}">
                                </div>

                                <div class="input-row">
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="shipyard">Shipyard</label>
                                        <input type="text" id="shipyard" name="shipyard" class="form-control-custom" value="{{ $vessel->shipyard }}">
                                    </div>
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="shipyard_location">Shipyard location</label>
                                        <input type="text" id="shipyard_location" name="shipyard_location" class="form-control-custom" value="{{ $vessel->shipyard_location }}">
                                    </div>
                                </div>

                                <div class="vessel-checkbox-stack">
                                    <label class="vessel-checkbox-item">
                                        <input type="checkbox" name="not_in_transit" value="1" {{ $vessel->not_in_transit ? 'checked' : '' }}>
                                        Vessel is not in transit
                                    </label>
                                    <label class="vessel-checkbox-item">
                                        <input type="checkbox" name="inactive_vessel" value="1" {{ $vessel->inactive_vessel ? 'checked' : '' }}>
                                        Inactive vessel
                                    </label>
                                    <label class="vessel-checkbox-item">
                                        <input type="checkbox" name="sanction_blocked" value="1" {{ $vessel->sanction_blocked ? 'checked' : '' }}>
                                        Sanction blocked
                                    </label>
                                    <label class="vessel-checkbox-item">
                                        <input type="checkbox" name="financially_blocked" value="1" {{ $vessel->financially_blocked ? 'checked' : '' }}>
                                        Financially blocked
                                    </label>
                                    <label class="vessel-checkbox-item">
                                        <input type="checkbox" name="pre_payment_only" value="1" {{ $vessel->pre_payment_only ? 'checked' : '' }}>
                                        Pre-payment only
                                    </label>
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="customer_vessel_code">Customer vessel code</label>
                                    <input type="text" id="customer_vessel_code" name="customer_vessel_code" class="form-control-custom" value="{{ $vessel->customer_vessel_code }}">
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="vessel_type_alias">Vessel type alias</label>
                                    <select id="vessel_type_alias" name="vessel_type_alias" class="form-control-custom vessel-select2">
                                        <option></option>
                                        <option value="MV" {{ $vessel->vessel_type_alias == 'MV' ? 'selected' : '' }}>MV</option>
                                        <option value="MT" {{ $vessel->vessel_type_alias == 'MT' ? 'selected' : '' }}>MT</option>
                                        <option value="LPG" {{ $vessel->vessel_type_alias == 'LPG' ? 'selected' : '' }}>LPG</option>
                                    </select>
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="po_example">PO example</label>
                                    <input type="text" id="po_example" name="po_example" class="form-control-custom" value="{{ $vessel->po_example }}">
                                </div>

                                <div class="input-row">
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="internal_shipment">Internal shipment</label>
                                        <select id="internal_shipment" name="internal_shipment" class="form-control-custom vessel-select2">
                                            <option></option>
                                            <option value="Yes" {{ $vessel->internal_shipment == 'Yes' ? 'selected' : '' }}>Yes</option>
                                            <option value="No" {{ $vessel->internal_shipment == 'No' ? 'selected' : '' }}>No</option>
                                        </select>
                                    </div>
                                    <div class="form-group-custom">
                                        <label class="form-label-custom" for="except_from_hubs">Except from Hubs</label>
                                        <select id="except_from_hubs" name="except_from_hubs" class="form-control-custom vessel-select2">
                                            <option></option>
                                            <option value="Yes" {{ $vessel->except_from_hubs == 'Yes' ? 'selected' : '' }}>Yes</option>
                                            <option value="No" {{ $vessel->except_from_hubs == 'No' ? 'selected' : '' }}>No</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="remarks">Remarks</label>
                                    <textarea id="remarks" name="remarks" class="form-textarea-custom" rows="3">{{ $vessel->remarks }}</textarea>
                                </div>
                            </div>

                            <div class="form-pillar">
                                <div class="form-section-header">Responsible managers</div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="manager">Manager (from customer)</label>
                                    <select id="manager" name="manager" class="form-control-custom vessel-select2">
                                        <option value=""></option>
                                        @foreach ($customerContacts as $contact)
                                            <option value="{{ $contact->name }}" {{ ($vessel->manager ?? '') == $contact->name ? 'selected' : '' }}>{{ $contact->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="account-manager-select">Account manager</label>
                                    <select id="account-manager-select" name="account_manager" class="form-control-custom select2-account-manager">
                                        <option value=""></option>
                                        @php
                                            $selectedAccountManager = old('account_manager', $vessel->account_manager ?? '');
                                        @endphp
                                        @if ($selectedAccountManager)
                                            <option value="{{ $selectedAccountManager }}" selected>{{ $selectedAccountManager }}</option>
                                        @endif
                                    </select>
                                </div>

                                <div class="form-section-header" style="margin-top: 4px;">Receivers of stocklists, pre-alert and notifications</div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="receivers_stocklists">Receivers of stocklists and pre-alert</label>
                                    <input type="text" id="receivers_stocklists" name="receivers_stocklists" class="form-control-custom" value="{{ $vessel->receivers_stocklists }}">
                                </div>

                                <div id="contact-cards-container">
                                    @if ($vessel->contact_id)
                                        <div class="vessel-contact-card" id="contact-card-1">
                                            <div class="vessel-contact-card-header">
                                                <label>Contact</label>
                                                <div class="contact-select-wrap">
                                                    <select name="contacts[1][contact_id]" class="form-control-custom">
                                                        <option value=""></option>
                                                        @foreach ($customerContacts as $contact)
                                                            <option value="{{ $contact->id }}" {{ ($vessel->contact_id ?? '') == $contact->id ? 'selected' : '' }}>{{ $contact->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <button type="button" class="btn-remove-vessel-contact" data-id="1" aria-label="Remove contact">&times;</button>
                                            </div>
                                            <div class="vessel-contact-checkboxes">
                                                <label><input type="checkbox" name="contacts[1][stocklists]" value="1" {{ $vessel->contact_stocklists ? 'checked' : '' }}> Stocklists</label>
                                                <label><input type="checkbox" name="contacts[1][pre_alerts]" value="1" {{ $vessel->contact_pre_alerts ? 'checked' : '' }}> Pre-alerts</label>
                                                <label><input type="checkbox" name="contacts[1][stock_notifications]" value="1" {{ $vessel->contact_stock_notifications ? 'checked' : '' }}> Stock notifications</label>
                                                <label><input type="checkbox" name="contacts[1][free_storage_notifications]" value="1" {{ $vessel->contact_free_storage_notifications ? 'checked' : '' }}> Free storage notifications</label>
                                                <label><input type="checkbox" name="contacts[1][offers]" value="1" {{ $vessel->contact_offers ? 'checked' : '' }}> Offers</label>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <a class="btn-add-vessel-contact" id="btn-add-vessel-contact">Add contact</a>
                            </div>

                            <div class="form-pillar">
                                <div class="form-section-header">Invoice details</div>

                                <label class="vessel-checkbox-item" style="margin-bottom: 4px;">
                                    <input type="checkbox" name="invoice_vessel_separately" value="1" {{ $vessel->invoice_vessel_separately ? 'checked' : '' }}>
                                    Invoice vessel separately
                                </label>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="title_invoice_recipient">Title for invoice recipient</label>
                                    <input type="text" id="title_invoice_recipient" name="title_invoice_recipient" class="form-control-custom" value="{{ $vessel->title_invoice_recipient }}">
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="yearly_customer_reference">Yearly customer reference</label>
                                    <input type="text" id="yearly_customer_reference" name="yearly_customer_reference" class="form-control-custom" value="{{ $vessel->yearly_customer_reference }}">
                                </div>
                            </div>

                            <div class="form-pillar">
                                <div class="form-section-header">Home ports</div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="home_consolidation_port">Home consolidation port</label>
                                    <input type="text" id="home_consolidation_port" name="home_consolidation_port" class="form-control-custom" value="{{ $vessel->home_consolidation_port }}">
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="home_delivery_port">Home delivery port</label>
                                    <input type="text" id="home_delivery_port" name="home_delivery_port" class="form-control-custom" value="{{ $vessel->home_delivery_port }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <div id="vessel-location" class="tab-content-custom">
                    <div class="vessel-tab-placeholder">
                        Vessel location content goes here...
                    </div>
                </div>

                <div id="change-log" class="tab-content-custom">
                    <div class="vessel-tab-placeholder">
                        View all administration change logs from the Administration menu.
                    </div>
                </div>
            </div>
        </div>

        <div class="vessel-edit-footer" id="vessel-edit-footer">
            <button type="submit" class="btn-save-custom" id="btn-save" form="vesselForm">Save vessel</button>
            <a href="{{ route('customers.edit', $vessel->customer_id) }}#vessels" class="btn-cancel-custom">Cancel</a>
            <div class="audit-info">
                @include('partials.audit-info', ['record' => $vessel])
            </div>
        </div>
    </div>

    @include('layouts.partials.pcoded-shell-end')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function () {
            var contactIndex = {{ $vessel->contact_id ? 1 : 0 }};

            function activateVesselTab(tabId) {
                if (!tabId || !$('#' + tabId).length || !$('.tab-item[data-tab="' + tabId + '"]').length) {
                    return false;
                }

                $('.tab-item').removeClass('active');
                $('.tab-item[data-tab="' + tabId + '"]').addClass('active');
                $('.tab-content-custom').removeClass('active');
                $('#' + tabId).addClass('active');
                $('#active_tab').val(tabId);
                $('#vessel-edit-footer').toggle(tabId === 'vessel-details');
                return true;
            }

            $('.tab-item').on('click', function (e) {
                e.preventDefault();
                var tabId = $(this).data('tab');
                activateVesselTab(tabId);
                if (history.replaceState) {
                    history.replaceState(null, '', '#' + tabId);
                } else {
                    window.location.hash = tabId;
                }
            });

            var hashTab = window.location.hash.replace(/^#/, '');
            if (hashTab) {
                activateVesselTab(hashTab);
            } else {
                activateVesselTab($('#active_tab').val() || 'vessel-details');
            }

            function initVesselSelect2($context) {
                ($context || $(document)).find('.vessel-select2').each(function () {
                    var $el = $(this);
                    if ($el.hasClass('select2-hidden-accessible')) {
                        return;
                    }
                    $el.select2({
                        placeholder: 'Click here',
                        allowClear: true,
                        width: '100%'
                    });
                });
            }

            function initContactCardSelect2($context) {
                ($context || $(document)).find('.vessel-contact-card select').each(function () {
                    var $el = $(this);
                    if ($el.hasClass('select2-hidden-accessible')) {
                        return;
                    }
                    $el.select2({
                        placeholder: 'Click here',
                        allowClear: true,
                        width: '100%'
                    });
                });
            }

            initVesselSelect2();
            initContactCardSelect2();

            if (contactIndex > 0) {
                $('#btn-add-vessel-contact').hide();
            }

            function formatAccountManager(item) {
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

            function formatAccountManagerSelection(item) {
                return item.text || item.id;
            }

            $('#account-manager-select').select2({
                placeholder: 'Select account manager',
                allowClear: true,
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
                        data.forEach(function (group) {
                            if (!group.children) {
                                return;
                            }

                            group.children.forEach(function (item) {
                                item.id = item.text;
                            });
                        });

                        return { results: data };
                    }
                },
                templateResult: formatAccountManager,
                templateSelection: formatAccountManagerSelection
            });

            $(document).on('click', '#btn-add-vessel-contact', function (e) {
                e.preventDefault();

                if ($('.vessel-contact-card').length >= 1) {
                    alert('Only one contact card can be added.');
                    return;
                }

                contactIndex = 1;

                var contactCardHtml =
                    '<div class="vessel-contact-card" id="contact-card-' + contactIndex + '">' +
                        '<div class="vessel-contact-card-header">' +
                            '<label>Contact</label>' +
                            '<div class="contact-select-wrap">' +
                                '<select name="contacts[' + contactIndex + '][contact_id]" class="form-control-custom">' +
                                    '<option value=""></option>' +
                                    @foreach ($customerContacts as $contact)
                                        '<option value="{{ $contact->id }}">{{ $contact->name }}</option>' +
                                    @endforeach
                                '</select>' +
                            '</div>' +
                            '<button type="button" class="btn-remove-vessel-contact" data-id="' + contactIndex + '" aria-label="Remove contact">&times;</button>' +
                        '</div>' +
                        '<div class="vessel-contact-checkboxes">' +
                            '<label><input type="checkbox" name="contacts[' + contactIndex + '][stocklists]" value="1" checked> Stocklists</label>' +
                            '<label><input type="checkbox" name="contacts[' + contactIndex + '][pre_alerts]" value="1" checked> Pre-alerts</label>' +
                            '<label><input type="checkbox" name="contacts[' + contactIndex + '][stock_notifications]" value="1"> Stock notifications</label>' +
                            '<label><input type="checkbox" name="contacts[' + contactIndex + '][free_storage_notifications]" value="1"> Free storage notifications</label>' +
                            '<label><input type="checkbox" name="contacts[' + contactIndex + '][offers]" value="1"> Offers</label>' +
                        '</div>' +
                    '</div>';

                $('#contact-cards-container').append(contactCardHtml);
                $(this).hide();
                initContactCardSelect2($('#contact-card-' + contactIndex));
            });

            $(document).on('click', '.btn-remove-vessel-contact', function () {
                var cardId = $(this).data('id');
                $('#contact-card-' + cardId).remove();
                $('#btn-add-vessel-contact').show();
            });

            $('#vesselForm').validate({
                rules: {
                    vessel: 'required',
                    vessel_name_alias: 'required',
                    vessel_imo: 'required',
                    vessel_type_alias: 'required',
                    account_manager: 'required'
                },
                messages: {
                    vessel: 'Please enter vessel name',
                    vessel_name_alias: 'Please enter vessel name alias',
                    vessel_imo: 'Please enter vessel IMO',
                    vessel_type_alias: 'Please select vessel type alias',
                    account_manager: 'Please enter account manager'
                },
                errorPlacement: function (error, element) {
                    if (element.hasClass('select2-account-manager') || element.hasClass('vessel-select2')) {
                        error.insertAfter(element.next('.select2-container'));
                    } else {
                        error.insertAfter(element);
                    }
                },
                highlight: function (element) {
                    $(element).addClass('error');
                    if ($(element).hasClass('vessel-select2') || $(element).hasClass('select2-account-manager')) {
                        $(element).next('.select2-container').find('.select2-selection').css('border-color', '#dc2626');
                    }
                },
                unhighlight: function (element) {
                    $(element).removeClass('error');
                    if ($(element).hasClass('vessel-select2') || $(element).hasClass('select2-account-manager')) {
                        $(element).next('.select2-container').find('.select2-selection').css('border-color', '#d6e3ee');
                    }
                }
            });

            $('select.vessel-select2, select.select2-account-manager').on('change', function () {
                $(this).valid();
            });
        });
    </script>

@include('partials.unsaved-changes-guard', [
    'formSelector' => '#vesselForm',
    'saveButtonSelector' => '#btn-save',
    'legacySaveLabelSwap' => true,
    'fallbackUrl' => route('customers.edit', $vessel->customer_id) . '#vessels',
])
@endsection
