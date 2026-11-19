<?php

namespace Tests\Feature;

use App\Livewire\DashboardTable;
use App\Models\Postcard;
use App\Models\User;
use App\Providers\AppServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class CurrencyDisplayTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        config(['currency.base' => 'USD']);
        $this->withoutVite();
        $this->user = User::create(['username' => 'tester', 'password' => 'secret']);
    }

    private function card(array $attributes): Postcard
    {
        return Postcard::create($attributes + [
            'user_id' => $this->user->id,
            'uid' => (string) Str::ulid(),
            'type' => 'sent',
            'tanggal_kirim' => '2024-03-15',
        ]);
    }

    public function test_new_cards_record_the_configured_base_currency(): void
    {
        $this->assertSame('USD', $this->card(['biaya_prangko' => 1.65])->fresh()->base_currency);
    }

    public function test_postcard_view_shows_cost_in_usd(): void
    {
        $card = $this->card([
            'type' => 'received',
            'postcard_id' => 'JP-987654',
            'nilai_asal' => 500,
            'mata_uang' => 'JPY',
            'exchange_rate' => 0.0067222,
            'biaya_prangko' => 3.36,
        ]);

        $this->actingAs($this->user)
            ->get(route('view', ['id' => $card->id]))
            ->assertOk()
            ->assertSee('$3.36')
            ->assertSee('JPY 500')
            ->assertSee('Rate: 0.006722')
            ->assertDontSee('IDR');
    }

    public function test_legacy_idr_card_still_displays_in_idr(): void
    {
        $card = $this->card(['biaya_prangko' => 15000, 'base_currency' => 'IDR', 'mata_uang' => 'IDR']);

        $this->actingAs($this->user)
            ->get(route('view', ['id' => $card->id]))
            ->assertOk()
            ->assertSee('IDR 15.000');
    }

    public function test_dashboard_total_only_sums_base_currency_cards(): void
    {
        $this->card(['biaya_prangko' => 1.65]);
        $this->card(['biaya_prangko' => 2.40]);
        $this->card(['biaya_prangko' => 15000, 'base_currency' => 'IDR']);

        $this->actingAs($this->user);

        Livewire::test(DashboardTable::class, ['type' => 'sent'])
            ->assertSee('Total (USD)')
            ->assertSee('$4.05')
            ->assertSee('IDR 15.000')
            ->assertSee('excludes 1 card(s) recorded in another currency');
    }

    public function test_dates_use_the_configured_format(): void
    {
        config(['app.date_format' => 'm/d/Y']);
        (new AppServiceProvider($this->app))->boot();

        $card = $this->card(['biaya_prangko' => 1.65, 'tanggal_kirim' => '2024-03-15', 'tanggal_terima' => '2024-04-02']);

        $this->actingAs($this->user)
            ->get(route('view', ['id' => $card->id]))
            ->assertOk()
            ->assertSee('03/15/2024')
            ->assertSee('04/02/2024');

        $this->actingAs($this->user)
            ->get(route('postcard.register'))
            ->assertOk()
            ->assertSee('(MM/DD/YYYY)')
            ->assertSee("altFormat: 'm\\/d\\/Y'", false);
    }

    public function test_currency_migration_backfills_existing_rows_as_idr(): void
    {
        $this->artisan('migrate:rollback', ['--step' => 1])->assertSuccessful();

        DB::table('postcards')->insert([
            'uid' => 'legacy-1',
            'user_id' => $this->user->id,
            'type' => 'received',
            'biaya_prangko' => 15618,
            'nilai_asal' => 1,
            'mata_uang' => 'USD',
            'kurs_idr' => 15618,
        ]);

        $this->artisan('migrate')->assertSuccessful();

        $row = DB::table('postcards')->where('uid', 'legacy-1')->first();
        $this->assertSame('IDR', $row->base_currency);
        $this->assertEquals(15618, (float) $row->exchange_rate);
    }
}
