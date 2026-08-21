@forelse ($crrs as $crr)
                                                            @php
                                                                $customerName = $crr->customerVessel?->customer?->customer_name ?? '';
                                                                $accountManager = $crr->accountManagerName() ?? '';
                                                                $poNumbers = is_array($crr->po_numbers) ? $crr->po_numbers : [];
                                                                $hasDocs = $crr->documents->isNotEmpty();
                                                                $hasDgr = $crr->packages->where('is_dgr', true)->isNotEmpty();
                                                                $hasMedicine = $crr->packages->where('is_medicine', true)->isNotEmpty();
                                                                $isNotStackable = $crr->packages->where('is_not_stackable', true)->isNotEmpty();
                                                                $hasDeliveryIrreg = is_array($crr->delivery_irregularities) && in_array('Yes', $crr->delivery_irregularities, true);
                                                                $hasCustomsValue = (float) ($crr->customs_value ?? 0) > 0;

                                                                $formatDate = function ($value) {
                                                                    if (!$value) {
                                                                        return ['display' => '—', 'filter' => ''];
                                                                    }

                                                                    try {
                                                                        $date = \Carbon\Carbon::parse($value);
                                                                        return [
                                                                            'display' => $date->format('d.m.Y'),
                                                                            'filter' => $date->format('Y-m-d'),
                                                                        ];
                                                                    } catch (\Exception $e) {
                                                                        return ['display' => $value, 'filter' => ''];
                                                                    }
                                                                };

                                                                $expectedDate = $formatDate($crr->expected_delivery_date);
                                                                $deadlineWarehouse = $formatDate($crr->deadline_warehouse);
                                                                $pickupDate = $formatDate($crr->actual_delivery_date);
                                                                $comment = trim(($crr->first_mile_comment ?: '') . ($crr->first_mile_updates ? ' ' . $crr->first_mile_updates : ''));
                                                                $statusLabel = \App\Models\Crr::getStatusLabels()[$crr->status] ?? 'Unknown';
                                                                $handledBy = $handledByMap->get($crr->hub_agent, '');
                                                            @endphp
                                                            <tr
                                                                data-account-manager="{{ $accountManager }}"
                                                                data-hub-agent="{{ $crr->hub_agent ?? '' }}"
                                                                data-vessel="{{ $crr->vessel_name ?? '' }}"
                                                                data-handled-by="{{ $handledBy }}"
                                                                data-expected-delivery="{{ $expectedDate['filter'] }}"
                                                                data-deadline-warehouse="{{ $deadlineWarehouse['filter'] }}"
                                                                data-pickup-date="{{ $pickupDate['filter'] }}"
                                                            >
                                                                <td>
                                                                    <div style="display: flex; align-items: center; justify-content: space-between;">
                                                                        <a href="{{ route('stocks.edit', $crr->id) }}" class="table-link">{{ $crr->stock_number }}</a>
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
                                                                            @if($crr->first_mile_updates)
                                                                                <i class="ti-bell icon-density icon-bell"></i>
                                                                            @endif
                                                                            @if($hasCustomsValue)
                                                                                <span style="color: #0ea5e9; font-weight: bold; font-size: 12px; margin-left: 2px;">$</span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>{{ $customerName ?: '—' }}</td>
                                                                <td>{{ $crr->vessel_name ?? '—' }}</td>
                                                                <td>{{ $poNumbers ? implode(', ', $poNumbers) : '—' }}</td>
                                                                <td>{{ $crr->supplier ?? '—' }}</td>
                                                                <td>{{ $crr->supplier_reference ?? '—' }}</td>
                                                                <td>{{ $expectedDate['display'] }}</td>
                                                                <td>{{ $deadlineWarehouse['display'] }}</td>
                                                                <td style="max-width: 300px; white-space: normal; line-height: 1.2;">{{ $comment ?: '—' }}</td>
                                                                <td><span class="stock-status-badge {{ \App\Models\Crr::statusBadgeClass($crr->status) }}">{{ $statusLabel }}</span></td>
                                                                <td>{{ $handledBy ?: '—' }}</td>
                                                                <td>{{ $pickupDate['display'] }}</td>
                                                            </tr>
                                                            @empty
                                                            <tr>
                                                                <td class="text-center py-4 text-muted">No pickup stocks found.</td>
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