<?php

namespace Tests\Feature;

use App\Livewire\DashboardTable;
use App\Livewire\EditPostcard;
use App\Livewire\ImportPostcards;
use App\Livewire\ReceiveConfirm;
use App\Livewire\RegisterPostcard;
use App\Models\Contact;
use App\Models\Country;
use App\Models\Origin;
use App\Models\Postcard;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Per-card "sent from" origins. Coordinates are public landmarks:
 * home = London, recipient = Paris, travel origin = Lisbon.
 */
class PostcardOriginTest extends TestCase
{
    use RefreshDatabase;

    private const LONDON = [51.507412, -0.127758];

    private const PARIS = [48.8566, 2.3522];

    private const LISBON = [38.7223, -9.1393];

    private User $user;

    private Country $france;

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.home_lat' => self::LONDON[0], 'app.home_lng' => self::LONDON[1], 'app.owner_location' => 'London, UK', 'currency.base' => 'USD']);
        $this->withoutVite();
        Http::preventStrayRequests();

        $this->user = User::create(['username' => 'tester', 'password' => 'secret']);
        $this->france = Country::create(['nama_indonesia' => 'Prancis', 'nama_inggris' => 'France', 'kode_iso' => 'FR']);
    }

    private function sentCard(array $attributes = []): Postcard
    {
        $contact = Contact::create([
            'user_id' => $this->user->id,
            'nama_kontak' => 'Marie',
            'alamat' => '1 Rue de Rivoli, Paris',
            'country_id' => $this->france->id,
            'lat' => self::PARIS[0],
            'lng' => self::PARIS[1],
        ]);

        return Postcard::create($attributes + [
            'user_id' => $this->user->id,
            'uid' => (string) Str::ulid(),
            'type' => 'sent',
            'postcard_id' => 'GB-123',
            'contact_id' => $contact->id,
            'country_id' => $this->france->id,
            'tanggal_kirim' => '2024-03-15',
            'biaya_prangko' => 1.65,
        ]);
    }

    private function lisbon(?int $userId = null): Origin
    {
        return Origin::remember($userId ?? $this->user->id, 'Lisbon, Portugal', self::LISBON[0], self::LISBON[1]);
    }

    public function test_cards_without_an_origin_are_measured_from_home(): void
    {
        $card = $this->sentCard();

        $this->assertNull($card->origin_id);
        $this->assertTrue($card->ownerPlace()->isHome);
        $this->assertSame('London, UK', $card->ownerPlace()->name);
        $this->assertEqualsWithDelta(344, $card->distanceKm(), 2); // London-Paris
    }

    public function test_a_sent_card_is_measured_from_its_own_origin(): void
    {
        $card = $this->sentCard(['origin_id' => $this->lisbon()->id]);

        $this->assertFalse($card->ownerPlace()->isHome);
        $this->assertSame('Lisbon, Portugal', $card->ownerPlace()->name);
        $this->assertEqualsWithDelta(1453, $card->distanceKm(), 5); // Lisbon-Paris
    }

    public function test_received_cards_are_always_measured_from_home(): void
    {
        $card = $this->sentCard(['type' => 'received', 'origin_id' => $this->lisbon()->id]);

        $this->assertTrue($card->ownerPlace()->isHome);
        $this->assertEqualsWithDelta(344, $card->distanceKm(), 2);
    }

    public function test_origin_coordinates_are_encrypted_at_rest(): void
    {
        $origin = $this->lisbon();

        $raw = DB::table('origins')->where('id', $origin->id)->first();
        $this->assertStringNotContainsString('38.7223', $raw->lat);
        $this->assertStringNotContainsString('9.1393', $raw->lng);
        $this->assertSame(self::LISBON[0], $origin->fresh()->lat);
        $this->assertSame(self::LISBON[1], $origin->fresh()->lng);
    }

    public function test_remember_reuses_a_matching_origin(): void
    {
        $first = $this->lisbon();
        $again = Origin::remember($this->user->id, ' lisbon, portugal ', self::LISBON[0], self::LISBON[1]);
        $elsewhere = Origin::remember($this->user->id, 'Lisbon, Portugal', 38.7, -9.2);

        $this->assertSame($first->id, $again->id);
        $this->assertNotSame($first->id, $elsewhere->id);
        $this->assertSame(2, Origin::count());
        $this->assertSame($elsewhere->id, Origin::recentFor($this->user->id)->first()->id);
    }

    private function registerSentCard()
    {
        return Livewire::test(RegisterPostcard::class)
            ->set('type', 'sent')
            ->set('postcard_id', 'GB-456')
            ->set('nama_kontak', 'Marie')
            ->set('alamat', '1 Rue de Rivoli, Paris')
            ->set('negara', 'France')
            ->set('tanggal_kirim', '2024-03-15')
            ->set('biaya_prangko', 1.65);
    }

    public function test_registering_from_a_new_place_saves_and_remembers_it_for_the_session(): void
    {
        $this->actingAs($this->user);

        $this->registerSentCard()
            ->assertSet('origin_choice', 'home')
            ->set('origin_choice', 'new')
            ->set('origin_name', 'Lisbon, Portugal')
            ->set('origin_lat', self::LISBON[0])
            ->set('origin_lng', self::LISBON[1])
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect();

        $origin = Origin::sole();
        $this->assertSame('Lisbon, Portugal', $origin->name);
        $this->assertSame($origin->id, Postcard::where('postcard_id', 'GB-456')->value('origin_id'));

        // The next card in this session starts at Lisbon...
        Livewire::test(RegisterPostcard::class)->assertSet('origin_choice', (string) $origin->id);

        // ...until home is picked again.
        $this->registerSentCard()->set('postcard_id', 'GB-789')->set('origin_choice', 'home')->call('save');
        $this->assertNull(Postcard::where('postcard_id', 'GB-789')->value('origin_id'));
        Livewire::test(RegisterPostcard::class)->assertSet('origin_choice', 'home');
    }

    public function test_a_new_place_needs_valid_coordinates(): void
    {
        $this->actingAs($this->user);

        $this->registerSentCard()
            ->set('origin_choice', 'new')
            ->set('origin_name', '')
            ->set('origin_lat', 123)
            ->set('origin_lng', -9.1)
            ->call('save')
            ->assertHasErrors(['origin_name', 'origin_lat']);

        $this->assertSame(0, Postcard::count());
    }

    public function test_another_users_origin_cannot_be_used(): void
    {
        $other = User::create(['username' => 'other', 'password' => 'secret']);
        $theirs = $this->lisbon($other->id);
        $this->actingAs($this->user);

        try {
            $this->registerSentCard()->set('origin_choice', (string) $theirs->id)->call('save');
            $this->fail('Using another user\'s origin should not succeed.');
        } catch (ModelNotFoundException) {
            // the lookup is scoped to the signed-in user (rendered as a 404)
        }

        $this->assertSame(0, Postcard::count());
    }

    public function test_editing_a_card_can_change_its_origin(): void
    {
        $card = $this->sentCard();
        $origin = $this->lisbon();
        $this->actingAs($this->user);

        Livewire::test(EditPostcard::class, ['id' => $card->id])
            ->assertSet('origin_choice', 'home')
            ->set('origin_choice', (string) $origin->id)
            ->call('preUpdate')
            ->assertHasNoErrors();

        $this->assertSame($origin->id, $card->fresh()->origin_id);
        // Editing does not change the default for new cards.
        $this->assertNull(session('last_origin_choice'));
    }

    public function test_postcard_page_and_dashboard_use_the_origin(): void
    {
        $card = $this->sentCard(['origin_id' => $this->lisbon()->id]);
        $this->actingAs($this->user);

        $this->get(route('view', ['id' => $card->id]))
            ->assertOk()
            ->assertSee('Sent From')
            ->assertSee('Lisbon, Portugal')
            ->assertSee('1,453 KM');

        Livewire::test(DashboardTable::class, ['type' => 'sent'])
            ->assertSee('1,453 km')
            ->assertSee('from Lisbon, Portugal');
    }

    /**
     * The arrival page is public. A recipient knows their own address, so
     * neither the exact origin nor an exact distance may reach it: the
     * origin is rounded to 1 decimal and the distance is measured from that
     * rounded point and rounded to 10 km (exact: 344 km and 1,453 km).
     */
    public function test_public_arrival_page_never_shows_a_precise_origin_or_distance(): void
    {
        $home = $this->sentCard();
        $travel = $this->sentCard(['postcard_id' => 'GB-999', 'origin_id' => $this->lisbon()->id]);

        $this->get(route('receive.confirm', ['uid' => $home->uid]))
            ->assertOk()
            ->assertSee('A postcard from London, UK has reached you.')
            ->assertSee('const myLat = 51.5;', false)
            ->assertSee('const myLng = -0.1;', false)
            ->assertDontSee('51.507412')
            ->assertDontSee('0.127758')
            ->assertSee('~340 km')
            ->assertDontSee('344');

        $this->get(route('receive.confirm', ['uid' => $travel->uid]))
            ->assertOk()
            ->assertSee('A postcard from Lisbon, Portugal has reached you.')
            ->assertSee('const myLat = 38.7;', false)
            ->assertDontSee('38.7223')
            ->assertDontSee('9.1393')
            ->assertSee('~1,450 km')
            ->assertDontSee('1,453');

        // The Livewire payload (serialized into the page) carries the same coarse values.
        Livewire::test(ReceiveConfirm::class, ['uid' => $home->uid])
            ->assertSet('myLat', 51.5)
            ->assertSet('myLng', -0.1)
            ->assertSet('distance', 340.0);

        // After confirming, the "Distance Traveled" line is still the coarse value.
        Livewire::test(ReceiveConfirm::class, ['uid' => $travel->uid])->call('confirm');
        $this->get(route('receive.confirm', ['uid' => $travel->uid]))
            ->assertSee('1,450 km')
            ->assertDontSee('1,453');
    }

    public function test_public_gallery_map_markers_are_rounded(): void
    {
        $this->sentCard(['type' => 'received', 'postcard_id' => 'FR-1', 'foto_depan' => 'storage/uploads/front.jpg']);

        $this->get(route('gallery'))
            ->assertOk()
            ->assertSee('"lat":48.9', false)
            ->assertSee('"lng":2.4', false)
            ->assertDontSee('48.8566')
            ->assertDontSee('2.3522');
    }

    public function test_csv_import_accepts_optional_origin_columns(): void
    {
        Storage::fake('local');
        $this->actingAs($this->user);

        $csv = "header\n"
            ."sent;GB-1;15/03/2024;;Tram;Marie;1 Rue de Rivoli;France;-;1.65;USD;Lisbon, Portugal;38.7223;-9.1393\n"
            ."sent;GB-2;16/03/2024;;Bridge;Marie;1 Rue de Rivoli;France;-;1.65;USD\n";

        Livewire::test(ImportPostcards::class)
            ->set('file_csv', UploadedFile::fake()->createWithContent('cards.csv', $csv))
            ->call('import')
            ->assertSet('messageType', 'success');

        $this->assertSame('Lisbon, Portugal', Postcard::where('postcard_id', 'GB-1')->first()->origin->name);
        $this->assertNull(Postcard::where('postcard_id', 'GB-2')->value('origin_id'));

        Livewire::test(ImportPostcards::class)
            ->set('file_csv', UploadedFile::fake()->createWithContent('bad.csv', "header\nsent;GB-3;15/03/2024;;x;Marie;a;France;-;1;USD;Nowhere;999;0\n"))
            ->call('import')
            ->assertSet('message', 'Import failed: CSV row 2: origin needs a name, latitude (-90..90) and longitude (-180..180).');
    }

    public function test_migration_rolls_back_and_reapplies_with_existing_cards(): void
    {
        $card = $this->sentCard(['origin_id' => $this->lisbon()->id]);

        $this->artisan('migrate:rollback', ['--path' => 'database/migrations/2026_11_19_000000_create_origins_table.php'])->assertSuccessful();
        $this->assertFalse(Schema::hasTable('origins'));
        $this->assertTrue(DB::table('postcards')->where('id', $card->id)->exists());

        $this->artisan('migrate')->assertSuccessful();
        $this->assertNull($card->fresh()->origin_id); // falls back to home
    }
}
