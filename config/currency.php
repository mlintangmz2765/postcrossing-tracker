<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Base Currency
    |--------------------------------------------------------------------------
    |
    | ISO 4217 code that postage costs are recorded and reported in. Amounts
    | entered in another currency (e.g. the stamp value on a received card)
    | are converted to this currency using the exchange rate for the send
    | date.
    |
    | Every postcard also stores the base currency it was recorded in
    | (postcards.base_currency), so changing this value later never relabels
    | existing amounts. Totals and averages only include cards recorded in
    | the current base currency.
    |
    */

    'base' => strtoupper((string) env('APP_BASE_CURRENCY', 'IDR')),

    /*
    |--------------------------------------------------------------------------
    | Display Formats
    |--------------------------------------------------------------------------
    |
    | How amounts are shown per currency. "decimals" is also used when
    | rounding converted amounts before they are stored. The base currency's
    | separators are used for other numbers in the UI (distances, etc.) so
    | the whole site reads consistently.
    |
    | Currencies not listed here use "default_format" and are prefixed with
    | their ISO code (e.g. "CHF 12.50").
    |
    */

    'formats' => [
        'USD' => ['symbol' => '$', 'decimals' => 2, 'decimal_separator' => '.', 'thousands_separator' => ','],
        'IDR' => ['symbol' => 'IDR ', 'decimals' => 0, 'decimal_separator' => ',', 'thousands_separator' => '.'],
        'EUR' => ['symbol' => '€', 'decimals' => 2, 'decimal_separator' => '.', 'thousands_separator' => ','],
        'GBP' => ['symbol' => '£', 'decimals' => 2, 'decimal_separator' => '.', 'thousands_separator' => ','],
        'CAD' => ['symbol' => 'CA$', 'decimals' => 2, 'decimal_separator' => '.', 'thousands_separator' => ','],
        'AUD' => ['symbol' => 'A$', 'decimals' => 2, 'decimal_separator' => '.', 'thousands_separator' => ','],
        'CNY' => ['symbol' => 'CN¥', 'decimals' => 2, 'decimal_separator' => '.', 'thousands_separator' => ','],
        'JPY' => ['symbol' => '¥', 'decimals' => 0, 'decimal_separator' => '.', 'thousands_separator' => ','],
    ],

    'default_format' => ['decimals' => 2, 'decimal_separator' => '.', 'thousands_separator' => ','],

];
