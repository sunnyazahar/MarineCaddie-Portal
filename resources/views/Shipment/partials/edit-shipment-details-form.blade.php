@php
    $workflowEditMode = ($createPreAlertMode ?? false) || ($transitMode ?? false);
    $stockCount = $shipment->crrs->count();
    $totalPackages = $shipment->crrs->sum(fn ($crr) => $crr->packages->count());
    $totalWeight = $shipment->crrs->sum(fn ($crr) => $crr->packages->sum('weight'));
    $totalCbm = $shipment->crrs->sum(fn ($crr) => $crr->packages->sum('cbm'));
    $totalValue = $shipment->crrs->sum('customs_value');
    $serviceOptions = ['Courier', 'Airfreight', 'Sea freight', 'Truck', 'Release', 'Hand Carry', 'On-board delivery'];
    $additionalServiceOptions = [
        'Console',
        'Economy',
        'Express',
        'Weekly console',
        'Normal',
        'Barge station',
        'Truck',
        'Via ship chandler',
        'On arrival',
        'Major storing',
        'Full container load (FCL)',
        'Less container load (LCL)',
    ];
@endphp

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert" style="font-size: 12px;">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert" style="font-size: 12px;">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
@endif

<div class="form-grid-3 cs-pillars">
    <div class="form-col">
        <div class="cs-pillar{{ $workflowEditMode ? ' cs-pillar--locked' : '' }}">
        <div class="cs-pillar__title">Departure</div>
        <div class="form-group-custom">
            <label>Departure</label>
            <select id="departure-select" name="departure" class="form-control-sm-custom select2-departure">
                @if ($shipment->departure)
                    <option value="{{ $shipment->departure }}" selected>{{ $departureDisplay }}</option>
                @endif
            </select>
        </div>
        <x-forms.port-select
            name="departure_port_code"
            id="departure-port-code"
            label="Port code"
            :value="$shipment->departure_port_code"
            class="form-control-sm-custom"
        />
        <div class="row">
            <div class="col-6">
                <div class="form-group-custom">
                    <label>Service <span class="text-danger">*</span></label>
                    <select name="service" class="form-control-sm-custom select2" required>
                        <option></option>
                        @foreach ($serviceOptions as $serviceOption)
                            <option value="{{ $serviceOption }}" {{ old('service', $shipment->service) === $serviceOption ? 'selected' : '' }}>{{ $serviceOption }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-6">
                <div class="form-group-custom">
                    <label>Additional service</label>
                    <select name="additional_service" class="form-control-sm-custom select2">
                        <option></option>
                        @foreach ($additionalServiceOptions as $additionalServiceOption)
                            <option value="{{ $additionalServiceOption }}" {{ old('additional_service', $shipment->additional_service) === $additionalServiceOption ? 'selected' : '' }}>{{ $additionalServiceOption }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <div class="form-group-custom">
                    <label>Preferred shipment date</label>
                    <div class="input-with-icon">
                        <input type="text" name="preferred_shipment_date" class="form-control-sm-custom datepicker" placeholder="DD.MM.YYYY" value="{{ old('preferred_shipment_date', $shipment->preferred_shipment_date?->format('d.m.Y')) }}">
                        <i class="ti-calendar"></i>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="form-group-custom">
                    <label>Deadline arrival</label>
                    <div class="input-with-icon">
                        <input type="text" name="deadline_arrival" class="form-control-sm-custom datepicker" placeholder="DD.MM.YYYY" value="{{ old('deadline_arrival', $shipment->deadline_arrival?->format('d.m.Y')) }}">
                        <i class="ti-calendar"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <div class="form-group-custom">
                    <label>Vessel ETA</label>
                    <div class="input-with-icon">
                        <input type="text" name="vessel_eta" class="form-control-sm-custom datepicker" placeholder="DD.MM.YYYY" value="{{ old('vessel_eta', $shipment->vessel_eta?->format('d.m.Y')) }}">
                        <i class="ti-calendar"></i>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="form-group-custom">
                    <label>Vessel ETD</label>
                    <div class="input-with-icon">
                        <input type="text" name="vessel_etd" class="form-control-sm-custom datepicker" placeholder="DD.MM.YYYY" value="{{ old('vessel_etd', $shipment->vessel_etd?->format('d.m.Y')) }}">
                        <i class="ti-calendar"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <div class="form-group-custom">
                    <label>Pre-alert reminder</label>
                    <div class="input-with-icon">
                        <input type="text" name="pre_alert_reminder" class="form-control-sm-custom datepicker" placeholder="DD.MM.YYYY" value="{{ old('pre_alert_reminder', $shipment->pre_alert_reminder?->format('d.m.Y')) }}">
                        <i class="ti-calendar"></i>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="form-group-custom">
                    <label>Customer reference</label>
                    <input type="text" name="customer_reference" class="form-control-sm-custom" value="{{ old('customer_reference', $shipment->customer_reference) }}">
                </div>
            </div>
        </div>
        <div class="checkbox-fade fade-in-primary mt-2">
            <label>
                <input type="checkbox" name="not_applicable_for_consolidation" value="1" {{ old('not_applicable_for_consolidation', $shipment->not_applicable_for_consolidation) ? 'checked' : '' }}>
                <span class="cr"><i class="cr-icon ti-check txt-primary"></i></span>
                <span class="text-inverse" style="font-size: 11px;">Not applicable for consolidation</span>
            </label>
        </div>
        </div>{{-- .cs-pillar --}}
    </div>

    <div class="form-col">
        <div class="cs-pillar">
        <div class="cs-pillar__title">Consignee</div>
        <div class="form-group-custom">
            <label>Consignee</label>
            <input type="hidden" id="consignee-party-code" value="{{ $consigneeCode ?? '' }}">
            <select id="consignee-select" name="consignee" class="form-control-sm-custom select2-consignee">
                @if ($shipment->consignee)
                    <option value="{{ $shipment->consignee }}" selected>{{ $consigneeDisplay }}</option>
                @endif
            </select>
        </div>
        <div class="form-group-custom">
            <label>Consignee address</label>
            <textarea name="consignee_address" id="consignee-address" class="form-control" style="height: 120px;">{{ old('consignee_address', $shipment->consignee_address) }}</textarea>
        </div>
        <div class="row">
            <div class="col-6">
                <div class="form-group-custom">
                    <label>City</label>
                    <input type="text" id="consignee-city" name="consignee_city" class="form-control-sm-custom" value="{{ old('consignee_city', $shipment->consignee_city) }}">
                </div>
            </div>
            <div class="col-3">
                <div class="form-group-custom">
                    <label>District</label>
                    <input type="text" id="consignee-district" name="consignee_district" class="form-control-sm-custom" value="{{ old('consignee_district', $shipment->consignee_district) }}">
                </div>
            </div>
            <div class="col-3 pl-0 pr-0">
                <div class="form-group-custom">
                    <label>Zip code</label>
                    <input type="text" id="consignee-zip" name="consignee_zip" class="form-control-sm-custom" value="{{ old('consignee_zip', $shipment->consignee_zip) }}">
                </div>
            </div>
        </div>
        <x-forms.country-select
            name="consignee_country"
            id="consignee-country"
            label="Consignee country"
            :countries="$countries"
            valueKey="name"
            :value="$shipment->consignee_country"
            wrapperClass="form-group-custom"
            class="form-control-sm-custom"
            placeholder="Select country"
            :allowClear="true"
            dropdownParent="body"
        />
        <div class="form-group-custom">
            <label>Contact person <span class="text-danger">*</span></label>
            <input type="text" id="consignee-att" name="consignee_att" class="form-control-sm-custom" value="{{ old('consignee_att', $shipment->consignee_att) }}" required>
        </div>
        <x-forms.port-select
            name="consignee_port_code"
            id="consignee-port-code"
            label="Port code"
            :value="$shipment->consignee_port_code"
            class="form-control-sm-custom"
            :required="true"
        />
        <div class="form-group-custom">
            <label>Location</label>
            <input type="text" id="location" name="location" class="form-control-sm-custom" value="{{ old('location', $shipment->location) }}">
        </div>
        <div class="form-group-custom">
            <label>Consignee email</label>
            <input type="text" id="consignee-email" name="consignee_email" class="form-control-sm-custom" value="{{ old('consignee_email', $shipment->consignee_email) }}">
        </div>
        </div>{{-- .cs-pillar --}}
    </div>

    <div class="form-col">
        <div class="cs-pillar">
        <div class="cs-pillar__title">Account &amp; comments</div>
        <div class="form-group-custom{{ $workflowEditMode ? ' cs-field--locked' : '' }}">
            <label>Special considerations for destination</label>
            <textarea name="special_considerations_destination" class="form-control" style="height: 120px;">{{ old('special_considerations_destination', $shipment->special_considerations_destination) }}</textarea>
            <div class="checkbox-fade fade-in-primary mt-1">
                <label>
                    <input type="checkbox" name="skip_instruction_dest" value="1" {{ old('skip_instruction_dest', $shipment->skip_instruction_dest) ? 'checked' : '' }}>
                    <span class="cr"><i class="cr-icon ti-check txt-primary"></i></span>
                    <span class="text-inverse" style="font-size: 10px;">Don't show on shipping instruction</span>
                </label>
            </div>
        </div>
        <div class="form-group-custom{{ $workflowEditMode ? ' cs-field--locked' : '' }}">
            <label>Comments to departure hub</label>
            <textarea name="comments_departure_hub" id="comments_departure_hub" class="form-control" style="height: 120px;">{{ old('comments_departure_hub', $shipment->comments_departure_hub) }}</textarea>
            <div class="checkbox-fade fade-in-primary mt-1">
                <label>
                    <input type="checkbox" name="skip_instruction_hub" value="1" {{ old('skip_instruction_hub', $shipment->skip_instruction_hub) ? 'checked' : '' }}>
                    <span class="cr"><i class="cr-icon ti-check txt-primary"></i></span>
                    <span class="text-inverse" style="font-size: 10px;">Don't show on shipping instruction</span>
                </label>
            </div>
        </div>
        <div class="form-group-custom">
            <label>Comments to consignee</label>
            <textarea name="comments_consignee" class="form-control" style="height: 120px;">{{ old('comments_consignee', $shipment->comments_consignee) }}</textarea>
            <div class="checkbox-fade fade-in-primary mt-1">
                <label>
                    <input type="checkbox" name="skip_prealert" value="1" {{ old('skip_prealert', $shipment->skip_prealert) ? 'checked' : '' }}>
                    <span class="cr"><i class="cr-icon ti-check txt-primary"></i></span>
                    <span class="text-inverse" style="font-size: 10px;">Don't show on pre-alert</span>
                </label>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <div class="checkbox-fade fade-in-primary">
                    <label>
                        <input type="checkbox" name="project_logistics" value="1" {{ old('project_logistics', $shipment->project_logistics) ? 'checked' : '' }}>
                        <span class="cr"><i class="cr-icon ti-check txt-primary"></i></span>
                        <span class="text-inverse" style="font-size: 10px;">Project logistics</span>
                    </label>
                </div>
            </div>
            <div class="col-6">
                <div class="checkbox-fade fade-in-primary">
                    <label>
                        <input type="checkbox" name="port_agency" value="1" {{ old('port_agency', $shipment->port_agency) ? 'checked' : '' }}>
                        <span class="cr"><i class="cr-icon ti-check txt-primary"></i></span>
                        <span class="text-inverse" style="font-size: 10px;">Port agency</span>
                    </label>
                </div>
            </div>
        </div>
        </div>{{-- .cs-pillar --}}
    </div>
</div>

<div class="stock-items-wrapper cs-section-shell">
    @php
        $lockServiceDetailsForInProcess = ($shipment->status === 'In process') && ! $workflowEditMode;
    @endphp
    <div class="stock-tabs">
        <div class="stock-tab active" data-panel="stock-panel-items">
            <span class="stock-tab__label">Stock items</span>
            <span class="stock-tab__count" data-stock-tab-count>{{ $stockCount }}</span>
        </div>
        <div class="stock-tab{{ $lockServiceDetailsForInProcess ? ' stock-tab--disabled' : '' }}" data-panel="stock-panel-service" @if($lockServiceDetailsForInProcess) title="Service details not available. Please create pre alert." @endif>
            <span class="stock-tab__label">Service details</span>
        </div>
        <div class="stock-tab" data-panel="stock-panel-irregularities">
            <span class="stock-tab__label">Irregularities</span>
            <span class="stock-tab__count" data-irregularity-tab-count>{{ $shipment->irregularities->count() }}</span>
        </div>
    </div>

    <div id="stock-panel-items" class="stock-panel active">
        <div class="stock-table-container">
            <table class="stock-table" id="stock-items-table">
                <thead>
                    <tr>
                        <th>Hub</th>
                        <th>Vessel</th>
                        <th>PO no</th>
                        <th>Supplier</th>
                        <th>Stock no</th>
                        <th>Pcs</th>
                        <th>Weight</th>
                        <th>CBM</th>
                        <th>Value</th>
                        <th>Status</th>
                        <th style="text-align: center;">Remove</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($shipment->crrs as $crr)
                        @php
                            $poNumbers = is_array($crr->po_numbers) ? implode(', ', $crr->po_numbers) : ($crr->po_numbers ?? '—');
                            $statusLabel = \App\Models\Crr::getStatusLabels()[$crr->status] ?? 'Pending';
                            $hubKey = strtolower(trim((string) ($crr->hub_code ?: $crr->hub_agent)));
                        @endphp
                        <tr class="selected-stock-row" data-crr-id="{{ $crr->id }}" data-hub-key="{{ $hubKey }}">
                            <td>{{ $crr->hub_code ?: ($crr->hub_agent ?: '—') }}</td>
                            <td>{{ $crr->vessel_name ?? '—' }}</td>
                            <td style="max-width: 150px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;display: block;">{{ $poNumbers ?: '—' }}</td>
                            <td>{{ $crr->supplier ?? '—' }}</td>
                            <td>
                                @if($crr->getKey())
                                    <a href="{{ route('stocks.edit', ['id' => $crr->getKey()]) }}" class="text-primary">
                                        {{ $crr->stock_number }}
                                    </a>
                                @else
                                    <span class="text-primary">{{ $crr->stock_number }}</span>
                                @endif
                            </td>
                            <td>{{ $crr->packages->count() }}</td>
                            <td>{{ number_format($crr->packages->sum('weight'), 2) }}</td>
                            <td>{{ \App\Support\PackageVolumeMetrics::formatCbm($crr->packages->sum('cbm')) }}</td>
                            <td>{{ $crr->customs_value ? number_format($crr->customs_value, 2) . ' ' . ($crr->currency ?? 'USD') : '—' }}</td>
                            <td><span class="stock-status-badge {{ \App\Models\Crr::statusBadgeClass($crr->status) }}">{{ $statusLabel }}</span></td>
                            <td style="text-align: center;"><button type="button" class="btn btn-link btn-sm p-0 remove-stock-item"><i class="ti-trash text-muted"></i></button></td>
                        </tr>
                    @empty
                        <tr id="empty-row">
                            <td colspan="11" class="text-center py-3 text-muted">No stock items added yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="stock-totals">
            <div class="stock-total-chip">
                <span class="stock-total-chip__label">Packages</span>
                <span class="stock-total-chip__value">{{ $totalPackages }} pcs</span>
            </div>
            <div class="stock-total-chip">
                <span class="stock-total-chip__label">Weight</span>
                <span class="stock-total-chip__value">{{ number_format($totalWeight, 2) }} kg</span>
            </div>
            <div class="stock-total-chip">
                <span class="stock-total-chip__label">CBM</span>
                <span class="stock-total-chip__value">{{ \App\Support\PackageVolumeMetrics::formatCbm($totalCbm) }}</span>
            </div>
            <div class="stock-total-chip">
                <span class="stock-total-chip__label">Value</span>
                <span class="stock-total-chip__value">{{ number_format($totalValue, 2) }}</span>
            </div>
            <div class="ml-auto">
                <button type="button" id="add-stock-items-btn" class="btn btn-premium btn-outline-custom ml-2" @if(in_array($shipment->status, ['Completed', 'Cancelled'], true)) disabled @endif>Add stock items</button>
            </div>
        </div>
        @include('Shipment.partials.stock-repacked-fields', [
            'itemsName' => 'stock_repacked_items',
            'weightName' => 'stock_repacked_weight',
            'itemsId' => 'stock_repacked_items',
            'weightId' => 'stock_repacked_weight',
            'itemsValue' => $shipment->stock_repacked_items,
            'weightValue' => $shipment->stock_repacked_weight !== null
                ? number_format((float) $shipment->stock_repacked_weight, 2, '.', '')
                : null,
            'disabled' => in_array($shipment->status, ['Completed', 'Cancelled'], true),
        ])
    </div>

    <div id="stock-panel-service" class="stock-panel{{ $lockServiceDetailsForInProcess ? ' stock-panel--locked' : '' }}">
        <div id="service-details-placeholder" class="text-center">Select a service type to enter service details.</div>
        <div id="service-details-airfreight" class="p-3" style="display: none;">
            <div id="airfreight-flights-container">
                @forelse ($shipment->flights as $flight)
                    @include('Shipment.partials.airfreight-flight-row', ['flight' => $flight, 'index' => $loop->index])
                @empty
                @endforelse
            </div>
            <div class="d-flex justify-content-end pt-2">
                <a href="#" id="add-airfreight-flight-btn" class="text-primary" style="font-size: 11px; font-weight: 600; text-decoration: none;">Add flight</a>
            </div>
        </div>
        <div id="service-details-sea-freight" class="p-3" style="display: none;">
            <div id="sea-freight-legs-container">
                @forelse ($shipment->seaLegs as $leg)
                    @include('Shipment.partials.sea-freight-leg-row', ['leg' => $leg, 'index' => $loop->index])
                @empty
                @endforelse
            </div>
            <div class="d-flex justify-content-end pt-2">
                <a href="#" id="add-sea-freight-leg-btn" class="text-primary" style="font-size: 11px; font-weight: 600; text-decoration: none;">Add leg</a>
            </div>
        </div>
        <div id="service-details-truck" class="p-3" style="display: none;">
            <div id="truck-legs-container">
                @forelse ($shipment->truckLegs as $leg)
                    @include('Shipment.partials.truck-leg-row', ['leg' => $leg, 'index' => $loop->index])
                @empty
                @endforelse
            </div>
            <div class="d-flex justify-content-end pt-2">
                <a href="#" id="add-truck-leg-btn" class="text-primary" style="font-size: 11px; font-weight: 600; text-decoration: none;">Add truck</a>
            </div>
        </div>
        <div id="service-details-courier" class="p-3" style="display: none;">
            <div id="courier-legs-container">
                @forelse ($shipment->courierLegs as $leg)
                    @include('Shipment.partials.courier-leg-row', ['leg' => $leg, 'index' => $loop->index])
                @empty
                @endforelse
            </div>
            <div class="d-flex justify-content-end pt-2">
                <a href="#" id="add-courier-leg-btn" class="text-primary" style="font-size: 11px; font-weight: 600; text-decoration: none;">Add courier</a>
            </div>
        </div>
        <div id="service-details-release" class="p-3" style="display: none;">
            <div id="release-legs-container">
                @forelse ($shipment->releaseLegs as $leg)
                    @include('Shipment.partials.release-leg-row', ['leg' => $leg, 'index' => $loop->index])
                @empty
                @endforelse
            </div>
            <div class="d-flex justify-content-end pt-2">
                <a href="#" id="add-release-leg-btn" class="text-primary" style="font-size: 11px; font-weight: 600; text-decoration: none;">Add release</a>
            </div>
        </div>
        <div id="service-details-hand-carry" class="p-3" style="display: none;">
            <div id="hand-carry-legs-container">
                @forelse ($shipment->handCarryLegs as $leg)
                    @include('Shipment.partials.hand-carry-leg-row', ['leg' => $leg, 'index' => $loop->index])
                @empty
                @endforelse
            </div>
            <div class="d-flex justify-content-end pt-2">
                <a href="#" id="add-hand-carry-leg-btn" class="text-primary" style="font-size: 11px; font-weight: 600; text-decoration: none;">Add hand carry</a>
            </div>
        </div>
        <div id="service-details-on-board" class="p-3" style="display: none;">
            <div id="on-board-legs-container">
                @forelse ($shipment->onBoardLegs as $leg)
                    @include('Shipment.partials.on-board-leg-row', ['leg' => $leg, 'index' => $loop->index])
                @empty
                @endforelse
            </div>
            <div class="d-flex justify-content-end pt-2">
                <a href="#" id="add-on-board-leg-btn" class="text-primary" style="font-size: 11px; font-weight: 600; text-decoration: none;">Add delivery</a>
            </div>
        </div>
        @include('Shipment.partials.stock-repacked-fields', [
            'itemsName' => 'repacked_items',
            'weightName' => 'repacked_weight',
            'itemsId' => 'repacked_items',
            'weightId' => 'repacked_weight',
            'itemsValue' => $shipment->repacked_items,
            'weightValue' => $shipment->repacked_weight !== null
                ? number_format((float) $shipment->repacked_weight, 2, '.', '')
                : null,
            'disabled' => in_array($shipment->status, ['Completed', 'Cancelled'], true) || $lockServiceDetailsForInProcess,
        ])
    </div>

    <div id="stock-panel-irregularities" class="stock-panel">
        <div class="p-3" id="irregularities-container">
            @forelse ($shipment->irregularities as $irregularity)
                @php $irIndex = $loop->index; @endphp
                <div class="irregularity-item" data-irregularity-index="{{ $irIndex }}">
                    <div class="row">
                        <div class="col-md-2 pr-1">
                            <div class="form-group-custom">
                                <label>Date</label>
                                <input type="text" name="irregularities[{{ $irIndex }}][irregularity_date]" class="form-control-sm-custom datepicker" placeholder="DD.MM.YYYY" value="{{ $irregularity->irregularity_date?->format('d.m.Y') }}">
                            </div>
                        </div>
                        <div class="col-md-2 px-1">
                            <div class="form-group-custom">
                                <label>Irregularity</label>
                                <select name="irregularities[{{ $irIndex }}][irregularity_type]" class="form-control-sm-custom select2">
                                    <option></option>
                                    @foreach ($irregularityTypeOptions as $option)
                                        <option {{ $irregularity->irregularity_type === $option ? 'selected' : '' }}>{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 px-1">
                            <div class="form-group-custom">
                                <label>Party responsible</label>
                                <select name="irregularities[{{ $irIndex }}][party_responsible]" class="form-control-sm-custom select2">
                                    <option></option>
                                    @foreach ($partyResponsibleOptions as $option)
                                        <option {{ $irregularity->party_responsible === $option ? 'selected' : '' }}>{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 px-1">
                            <div class="form-group-custom">
                                <label>Consequence</label>
                                <select name="irregularities[{{ $irIndex }}][consequence]" class="form-control-sm-custom select2">
                                    <option></option>
                                    @foreach ($consequenceOptions as $option)
                                        <option {{ $irregularity->consequence === $option ? 'selected' : '' }}>{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 px-1">
                            <div class="form-group-custom">
                                <label>Extra cost for MT (USD)</label>
                                <input type="text" name="irregularities[{{ $irIndex }}][extra_cost_mt_usd]" class="form-control-sm-custom" value="{{ $irregularity->extra_cost_mt_usd }}">
                            </div>
                        </div>
                        <div class="col-md-2 pl-1 d-flex align-items-end">
                            <div class="form-group-custom flex-grow-1 mr-2">
                                <label>Status</label>
                                <select name="irregularities[{{ $irIndex }}][status]" class="form-control-sm-custom select2">
                                    <option></option>
                                    @foreach ($statusOptions as $option)
                                        <option {{ $irregularity->status === $option ? 'selected' : '' }}>{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="button" class="btn btn-link text-muted p-0 mb-2 remove-irregularity"><i class="ti-trash"></i></button>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-4 pr-1">
                            <div class="form-group-custom">
                                <label>Cause of irregularity</label>
                                <textarea name="irregularities[{{ $irIndex }}][cause_of_irregularity]" class="form-control" style="height: 80px;">{{ $irregularity->cause_of_irregularity }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-4 px-1">
                            <div class="form-group-custom">
                                <label>Action taken</label>
                                <textarea name="irregularities[{{ $irIndex }}][action_taken]" class="form-control" style="height: 80px;">{{ $irregularity->action_taken }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-4 pl-1">
                            <div class="form-group-custom">
                                <label>Customer response</label>
                                <textarea name="irregularities[{{ $irIndex }}][customer_response]" class="form-control" style="height: 80px;">{{ $irregularity->customer_response }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-4 pr-1">
                            <div class="form-group-custom">
                                <label>Hub/agent comments</label>
                                <textarea name="irregularities[{{ $irIndex }}][hub_agent_comments]" class="form-control" style="height: 80px;">{{ $irregularity->hub_agent_comments }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-4 px-1">
                            <div class="form-group-custom">
                                <label>Handled by</label>
                                <input type="text" name="irregularities[{{ $irIndex }}][handled_by]" class="form-control-sm-custom" value="{{ $irregularity->handled_by }}">
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="irregularity-item" data-irregularity-index="0">
                    <div class="row">
                        <div class="col-md-2 pr-1">
                            <div class="form-group-custom">
                                <label>Date</label>
                                <input type="text" name="irregularities[{{ 0 }}][irregularity_date]" class="form-control-sm-custom datepicker" placeholder="DD.MM.YYYY">
                            </div>
                        </div>
                        <div class="col-md-2 px-1">
                            <div class="form-group-custom">
                                <label>Irregularity</label>
                                <select name="irregularities[{{ 0 }}][irregularity_type]" class="form-control-sm-custom select2">
                                    <option></option>
                                    @foreach ($irregularityTypeOptions as $option)
                                        <option>{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 px-1">
                            <div class="form-group-custom">
                                <label>Party responsible</label>
                                <select name="irregularities[{{ 0 }}][party_responsible]" class="form-control-sm-custom select2">
                                    <option></option>
                                    @foreach ($partyResponsibleOptions as $option)
                                        <option>{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 px-1">
                            <div class="form-group-custom">
                                <label>Consequence</label>
                                <select name="irregularities[{{ 0 }}][consequence]" class="form-control-sm-custom select2">
                                    <option></option>
                                    @foreach ($consequenceOptions as $option)
                                        <option>{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 px-1">
                            <div class="form-group-custom">
                                <label>Extra cost for MT (USD)</label>
                                <input type="text" name="irregularities[{{ 0 }}][extra_cost_mt_usd]" class="form-control-sm-custom">
                            </div>
                        </div>
                        <div class="col-md-2 pl-1">
                            <div class="form-group-custom">
                                <label>Status</label>
                                <select name="irregularities[{{ 0 }}][status]" class="form-control-sm-custom select2">
                                    <option></option>
                                    @foreach ($statusOptions as $option)
                                        <option>{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
        <div class="p-3 d-flex justify-content-end">
            <button type="button" id="add-irregularity-btn" class="btn btn-premium btn-outline-custom">Add irregularity</button>
        </div>
    </div>
</div>
