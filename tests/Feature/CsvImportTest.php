<?php

namespace Tests\Feature;

use App\Livewire\ImportPostcards;
use App\Models\Country;
use App\Models\Postcard;
use App\Models\User;
use Database\Seeders\CountrySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class CsvImportTest extends TestCase
{
    use RefreshDatabase;

    private const CSV = <<<'CSV'
type;postcard_id;tgl_kirim;tgl_terima;deskripsi;nama;alamat;negara;telepon;biaya_asal;mata_uang
sent;US-1234567;15/03/2024;02/04/2024;Lighthouse card;Anna Schmidt;Hauptstr. 1, 10115 Berlin;Germany;+49 30 1234567;1.65;USD
received;JP-987654;2024-03-15;2024-04-01;Mount Fuji;Taro Yamada;1-1 Chiyoda, Tokyo;Japan;500;JPY
received;DE-55555;10/01/2024;;Castle;Lena Weber;Domplatz 3, Koeln;DE;0,95;EUR

CSV;

    protected function setUp(): void
    {
        parent::setUp();

        config(['currency.base' => 'USD']);
        Storage::fake('local');
        Http::preventStrayRequests();
        $this->seed(CountrySeeder::class);
        $this->actingAs(User::create(['username' => 'tester', 'password' => 'secret']));
    }

    private function import(string $csv)
    {
        return Livewire::test(ImportPostcards::class)
            ->set('file_csv', UploadedFile::fake()->createWithContent('postcards.csv', $csv))
            ->call('import');
    }

    public function test_imports_rows_and_converts_costs_to_usd(): void
    {
        Http::fake([
            'api.frankfurter.dev/v1/2024-03-15*' => Http::response(['rates' => ['USD' => 0.0067222]]),
            'api.frankfurter.dev/v1/2024-01-10*' => Http::response(['rates' => ['USD' => 1.0945]]),
        ]);

        $this->import(self::CSV)
            ->assertSet('messageType', 'success')
            ->assertSet('message', 'Successfully imported 3 records!');

        $this->assertSame(3, Postcard::count());

        // Already in the base currency: no lookup, cents preserved (upstream rounded to whole units)
        $sent = Postcard::where('postcard_id', 'US-1234567')->firstOrFail();
        $this->assertSame('sent', $sent->type);
        $this->assertSame('1.65', $sent->biaya_prangko);
        $this->assertSame('USD', $sent->mata_uang);
        $this->assertSame('USD', $sent->base_currency);
        $this->assertEquals(1.0, (float) $sent->exchange_rate);
        $this->assertSame('2024-03-15', $sent->tanggal_kirim->format('Y-m-d'));
        $this->assertSame('2024-04-02', $sent->tanggal_terima->format('Y-m-d'));
        $this->assertSame(Country::where('kode_iso', 'DE')->value('id'), $sent->country_id);
        $this->assertSame('Anna Schmidt', $sent->contact->nama_kontak);
        $this->assertSame('+49 30 1234567', $sent->contact->nomor_telepon);

        // ISO date, English country name, small rate kept at full precision
        $jp = Postcard::where('postcard_id', 'JP-987654')->firstOrFail();
        $this->assertSame('2024-03-15', $jp->tanggal_kirim->format('Y-m-d'));
        $this->assertSame(Country::where('kode_iso', 'JP')->value('id'), $jp->country_id);
        $this->assertSame('JPY', $jp->mata_uang);
        $this->assertSame('500.00', $jp->nilai_asal);
        $this->assertEquals(0.0067222, (float) $jp->exchange_rate);
        $this->assertSame('3.36', $jp->biaya_prangko);
        $this->assertSame('USD', $jp->base_currency);

        // ISO country code, comma decimal
        $de = Postcard::where('postcard_id', 'DE-55555')->firstOrFail();
        $this->assertSame(Country::where('kode_iso', 'DE')->value('id'), $de->country_id);
        $this->assertSame('0.95', $de->nilai_asal);
        $this->assertSame('1.04', $de->biaya_prangko);
        $this->assertNull($de->tanggal_terima);
    }

    public function test_blank_currency_means_base_currency(): void
    {
        Http::fake();

        $csv = "header\nreceived;US-1;01/02/2024;;Card;Bob;1 Main St;United States;2.10;\n";

        $this->import($csv)->assertSet('messageType', 'success');

        $card = Postcard::firstOrFail();
        $this->assertSame('USD', $card->mata_uang);
        $this->assertSame('2.10', $card->biaya_prangko);
        Http::assertNothingSent();
    }

    public function test_rejects_us_style_dates_instead_of_rolling_them_over(): void
    {
        Http::fake();

        // Read as DD/MM/YYYY this is day 3 of month 15, which PHP would
        // silently turn into 2025-03-03.
        $csv = "header\nreceived;US-1;03/15/2024;;Card;Bob;1 Main St;United States;2.10;USD\n";

        $this->import($csv)
            ->assertSet('messageType', 'error')
            ->assertSet('message', 'Import failed: CSV row 2: invalid date "03/15/2024" (use DD/MM/YYYY or YYYY-MM-DD).');

        $this->assertSame(0, Postcard::count());
    }

    public function test_aborts_the_import_instead_of_storing_a_one_to_one_rate_when_no_rate_is_available(): void
    {
        Http::fake(['*' => Http::response('', 500)]);

        $csv = "header\nreceived;JP-1;15/03/2024;;Card;Taro;Tokyo;Japan;500;JPY\n";

        $this->import($csv)
            ->assertSet('messageType', 'error')
            ->assertSet('message', 'Import failed: CSV row 2: no exchange rate available for JPY to USD.');

        $this->assertSame(0, Postcard::count());
    }
}
