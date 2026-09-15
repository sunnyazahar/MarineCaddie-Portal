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

        <div class="edit-vessel-card">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show edit-vessel-alert" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            @endif
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show edit-vessel-alert" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            @endif

            <form id="vesselForm" method="POST" action="{{ route('customers.vessels.update', $vessel->id) }}">
                @csrf
                @method('PUT')

                <div id="vessel-details" class="vessel-form-body">
                    <div class="form-pillar-container vessel-details-grid">
                        <div class="form-pillar vessel-pillar-card">
                            <div class="vessel-pillar-head">
                                <span class="vessel-pillar-head-icon" aria-hidden="true"><i class="ti-anchor"></i></span>
                                <div>
                                    <div class="vessel-pillar-head-title">Identity</div>
                                    <p class="vessel-pillar-head-sub">Name, IMO, type, and vessel codes.</p>
                                </div>
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom" for="vessel">Vessel <span class="text-danger">*</span></label>
                                <input type="text" id="vessel" name="vessel" class="form-control-custom"
                                    value="{{ old('vessel', $vessel->vessel) }}" required>
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom" for="vessel_name_alias">Vessel name alias <span class="text-danger">*</span></label>
                                <input type="text" id="vessel_name_alias" name="vessel_name_alias" class="form-control-custom"
                                    value="{{ old('vessel_name_alias', $vessel->vessel_name_alias) }}" required>
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom" for="vessel_imo">Vessel IMO <span class="text-danger">*</span></label>
                                <input type="text" id="vessel_imo" name="vessel_imo" class="form-control-custom"
                                    value="{{ old('vessel_imo', $vessel->vessel_imo) }}" required>
                            </div>

                            <div class="input-row vessel-details-input-row">
                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="customer_vessel_code">Customer vessel code</label>
                                    <input type="text" id="customer_vessel_code" name="customer_vessel_code" class="form-control-custom"
                                        value="{{ old('customer_vessel_code', $vessel->customer_vessel_code) }}">
                                </div>
                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="vessel_type_alias">Vessel type alias <span class="text-danger">*</span></label>
                                    <select id="vessel_type_alias" name="vessel_type_alias" class="form-control-custom vessel-select2" required>
                                        <option></option>
                                        @php $selectedVesselType = old('vessel_type_alias', $vessel->vessel_type_alias); @endphp
                                        @if ($selectedVesselType && ! in_array($selectedVesselType, ['MV', 'LPG'], true))
                                            <option value="{{ $selectedVesselType }}" selected>{{ $selectedVesselType }}</option>
                                        @endif
                                        <option value="MV" {{ $selectedVesselType == 'MV' ? 'selected' : '' }}>MV</option>
                                        <option value="LPG" {{ $selectedVesselType == 'LPG' ? 'selected' : '' }}>LPG</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom" for="po_example">PO example</label>
                                <input type="text" id="po_example" name="po_example" class="form-control-custom"
                                    value="{{ old('po_example', $vessel->po_example) }}">
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom" for="remarks">Remarks</label>
                                <textarea id="remarks" name="remarks" class="form-textarea-custom" rows="3">{{ old('remarks', $vessel->remarks) }}</textarea>
                            </div>
                        </div>

                        <div class="form-pillar vessel-pillar-card">
                            <div class="vessel-pillar-head">
                                <span class="vessel-pillar-head-icon is-ops" aria-hidden="true"><i class="ti-settings"></i></span>
                                <div>
                                    <div class="vessel-pillar-head-title">Operations &amp; ownership</div>
                                    <p class="vessel-pillar-head-sub">Status flags, shipment rules, and managers.</p>
                                </div>
                            </div>

                            <div class="vessel-soft-panel">
                                <div class="vessel-soft-panel-title">Status flags</div>
                                <div class="vessel-flag-grid">
                                    <label class="vessel-flag-chip">
                                        <input type="checkbox" name="not_in_transit" value="1" {{ old('not_in_transit', $vessel->not_in_transit) ? 'checked' : '' }}>
                                        <span>Not in transit</span>
                                    </label>
                                    <label class="vessel-flag-chip">
                                        <input type="checkbox" name="inactive_vessel" value="1" {{ old('inactive_vessel', $vessel->inactive_vessel) ? 'checked' : '' }}>
                                        <span>Inactive</span>
                                    </label>
                                    <label class="vessel-flag-chip">
                                        <input type="checkbox" name="sanction_blocked" value="1" {{ old('sanction_blocked', $vessel->sanction_blocked) ? 'checked' : '' }}>
                                        <span>Sanction blocked</span>
                                    </label>
                                    <label class="vessel-flag-chip">
                                        <input type="checkbox" name="financially_blocked" value="1" {{ old('financially_blocked', $vessel->financially_blocked) ? 'checked' : '' }}>
                                        <span>Financially blocked</span>
                                    </label>
                                    <label class="vessel-flag-chip">
                                        <input type="checkbox" name="pre_payment_only" value="1" {{ old('pre_payment_only', $vessel->pre_payment_only) ? 'checked' : '' }}>
                                        <span>Pre-payment only</span>
                                    </label>
                                </div>
                            </div>

                            <div class="input-row vessel-details-input-row">
                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="internal_shipment">Internal shipment</label>
                                    <select id="internal_shipment" name="internal_shipment" class="form-control-custom vessel-select2">
                                        <option></option>
                                        <option value="Yes" {{ old('internal_shipment', $vessel->internal_shipment) == 'Yes' ? 'selected' : '' }}>Yes</option>
                                        <option value="No" {{ old('internal_shipment', $vessel->internal_shipment) == 'No' ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="except_from_hubs">Except from Hubs</label>
                                    <select id="except_from_hubs" name="except_from_hubs" class="form-control-custom vessel-select2">
                                        <option></option>
                                        <option value="Yes" {{ old('except_from_hubs', $vessel->except_from_hubs) == 'Yes' ? 'selected' : '' }}>Yes</option>
                                        <option value="No" {{ old('except_from_hubs', $vessel->except_from_hubs) == 'No' ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                            </div>

                            <div class="vessel-soft-panel">
                                <div class="vessel-soft-panel-title">Responsible managers</div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="manager">Manager (from customer)</label>
                                    <select id="manager" name="manager" class="form-control-custom vessel-select2">
                                        <option value=""></option>
                                        @foreach ($customerContacts as $contact)
                                            <option value="{{ $contact->name }}" {{ old('manager', $vessel->manager ?? '') == $contact->name ? 'selected' : '' }}>{{ $contact->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group-custom">
                                    <label class="form-label-custom" for="account-manager-select">Account manager <span class="text-danger">*</span></label>
                                    <select id="account-manager-select" name="account_manager" class="form-control-custom select2-account-manager" required>
                                        <option value=""></option>
                                        @php
                                            $selectedAccountManager = old('account_manager', $vessel->account_manager ?? '');
                                        @endphp
                                        @if ($selectedAccountManager)
                                            <option value="{{ $selectedAccountManager }}" selected>{{ $selectedAccountManager }}</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
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
            $('body').addClass('edit-vessel-page');

            function autoResizeVesselTextarea(textarea) {
                if (!textarea) {
                    return;
                }

                var computedStyle = window.getComputedStyle(textarea);
                var minHeight = parseFloat(computedStyle.minHeight) || 0;

                textarea.style.setProperty('height', 'auto', 'important');
                textarea.style.setProperty('overflow-y', 'hidden', 'important');
                textarea.style.setProperty('height', Math.max(textarea.scrollHeight, minHeight) + 'px', 'important');
            }

            function refreshAutoResizeVesselTextareas() {
                $('#vesselForm textarea.form-textarea-custom').each(function () {
                    autoResizeVesselTextarea(this);
                });
            }

            $(document).on('input.autoResizeVesselTextarea change.autoResizeVesselTextarea', '#vesselForm textarea.form-textarea-custom', function () {
                autoResizeVesselTextarea(this);
            });

            refreshAutoResizeVesselTextareas();

            $('.vessel-select2').each(function () {
                var $el = $(this);
                if ($el.hasClass('select2-hidden-accessible')) {
                    return;
                }
                $el.select2({
                    placeholder: 'Select an option',
                    allowClear: true,
                    width: '100%'
                });
            });

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
                    account_manager: 'Please select account manager'
                },
                errorElement: 'div',
                errorClass: 'error-message',
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
                        $(element).next('.select2-container').addClass('error');
                    }
                },
                unhighlight: function (element) {
                    $(element).removeClass('error');
                    if ($(element).hasClass('vessel-select2') || $(element).hasClass('select2-account-manager')) {
                        $(element).next('.select2-container').removeClass('error');
                    }
                }
            });

            $('select.vessel-select2, select.select2-account-manager').on('change', function () {
                $(this).valid();
            });

            $(document).on('select2:open', '.vessel-pillar-card select', function () {
                $('.vessel-pillar-card').css('z-index', '');
                $(this).closest('.vessel-pillar-card').css('z-index', 40);
            });

            $(document).on('select2:close', '.vessel-pillar-card select', function () {
                $(this).closest('.vessel-pillar-card').css('z-index', '');
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
