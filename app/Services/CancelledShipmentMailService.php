<?php

namespace App\Services;

use App\Mail\CancelledShipmentMail;
use App\Models\Agent;
use App\Models\Contact;
use App\Models\Hub;
use App\Models\Office;
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

        $accountManager = $this->resolveDepartureAccountManager($shipment);
        $email = trim((string) ($accountManager?->email ?? ''));

        if ($accountManager === null || $email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Log::info('Cancelled shipment email skipped: no departure account manager email', [
                'shipment_id' => $shipment->id,
                'shipment_number' => $shipment->shipment_number,
            ]);

            return false;
        }

        $partyNames = Shipment::batchResolvePartyNames(collect([$shipment]));
        $departurePartyName = $this->resolveDeparturePartyName($shipment, $partyNames);
        $subject = $this->buildSubject($shipment);
        $body = $this->buildBody($shipment, $departurePartyName, $accountManager);

        try {
            Mail::to($email)->send(new CancelledShipmentMail(
                $subject,
                $body,
                $accountManager,
            ));

            return true;
        } catch (\Throwable $e) {
            Log::warning('Cancelled shipment email failed: ' . $e->getMessage(), [
                'shipment_id' => $shipment->id,
                'shipment_number' => $shipment->shipment_number,
                'email' => $email,
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
        $portCode = trim((string) ($shipment->departure_port_code ?: ''));
        $service = trim((string) ($shipment->service ?: '—'));

        return sprintf(
            'Cancelled shipment %s / %s / %s',
            $shipment->shipment_number,
            $portCode !== '' ? $portCode : '—',
            $service !== '' ? $service : '—',
        );
    }

    private function buildBody(
        Shipment $shipment,
        string $departurePartyName,
        Contact $accountManager,
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
        $lines[] = trim((string) ($accountManager->name ?: 'Account Manager'));
        $phone = trim((string) ($accountManager->phone_number ?? ''));
        if ($phone !== '') {
            $lines[] = $phone;
        }
        $lines[] = '';
        $lines[] = 'Marinecaddie';

        return implode("\r\n", $lines);
    }

    /**
     * @param  array<string, string>  $partyNames
     */
    private function resolveDeparturePartyName(Shipment $shipment, array $partyNames): string
    {
        $composite = $shipment->departure;

        if (! $composite) {
            return '';
        }

        if (! str_contains($composite, ':')) {
            return $partyNames[$composite] ?? $composite;
        }

        [$type, $id] = explode(':', $composite, 2);
        $id = (int) $id;

        return match ($type) {
            'agent' => Agent::find($id)?->agent_name ?? ($partyNames[$composite] ?? ''),
            'hub' => Hub::find($id)?->hub_name ?? ($partyNames[$composite] ?? ''),
            'office' => Office::find($id)?->office_name ?? ($partyNames[$composite] ?? ''),
            default => $partyNames[$composite] ?? $composite,
        };
    }
}
