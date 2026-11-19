<?php

namespace App\Livewire;

use App\Livewire\Concerns\PicksOrigin;
use App\Models\Contact;
use App\Models\Country;
use App\Models\Origin;
use App\Models\Postcard;
use App\Models\PostcardStamp;
use App\Services\GeocodingService;
use App\Support\Money;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;

class RegisterPostcard extends Component
{
    use PicksOrigin;

    public $type = 'sent';

    public $postcard_id;

    public $nama_kontak;

    public $nomor_telepon;

    public $alamat;

    public $tanggal_kirim;

    public $tanggal_terima;

    public $negara;

    public $deskripsi_gambar;

    public $nilai_asal;

    public $mata_uang;

    public $exchange_rate = 1;

    public $biaya_prangko;

    public $img_d_data;

    public $img_b_data;

    public $stamp_data = [];

    public $contacts = [];

    public function mount()
    {
        $this->tanggal_kirim = date('Y-m-d');
        $this->mata_uang = Money::base();
        $this->restoreSessionOrigin();

        $userId = auth()->id();
        abort_unless($userId, 403);

        $this->contacts = Contact::where('user_id', $userId)->orderBy('nama_kontak')->pluck('nama_kontak')->toArray();
    }

    public function updatedNamaKontak($value)
    {
        $value = trim($value);
        if (empty($value)) {
            return;
        }

        $userId = auth()->id();
        abort_unless($userId, 403);

        $contact = Contact::where('user_id', $userId)
            ->where('nama_kontak', $value)
            ->first();

        if ($contact) {
            $this->alamat = $contact->alamat;
            $this->negara = $contact->country?->display_name;
            $this->nomor_telepon = $contact->nomor_telepon;

            $this->detectCurrency($this->negara);
        }
    }

    public function detectCurrency($countryName)
    {
        $this->dispatch('check-currency', country: $countryName);
    }

    public function updatedNilaiAsal($value)
    {
        $this->calculateBiaya();
    }

    public function updatedExchangeRate($value)
    {
        $this->calculateBiaya();
    }

    protected function calculateBiaya()
    {
        // For sent postcards, we use manual input for biaya_prangko, so skip calculation.
        if ($this->type === 'sent') {
            return;
        }

        $base = Money::base();

        if (empty($this->mata_uang) || strtoupper($this->mata_uang) === $base) {
            $this->biaya_prangko = Money::round($this->nilai_asal ?? 0, $base);
            $this->exchange_rate = 1;

            return;
        }
        $this->biaya_prangko = Money::round((float) ($this->nilai_asal ?? 0) * (float) ($this->exchange_rate ?? 1), $base);
    }

    protected function saveImage($base64Data, $prefix)
    {
        if (empty($base64Data) || strlen($base64Data) < 100) {
            return null;
        }

        $img = str_replace(['data:image/jpeg;base64,', ' '], ['', '+'], $base64Data);
        $data = base64_decode($img);
        $filename = 'uploads/'.$prefix.'_'.time().'_'.Str::random(5).'.jpg';

        Storage::disk('public')->put($filename, $data);

        return 'storage/'.$filename;
    }

    public function save(GeocodingService $geoService)
    {
        $this->validate([
            'negara' => 'required',
            'alamat' => 'required',
            'tanggal_kirim' => 'required|date',
            'img_d_data' => 'nullable', // Optional but handled
            'img_b_data' => 'nullable',
        ] + $this->originRules());

        $coords = $geoService->getCoordinates($this->alamat, $this->negara);
        $lat = $coords['lat'];
        $lng = $coords['lng'];

        $newPostcardId = null;

        DB::transaction(function () use ($lat, $lng, &$newPostcardId) {
            $foto_depan = $this->saveImage($this->img_d_data, 'f');
            $foto_belakang = $this->saveImage($this->img_b_data, 'b');

            $country = Country::findByName($this->negara);
            $country_id = $country?->id;

            // Sent-card postage is entered directly in the base currency.
            $isSent = $this->type === 'sent';
            $originId = $this->resolveOriginId(true);

            $contact = null;
            if ($this->nama_kontak) {
                $contact = Contact::updateOrCreate(
                    ['user_id' => auth()->id(), 'nama_kontak' => $this->nama_kontak],
                    [
                        'alamat' => $this->alamat,
                        'country_id' => $country_id,
                        'nomor_telepon' => $this->nomor_telepon,
                        'lat' => $lat,
                        'lng' => $lng,
                    ]
                );
            }

            $postcard = Postcard::create([
                'user_id' => auth()->id(),
                'uid' => Str::ulid(),
                'contact_id' => $contact?->id,
                'country_id' => $country_id,
                'origin_id' => $originId,
                'postcard_id' => $this->postcard_id,
                'type' => $this->type,
                'tanggal_kirim' => $this->tanggal_kirim,
                'tanggal_terima' => $this->tanggal_terima ?: null,
                'biaya_prangko' => $this->biaya_prangko,
                'nilai_asal' => $this->nilai_asal ?? 0,
                'mata_uang' => $isSent ? Money::base() : ($this->mata_uang ?: Money::base()),
                'base_currency' => Money::base(),
                'exchange_rate' => $isSent ? 1 : ($this->exchange_rate ?? 1),
                'deskripsi_gambar' => $this->deskripsi_gambar,
                'foto_depan' => $foto_depan,
                'foto_belakang' => $foto_belakang,
                'notif_read' => 1,
            ]);

            $newPostcardId = $postcard->id;

            if (! empty($this->stamp_data)) {
                foreach ($this->stamp_data as $index => $stampBase64) {
                    $stampPath = $this->saveImage($stampBase64, 'stamp');
                    if ($stampPath) {
                        PostcardStamp::create([
                            'postcard_id' => $postcard->id,
                            'foto_prangko' => $stampPath,
                        ]);
                    }
                }
            }

        });

        return redirect()->route('view', ['id' => $newPostcardId]);
    }

    public function removeStamp($index)
    {
        if (isset($this->stamp_data[$index])) {
            unset($this->stamp_data[$index]);
            $this->stamp_data = array_values($this->stamp_data);
        }
    }

    public function render()
    {
        return view('livewire.register-postcard', [
            'recentOrigins' => Origin::recentFor(auth()->id()),
        ])->layout('components.layouts.app', ['title' => 'Register New Postcard - Archive']);
    }
}
