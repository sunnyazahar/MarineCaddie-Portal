@forelse($crrs as $crr)
                                                            @php
                                                                $hubInProgressShipmentInfo = $hubInProgressShipmentInfo ?? collect();
                                                                $shipmentColumn = $crr->stockListShipmentColumn($hubInProgressShipmentInfo);
                                                                $status = $crr->status ?? 'Pending';
                                                                $statusLabel = \App\Models\Crr::getStatusLabels()[$crr->status] ?? 'Unknown';
                                                                $customerName = $crr->customerVessel?->customer?->customer_name ?? '';
                                                                $accountManager = $crr->accountManagerName() ?? '';
                                                                $officeName = $crr->customerVessel?->customer?->responsible?->accountManager?->office?->office_name ?? '';
                                                                $poNumbers = is_array($crr->po_numbers) ? implode(', ', $crr->po_numbers) : ($crr->po_numbers ?? '');
                                                                $totalItems = $crr->packages->count();
                                                                $totalWeight = $crr->packages->sum('weight');
                                                                $totalCbm = \App\Support\PackageVolumeMetrics::totalCbm($crr->packages);
                                                                $hasDgr = $crr->packages->where('is_dgr', true)->isNotEmpty();
                                                                $hasDocs = $crr->documents->isNotEmpty();
                                                                $isNotStackable = $crr->packages->where('is_not_stackable', true)->isNotEmpty();
                                                                $hasMedicine = $crr->packages->where('is_medicine', true)->isNotEmpty();
                                                                $hasDeliveryIrreg = is_array($crr->delivery_irregularities) && in_array('Yes', $crr->delivery_irregularities);
                                                                $isOversized = $crr->packages->contains(function ($pkg) {
                                                                    return (float) ($pkg->length ?? 0) >= 120
                                                                        || (float) ($pkg->width ?? 0) >= 120
                                                                        || (float) ($pkg->height ?? 0) >= 120;
                                                                });
                                                            @endphp
                                                            <tr
                                                                data-customer="{{ $customerName }}"
                                                                data-vessel="{{ $crr->vessel_name ?? '' }}"
                                                                data-hub-agent="{{ $crr->hub_code ?? '' }}"
                                                                data-hub-agent-raw="{{ $crr->hub_agent ?? '' }}"
                                                                data-status="{{ $statusLabel }}"
                                                                data-account-manager="{{ $accountManager }}"
                                                                data-office="{{ $officeName }}"
                                                                data-stock-number="{{ $crr->stock_number ?? '' }}"
                                                                data-po-numbers="{{ $poNumbers }}"
                                                                data-supplier="{{ $crr->supplier ?? '' }}"
                                                                data-service-reference="{{ $crr->supplier_reference ?? '' }}"
                                                                data-shipment="{{ $crr->internal_shipment ?? '' }}"
                                                                data-transit-id="{{ $crr->transit_id ?? '' }}"
                                                                data-items="{{ $totalItems }}"
                                                                data-weight="{{ $totalWeight > 0 ? number_format($totalWeight, 2, '.', '') : '0' }}"
                                                                data-cbm="{{ $totalCbm > 0 ? \App\Support\PackageVolumeMetrics::formatCbm($totalCbm) : '0' }}"
                                                                data-value="{{ $crr->customs_value !== null ? number_format((float) $crr->customs_value, 2, '.', '') : '' }}"
                                                                data-currency="{{ $crr->currency ?? '' }}"
                                                                data-dgr="{{ $hasDgr ? 'Yes' : '' }}"
                                                                data-oversized="{{ $isOversized ? 'Yes' : '' }}"
                                                            >
                                                                <td class="text-center stock-col-check"><input type="checkbox" class="row-checkbox" value="{{ $crr->id }}"></td>
                                                                <td title="{{ $crr->hub_code ?? '—' }}"><span class="cell-ellipsis">{{ $crr->hub_code ?? '—' }}</span></td>
                                                                <td class="stock-no-cell">
                                                                    <div class="stock-no-row">
                                                                        <a href="{{ route('stocks.edit', $crr->id) }}" style="color: #008080; font-weight: 500;" title="{{ $crr->stock_number }}">{{ $crr->stock_number }}</a>
                                                                        <div class="stock-no-flags">
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
                                                                <td title="{{ $customerName ?: '—' }}"><span class="cell-ellipsis">{{ $customerName ?: '—' }}</span></td>
                                                                <td title="{{ $crr->vessel_name ?? '—' }}"><span class="cell-ellipsis">{{ $crr->vessel_name ?? '—' }}</span></td>
                                                                <td title="{{ $crr->expected_delivery_date ?? '—' }}"><span class="cell-ellipsis">{{ $crr->expected_delivery_date ?? '—' }}</span></td>
                                                                <td title="{{ $poNumbers ?: '—' }}"><span class="cell-ellipsis">{{ $poNumbers ?: '—' }}</span></td>
                                                                <td title="{{ $crr->supplier ?? '—' }}"><span class="cell-ellipsis">{{ $crr->supplier ?? '—' }}</span></td>
                                                                <td class="text-center">{{ $totalItems }}</td>
                                                                <td class="text-right">{{ $totalWeight > 0 ? number_format($totalWeight, 2) : '—' }}</td>
                                                                <td class="text-right">{{ $crr->customs_value ? number_format($crr->customs_value, 2) : '—' }}</td>
                                                                <td title="{{ $crr->currency ?? '—' }}"><span class="cell-ellipsis">{{ $crr->currency ?? '—' }}</span></td>
                                                                <td title="{{ $crr->transit_id ?? '—' }}"><span class="cell-ellipsis">{{ $crr->transit_id ?? '—' }}</span></td>
                                                                <td title="{{ $shipmentColumn['number'] !== '' ? $shipmentColumn['number'] : '—' }}">
                                                                    @if ($shipmentColumn['inherited'] && $shipmentColumn['number'] !== '')
                                                                        @if ($shipmentColumn['shipment_id'])
                                                                            <a
                                                                                href="{{ route('shipments.edit', $shipmentColumn['shipment_id']) }}"
                                                                                class="shipment-badge"
                                                                            >{{ $shipmentColumn['number'] }}</a>
                                                                        @else
                                                                            <span class="shipment-badge">{{ $shipmentColumn['number'] }}</span>
                                                                        @endif
                                                                    @elseif ($shipmentColumn['number'] !== '')
                                                                        <span class="cell-ellipsis">{{ $shipmentColumn['number'] }}</span>
                                                                    @else
                                                                        <span class="cell-ellipsis">—</span>
                                                                    @endif
                                                                </td>
                                                                <td class="stock-col-status">
                                                                    <span class="stock-status-badge {{ \App\Models\Crr::statusBadgeClass($crr->status) }}">{{ $statusLabel }}</span>
                                                                </td>
                                                            </tr>
                                                            @empty
                                                            <tr>
                                                                <td class="text-center py-4 text-muted" style="font-size: 12px;">
                                                                    No stock entries found. <a href="{{ route('create-crr') }}" style="color: #008080;">Create a CRR</a> to get started.
                                                                </td>
                                                                <td></td>
                                                                <td></td>
                                                                <td></td>
                                                                <td></td>
                                                                <td></td>
                                                                <td></td>
                                                                <td></td>
                                                                <td></td>
                                                                <td></td>
                                                                <td></td>
                                                                <td></td>
                                                                <td></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                            @endforelse