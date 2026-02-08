<?php

namespace App\Livewire;

use App\Models\Contact;
use App\Models\Postcard;
use App\Models\PostcardStamp;
use App\Services\GeocodingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;

class RegisterPostcard extends Component
{
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

    public $mata_uang = 'IDR';

    public $kurs_idr = 1;

    public $biaya_prangko;

    public $img_d_data;

    public $img_b_data;

    public $stamp_data = [];

    public $contacts = [];

    public function mount()
    {
        $this->tanggal_kirim = date('Y-m-d');
        $userId = auth()->id() ?? 1;
        $this->contacts = Contact::where('user_id', $userId)->orderBy('nama_kontak')->pluck('nama_kontak')->toArray();
    }

    public function updatedNamaKontak($value)
    {
        $value = trim($value);
        if (empty($value)) {
            return;
        }

        $userId = auth()->id() ?? 1;

        $contact = Contact::where('user_id', $userId)
            ->where('nama_kontak', $value)
            ->first();

        if ($contact) {
            $this->alamat = $contact->alamat;
            $this->negara = $contact->country?->nama_indonesia;
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

    public function updatedKursIdr($value)
    {
        $this->calculateBiaya();
    }

    protected function calculateBiaya()
    {
        $this->biaya_prangko = round((float) ($this->nilai_asal ?? 0) * (float) ($this->kurs_idr ?? 1));
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
        ]);

        $coords = $geoService->getCoordinates($this->alamat, $this->negara);
        $lat = $coords['lat'];
        $lng = $coords['lng'];

        $newPostcardId = null;

        DB::transaction(function () use ($lat, $lng, &$newPostcardId) {
            $foto_depan = $this->saveImage($this->img_d_data, 'f');
            $foto_belakang = $this->saveImage($this->img_b_data, 'b');

            $country = \App\Models\Country::where('nama_indonesia', $this->negara)
                ->orWhere('nama_inggris', $this->negara)
                ->first();
            $country_id = $country?->id;

            $contact = null;
            if ($this->nama_kontak) {
                $contact = Contact::updateOrCreate(
                    ['user_id' => auth()->id() ?? 1, 'nama_kontak' => $this->nama_kontak],
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
                'user_id' => auth()->id() ?? 1,
                'uid' => uniqid('pc_'),
                'contact_id' => $contact?->id,
                'country_id' => $country_id,
                'postcard_id' => $this->postcard_id,
                'type' => $this->type,
                'tanggal_kirim' => $this->tanggal_kirim,
                'tanggal_terima' => $this->tanggal_terima ?: null,
                'biaya_prangko' => $this->biaya_prangko,
                'nilai_asal' => $this->nilai_asal ?? 0,
                'mata_uang' => $this->mata_uang ?? 'IDR',
                'kurs_idr' => $this->kurs_idr ?? 1,
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
        return view('livewire.register-postcard')->layout('components.layouts.app', ['title' => 'Register New Postcard - Archive']);
    }
}
