<?php

namespace App\Services;

use App\Support\Money;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CurrencyService
{
    /**
     * Frankfurter (ECB reference rates, no API key). api.frankfurter.app
     * now 301-redirects here.
     */
    public const FRANKFURTER_URL = 'https://api.frankfurter.dev/v1/';

    /**
     * ExchangeRate-API open endpoint (no API key, latest rates only, ~160
     * currencies). Used for currencies the ECB does not publish.
     */
    public const OPEN_ER_URL = 'https://open.er-api.com/v6/latest/';

    /**
     * Exchange rate from $currency to the base currency (default: the
     * configured APP_BASE_CURRENCY) on $date.
     *
     * Returns null when no source can provide a rate, so callers never store
     * an invented 1:1 conversion (500 JPY recorded as $500).
     */
    public function getHistoricalRate($currency, $date, ?string $base = null): ?float
    {
        $base = strtoupper($base ?: Money::base());
        $currency = strtoupper(trim((string) $currency));

        if ($currency === '' || $currency === $base) {
            return 1.0;
        }

        // Frankfurter expects YYYY-MM-DD
        $date = date('Y-m-d', strtotime($date));

        // Historical rate for the send date, then latest
        foreach ([$date, 'latest'] as $when) {
            try {
                $response = Http::timeout(10)->get(self::FRANKFURTER_URL.$when, [
                    'from' => $currency,
                    'to' => $base,
                ]);

                if ($response->successful() && isset($response->json()['rates'][$base])) {
                    return (float) $response->json()['rates'][$base];
                }
            } catch (\Exception $e) {
                Log::warning('Frankfurter API failed for '.$currency.'->'.$base.' on '.$when.': '.$e->getMessage());
            }
        }

        // Final fallback: open.er-api.com (latest only)
        try {
            $response = Http::timeout(10)->get(self::OPEN_ER_URL.$currency);

            if ($response->successful() && isset($response->json()['rates'][$base])) {
                return (float) $response->json()['rates'][$base];
            }
        } catch (\Exception $e) {
            Log::warning('open.er-api failed for '.$currency.'->'.$base.': '.$e->getMessage());
        }

        Log::error('All exchange rate APIs failed for '.$currency.'->'.$base.' on '.$date);

        return null;
    }
}
