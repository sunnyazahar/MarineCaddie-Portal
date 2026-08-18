@forelse($vessels as $vessel)
                                                                <tr>
                                                                    <td>{{ $vessel->vessel }}</td>
                                                                    <td>{{ $vessel->vessel_imo }}</td>
                                                                    <td>{{ $vessel->vessel_type_alias }}</td>
                                                                    <td style="color: #555;">{{ $vessel->customer->customer_name ?? '-' }}</td>
                                                                </tr>
                                                            @empty
                                                            <tr>
                                                                <td colspan="4" class="text-center py-4 text-muted">No vessels found.</td>
                                                            </tr>
                                                            @endforelse
