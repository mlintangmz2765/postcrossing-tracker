<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CurrencyService
{
    /**
     * Get historical exchange rate from a currency to IDR on a specific date.
     */
    public function getHistoricalRate($currency, $date)
    {
        if (! $currency || $currency === 'IDR') {
            return 1.0;
        }

        // Frankfurter API expects YYYY-MM-DD
        $date = date('Y-m-d', strtotime($date));

        // Try Frankfurter API first
        try {
            $response = Http::timeout(10)->get('https://api.frankfurter.app/'.$date, [
                'from' => $currency,
                'to' => 'IDR',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['rates']['IDR'])) {
                    return (float) $data['rates']['IDR'];
                }
            }
        } catch (\Exception $e) {
            Log::warning('Frankfurter API failed for '.$currency.' on '.$date.': '.$e->getMessage());
        }

        // Fallback to latest Frankfurter
        try {
            $response = Http::timeout(10)->get('https://api.frankfurter.app/latest', [
                'from' => $currency,
                'to' => 'IDR',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['rates']['IDR'])) {
                    return (float) $data['rates']['IDR'];
                }
            }
        } catch (\Exception $e) {
            Log::warning('Frankfurter Latest API failed for '.$currency.': '.$e->getMessage());
        }

        // Final Fallback: Open ER API (Latest only)
        try {
            $response = Http::timeout(10)->get('https://open.er-api.com/v6/latest/'.$currency);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['rates']['IDR'])) {
                    return (float) $data['rates']['IDR'];
                }
            }
        } catch (\Exception $e) {
            Log::error('All exchange rate APIs failed for '.$currency.': '.$e->getMessage());
        }

        return 1.0; // Default fallback
    }
}
