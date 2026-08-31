<?php

namespace App\Services;

use App\Mail\CancelledShipmentMail;
use App\Models\Contact;
use App\Models\Shipment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CancelledShipmentMailService
{
    public function __construct(
        private ShipmentManifestPdfBuilder $manifestPdfBuilder,
        private ShipmentPreAlertPdfBuilder $preAlertPdfBuilder,
    ) {}

    public function notify(Shipment $shipment): bool
    {
        $shipment->loadMissing([
            'accountManager.office',
            'crrs.packages',
            'flights',
            'seaLegs',
            'truckLegs',
            'courierLegs',
            'releaseLegs',
            'handCarryLegs',
            'onBoardLegs',
        ]);

        $partyNames = Shipment::batchResolvePartyNames(collect([$shipment]));
        $departureParty = $this->manifestPdfBuilder->resolvePartyContact($shipment->departure, $partyNames);
        $departureEmail = trim((string) ($departureParty['email'] ?? ''));

        if ($departureEmail === '' || ! filter_var($departureEmail, FILTER_VALIDATE_EMAIL)) {
            Log::info('Cancelled shipment email skipped: no departure party email', [
                'shipment_id' => $shipment->id,
                'shipment_number' => $shipment->shipment_number,
            ]);

            return false;
        }

        $accountManager = $this->resolveDepartureAccountManager($shipment);
        $departurePartyName = trim((string) ($departureParty['name'] ?? ''));
        $subject = $this->buildSubject($shipment);
        $body = $this->buildBody($shipment, $departurePartyName, $accountManager);
        $ccAddresses = $this->buildCcAddresses($accountManager, $departureEmail);

        try {
            Mail::send(new CancelledShipmentMail(
                $subject,
                $body,
                $accountManager,
                $departureEmail,
                $departurePartyName,
                $ccAddresses,
            ));

            return true;
        } catch (\Throwable $e) {
            Log::warning('Cancelled shipment email failed: ' . $e->getMessage(), [
                'shipment_id' => $shipment->id,
                'shipment_number' => $shipment->shipment_number,
                'email' => $departureEmail,
            ]);

            return false;
        }
    }

    public function resolveDepartureAccountManager(Shipment $shipment): ?Contact
    {
        $shipment->loadMissing(['accountManager.office']);

        $contact = $shipment->accountManager;

        return $contact instanceof Contact ? $contact : null;
    }

    private function buildSubject(Shipment $shipment): string
    {
        $fromCity = $this->manifestPdfBuilder->formatPortCity($shipment->departure_port_code);
        $toCity = $this->manifestPdfBuilder->formatPortCity(
            $shipment->consignee_port_code,
            $shipment->consignee_city,
        );
        $service = trim((string) ($shipment->service ?: '—'));

        return sprintf(
            'Cancelled shipment %s/ From %s to %s/ %s',
            $shipment->shipment_number,
            $fromCity !== '—' ? $fromCity : '—',
            $toCity !== '—' ? $toCity : '—',
            $service !== '' ? $service : '—',
        );
    }

  /**
     * @return array<int, array{name?: string, email: string}>
     */
    private function buildCcAddresses(?Contact $accountManager, string $departureEmail): array
    {
        $accountManagerEmail = trim((string) ($accountManager?->email ?? ''));

        if ($accountManagerEmail === ''
            || ! filter_var($accountManagerEmail, FILTER_VALIDATE_EMAIL)
            || strcasecmp($accountManagerEmail, $departureEmail) === 0) {
            return [];
        }

        return [[
            'name' => $accountManager->name ?? '',
            'email' => $accountManagerEmail,
        ]];
    }

    private function buildBody(
        Shipment $shipment,
        string $departurePartyName,
        ?Contact $accountManager,
    ): string {
        $portCode = trim((string) ($shipment->departure_port_code ?: ''));
        $city = $this->manifestPdfBuilder->formatPortCity($shipment->departure_port_code);
        $departureLocation = collect([$portCode, $city !== '—' ? $city : null])
            ->filter()
            ->implode(', ');

        $serviceDetailLines = $this->preAlertPdfBuilder->reminderMailServiceDetailLines($shipment);
        $vesselAndServiceLines = array_slice($serviceDetailLines, 0, 2);
        $legDetailLines = array_slice($serviceDetailLines, 2);

        $packages = $shipment->crrs->flatMap(fn ($crr) => $crr->packages);
        $totalPackages = $packages->count();
        $totalWeight = round((float) $packages->sum('weight'), 2);
        if (filled($shipment->repacked_items)) {
            $totalPackages = (int) $shipment->repacked_items;
        }
        if (filled($shipment->repacked_weight)) {
            $totalWeight = (float) $shipment->repacked_weight;
        }

        $fromLabel = $this->manifestPdfBuilder->formatPortCodeCityCountry($shipment->departure_port_code);
        $toLabel = $this->manifestPdfBuilder->formatPortCodeCityCountry(
            $shipment->consignee_port_code,
            $shipment->consignee_city,
            $shipment->consignee_country
        );

        $lines = [
            'Good day,',
            '',
            'This is to notify ' . ($departurePartyName !== '' ? $departurePartyName : 'Sir/Madam')
                . ', regarding shipment from ' . ($departureLocation !== '' ? $departureLocation : '—') . '.',
            '',
            'The shipment has been cancelled.',
            '',
        ];

        array_push($lines, ...$vesselAndServiceLines);
        $lines[] = '';
        $lines[] = 'From: ' . $fromLabel;
        $lines[] = 'To: ' . $toLabel;
        $lines[] = '';

        if ($legDetailLines !== []) {
            array_push($lines, ...$legDetailLines);
            $lines[] = '';
        }

        $lines[] = '';
        $lines[] = 'Total pieces: ' . $totalPackages;
        $lines[] = 'Total weight: ' . rtrim(rtrim(number_format($totalWeight, 2, '.', ''), '0'), '.') . ' kg';
        $lines[] = '';
        $lines[] = '';
        $lines[] = 'Best regards,';
        $lines[] = '';
        $lines[] = trim((string) ($accountManager?->name ?: 'Account Manager'));
        $phone = trim((string) ($accountManager?->phone_number ?? ''));
        if ($phone !== '') {
            $lines[] = $phone;
        }
        $lines[] = '';
        $lines[] = 'Marinecaddie';

        return implode("\r\n", $lines);
    }
}
