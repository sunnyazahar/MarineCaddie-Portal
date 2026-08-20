@forelse($suppliers as $supplier)
                                                                <tr>
                                                                    <td>{{ $supplier->supplier_name }}</td>
                                                                    <td class="col-address" title="{{ $supplier->supplier_address }}"><span class="cell-ellipsis">{{ $supplier->supplier_address }}</span></td>
                                                                    <td>{{ $supplier->city }}</td>
                                                                    <td>
                                                                        @if($supplier->country && $supplier->country->flag_url)
                                                                            <img src="{{ $supplier->country->flag_url }}" width="16" height="12" alt="{{ $supplier->country->name }}" style="margin-right: 5px; vertical-align: middle; border: 1px solid #eee;">
                                                                             {{ $supplier->country->name }}
                                                                        @elseif($supplier->country)
                                                                            {{ $supplier->country->name }}
                                                                        @endif
                                                                    </td>
                                                                    <td>{{ $supplier->phone_number }}</td>
                                                                    <td>{{ $supplier->email }}</td>
                                                                    <td class="text-right">
                                                                        <a href="{{ route('suppliers.edit', $supplier->id) }}" style="color: #666; margin-right: 5px;">
                                                                            <i class="ti-pencil"></i>
                                                                        </a>
                                                                        <a href="javascript:void(0)" class="delete-supplier" data-id="{{ $supplier->id }}" data-name="{{ $supplier->supplier_name }}" style="color: #ff5252;" title="Delete supplier">
                                                                            <i class="ti-trash"></i>
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                            @empty
                                                            <tr>
                                                                <td colspan="7" class="text-center py-4 text-muted">No suppliers found.</td>
                                                            </tr>
                                                            @endforelse
