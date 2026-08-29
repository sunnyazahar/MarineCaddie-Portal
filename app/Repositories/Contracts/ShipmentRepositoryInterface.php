<?php

namespace App\Repositories\Contracts;

use App\Models\Shipment;
use App\Models\ShipmentDocument;
use App\Models\ShipmentManifest;
use App\Models\ShipmentPreAlert;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

interface ShipmentRepositoryInterface
{
    public function buildIndexQuery(array $filters): Builder;

    public function paginateIndex(array $filters, int $perPage): LengthAwarePaginator;

    public function indexFilterOptions(): array;

    public function buildPreAlertRemindersQuery(array $filters): Builder;

    public function paginatePreAlertReminders(array $filters, int $perPage): LengthAwarePaginator;

    public function buildShipmentFollowUpQuery(array $filters): Builder;

    public function paginateShipmentFollowUp(array $filters, int $perPage): LengthAwarePaginator;

    public function buildCostFollowUpSearchQuery(array $filters): Builder;

    public function followUpFilterOptions(string $scope): array;

    public function findOrFail(int $id): Shipment;

    public function findWithRelationsOrFail(int $id, array $relations = []): Shipment;

    public function shipmentNumberExists(string $shipmentNumber): bool;

    public function searchByShipmentNumber(string $q, int $limit = 40): EloquentCollection;

    public function findByShipmentNumberLookup(string $q): ?Shipment;

    public function createShipment(array $attributes): Shipment;

    public function createPreAlertReminderSend(int $shipmentId, ?int $userId): void;

    public function selectableCrrsForShipment(): EloquentCollection;

    public function shipmentEditReferenceData(): array;

    public function replaceIrregularities(Shipment $shipment, array $irregularities): void;

    public function replaceFlights(Shipment $shipment, array $flights): void;

    public function replaceSeaLegs(Shipment $shipment, array $seaLegs): void;

    public function replaceTruckLegs(Shipment $shipment, array $truckLegs): void;

    public function replaceCourierLegs(Shipment $shipment, array $courierLegs): void;

    public function replaceReleaseLegs(Shipment $shipment, array $releaseLegs): void;

    public function replaceOnBoardLegs(Shipment $shipment, array $onBoardLegs): void;

    public function replaceHandCarryLegs(Shipment $shipment, array $handCarryLegs): void;

    public function invalidSelectableCrrCount(array $crrIds): int;

    public function selectedHubValues(array $crrIds): Collection;

    public function updateCrrStatuses(array $crrIds, array $attributes): int;

    public function updateCrrStatusesForShipmentNumber(array $crrIds, string $shipmentNumber, array $attributes): int;

    public function adminUserIds(): Collection;

    public function findManifestForShipmentOrFail(int $shipmentId, int $manifestId, bool $withShipment = false): ShipmentManifest;

    public function findPreAlertForShipmentOrFail(int $shipmentId, int $preAlertId, bool $withShipment = false): ShipmentPreAlert;

    public function createDocument(array $attributes): ShipmentDocument;

    public function findDocumentOrFail(int $docId): ShipmentDocument;

    public function findDocumentForShipmentOrFail(int $shipmentId, int $docId): ShipmentDocument;
}
