<?php

namespace App\Services;

use App\Models\Crr;
use App\Models\CrrChangeLog;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class CrrChangeLogService
{
    private const FIELD_LABELS = [
        'vessel_name' => 'Vessel',
        'po_numbers' => 'PO numbers',
        'po_remarks' => 'PO remarks',
        'content' => 'Content',
        'first_mile_updates' => 'First mile updates',
        'first_mile_comment' => 'First mile comment',
        'supplier' => 'Supplier',
        'is_landed_goods' => 'Landed goods',
        'expected_delivery_date' => 'Expected delivery date',
        'actual_delivery_date' => 'Actual delivery date',
        'supplier_reference' => 'Supplier reference',
        'deadline_warehouse' => 'Deadline warehouse',
        'internal_shipment' => 'Related shipment',
        'delivery_irregularities' => 'Delivery irregularities',
        'incoterm' => 'Incoterm',
        'hub_agent' => 'Hub/Agent',
        'location' => 'Location',
        'transit_type' => 'Transit type',
        'transit_id' => 'Transit id',
        'is_bonded_goods' => 'Bonded goods',
        'customs_doc_type' => 'Customs doc type',
        'bonded_date' => 'Bonded date',
        'customs_doc_reference' => 'Customs doc reference',
        'customs_lot_number' => 'Customs lot number',
        'country_of_origin' => 'Country of origin',
        'hs_code' => 'HS code',
        'currency' => 'Currency',
        'customs_value' => 'Customs value',
        'customs_value_usd' => 'Customs value USD',
        'priority' => 'Priority',
        'status' => 'Status',
        'accept' => 'Accept',
        'flags' => 'Flags',
        'internal_comments' => 'Internal comments',
        'landed_from_vessel' => 'Landed from vessel',
    ];

    public function log(Crr $crr, string $title, ?string $description = null): CrrChangeLog
    {
        return CrrChangeLog::create([
            'crr_id' => $crr->id,
            'user_id' => auth()->id(),
            'title' => $title,
            'description' => $description,
            'created_at' => now(),
        ]);
    }

    public function logCreated(Crr $crr): void
    {
        $this->log($crr, 'Stock item created');
    }

    public function logAccepted(Crr $crr): void
    {
        $this->log($crr, 'Stock accepted');
    }

    public function captureSnapshot(Crr $crr): array
    {
        $crr->loadMissing(['packages', 'costs']);

        return [
            'attributes' => $this->normalizeAttributes($crr),
            'packages' => $this->normalizePackages($crr->packages),
            'costs' => $this->normalizeCosts($crr->costs),
        ];
    }

    public function logChangesFromSnapshot(Crr $crr, array $before): array
    {
        $crr->loadMissing(['packages', 'costs']);
        $after = $this->captureSnapshot($crr);
        $changes = [];

        foreach (self::FIELD_LABELS as $field => $label) {
            $old = $before['attributes'][$field] ?? null;
            $new = $after['attributes'][$field] ?? null;

            if ($this->valuesEqual($old, $new)) {
                continue;
            }

            if ($field === 'accept' && $new === true && $old !== true) {
                $this->logAccepted($crr);
                $changes[] = [
                    'title' => 'Stock accepted',
                    'description' => null,
                ];
                continue;
            }

            $oldDisplay = $this->formatFieldValue($field, $old);
            $newDisplay = $this->formatFieldValue($field, $new);
            $description = $this->fromToDescription($oldDisplay, $newDisplay);

            if ($description === '') {
                continue;
            }

            $title = $field === 'internal_shipment'
                ? 'Related shipment edited'
                : $label . ' edited';

            $this->log($crr, $title, $description);
            $changes[] = [
                'title' => $title,
                'description' => $description,
            ];
        }

        if (($before['packages'] ?? []) !== ($after['packages'] ?? [])) {
            $description = $this->collectionCountDescription(count($before['packages'] ?? []), count($after['packages'] ?? []));
            $this->log($crr, 'Packages edited', $description);
            $changes[] = [
                'title' => 'Packages edited',
                'description' => $description,
            ];
        }

        if (($before['costs'] ?? []) !== ($after['costs'] ?? [])) {
            $description = $this->collectionCountDescription(count($before['costs'] ?? []), count($after['costs'] ?? []));
            $this->log($crr, 'Costs edited', $description);
            $changes[] = [
                'title' => 'Costs edited',
                'description' => $description,
            ];
        }

        return $changes;
    }

    private function normalizeAttributes(Crr $crr): array
    {
        $attributes = [];

        foreach (array_keys(self::FIELD_LABELS) as $field) {
            $value = $crr->{$field};

            if ($field === 'po_numbers' || $field === 'delivery_irregularities') {
                $attributes[$field] = $this->normalizeArrayValue($value);
                continue;
            }

            if ($field === 'flags') {
                $attributes[$field] = $this->normalizeArrayValue($value);
                continue;
            }

            if (is_bool($value)) {
                $attributes[$field] = $value;
                continue;
            }

            if ($field === 'status') {
                $attributes[$field] = (int) $value;
                continue;
            }

            $attributes[$field] = $value === null || $value === '' ? null : (string) $value;
        }

        return $attributes;
    }

    private function normalizePackages(Collection $packages): array
    {
        return $packages
            ->map(fn ($item) => [
                'length' => $item->length !== null ? (string) $item->length : null,
                'width' => $item->width !== null ? (string) $item->width : null,
                'height' => $item->height !== null ? (string) $item->height : null,
                'weight' => $item->weight !== null ? (string) $item->weight : null,
                'cbm' => $item->cbm !== null ? (string) $item->cbm : null,
                'warehouse_location' => $item->warehouse_location,
                'remarks' => $item->remarks,
                'is_dgr' => (bool) $item->is_dgr,
                'dgr_description' => $item->dgr_description,
                'un_number' => $item->un_number,
                'dgr_class' => $item->dgr_class,
                'is_delivery_irregularity' => (bool) $item->is_delivery_irregularity,
                'delivery_irregularities' => $this->normalizeArrayValue($item->delivery_irregularities),
                'is_not_stackable' => (bool) $item->is_not_stackable,
                'is_medicine' => (bool) $item->is_medicine,
                'is_xray' => (bool) $item->is_xray,
            ])
            ->values()
            ->all();
    }

    private function normalizeCosts(Collection $costs): array
    {
        return $costs
            ->map(fn ($item) => [
                'type' => $item->type,
                'carrier' => $item->carrier,
                'net_value' => $item->net_value !== null ? (string) $item->net_value : null,
                'currency' => $item->currency,
                'net_value_usd' => $item->net_value_usd !== null ? (string) $item->net_value_usd : null,
                'invoice_no' => $item->invoice_no,
                'remarks' => $item->remarks,
                'hub_agent' => $item->hub_agent,
                'tag' => $item->tag,
            ])
            ->values()
            ->all();
    }

    private function formatFieldValue(string $field, mixed $value): string
    {
        if ($value === null || $value === '') {
            return 'empty';
        }

        if ($field === 'status') {
            return Crr::getStatusLabels()[(int) $value] ?? (string) $value;
        }

        if (in_array($field, ['is_landed_goods', 'is_bonded_goods', 'accept'], true)) {
            return $value ? 'Yes' : 'No';
        }

        if ($field === 'flags') {
            $flags = is_array($value) ? $value : [];

            return $flags !== [] ? $this->truncate(implode(', ', $flags)) : 'empty';
        }

        if (in_array($field, ['po_numbers', 'delivery_irregularities'], true)) {
            $items = is_array($value) ? $value : [];

            return $items !== [] ? $this->truncate(implode(', ', $items)) : 'empty';
        }

        if (in_array($field, [
            'expected_delivery_date',
            'actual_delivery_date',
            'deadline_warehouse',
            'bonded_date',
        ], true)) {
            try {
                return Carbon::parse((string) $value)->format('d.m.Y');
            } catch (\Throwable) {
                return $this->truncate((string) $value);
            }
        }

        return $this->truncate((string) $value);
    }

    private function normalizeArrayValue(mixed $value): ?array
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (! is_array($value)) {
            $value = [$value];
        }

        $normalized = array_values(array_filter(array_map(
            fn ($item) => trim((string) $item),
            $value
        ), fn ($item) => $item !== ''));

        sort($normalized);

        return $normalized === [] ? null : $normalized;
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
        if (is_array($left) || is_array($right)) {
            return json_encode($left) === json_encode($right);
        }

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
