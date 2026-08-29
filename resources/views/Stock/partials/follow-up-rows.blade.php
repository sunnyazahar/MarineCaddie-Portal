@forelse ($crrs as $crr)
                                                            @php
                                                                $customerName = $crr->customerVessel?->customer?->customer_name ?? '';
                                                                $accountManager = $crr->customerVessel?->account_manager
                                                                    ?: $crr->customerVessel?->customer?->responsible?->accountManager?->name
                                                                    ?: '';
                                                                $poNumbers = is_array($crr->po_numbers) ? $crr->po_numbers : [];
                                                                $totalItems = $crr->packages->count();
                                                                $totalWeight = $crr->packages->sum('weight');
                                                                $hasDgr = $crr->packages->where('is_dgr', true)->isNotEmpty();
                                                                $hasDocs = $crr->documents->isNotEmpty();
                                                                $hasMedicine = $crr->packages->where('is_medicine', true)->isNotEmpty();
                                                                $isNotStackable = $crr->packages->where('is_not_stackable', true)->isNotEmpty();
                                                                $hasDeliveryIrreg = is_array($crr->delivery_irregularities) && in_array('Yes', $crr->delivery_irregularities, true);
                                                                $statusLabel = \App\Models\Crr::getStatusLabels()[$crr->status] ?? 'Unknown';
                                                                $valueDisplay = $crr->customs_value
                                                                    ? number_format((float) $crr->customs_value, 2) . ' ' . ($crr->currency ?: 'USD')
                                                                    : '—';
                                                                $isEtl = strtoupper((string) $crr->internal_shipment) === 'ETL';
                                                            @endphp
                                                            <tr
                                                                data-account-manager="{{ $accountManager }}"
                                                                data-customer="{{ $customerName }}"
                                                                data-has-etl="{{ $isEtl ? '1' : '0' }}"
                                                            >
                                                                <td title="{{ $crr->hub_code ?? ($crr->hub_agent ?? '—') }}"><span class="cell-ellipsis">{{ $crr->hub_code ?? ($crr->hub_agent ?? '—') }}</span></td>
                                                                <td class="stock-no-cell">
                                                                    <div class="stock-no-row">
                                                                        <a href="{{ route('stocks.edit', $crr->id) }}" style="color: #0ea5e9;" title="{{ $crr->stock_number }}">{{ $crr->stock_number }}</a>
                                                                        <div class="stock-no-flags">
                                                                            @if($crr->is_landed_goods)
                                                                                <span class="badge-landed" title="Landed Goods">Landed</span>
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
                                                                <td title="{{ $poNumbers ? implode(', ', $poNumbers) : '—' }}">
                                                                    <span class="cell-ellipsis">
                                                                    @forelse ($poNumbers as $poNumber)
                                                                        <span class="po-badge">{{ $poNumber }}</span>
                                                                    @empty
                                                                        —
                                                                    @endforelse
                                                                    </span>
                                                                </td>
                                                                <td title="{{ $crr->supplier ?? '—' }}"><span class="cell-ellipsis">{{ $crr->supplier ?? '—' }}</span></td>
                                                                <td class="text-center">{{ $totalItems ?: '—' }}</td>
                                                                <td class="text-center">{{ $totalWeight > 0 ? number_format((float) $totalWeight, 1) : '—' }}</td>
                                                                <td class="text-right" title="{{ $valueDisplay }}"><span class="cell-ellipsis">{{ $valueDisplay }}</span></td>
                                                                <td title="{{ $crr->shipments->pluck('shipment_number')->filter()->implode(', ') ?: '—' }}">
                                                                    <span class="cell-ellipsis">
                                                                    @forelse ($crr->shipments as $shipment)
                                                                        <span class="shipment-badge">{{ $shipment->shipment_number }}</span>
                                                                    @empty
                                                                        —
                                                                    @endforelse
                                                                    </span>
                                                                </td>
                                                                <td title="{{ $crr->registeredBy?->name ?? '—' }}"><span class="cell-ellipsis">{{ $crr->registeredBy?->name ?? '—' }}</span></td>
                                                                <td title="{{ $isEtl ? 'ETL' : ($crr->internal_shipment ?: '—') }}"><span class="cell-ellipsis">{{ $isEtl ? 'ETL' : ($crr->internal_shipment ?: '—') }}</span></td>
                                                                <td class="stock-status-cell"><span class="stock-status-badge {{ \App\Models\Crr::statusBadgeClass($crr->status) }}">{{ $statusLabel }}</span></td>
                                                                <td class="text-center stock-action-cell">
                                                                    <button type="button"
                                                                        class="btn-accept accept-stock-btn"
                                                                        data-crr-id="{{ $crr->id }}"
                                                                        data-stock-number="{{ $crr->stock_number }}"
                                                                        data-accept-url="{{ route('stocks.crr.update-accept', $crr->id) }}">Accept</button>
                                                                </td>
                                                            </tr>
                                                            @empty
                                                            <tr>
                                                                <td class="text-center py-4 text-muted">No stocks found.</td>
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