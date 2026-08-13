<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\Crr;
use App\Models\Shipment;
use App\Models\ShipmentChangeLog;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ShipmentChangeLogService
{
  private const FIELD_LABELS = [
    'departure' => 'Departure',
    'departure_port_code' => 'Departure port code',
    'service' => 'Service',
    'additional_service' => 'Additional service',
    'preferred_shipment_date' => 'Preferred shipment date',
    'deadline_arrival' => 'Deadline arrival',
    'vessel_eta' => 'Vessel ETA',
    'vessel_etd' => 'Vessel ETD',
    'pre_alert_reminder' => 'Pre-alert reminder',
    'customer_reference' => 'Customer reference',
    'not_applicable_for_consolidation' => 'Not applicable for consolidation',
    'consignee' => 'Consignee',
    'consignee_address' => 'Consignee address',
    'consignee_city' => 'Consignee city',
    'consignee_district' => 'Consignee district',
    'consignee_zip' => 'Consignee zip',
    'consignee_country' => 'Consignee country',
    'consignee_port_code' => 'Consignee port code',
    'location' => 'Location',
    'consignee_att' => 'Consignee att.',
    'consignee_email' => 'Consignee email',
    'account_manager_id' => 'Account manager',
    'special_considerations_destination' => 'Special considerations destination',
    'skip_instruction_dest' => 'Skip instruction destination',
    'comments_departure_hub' => 'Comments to departure hub',
    'skip_instruction_hub' => 'Skip instruction hub',
    'comments_consignee' => 'Comments to consignee',
    'skip_prealert' => 'Skip pre-alert',
    'project_logistics' => 'Project logistics',
    'port_agency' => 'Port agency',
    'status' => 'Status',
    'repacked_items' => 'Repacked items',
    'repacked_weight' => 'Repacked weight',
  ];

  private const LEG_GROUPS = [
    'flights' => 'Airfreight legs',
    'sea_legs' => 'Sea freight legs',
    'truck_legs' => 'Truck legs',
    'courier_legs' => 'Courier legs',
    'release_legs' => 'Release legs',
    'hand_carry_legs' => 'Hand carry legs',
    'on_board_legs' => 'On-board delivery legs',
  ];

  public function log(Shipment $shipment, string $title, ?string $description = null): ShipmentChangeLog
  {
    return ShipmentChangeLog::create([
      'shipment_id' => $shipment->id,
      'user_id' => auth()->id(),
      'title' => $title,
      'description' => $description,
      'created_at' => now(),
    ]);
  }

  public function logCreated(Shipment $shipment): void
  {
    $this->log($shipment, 'Created');
  }

  public function captureSnapshot(Shipment $shipment): array
  {
    $shipment->loadMissing([
      'crrs',
      'irregularities',
      'flights',
      'seaLegs',
      'truckLegs',
      'courierLegs',
      'releaseLegs',
      'handCarryLegs',
      'onBoardLegs',
      'accountManager',
    ]);

    return [
      'attributes' => $this->normalizeAttributes($shipment),
      'crr_ids' => $shipment->crrs->pluck('id')->sort()->values()->all(),
      'crr_stock_numbers' => $shipment->crrs->pluck('stock_number', 'id')->all(),
      'irregularities' => $this->normalizeIrregularities($shipment->irregularities),
      'flights' => $this->normalizeFlights($shipment->flights),
      'sea_legs' => $this->normalizeSeaLegs($shipment->seaLegs),
      'truck_legs' => $this->normalizeTruckLegs($shipment->truckLegs),
      'courier_legs' => $this->normalizeCourierLegs($shipment->courierLegs),
      'release_legs' => $this->normalizeReleaseLegs($shipment->releaseLegs),
      'hand_carry_legs' => $this->normalizeHandCarryLegs($shipment->handCarryLegs),
      'on_board_legs' => $this->normalizeOnBoardLegs($shipment->onBoardLegs),
    ];
  }

  public function logChangesFromSnapshot(Shipment $shipment, array $before, array $partyNamesBefore): void
  {
    $shipment->loadMissing([
      'crrs',
      'irregularities',
      'flights',
      'seaLegs',
      'truckLegs',
      'courierLegs',
      'releaseLegs',
      'handCarryLegs',
      'onBoardLegs',
      'accountManager',
    ]);

    $after = $this->captureSnapshot($shipment);
    $partyNamesAfter = Shipment::batchResolvePartyNames(collect([$shipment]));

    foreach (self::FIELD_LABELS as $field => $label) {
      $old = $before['attributes'][$field] ?? null;
      $new = $after['attributes'][$field] ?? null;

      if ($this->valuesEqual($old, $new)) {
        continue;
      }

      $oldDisplay = $this->formatFieldValue($field, $old, $partyNamesBefore, $before);
      $newDisplay = $this->formatFieldValue($field, $new, $partyNamesAfter, $after);
      $description = $this->fromToDescription($oldDisplay, $newDisplay);

      if ($description === '') {
        continue;
      }

      $this->log($shipment, $label . ' edited', $description);
    }

    if (($before['crr_ids'] ?? []) !== ($after['crr_ids'] ?? [])) {
      $this->logStockItemChanges($shipment, $before, $after);
    }

    if (($before['irregularities'] ?? []) !== ($after['irregularities'] ?? [])) {
      $this->log($shipment, 'Irregularities edited', $this->collectionCountDescription(
        count($before['irregularities'] ?? []),
        count($after['irregularities'] ?? [])
      ));
    }

    foreach (self::LEG_GROUPS as $key => $label) {
      if (($before[$key] ?? []) === ($after[$key] ?? [])) {
        continue;
      }

      $this->log($shipment, $label . ' edited', $this->collectionCountDescription(
        count($before[$key] ?? []),
        count($after[$key] ?? [])
      ));
    }
  }

  private function logStockItemChanges(Shipment $shipment, array $before, array $after): void
  {
    $beforeIds = $before['crr_ids'] ?? [];
    $afterIds = $after['crr_ids'] ?? [];
    $stockNumbers = array_merge($before['crr_stock_numbers'] ?? [], $after['crr_stock_numbers'] ?? []);

    $added = array_values(array_diff($afterIds, $beforeIds));
    $removed = array_values(array_diff($beforeIds, $afterIds));

    $parts = [];

    if ($added !== []) {
      $parts[] = 'Added: ' . $this->stockNumberList($added, $stockNumbers);
    }

    if ($removed !== []) {
      $parts[] = 'Removed: ' . $this->stockNumberList($removed, $stockNumbers);
    }

    $this->log($shipment, 'Stock items edited', $parts !== [] ? implode('. ', $parts) : null);
  }

  private function stockNumberList(array $ids, array $stockNumbers): string
  {
    $labels = array_map(
      fn (int $id) => $stockNumbers[$id] ?? ('#' . $id),
      $ids
    );

    return $this->truncate(implode(', ', $labels));
  }

  private function normalizeAttributes(Shipment $shipment): array
  {
    $attributes = [];

    foreach (array_keys(self::FIELD_LABELS) as $field) {
      $value = $shipment->{$field};

      if ($value instanceof Carbon) {
        $attributes[$field] = $value->format('Y-m-d');
        continue;
      }

      if (is_bool($value)) {
        $attributes[$field] = $value;
        continue;
      }

      $attributes[$field] = $value === null ? null : (string) $value;
    }

    return $attributes;
  }

  private function normalizeIrregularities(Collection $irregularities): array
  {
    return $irregularities
      ->map(fn ($item) => [
        'irregularity_date' => $item->irregularity_date?->format('Y-m-d'),
        'irregularity_type' => $item->irregularity_type,
        'party_responsible' => $item->party_responsible,
        'consequence' => $item->consequence,
        'extra_cost_mt_usd' => $item->extra_cost_mt_usd !== null ? (string) $item->extra_cost_mt_usd : null,
        'status' => $item->status,
        'cause_of_irregularity' => $item->cause_of_irregularity,
        'action_taken' => $item->action_taken,
        'customer_response' => $item->customer_response,
        'hub_agent_comments' => $item->hub_agent_comments,
        'handled_by' => $item->handled_by,
      ])
      ->values()
      ->all();
  }

  private function normalizeFlights(Collection $flights): array
  {
    return $flights
      ->map(fn ($item) => [
        'leg_reference' => $item->leg_reference,
        'flight_number' => $item->flight_number,
        'departure_date' => $item->departure_date?->format('Y-m-d'),
        'arrival_date' => $item->arrival_date?->format('Y-m-d'),
        'arrival_time' => $item->arrival_time,
      ])
      ->values()
      ->all();
  }

  private function normalizeSeaLegs(Collection $legs): array
  {
    return $legs
      ->map(fn ($item) => [
        'bill_of_lading' => $item->bill_of_lading,
        'container_number' => $item->container_number,
        'transport_vessel_imo' => $item->transport_vessel_imo,
        'transport_vessel_name' => $item->transport_vessel_name,
        'etd' => $item->etd?->format('Y-m-d'),
        'eta' => $item->eta?->format('Y-m-d'),
        'arrival_time' => $item->arrival_time,
      ])
      ->values()
      ->all();
  }

  private function normalizeTruckLegs(Collection $legs): array
  {
    return $legs
      ->map(fn ($item) => [
        'cmr' => $item->cmr,
        'freight_company' => $item->freight_company,
        'departure_date' => $item->departure_date?->format('Y-m-d'),
        'arrival_date' => $item->arrival_date?->format('Y-m-d'),
        'arrival_time' => $item->arrival_time,
      ])
      ->values()
      ->all();
  }

  private function normalizeCourierLegs(Collection $legs): array
  {
    return $legs
      ->map(fn ($item) => [
        'airway_bill' => $item->airway_bill,
        'carrier' => $item->carrier,
        'departure_date' => $item->departure_date?->format('Y-m-d'),
        'arrival_date' => $item->arrival_date?->format('Y-m-d'),
        'arrival_time' => $item->arrival_time,
      ])
      ->values()
      ->all();
  }

  private function normalizeReleaseLegs(Collection $legs): array
  {
    return $legs
      ->map(fn ($item) => [
        'freight_company' => $item->freight_company,
        'delivery_date' => $item->delivery_date?->format('Y-m-d'),
        'delivery_time' => $item->delivery_time,
      ])
      ->values()
      ->all();
  }

  private function normalizeHandCarryLegs(Collection $legs): array
  {
    return $legs
      ->map(fn ($item) => [
        'departure_date' => $item->departure_date?->format('Y-m-d'),
        'arrival_date' => $item->arrival_date?->format('Y-m-d'),
        'arrival_time' => $item->arrival_time,
        'contact_name' => $item->contact_name,
        'contact_phone' => $item->contact_phone,
        'onboard_hand_carry' => (bool) $item->onboard_hand_carry,
      ])
      ->values()
      ->all();
  }

  private function normalizeOnBoardLegs(Collection $legs): array
  {
    return $legs
      ->map(fn ($item) => [
        'departure_date' => $item->departure_date?->format('Y-m-d'),
        'delivery_date' => $item->delivery_date?->format('Y-m-d'),
        'delivery_time' => $item->delivery_time,
      ])
      ->values()
      ->all();
  }

  private function formatFieldValue(string $field, mixed $value, array $partyNames, array $snapshot): string
  {
    if ($value === null || $value === '') {
      return 'empty';
    }

    if (in_array($field, ['departure', 'consignee'], true)) {
      return $this->truncate($partyNames[(string) $value] ?? (string) $value);
    }

    if ($field === 'account_manager_id') {
      $contact = Contact::find((int) $value);

      return $this->truncate($contact?->name ?? ('#' . $value));
    }

    if (in_array($field, [
      'not_applicable_for_consolidation',
      'skip_instruction_dest',
      'skip_instruction_hub',
      'skip_prealert',
      'project_logistics',
      'port_agency',
    ], true)) {
      return $value ? 'Yes' : 'No';
    }

    if (in_array($field, [
      'preferred_shipment_date',
      'deadline_arrival',
      'vessel_eta',
      'vessel_etd',
      'pre_alert_reminder',
    ], true)) {
      try {
        return Carbon::parse((string) $value)->format('d.m.Y');
      } catch (\Throwable) {
        return $this->truncate((string) $value);
      }
    }

    return $this->truncate((string) $value);
  }

  private function fromToDescription(string $old, string $new): string
  {
    if ($old === $new) {
      return '';
    }

    return 'From ' . $old . ' to ' . $new;
  }

  private function collectionCountDescription(int $beforeCount, int $afterCount): string
  {
    return 'From ' . $beforeCount . ' to ' . $afterCount;
  }

  private function valuesEqual(mixed $left, mixed $right): bool
  {
    if (is_bool($left) || is_bool($right)) {
      return (bool) $left === (bool) $right;
    }

    return (string) ($left ?? '') === (string) ($right ?? '');
  }

  private function truncate(string $value, int $limit = 180): string
  {
    $value = trim(preg_replace('/\s+/u', ' ', $value) ?? '');

    if ($value === '') {
      return 'empty';
    }

    if (mb_strlen($value) <= $limit) {
      return $value;
    }

    return mb_substr($value, 0, $limit - 3) . '...';
  }
}
