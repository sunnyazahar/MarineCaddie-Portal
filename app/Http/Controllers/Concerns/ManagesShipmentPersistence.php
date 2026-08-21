<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Crr;
use App\Models\Shipment;
use App\Models\ShipmentManifest;
use App\Models\ShipmentPreAlert;
use App\Services\CombinedPoPdfService;
use Carbon\Carbon;
use Illuminate\Http\Request;

trait ManagesShipmentPersistence
{
    protected function validateShipmentRequest(Request $request, ?Shipment $shipment = null): array
    {
        $rules = [
            'departure' => 'nullable|string|max:255',
            'departure_port_code' => 'nullable|string|max:255',
            'service' => 'nullable|string|max:255',
            'additional_service' => 'nullable|string|max:255',
            'preferred_shipment_date' => 'nullable|string',
            'deadline_arrival' => 'nullable|string',
            'vessel_eta' => 'nullable|string',
            'vessel_etd' => 'nullable|string',
            'pre_alert_reminder' => 'nullable|string',
            'customer_reference' => 'nullable|string|max:255',
            'consignee' => 'nullable|string|max:255',
            'consignee_address' => 'nullable|string',
            'consignee_city' => 'nullable|string|max:255',
            'consignee_district' => 'nullable|string|max:255',
            'consignee_zip' => 'nullable|string|max:255',
            'consignee_country' => 'nullable|string|max:255',
            'consignee_port_code' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'consignee_att' => 'required|string|max:255',
            'consignee_email' => 'nullable|email|max:255',
            'account_manager' => 'nullable|integer|exists:contacts,id',
            'status' => 'nullable|string|max:255',
            'repacked_items' => 'nullable|integer|min:0',
            'repacked_weight' => 'nullable|numeric|min:0',
            'special_considerations_destination' => 'nullable|string',
            'comments_departure_hub' => 'nullable|string',
            'comments_consignee' => 'nullable|string',
            'crr_ids' => 'nullable|array',
            'crr_ids.*' => 'integer|exists:crrs,id',
            'irregularities' => 'nullable|array',
            'irregularities.*.irregularity_date' => 'nullable|string',
            'irregularities.*.irregularity_type' => 'nullable|string|max:255',
            'irregularities.*.party_responsible' => 'nullable|string|max:255',
            'irregularities.*.consequence' => 'nullable|string|max:255',
            'irregularities.*.extra_cost_mt_usd' => 'nullable|numeric',
            'irregularities.*.status' => 'nullable|string|max:255',
            'irregularities.*.cause_of_irregularity' => 'nullable|string',
            'irregularities.*.action_taken' => 'nullable|string',
            'irregularities.*.customer_response' => 'nullable|string',
            'irregularities.*.hub_agent_comments' => 'nullable|string',
            'irregularities.*.handled_by' => 'nullable|string|max:255',
            'flights' => 'nullable|array',
            'flights.*.leg_reference' => 'nullable|string|max:255',
            'flights.*.flight_number' => 'nullable|string|max:255',
            'flights.*.departure_date' => 'nullable|string',
            'flights.*.arrival_date' => 'nullable|string',
            'flights.*.arrival_time' => 'nullable|string|max:5',
            'sea_legs' => 'nullable|array',
            'sea_legs.*.bill_of_lading' => 'nullable|string|max:255',
            'sea_legs.*.container_number' => 'nullable|string|max:255',
            'sea_legs.*.transport_vessel_imo' => 'nullable|string|max:255',
            'sea_legs.*.transport_vessel_name' => 'nullable|string|max:255',
            'sea_legs.*.etd' => 'nullable|string',
            'sea_legs.*.eta' => 'nullable|string',
            'sea_legs.*.arrival_time' => 'nullable|string|max:5',
            'truck_legs' => 'nullable|array',
            'truck_legs.*.cmr' => 'nullable|string|max:255',
            'truck_legs.*.freight_company' => 'nullable|string|max:255',
            'truck_legs.*.departure_date' => 'nullable|string',
            'truck_legs.*.arrival_date' => 'nullable|string',
            'truck_legs.*.arrival_time' => 'nullable|string|max:5',
            'courier_legs' => 'nullable|array',
            'courier_legs.*.airway_bill' => 'nullable|string|max:255',
            'courier_legs.*.carrier' => 'nullable|string|max:255',
            'courier_legs.*.departure_date' => 'nullable|string',
            'courier_legs.*.arrival_date' => 'nullable|string',
            'courier_legs.*.arrival_time' => 'nullable|string|max:5',
            'release_legs' => 'nullable|array',
            'release_legs.*.freight_company' => 'nullable|string|max:255',
            'release_legs.*.delivery_date' => 'nullable|string',
            'release_legs.*.delivery_time' => 'nullable|string|max:5',
            'hand_carry_legs' => 'nullable|array',
            'hand_carry_legs.*.departure_date' => 'nullable|string',
            'hand_carry_legs.*.arrival_date' => 'nullable|string',
            'hand_carry_legs.*.arrival_time' => 'nullable|string|max:5',
            'hand_carry_legs.*.contact_name' => 'nullable|string|max:255',
            'hand_carry_legs.*.contact_phone' => 'nullable|string|max:255',
            'hand_carry_legs.*.onboard_hand_carry' => 'nullable|boolean',
            'on_board_legs' => 'nullable|array',
            'on_board_legs.*.departure_date' => 'nullable|string',
            'on_board_legs.*.delivery_date' => 'nullable|string',
            'on_board_legs.*.delivery_time' => 'nullable|string|max:5',
        ];

        $validator = validator($request->all(), $rules, [], [
            'consignee_att' => 'contact person',
        ]);

        $validator->after(function ($validator) use ($request, $shipment) {
            $crrIds = array_values(array_unique($request->input('crr_ids', [])));

            $this->validateSelectableCrrsForShipment($crrIds, $validator, $shipment);
            $this->validateSingleHubForSelectedCrrs($crrIds, $validator);
        });

        return $validator->validate();
    }

    protected function validateSelectableCrrsForShipment(array $crrIds, \Illuminate\Contracts\Validation\Validator $validator, ?Shipment $shipment = null): void
    {
        if (empty($crrIds)) {
            return;
        }

        $attachedIds = $shipment
            ? $shipment->crrs()->pluck('crrs.id')->map(fn ($id) => (int) $id)->all()
            : [];
        $newCrrIds = array_values(array_diff(array_map('intval', $crrIds), $attachedIds));

        if ($newCrrIds === []) {
            return;
        }

        $invalidCount = $this->shipmentRepository->invalidSelectableCrrCount($newCrrIds);

        if ($invalidCount > 0) {
            $validator->errors()->add('crr_ids', 'In Progress, completed and cancelled stock items cannot be added to a shipment.');
        }
    }

    protected function validateSingleHubForSelectedCrrs(array $crrIds, \Illuminate\Contracts\Validation\Validator $validator): void
    {
        if (count($crrIds) <= 1) {
            return;
        }

        $hubValues = $this->shipmentRepository->selectedHubValues($crrIds);

        if ($hubValues->count() <= 1) {
            return;
        }

        $validator->errors()->add('crr_ids', 'All selected stock items must belong to the same hub.');
    }

    protected function normalizeManifestGenerationRequest(Request $request): void
    {
        // Only normalize an explicitly submitted empty value. Merging null when the
        // field is absent makes Request::has('account_manager') true in Laravel 12
        // and wipes account_manager_id on partial updates (service details / pre-alert).
        if ($request->exists('account_manager') && $request->input('account_manager') === '') {
            $request->merge(['account_manager' => null]);
        }
    }

    protected function manifestGenerationRules(): array
    {
        return [
            'departure' => 'nullable|string|max:255',
            'departure_port_code' => 'nullable|string|max:255',
            'service' => 'nullable|string|max:255',
            'additional_service' => 'nullable|string|max:255',
            'preferred_shipment_date' => 'nullable|string',
            'deadline_arrival' => 'nullable|string',
            'vessel_eta' => 'nullable|string',
            'vessel_etd' => 'nullable|string',
            'pre_alert_reminder' => 'nullable|string',
            'customer_reference' => 'nullable|string|max:255',
            'consignee' => 'nullable|string|max:255',
            'consignee_address' => 'nullable|string',
            'consignee_city' => 'nullable|string|max:255',
            'consignee_district' => 'nullable|string|max:255',
            'consignee_zip' => 'nullable|string|max:255',
            'consignee_country' => 'nullable|string|max:255',
            'consignee_port_code' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'consignee_att' => 'nullable|string|max:255',
            'consignee_email' => 'nullable|string|max:255',
            'account_manager' => 'nullable|integer|exists:contacts,id',
            'status' => 'nullable|string|max:255',
            'repacked_items' => 'nullable|integer|min:0',
            'repacked_weight' => 'nullable|numeric|min:0',
            'special_considerations_destination' => 'nullable|string',
            'comments_departure_hub' => 'nullable|string',
            'comments_consignee' => 'nullable|string',
            'not_applicable_for_consolidation' => 'nullable|boolean',
            'skip_instruction_dest' => 'nullable|boolean',
            'skip_instruction_hub' => 'nullable|boolean',
            'skip_prealert' => 'nullable|boolean',
            'project_logistics' => 'nullable|boolean',
            'port_agency' => 'nullable|boolean',
            'crr_ids' => 'nullable|array',
            'crr_ids.*' => 'integer|exists:crrs,id',
        ];
    }

    protected function preAlertMailRules(): array
    {
        return array_merge($this->manifestGenerationRules(), [
            'flights' => 'nullable|array',
            'flights.*.leg_reference' => 'nullable|string|max:255',
            'flights.*.flight_number' => 'nullable|string|max:255',
            'flights.*.departure_date' => 'nullable|string',
            'flights.*.arrival_date' => 'nullable|string',
            'flights.*.arrival_time' => 'nullable|string|max:5',
            'sea_legs' => 'nullable|array',
            'sea_legs.*.bill_of_lading' => 'nullable|string|max:255',
            'sea_legs.*.container_number' => 'nullable|string|max:255',
            'sea_legs.*.transport_vessel_imo' => 'nullable|string|max:255',
            'sea_legs.*.transport_vessel_name' => 'nullable|string|max:255',
            'sea_legs.*.etd' => 'nullable|string',
            'sea_legs.*.eta' => 'nullable|string',
            'sea_legs.*.arrival_time' => 'nullable|string|max:5',
            'truck_legs' => 'nullable|array',
            'truck_legs.*.cmr' => 'nullable|string|max:255',
            'truck_legs.*.freight_company' => 'nullable|string|max:255',
            'truck_legs.*.departure_date' => 'nullable|string',
            'truck_legs.*.arrival_date' => 'nullable|string',
            'truck_legs.*.arrival_time' => 'nullable|string|max:5',
            'courier_legs' => 'nullable|array',
            'courier_legs.*.airway_bill' => 'nullable|string|max:255',
            'courier_legs.*.carrier' => 'nullable|string|max:255',
            'courier_legs.*.departure_date' => 'nullable|string',
            'courier_legs.*.arrival_date' => 'nullable|string',
            'courier_legs.*.arrival_time' => 'nullable|string|max:5',
            'release_legs' => 'nullable|array',
            'release_legs.*.freight_company' => 'nullable|string|max:255',
            'release_legs.*.delivery_date' => 'nullable|string',
            'release_legs.*.delivery_time' => 'nullable|string|max:5',
            'hand_carry_legs' => 'nullable|array',
            'hand_carry_legs.*.departure_date' => 'nullable|string',
            'hand_carry_legs.*.arrival_date' => 'nullable|string',
            'hand_carry_legs.*.arrival_time' => 'nullable|string|max:5',
            'hand_carry_legs.*.contact_name' => 'nullable|string|max:255',
            'hand_carry_legs.*.contact_phone' => 'nullable|string|max:255',
            'hand_carry_legs.*.onboard_hand_carry' => 'nullable|boolean',
            'on_board_legs' => 'nullable|array',
            'on_board_legs.*.departure_date' => 'nullable|string',
            'on_board_legs.*.delivery_date' => 'nullable|string',
            'on_board_legs.*.delivery_time' => 'nullable|string|max:5',
        ]);
    }

    /**
     * @return array<int, int>
     */
    protected function parseMailDocumentIds(mixed $documentIds): array
    {
        if ($documentIds === null || $documentIds === '') {
            return [];
        }

        return array_values(array_unique(array_filter(array_map(
            'intval',
            is_array($documentIds) ? $documentIds : explode(',', (string) $documentIds)
        ))));
    }

    /**
     * @return array<int, string>
     */
    protected function parseMailExcludeAttachments(mixed $excludeAttachments): array
    {
        if ($excludeAttachments === null || $excludeAttachments === '') {
            return [];
        }

        $keys = is_array($excludeAttachments)
            ? $excludeAttachments
            : explode(',', (string) $excludeAttachments);

        return array_values(array_unique(array_filter(array_map(
            static fn ($key) => trim((string) $key),
            $keys
        ), static fn ($key) => $key !== '')));
    }

    /**
     * @return array<int, array{filename: string, content: string, mime: string}>
     */
    protected function collectUploadedMailAttachments(Request $request): array
    {
        $attachments = [];

        foreach ($request->file('files', []) as $file) {
            if (! $file || ! $file->isValid()) {
                continue;
            }

            $extension = strtolower((string) $file->getClientOriginalExtension());
            if (! in_array($extension, ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'webp'], true)) {
                continue;
            }

            $attachments[] = [
                'filename' => $file->getClientOriginalName(),
                'content' => (string) file_get_contents($file->getRealPath()),
                'mime' => $file->getMimeType() ?: 'application/octet-stream',
            ];
        }

        return $attachments;
    }

    /**
     * @return array<int, array{url: string, filename: string}>
     */
    protected function manifestMailAttachmentSources(Shipment $shipment, CombinedPoPdfService $combinedPoPdfService): array
    {
        $sources = [];
        $latestManifest = $shipment->latestManifest();

        if ($latestManifest) {
            $sources[] = [
                'key' => 'manifest',
                'url' => route('shipments.manifests.show', [$shipment->id, $latestManifest->id]),
                'filename' => str_replace(' ', '-', $latestManifest->displayLabel())
                    . '-' . $shipment->shipment_number . '.pdf',
            ];
        } else {
            $sources[] = [
                'key' => 'manifest',
                'url' => route('shipments.combined-manifest-documents', $shipment->id),
                'filename' => 'manifest-' . $shipment->shipment_number . '.pdf',
            ];
        }

        $sources = array_merge($sources, $this->checkedShipmentDocumentAttachmentSources($shipment));

        if ($combinedPoPdfService->documentsForShipment($shipment)->isNotEmpty()) {
            $sources[] = [
                'key' => 'combined_po',
                'url' => route('shipments.combined-po-documents', $shipment->id),
                'filename' => 'combined-po-documents-' . $shipment->shipment_number . '.pdf',
            ];
        }

        return $sources;
    }

    /**
     * @return array<int, array{url: string, filename: string}>
     */
    protected function preAlertMailAttachmentSources(Shipment $shipment, CombinedPoPdfService $combinedPoPdfService): array
    {
        $sources = [];
        $latestPreAlert = $shipment->latestPreAlert();

        if ($latestPreAlert) {
            $sources[] = [
                'key' => 'pre_alert',
                'url' => route('shipments.pre-alerts.show', [$shipment->id, $latestPreAlert->id]),
                'filename' => str_replace(' ', '-', $latestPreAlert->displayLabel())
                    . '-' . $shipment->shipment_number . '.pdf',
            ];
        }

        $sources = array_merge($sources, $this->checkedShipmentDocumentAttachmentSources($shipment));

        if ($combinedPoPdfService->documentsForShipment($shipment)->isNotEmpty()) {
            $sources[] = [
                'key' => 'combined_po',
                'url' => route('shipments.combined-po-documents', $shipment->id),
                'filename' => 'combined-po-documents-' . $shipment->shipment_number . '.pdf',
            ];
        }

        return $sources;
    }

    /**
     * @return array<int, array{key: string, url: string, filename: string, document_id: int}>
     */
    protected function checkedShipmentDocumentAttachmentSources(Shipment $shipment): array
    {
        $sources = [];

        foreach ($shipment->documents as $document) {
            if (! $document->is_internal) {
                continue;
            }

            $sources[] = [
                'key' => 'document-' . $document->id,
                'url' => $document->fileUrl(),
                'filename' => $document->file_name,
                'document_id' => $document->id,
            ];
        }

        return $sources;
    }

    protected function manifestToArray(ShipmentManifest $manifest): array
    {
        return [
            'id' => $manifest->id,
            'version' => $manifest->version,
            'file_name' => $manifest->file_name,
            'display_label' => $manifest->displayLabel(),
            'file_url' => route('shipments.manifests.show', [$manifest->shipment_id, $manifest->id]),
            'date' => $manifest->created_at->format('d.m.Y'),
            'time' => $manifest->created_at->format('H:i'),
        ];
    }

    protected function preAlertToArray(ShipmentPreAlert $preAlert): array
    {
        return [
            'id' => $preAlert->id,
            'version' => $preAlert->version,
            'file_name' => $preAlert->file_name,
            'display_label' => $preAlert->displayLabel(),
            'file_url' => route('shipments.pre-alerts.show', [$preAlert->shipment_id, $preAlert->id]),
            'date' => $preAlert->created_at->format('d.m.Y'),
            'time' => $preAlert->created_at->format('H:i'),
        ];
    }

    protected function shipmentDocumentCount(Shipment $shipment, CombinedPoPdfService $combinedPoPdfService): int
    {
        $combinedPoCount = $combinedPoPdfService->documentsForShipment($shipment)->isNotEmpty() ? 1 : 0;

        return $combinedPoCount
            + $shipment->manifests()->count()
            + $shipment->preAlerts()->count()
            + $shipment->documents()->count();
    }

    protected function buildShipmentAttributes(Request $request, array $validated, bool $onlyPresent = false): array
    {
        $attributes = [
            'departure' => $validated['departure'] ?? null,
            'departure_port_code' => $validated['departure_port_code'] ?? null,
            'service' => $validated['service'] ?? null,
            'additional_service' => $validated['additional_service'] ?? null,
            'preferred_shipment_date' => $this->parseDate($validated['preferred_shipment_date'] ?? null),
            'deadline_arrival' => $this->parseDate($validated['deadline_arrival'] ?? null),
            'vessel_eta' => $this->parseDate($validated['vessel_eta'] ?? null),
            'vessel_etd' => $this->parseDate($validated['vessel_etd'] ?? null),
            'pre_alert_reminder' => $this->parseDate($validated['pre_alert_reminder'] ?? null),
            'customer_reference' => $validated['customer_reference'] ?? null,
            'not_applicable_for_consolidation' => $request->boolean('not_applicable_for_consolidation'),
            'consignee' => $validated['consignee'] ?? null,
            'consignee_address' => $validated['consignee_address'] ?? null,
            'consignee_city' => $validated['consignee_city'] ?? null,
            'consignee_district' => $validated['consignee_district'] ?? null,
            'consignee_zip' => $validated['consignee_zip'] ?? null,
            'consignee_country' => $validated['consignee_country'] ?? null,
            'consignee_port_code' => $validated['consignee_port_code'] ?? null,
            'location' => $validated['location'] ?? null,
            'consignee_att' => $validated['consignee_att'] ?? null,
            'consignee_email' => $validated['consignee_email'] ?? null,
            'account_manager_id' => !empty($validated['account_manager']) ? (int) $validated['account_manager'] : null,
            'special_considerations_destination' => $validated['special_considerations_destination'] ?? null,
            'skip_instruction_dest' => $request->boolean('skip_instruction_dest'),
            'comments_departure_hub' => $validated['comments_departure_hub'] ?? null,
            'skip_instruction_hub' => $request->boolean('skip_instruction_hub'),
            'comments_consignee' => $validated['comments_consignee'] ?? null,
            'skip_prealert' => $request->boolean('skip_prealert'),
            'project_logistics' => $request->boolean('project_logistics'),
            'port_agency' => $request->boolean('port_agency'),
            'status' => $validated['status'] ?? $request->input('status', 'In process'),
            'repacked_items' => array_key_exists('repacked_items', $validated)
                ? ($validated['repacked_items'] !== null ? (int) $validated['repacked_items'] : null)
                : null,
            'repacked_weight' => array_key_exists('repacked_weight', $validated)
                ? ($validated['repacked_weight'] !== null ? (float) $validated['repacked_weight'] : null)
                : null,
        ];

        if (!$onlyPresent) {
            return $attributes;
        }

        $booleanFields = [
            'not_applicable_for_consolidation',
            'skip_instruction_dest',
            'skip_instruction_hub',
            'skip_prealert',
            'project_logistics',
            'port_agency',
        ];

        $requestKeyMap = [
            'account_manager_id' => 'account_manager',
        ];

        return collect($attributes)->filter(function ($value, $key) use ($request, $booleanFields, $requestKeyMap) {
            if (in_array($key, $booleanFields, true)) {
                return true;
            }

            $requestKey = $requestKeyMap[$key] ?? $key;

            // Preserve existing account manager unless a real ID was posted.
            if ($key === 'account_manager_id') {
                return $request->filled('account_manager');
            }

            return $request->has($requestKey);
        })->all();
    }

    protected function syncCrrInternalShipments(Shipment $shipment, array $crrIds, ?array $previousCrrIds = null): void
    {
        if (!empty($crrIds)) {
            $this->shipmentRepository->updateCrrStatuses($crrIds, [
                'internal_shipment' => $shipment->shipment_number,
                'status' => Crr::STATUS_IN_PROGRESS,
            ]);
        }

        if ($previousCrrIds === null) {
            return;
        }

        $removedCrrIds = array_diff($previousCrrIds, $crrIds);
        if (empty($removedCrrIds)) {
            return;
        }

        $this->shipmentRepository->updateCrrStatusesForShipmentNumber($removedCrrIds, $shipment->shipment_number, [
            'internal_shipment' => null,
            'status' => Crr::STATUS_ACTIVE,
        ]);
    }

    protected function syncIrregularities(Shipment $shipment, array $irregularities): void
    {
        $rows = [];

        foreach ($irregularities as $irregularity) {
            if (!$this->irregularityHasData($irregularity)) {
                continue;
            }

            $rows[] = [
                'shipment_id' => $shipment->id,
                'irregularity_date' => $this->parseDate($irregularity['irregularity_date'] ?? null),
                'irregularity_type' => $irregularity['irregularity_type'] ?? null,
                'party_responsible' => $irregularity['party_responsible'] ?? null,
                'consequence' => $irregularity['consequence'] ?? null,
                'extra_cost_mt_usd' => $irregularity['extra_cost_mt_usd'] ?? null,
                'status' => $irregularity['status'] ?? null,
                'cause_of_irregularity' => $irregularity['cause_of_irregularity'] ?? null,
                'action_taken' => $irregularity['action_taken'] ?? null,
                'customer_response' => $irregularity['customer_response'] ?? null,
                'hub_agent_comments' => $irregularity['hub_agent_comments'] ?? null,
                'handled_by' => $irregularity['handled_by'] ?? null,
            ];
        }

        $this->shipmentRepository->replaceIrregularities($shipment, $rows);
    }

    protected function syncFlights(Shipment $shipment, array $flights, ?string $service): void
    {
        if ($service !== 'Airfreight') {
            $this->shipmentRepository->replaceFlights($shipment, []);
            return;
        }

        $rows = [];
        foreach ($flights as $index => $flight) {
            if (!$this->flightHasData($flight)) {
                continue;
            }

            $rows[] = [
                'shipment_id' => $shipment->id,
                'leg_reference' => $flight['leg_reference'] ?? null,
                'flight_number' => $flight['flight_number'] ?? null,
                'departure_date' => $this->parseDate($flight['departure_date'] ?? null),
                'arrival_date' => $this->parseDate($flight['arrival_date'] ?? null),
                'arrival_time' => $this->parseArrivalTime($flight['arrival_time'] ?? null),
                'sort_order' => $index,
            ];
        }

        $this->shipmentRepository->replaceFlights($shipment, $rows);
    }

    protected function syncSeaLegs(Shipment $shipment, array $seaLegs, ?string $service): void
    {
        if ($service !== 'Sea freight') {
            $this->shipmentRepository->replaceSeaLegs($shipment, []);
            return;
        }

        $rows = [];
        foreach ($seaLegs as $index => $leg) {
            if (!$this->seaLegHasData($leg)) {
                continue;
            }

            $rows[] = [
                'shipment_id' => $shipment->id,
                'bill_of_lading' => $leg['bill_of_lading'] ?? null,
                'container_number' => $leg['container_number'] ?? null,
                'transport_vessel_imo' => $leg['transport_vessel_imo'] ?? null,
                'transport_vessel_name' => $leg['transport_vessel_name'] ?? null,
                'etd' => $this->parseDate($leg['etd'] ?? null),
                'eta' => $this->parseDate($leg['eta'] ?? null),
                'arrival_time' => $this->parseArrivalTime($leg['arrival_time'] ?? null),
                'sort_order' => $index,
            ];
        }

        $this->shipmentRepository->replaceSeaLegs($shipment, $rows);
    }

    protected function seaLegHasData(array $leg): bool
    {
        foreach (['bill_of_lading', 'container_number', 'transport_vessel_imo', 'transport_vessel_name', 'etd', 'eta', 'arrival_time'] as $field) {
            if (!empty($leg[$field])) {
                return true;
            }
        }

        return false;
    }

    protected function syncTruckLegs(Shipment $shipment, array $truckLegs, ?string $service): void
    {
        if ($service !== 'Truck') {
            $this->shipmentRepository->replaceTruckLegs($shipment, []);
            return;
        }

        $rows = [];
        foreach ($truckLegs as $index => $leg) {
            if (!$this->truckLegHasData($leg)) {
                continue;
            }

            $rows[] = [
                'shipment_id' => $shipment->id,
                'cmr' => $leg['cmr'] ?? null,
                'freight_company' => $leg['freight_company'] ?? null,
                'departure_date' => $this->parseDate($leg['departure_date'] ?? null),
                'arrival_date' => $this->parseDate($leg['arrival_date'] ?? null),
                'arrival_time' => $this->parseArrivalTime($leg['arrival_time'] ?? null),
                'sort_order' => $index,
            ];
        }

        $this->shipmentRepository->replaceTruckLegs($shipment, $rows);
    }

    protected function truckLegHasData(array $leg): bool
    {
        foreach (['cmr', 'freight_company', 'departure_date', 'arrival_date', 'arrival_time'] as $field) {
            if (!empty($leg[$field])) {
                return true;
            }
        }

        return false;
    }

    protected function syncCourierLegs(Shipment $shipment, array $courierLegs, ?string $service): void
    {
        if ($service !== 'Courier') {
            $this->shipmentRepository->replaceCourierLegs($shipment, []);
            return;
        }

        $rows = [];
        foreach ($courierLegs as $index => $leg) {
            if (!$this->courierLegHasData($leg)) {
                continue;
            }

            $rows[] = [
                'shipment_id' => $shipment->id,
                'airway_bill' => $leg['airway_bill'] ?? null,
                'carrier' => $leg['carrier'] ?? null,
                'departure_date' => $this->parseDate($leg['departure_date'] ?? null),
                'arrival_date' => $this->parseDate($leg['arrival_date'] ?? null),
                'arrival_time' => $this->parseArrivalTime($leg['arrival_time'] ?? null),
                'sort_order' => $index,
            ];
        }

        $this->shipmentRepository->replaceCourierLegs($shipment, $rows);
    }

    protected function courierLegHasData(array $leg): bool
    {
        foreach (['airway_bill', 'carrier', 'departure_date', 'arrival_date', 'arrival_time'] as $field) {
            if (!empty($leg[$field])) {
                return true;
            }
        }

        return false;
    }

    protected function syncReleaseLegs(Shipment $shipment, array $releaseLegs, ?string $service): void
    {
        if ($service !== 'Release') {
            $this->shipmentRepository->replaceReleaseLegs($shipment, []);
            return;
        }

        $rows = [];
        foreach ($releaseLegs as $index => $leg) {
            if (!$this->releaseLegHasData($leg)) {
                continue;
            }

            $rows[] = [
                'shipment_id' => $shipment->id,
                'freight_company' => $leg['freight_company'] ?? null,
                'delivery_date' => $this->parseDate($leg['delivery_date'] ?? null),
                'delivery_time' => $this->parseArrivalTime($leg['delivery_time'] ?? null),
                'sort_order' => $index,
            ];
        }

        $this->shipmentRepository->replaceReleaseLegs($shipment, $rows);
    }

    protected function releaseLegHasData(array $leg): bool
    {
        foreach (['freight_company', 'delivery_date', 'delivery_time'] as $field) {
            if (!empty($leg[$field])) {
                return true;
            }
        }

        return false;
    }

    protected function syncOnBoardLegs(Shipment $shipment, array $onBoardLegs, ?string $service): void
    {
        if ($service !== 'On-board delivery') {
            $this->shipmentRepository->replaceOnBoardLegs($shipment, []);
            return;
        }

        $rows = [];
        foreach ($onBoardLegs as $index => $leg) {
            if (!$this->onBoardLegHasData($leg)) {
                continue;
            }

            $rows[] = [
                'shipment_id' => $shipment->id,
                'departure_date' => $this->parseDate($leg['departure_date'] ?? null),
                'delivery_date' => $this->parseDate($leg['delivery_date'] ?? null),
                'delivery_time' => $this->parseArrivalTime($leg['delivery_time'] ?? null),
                'sort_order' => $index,
            ];
        }

        $this->shipmentRepository->replaceOnBoardLegs($shipment, $rows);
    }

    protected function onBoardLegHasData(array $leg): bool
    {
        foreach (['departure_date', 'delivery_date', 'delivery_time'] as $field) {
            if (!empty($leg[$field])) {
                return true;
            }
        }

        return false;
    }

    protected function syncHandCarryLegs(Shipment $shipment, array $handCarryLegs, ?string $service): void
    {
        if ($service !== 'Hand Carry') {
            $this->shipmentRepository->replaceHandCarryLegs($shipment, []);
            return;
        }

        $rows = [];
        foreach ($handCarryLegs as $index => $leg) {
            if (!$this->handCarryLegHasData($leg)) {
                continue;
            }

            $rows[] = [
                'shipment_id' => $shipment->id,
                'departure_date' => $this->parseDate($leg['departure_date'] ?? null),
                'arrival_date' => $this->parseDate($leg['arrival_date'] ?? null),
                'arrival_time' => $this->parseArrivalTime($leg['arrival_time'] ?? null),
                'contact_name' => $leg['contact_name'] ?? null,
                'contact_phone' => $leg['contact_phone'] ?? null,
                'onboard_hand_carry' => !empty($leg['onboard_hand_carry']),
                'sort_order' => $index,
            ];
        }

        $this->shipmentRepository->replaceHandCarryLegs($shipment, $rows);
    }

    protected function handCarryLegHasData(array $leg): bool
    {
        if (!empty($leg['onboard_hand_carry'])) {
            return true;
        }

        foreach (['departure_date', 'arrival_date', 'arrival_time', 'contact_name', 'contact_phone'] as $field) {
            if (!empty($leg[$field])) {
                return true;
            }
        }

        return false;
    }

    protected function flightHasData(array $flight): bool
    {
        foreach (['leg_reference', 'flight_number', 'departure_date', 'arrival_date', 'arrival_time'] as $field) {
            if (!empty($flight[$field])) {
                return true;
            }
        }

        return false;
    }

    protected function parseArrivalTime(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        $value = trim($value);

        if (preg_match('/^\d{1,2}:\d{2}$/', $value)) {
            [$hours, $minutes] = explode(':', $value);

            return sprintf('%02d:%02d', (int) $hours, (int) $minutes);
        }

        return $value;
    }

    protected function irregularityFormOptions(): array
    {
        return [
            'irregularityTypeOptions' => [
                'Customer complaint',
                'Shipment missing (and found)',
                'Delayed shipment',
                'Damage to shipment',
                'Incorrect or missing shipping documentation',
                'Cross label',
                'Shipment short shipped',
                'Shipment misrouted',
                'Slow or unclear communication by agent',
                'Quotation unclear or incomplete',
                'Other',
                'No cost provided by agent',
                'Send pre-alert in wrong format',
                'Billing discrepancy',
            ],
            'partyResponsibleOptions' => [
                'Marinetrans',
                'Departing Hub',
                'Receiving Agent',
                'Customer',
                'Carrier',
            ],
            'consequenceOptions' => [
                'Deadline and delivery met',
                'Original deadline missed, but vessel/destination reached',
                'Deadline and vessel missed',
                'Official customer claim',
            ],
            'statusOptions' => [
                'Not started',
                'In process',
                'Closed',
            ],
        ];
    }

    protected function parseDate(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        try {
            return Carbon::createFromFormat('d.m.Y', $value)->format('Y-m-d');
        } catch (\Exception $e) {
            try {
                return Carbon::parse($value)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }
    }

    protected function generateShipmentNumber(): string
    {
        $userName = (string) (auth()->user()?->name ?? '');
        $prefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $userName) ?? '', 0, 3));

        if (strlen($prefix) < 3) {
            $prefix = str_pad($prefix, 3, 'X');
        }

        $random = str_pad((string) random_int(0, 99999), 5, '0', STR_PAD_LEFT);
        $monthYear = now()->format('my');

        return $prefix . '-' . $random . '-' . $monthYear;
    }

    protected function irregularityHasData(array $irregularity): bool
    {
        foreach ($irregularity as $value) {
            if ($value !== null && $value !== '') {
                return true;
            }
        }

        return false;
    }
}
