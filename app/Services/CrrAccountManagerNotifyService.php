<?php

namespace App\Services;

use App\Mail\CrrStockChangedMail;
use App\Models\Contact;
use App\Models\Crr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CrrAccountManagerNotifyService
{
    /**
     * Email the vessel customer's account manager with the list of stock changes.
     * Never used for first-time stock creation — only subsequent edits.
     *
     * @param  array<int, array{title: string, description: ?string}>  $changes
     */
    public function notifyOfChanges(Crr $crr, array $changes): bool
    {
        // Defensive: creation-only changelog entries must never trigger AM mail.
        $changes = array_values(array_filter(
            $changes,
            fn (array $change) => ($change['title'] ?? '') !== 'Stock item created'
        ));

        if ($changes === []) {
            return false;
        }

        $crr->loadMissing([
            'customerVessel.customer.responsible.accountManager',
            'shipments',
        ]);

        $accountManager = $this->resolveAccountManager($crr);
        $email = trim((string) ($accountManager?->email ?? ''));

        if ($accountManager === null || $email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Log::info('Stock change email skipped: no account manager email', [
                'crr_id' => $crr->id,
                'stock_number' => $crr->stock_number,
                'vessel_name' => $crr->vessel_name,
            ]);

            return false;
        }

        $accountManagerName = trim((string) ($accountManager->name ?: 'Account Manager'));
        if ($accountManagerName === '') {
            $accountManagerName = 'Account Manager';
        }

        $user = auth()->user();
        $changedByName = trim((string) ($user?->name ?: ''));
        if ($changedByName === '') {
            $changedByName = trim((string) ($user?->email ?: 'Unknown user'));
        }

        $shipmentNumbers = $crr->shipments
            ->pluck('shipment_number')
            ->filter(fn ($number) => filled($number))
            ->unique()
            ->values()
            ->all();

        try {
            Mail::to($email)->send(new CrrStockChangedMail(
                $crr,
                $changes,
                $accountManagerName,
                $changedByName,
                $shipmentNumbers,
            ));

            return true;
        } catch (\Throwable $e) {
            Log::warning('Stock change email failed: ' . $e->getMessage(), [
                'crr_id' => $crr->id,
                'stock_number' => $crr->stock_number,
                'email' => $email,
            ]);

            return false;
        }
    }

    public function resolveAccountManager(Crr $crr): ?Contact
    {
        $crr->loadMissing([
            'customerVessel.customer.responsible.accountManager',
        ]);

        $contact = $crr->customerVessel?->customer?->responsible?->accountManager;

        return $contact instanceof Contact ? $contact : null;
    }
}
