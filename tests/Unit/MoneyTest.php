<?php

namespace Tests\Unit;

use App\Support\Money;
use Tests\TestCase;

class MoneyTest extends TestCase
{
    public function test_base_currency_defaults_to_idr_when_unset(): void
    {
        config(['currency.base' => null]);

        $this->assertSame('IDR', Money::base());
    }

    public function test_base_currency_comes_from_config(): void
    {
        config(['currency.base' => 'idr']);

        $this->assertSame('IDR', Money::base());
    }

    public function test_usd_uses_dollar_sign_comma_thousands_and_two_decimals(): void
    {
        config(['currency.base' => 'USD']);

        $this->assertSame('$1,234.50', Money::format(1234.5));
        $this->assertSame('$0.00', Money::format(null));
        $this->assertSame('$1,234,567.89', Money::format('1234567.891'));
        $this->assertSame('-$3.25', Money::format(-3.25));
        $this->assertSame('1,234.50', Money::amount(1234.5));
        $this->assertSame(2, Money::decimals());
    }

    public function test_idr_keeps_the_original_upstream_format(): void
    {
        config(['currency.base' => 'IDR']);

        $this->assertSame('IDR 15.000', Money::format(15000));
        $this->assertSame(0, Money::decimals());
        $this->assertSame('1.234', Money::number(1234));
    }

    public function test_explicit_currency_overrides_the_base(): void
    {
        config(['currency.base' => 'USD']);

        $this->assertSame('IDR 15.000', Money::format(15000, 'IDR'));
        $this->assertSame('¥500', Money::format(500, 'jpy'));
        $this->assertSame('$2.00', Money::format(2, ''));
    }

    public function test_unknown_currency_falls_back_to_code_prefix(): void
    {
        $this->assertSame('CHF 12.50', Money::format(12.5, 'CHF'));
        $this->assertSame(2, Money::decimals('CHF'));
    }

    public function test_round_uses_the_currency_minor_unit(): void
    {
        $this->assertSame(3.36, Money::round(500 * 0.0067222, 'USD'));
        $this->assertSame(15618.0, Money::round(15618.4, 'IDR'));
    }

    public function test_number_uses_base_currency_separators(): void
    {
        config(['currency.base' => 'USD']);
        $this->assertSame('12,345', Money::number(12345.4));
        $this->assertSame('3.08', Money::number(3.0751, 2));

        config(['currency.base' => 'IDR']);
        $this->assertSame('3,08', Money::number(3.0751, 2));
    }

    public function test_rate_keeps_precision_for_small_and_large_rates(): void
    {
        config(['currency.base' => 'USD']);

        $this->assertSame('0.006722', Money::rate(0.0067222));
        $this->assertSame('0.0000615', Money::rate(0.0000615));
        $this->assertSame('1.0968', Money::rate(1.0968));
        $this->assertSame('15,618.00', Money::rate(15618));
        $this->assertSame('1.00', Money::rate(1));
        $this->assertSame('0.00', Money::rate(0));
    }
}
