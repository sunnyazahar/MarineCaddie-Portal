Stock {{ $stockNumber }} was updated

Hello {{ $accountManagerName }},

The following changes were saved for vessel {{ $vesselName }}.
@if (! empty($shipmentNumbersLabel))
Linked shipment: {{ $shipmentNumbersLabel }}
@endif
Changed by: {{ $changedByName }}

@foreach ($changes as $change)
- {{ $change['title'] }}@if (! empty($change['description'])): {{ $change['description'] }}@endif

@endforeach

Open stock: {{ $stockUrl }}

This is an automated MarineCaddie notification for the vessel account manager.
