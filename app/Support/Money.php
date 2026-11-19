<?php

namespace App\Support;

/**
 * Currency-aware formatting and rounding driven by config/currency.php.
 *
 * Deliberately dependency-free (no ext-intl) so it works on the same shared
 * hosting the rest of the app targets.
 */
final class Money
{
    /**
     * The configured base currency (ISO 4217 code).
     */
    public static function base(): string
    {
        return strtoupper((string) (config('currency.base') ?: 'IDR'));
    }

    /**
     * Display format for a currency (defaults to the base currency).
     *
     * @return array{code: string, symbol: string, decimals: int, decimal_separator: string, thousands_separator: string}
     */
    public static function formatFor(?string $currency = null): array
    {
        $code = self::code($currency);
        $formats = (array) config('currency.formats', []);
        $format = $formats[$code] ?? null;

        if (! is_array($format)) {
            $format = (array) config('currency.default_format', []);
            $format['symbol'] = $code.' ';
        }

        return [
            'code' => $code,
            'symbol' => (string) ($format['symbol'] ?? $code.' '),
            'decimals' => (int) ($format['decimals'] ?? 2),
            'decimal_separator' => (string) ($format['decimal_separator'] ?? '.'),
            'thousands_separator' => (string) ($format['thousands_separator'] ?? ','),
        ];
    }

    /**
     * Number of minor-unit decimals used for a currency.
     */
    public static function decimals(?string $currency = null): int
    {
        return self::formatFor($currency)['decimals'];
    }

    /**
     * Round an amount to the currency's minor unit (2 for USD, 0 for IDR/JPY).
     */
    public static function round(mixed $amount, ?string $currency = null): float
    {
        return round((float) $amount, self::decimals($currency));
    }

    /**
     * Format an amount without the currency symbol, e.g. "1,234.50".
     */
    public static function amount(mixed $amount, ?string $currency = null): string
    {
        $f = self::formatFor($currency);

        return number_format((float) $amount, $f['decimals'], $f['decimal_separator'], $f['thousands_separator']);
    }

    /**
     * Format an amount with the currency symbol, e.g. "$1,234.50" or "IDR 15.000".
     */
    public static function format(mixed $amount, ?string $currency = null): string
    {
        $f = self::formatFor($currency);
        $value = (float) $amount;
        $sign = $value < 0 ? '-' : '';

        return $sign.$f['symbol'].number_format(abs($value), $f['decimals'], $f['decimal_separator'], $f['thousands_separator']);
    }

    /**
     * Format a non-money number (distance, counts) with the base currency's separators.
     */
    public static function number(mixed $value, int $decimals = 0): string
    {
        $f = self::formatFor();

        return number_format((float) $value, $decimals, $f['decimal_separator'], $f['thousands_separator']);
    }

    /**
     * Format an exchange rate with enough precision to be meaningful whether it
     * is large (USD→IDR ≈ 15618) or small (JPY→USD ≈ 0.0067222).
     */
    public static function rate(mixed $rate): string
    {
        $rate = (float) $rate;
        $f = self::formatFor();

        if ($rate <= 0) {
            return self::number(0, 2);
        }

        $decimals = $rate >= 1 ? 4 : min(10, (int) ceil(-log10($rate)) + 3);
        $formatted = number_format($rate, $decimals, $f['decimal_separator'], $f['thousands_separator']);

        [$whole, $fraction] = array_pad(explode($f['decimal_separator'], $formatted, 2), 2, '');
        $fraction = str_pad(rtrim($fraction, '0'), 2, '0');

        return $whole.$f['decimal_separator'].$fraction;
    }

    private static function code(?string $currency): string
    {
        $code = strtoupper(trim((string) $currency));

        return $code !== '' ? $code : self::base();
    }
}
