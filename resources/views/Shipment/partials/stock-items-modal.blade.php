    <!-- Stock Items Modal -->
    <div class="modal fade" id="stock-items-modal" tabindex="-1" role="dialog" aria-labelledby="stockItemsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document" style="max-width: 95%;">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="modal-title mb-1" id="stockItemsModalLabel" style="font-size: 14px; font-weight: 600;">Select Stock Items</h5>
                        <div id="stock-items-modal-error" style="display:none; font-size: 11px; color: #dc2626; font-weight: 600;"></div>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="padding: 10px;">
                    <!-- Filter Section -->
                    <div class="d-flex flex-wrap align-items-center" style="gap: 8px; margin-bottom: 10px;">
                        <div class="filter-group" style="width: 210px; min-width: 180px;">
                            <span class="filter-label">Customer</span>
                            <select id="modal-customer-filter" class="form-control filter-input select2 modal-select2">
                                <option value="" selected disabled hidden></option>
                                @php $modalCustomers = []; @endphp
                                @foreach($crrs as $crr)
                                    @php
                                        $customerName = $crr->customerVessel && $crr->customerVessel->customer ? $crr->customerVessel->customer->customer_name : '';
                                    @endphp
                                    @if($customerName && !in_array($customerName, $modalCustomers, true))
                                        @php $modalCustomers[] = $customerName; @endphp
                                        <option value="{{ $customerName }}">{{ $customerName }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-group" style="width: 180px; min-width: 160px;">
                            <span class="filter-label">Vessel</span>
                            <select id="modal-vessel-filter" class="form-control filter-input select2 modal-select2">
                                <option value="" selected disabled hidden></option>
                                @php $modalVessels = []; @endphp
                                @foreach($crrs as $crr)
                                    @php $vesselName = $crr->vessel_name ?? ''; @endphp
                                    @if($vesselName && !in_array($vesselName, $modalVessels, true))
                                        @php $modalVessels[] = $vesselName; @endphp
                                        <option value="{{ $vesselName }}">{{ $vesselName }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-group" style="width: 180px; min-width: 160px;">
                            <span class="filter-label">Hub/Agent</span>
                            <select id="modal-hub-filter" class="form-control filter-input select2 modal-select2">
                                <option value="" selected disabled hidden></option>
                                <optgroup label="Hubs">
                                    @foreach($hubs as $hub)
                                        <option value="{{ $hub->code }}">{{ $hub->code }}</option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="Agents">
                                    @foreach($agents as $agent)
                                        <option value="{{ $agent->code }}">{{ $agent->code }}</option>
                                    @endforeach
                                </optgroup>
                            </select>
                        </div>
                        <div class="filter-group" style="width: 150px; min-width: 140px;">
                            <span class="filter-label">Status</span>
                            <select id="modal-status-filter" class="form-control filter-input select2 modal-select2">
                                <option value="" selected disabled hidden></option>
                                @foreach(\App\Models\Crr::getStatusLabels() as $value => $label)
                                    @if(!in_array($value, [\App\Models\Crr::STATUS_COMPLETED, \App\Models\Crr::STATUS_CANCELLED], true))
                                        <option value="{{ $label }}">{{ $label }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-group" style="width: 140px; min-width: 120px;">
                            <span class="filter-label">Landed goods</span>
                            <select id="modal-landed-filter" class="form-control filter-input select2 modal-select2">
                                <option value="" selected disabled hidden></option>
                                <option value="">All</option>
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                        </div>
                        <div class="filter-group" style="width: 180px; min-width: 160px;">
                            <span class="filter-label">Stock no.</span>
                            <input id="modal-stock-filter" type="text" class="form-control filter-input modal-filter-input" placeholder="type here">
                        </div>
                        <div class="filter-group" style="width: 180px; min-width: 160px;">
                            <span class="filter-label">PO no.</span>
                            <input id="modal-po-filter" type="text" class="form-control filter-input modal-filter-input" placeholder="type here">
                        </div>
                        <div class="filter-group" style="width: 200px; min-width: 180px;">
                            <span class="filter-label">Supplier</span>
                            <input id="modal-supplier-filter" type="text" class="form-control filter-input modal-filter-input" placeholder="type here">
                        </div>
                        <a class="clear-filters modal-clear-filters" style="height: 32px; display: inline-flex; align-items: center; margin-left: auto;"><i class="ti-close"></i> Clear filters</a>
                    </div>

                    <!-- Table Section -->
                    <div class="table-scroll-wrapper" style="max-height: 400px;">
                        <table id="stock-items-modal-table" class="office-table">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="modal-select-all"></th>
                                    <th>Hub</th>
                                    <th>Stock no</th>
                                    <th>Customer</th>
                                    <th>Vessel</th>
                                    <th>Delivery</th>
                                    <th>PO numbers</th>
                                    <th>Supplier</th>
                                    <th>Items</th>
                                    <th>Weight</th>
                                    <th>Value</th>
                                    <th>Cur.</th>
                                    <th>Transit id</th>
                                    <th>Shipment</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($crrs as $crr)
                                @php
                                    $status = $crr->status ?? 'Pending';
                                    $badgeClass = ($status === 'Stock') ? 'label label-stock' : 'label label-pending';
                                    $poNumbers = is_array($crr->po_numbers) ? implode(', ', $crr->po_numbers) : ($crr->po_numbers ?? '');
                                    $totalItems = $crr->packages->count();
                                    $totalWeight = $crr->packages->sum('weight');
                                    $totalCbm = \App\Support\PackageVolumeMetrics::totalCbm($crr->packages);
                                    $hasDgr = $crr->packages->where('is_dgr', true)->isNotEmpty();
                                    $hasDocs = $crr->documents->isNotEmpty();
                                    $isNotStackable = $crr->packages->where('is_not_stackable', true)->isNotEmpty();
                                    $hasMedicine = $crr->packages->where('is_medicine', true)->isNotEmpty();
                                    $hasDeliveryIrreg = is_array($crr->delivery_irregularities) && in_array('Yes', $crr->delivery_irregularities);
                                @endphp
                                <tr data-id="{{ $crr->id }}"
                                    data-hub="{{ trim((string) ($crr->hub_code ?? '')) }}"
                                    data-hub-agent="{{ trim((string) ($crr->hub_agent ?? '')) }}"
                                    data-customer="{{ $crr->customerVessel && $crr->customerVessel->customer ? $crr->customerVessel->customer->customer_name : '' }}"
                                    data-vessel="{{ $crr->vessel_name ?? '' }}"
                                    data-status="{{ \App\Models\Crr::getStatusLabels()[$crr->status] ?? 'Unknown' }}"
                                    data-landed="{{ $crr->is_landed_goods ? '1' : '0' }}"
                                    data-stock="{{ $crr->stock_number ?? '' }}"
                                    data-po="{{ is_array($crr->po_numbers) ? implode(', ', $crr->po_numbers) : ($crr->po_numbers ?? '') }}"
                                    data-supplier="{{ $crr->supplier ?? '' }}"
                                    data-items="{{ $totalItems }}"
                                    data-weight="{{ $totalWeight > 0 ? number_format($totalWeight, 2, '.', '') : '' }}"
                                    data-cbm="{{ $totalCbm > 0 ? \App\Support\PackageVolumeMetrics::formatCbm($totalCbm) : '' }}"
                                    data-value="{{ $crr->customs_value ? number_format($crr->customs_value, 2, '.', '') : '' }}">
                                    <td class="text-center">
                                        <input type="checkbox"
                                            class="modal-row-checkbox"
                                            value="{{ $crr->id }}"
                                            @if((int) $crr->status === \App\Models\Crr::STATUS_IN_PROGRESS) disabled title="In Progress stock cannot be added to a shipment." @endif>
                                    </td>
                                    <td>{{ $crr->hub_code ?: ($crr->hub_agent ?: '—') }}</td>
                                    <td>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <span style="color: #008080; font-weight: 500;">{{ $crr->stock_number }}</span>
                                            <div class="d-flex align-items-center" style="gap: 8px;">
                                                @if($crr->is_landed_goods)
                                                    <span class="landed-badge" title="Landed Goods">Landed</span>
                                                @endif
                                                @if($hasDgr)
                                                    <i class="icofont icofont-warning text-danger" title="Dangerous Goods" style="font-size: 15px;"></i>
                                                @endif
                                                @if($hasDocs)
                                                    <i class="icofont icofont-file-alt text-muted" title="Documents Attached" style="font-size: 15px; color: #64748b !important;"></i>
                                                @endif
                                                @if($hasMedicine)
                                                    <i class="icofont icofont-first-aid text-success" title="Medicine" style="font-size: 15px;"></i>
                                                @endif
                                                @if($hasDeliveryIrreg) 
                                                    <i class="icofont icofont-info-circle text-pending" title="Delivery irregularities - missing info" style="font-size: 15px;"></i>
                                                @endif
                                                @if($isNotStackable)
                                                    <i class="icofont icofont-info-square text-warning" title="Non-Stackable Content" style="font-size: 15px;"></i>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $crr->customerVessel && $crr->customerVessel->customer ? $crr->customerVessel->customer->customer_name : '—' }}</td>
                                    <td>{{ $crr->vessel_name ?? '—' }}</td>
                                    <td>{{ $crr->expected_delivery_date ?? '—' }}</td>
                                    <td style="max-width: 150px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;display: block;">{{ $poNumbers ?: '—' }}</td>
                                    <td>{{ $crr->supplier ?? '—' }}</td>
                                    <td class="text-center">{{ $totalItems }}</td>
                                    <td class="text-right">{{ $totalWeight > 0 ? number_format($totalWeight, 2) : '—' }}</td>
                                    <td class="text-right">{{ $crr->customs_value ? number_format($crr->customs_value, 2) : '—' }}</td>
                                    <td>{{ $crr->currency ?? '—' }}</td>
                                    <td>{{ $crr->transit_id ?? '—' }}</td>
                                    <td>{{ $crr->internal_shipment ?? '—' }}</td>
                                    <td>
                                        @php
                                            $statusLabel = \App\Models\Crr::getStatusLabels()[$crr->status] ?? 'Unknown';
                                        @endphp
                                        <span class="stock-status-badge {{ \App\Models\Crr::statusBadgeClass($crr->status) }}">{{ $statusLabel }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="15" class="text-center py-4 text-muted" style="font-size: 12px;">
                                        No stock entries found.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="font-size: 12px;">Cancel</button>
                    <button type="button" class="btn btn-teal" id="modal-add-selected" style="font-size: 12px;">Add Selected</button>
                </div>
            </div>
        </div>
    </div>
