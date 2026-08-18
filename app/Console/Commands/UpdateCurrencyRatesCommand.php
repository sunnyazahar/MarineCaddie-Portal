<?php

namespace App\Console\Commands;

use App\Services\CurrencyRateService;
use Illuminate\Console\Command;
use RuntimeException;
use Throwable;

class UpdateCurrencyRatesCommand extends Command
{
    protected $signature = 'currency:update-rates';

    protected $description = 'Fetch latest USD exchange rates and update country currency values';

    public function handle(CurrencyRateService $rates): int
    {
        try {
            $result = $rates->updateFromUsd();
        } catch (RuntimeException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        } catch (Throwable $e) {
            $this->error('An error occurred: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->info("Successfully updated {$result['updated']} currency rates.");

        if ($result['last_update']) {
            $this->line('Last API update: '.$result['last_update']);
        }

        return self::SUCCESS;
    }
}
