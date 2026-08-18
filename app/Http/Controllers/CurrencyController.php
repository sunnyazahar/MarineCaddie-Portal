<?php

namespace App\Http\Controllers;

use App\Services\CurrencyRateService;
use RuntimeException;
use Throwable;

class CurrencyController extends Controller
{
    public function updateRates(CurrencyRateService $rates)
    {
        try {
            $result = $rates->updateFromUsd();

            return response()->json([
                'status' => 'success',
                'message' => "Successfully updated {$result['updated']} currency rates.",
                'last_update' => $result['last_update'],
            ]);
        } catch (RuntimeException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred: '.$e->getMessage(),
            ], 500);
        }
    }
}
