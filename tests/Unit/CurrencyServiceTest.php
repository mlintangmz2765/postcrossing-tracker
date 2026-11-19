<?php

namespace Tests\Unit;

use App\Services\CurrencyService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CurrencyServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
        config(['currency.base' => 'USD']);
    }

    public function test_same_currency_needs_no_lookup(): void
    {
        Http::fake();

        $this->assertSame(1.0, (new CurrencyService)->getHistoricalRate('usd', '2024-03-15'));
        $this->assertSame(1.0, (new CurrencyService)->getHistoricalRate('', '2024-03-15'));
        Http::assertNothingSent();
    }

    public function test_fetches_historical_rate_to_the_configured_base(): void
    {
        Http::fake([
            'api.frankfurter.dev/v1/2024-03-15*' => Http::response(['base' => 'JPY', 'rates' => ['USD' => 0.0067222]]),
        ]);

        $rate = (new CurrencyService)->getHistoricalRate('JPY', '2024-03-15');

        $this->assertSame(0.0067222, $rate);
        Http::assertSent(fn (Request $r) => str_contains($r->url(), '/v1/2024-03-15')
            && $r['from'] === 'JPY' && $r['to'] === 'USD');
    }

    public function test_explicit_base_overrides_config(): void
    {
        Http::fake([
            'api.frankfurter.dev/*' => Http::response(['rates' => ['IDR' => 15618]]),
        ]);

        $this->assertSame(15618.0, (new CurrencyService)->getHistoricalRate('USD', '2024-03-15', 'IDR'));
    }

    public function test_falls_back_to_open_er_api_for_currencies_the_ecb_does_not_publish(): void
    {
        Http::fake([
            'api.frankfurter.dev/*' => Http::response(['message' => 'not found'], 404),
            'open.er-api.com/v6/latest/TWD' => Http::response(['result' => 'success', 'rates' => ['USD' => 0.030328]]),
        ]);

        $this->assertSame(0.030328, (new CurrencyService)->getHistoricalRate('TWD', '2024-03-15'));
    }

    public function test_returns_null_instead_of_a_fake_one_to_one_rate_when_every_source_fails(): void
    {
        Http::fake([
            '*' => Http::response('', 500),
        ]);

        $this->assertNull((new CurrencyService)->getHistoricalRate('JPY', '2024-03-15'));
    }
}
