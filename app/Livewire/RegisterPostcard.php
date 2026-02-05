<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Postcard;
use App\Models\Contact;
use App\Models\PostcardStamp;
use App\Services\GeocodingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
    
    // Currency
    public $nilai_asal;
    public $mata_uang = 'IDR';
    public $kurs_idr = 1;
    public $biaya_prangko;

    // Images (Base64 Data)
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
        if (empty($value)) return;
        
        $userId = auth()->id() ?? 1;
        
        // Auto-fill logic
        $contact = Contact::where('user_id', $userId)
                    ->where('nama_kontak', $value)
                    ->first();
        
        if ($contact) {
            $this->alamat = $contact->alamat;
            $this->negara = $contact->negara;
            $this->nomor_telepon = $contact->nomor_telepon;
            
            // Trigger currency detection
            $this->detectCurrency($this->negara);
        }
    }

    public function detectCurrency($countryName)
    {
        // Simple mapping or call external API via JS in view
        // Legacy used restcountries.com in JS. We can keep it in JS or do it here.
        // Let's stick to JS for "maintaining logic" as per request.
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
        $this->biaya_prangko = round((float)($this->nilai_asal ?? 0) * (float)($this->kurs_idr ?? 1));
    }

    protected function saveImage($base64Data, $prefix)
    {
        if (empty($base64Data) || strlen($base64Data) < 100) return null;

        $img = str_replace(['data:image/jpeg;base64,', ' '], ['', '+'], $base64Data);
        $data = base64_decode($img);
        $filename = 'uploads/' . $prefix . '_' . time() . '_' . Str::random(5) . '.jpg';
        
        // Use Storage facade for safer file writing
        \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $data);
        
        return 'storage/' . $filename; // Return accessible public path
    }

    public function save(GeocodingService $geoService)
    {
        // Validation
        $this->validate([
            'alamat' => 'required',
            'negara' => 'required',
            'biaya_prangko' => 'required|numeric',
        ]);

        // Geocoding
        $coords = $geoService->getCoordinates($this->alamat, $this->negara);
        $lat = $coords['lat'];
        $lng = $coords['lng'];

        $newPostcardId = null;

        DB::transaction(function () use ($lat, $lng, &$newPostcardId) {
            // Save Images
            $foto_depan = $this->saveImage($this->img_d_data, 'f');
            $foto_belakang = $this->saveImage($this->img_b_data, 'b');
            
            // Create Postcard
            $postcard = Postcard::create([
                'user_id' => auth()->id() ?? 1,
                'uid' => uniqid('pc_'),
                'postcard_id' => $this->postcard_id,
                'type' => $this->type,
                'nama_kontak' => $this->nama_kontak ?? '-',
                'negara' => $this->negara,
                'alamat' => $this->alamat,
                'nomor_telepon' => $this->nomor_telepon ?? '-',
                'tanggal_kirim' => $this->tanggal_kirim,
                'tanggal_terima' => $this->tanggal_terima ?: null,
                'biaya_prangko' => $this->biaya_prangko,
                'nilai_asal' => $this->nilai_asal ?? 0,
                'mata_uang' => $this->mata_uang ?? 'IDR',
                'kurs_idr' => $this->kurs_idr ?? 1,
                'deskripsi_gambar' => $this->deskripsi_gambar,
                'lat' => $lat,
                'lng' => $lng,
                'foto_depan' => $foto_depan,
                'foto_belakang' => $foto_belakang,
                'notif_read' => 1
            ]);

            $newPostcardId = $postcard->id;

            // Save Stamps
            if (!empty($this->stamp_data)) {
                foreach ($this->stamp_data as $index => $stampBase64) {
                    $stampPath = $this->saveImage($stampBase64, 'stamp');
                    if ($stampPath) {
                        PostcardStamp::create([
                            'postcard_id' => $postcard->id,
                            'foto_prangko' => $stampPath
                        ]);
                    }
                }
            }

            // Update/Create Contact
            if ($this->nama_kontak) {
                Contact::updateOrCreate(
                    ['user_id' => auth()->id() ?? 1, 'nama_kontak' => $this->nama_kontak],
                    [
                        'alamat' => $this->alamat,
                        'negara' => $this->negara,
                        'nomor_telepon' => $this->nomor_telepon
                    ]
                );
            }
        });

        return redirect()->route('view', ['id' => $newPostcardId]);
    }

    public function render()
    {
        return view('livewire.register-postcard')->layout('components.layouts.app', ['title' => 'Register New Postcard - Archive']);
    }
}
