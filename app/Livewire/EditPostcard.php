<?php

namespace App\Livewire;

use App\Models\Contact;
use App\Models\Postcard;
use App\Models\PostcardStamp;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class EditPostcard extends Component
{
    use WithFileUploads;

    public $id;

    public $type;

    public $postcard_id;

    public $nama_kontak;

    public $negara;

    public $nomor_telepon;

    public $alamat;

    public $tanggal_kirim;

    public $tanggal_terima;

    public $biaya_prangko;

    public $deskripsi_gambar;

    public $nilai_asal;

    public $mata_uang = 'IDR';

    public $kurs_idr = 1;

    public $currentFotoDepan;

    public $currentFotoBelakang;

    public $newFotoDepanBase64;

    public $newFotoBelakangBase64;

    public $newStampsBase64 = [];

    public $existingStamps = [];

    protected $listeners = ['refreshStamps' => '$refresh'];

    public function mount($id)
    {
        $this->id = $id;
        $data = Postcard::where('id', $id)->where('user_id', auth()->id())->first();

        if (! $data) {
            return redirect()->route('dashboard');
        }

        $this->type = $data->type;
        $this->postcard_id = $data->postcard_id;
        $this->nama_kontak = $data->contact?->nama_kontak;
        $this->negara = $data->country?->nama_indonesia;
        $this->nomor_telepon = $data->contact?->nomor_telepon;
        $this->alamat = $data->contact?->alamat;
        $this->tanggal_kirim = $data->tanggal_kirim?->format('Y-m-d');
        $this->tanggal_terima = $data->tanggal_terima?->format('Y-m-d');
        $this->biaya_prangko = $data->biaya_prangko;
        $this->deskripsi_gambar = $data->deskripsi_gambar;
        $this->nilai_asal = $data->nilai_asal;
        $this->mata_uang = $data->mata_uang ?? 'IDR';
        $this->kurs_idr = $data->kurs_idr ?? 1;

        $this->currentFotoDepan = $data->foto_depan;
        $this->currentFotoBelakang = $data->foto_belakang;

        $this->loadStamps();
    }

    public function loadStamps()
    {
        $this->existingStamps = PostcardStamp::where('postcard_id', $this->id)->get();
    }

    public function deletePostcard()
    {
        $data = Postcard::where('id', $this->id)->where('user_id', auth()->id())->first();

        if ($data) {
            if ($data->foto_depan && file_exists(public_path($data->foto_depan))) {
                @unlink(public_path($data->foto_depan));
            }
            if ($data->foto_belakang && file_exists(public_path($data->foto_belakang))) {
                @unlink(public_path($data->foto_belakang));
            }

            $stamps = PostcardStamp::where('postcard_id', $this->id)->get();
            foreach ($stamps as $stamp) {
                if (file_exists(public_path($stamp->foto_prangko))) {
                    @unlink(public_path($stamp->foto_prangko));
                }
            }
            PostcardStamp::where('postcard_id', $this->id)->delete();

            $data->delete();
        }

        return redirect()->route('dashboard');
    }

    public function deleteStamp($stampId)
    {
        $stamp = PostcardStamp::where('id', $stampId)
            ->whereHas('postcard', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->first();

        if ($stamp) {
            if (file_exists(public_path($stamp->foto_prangko))) {
                @unlink(public_path($stamp->foto_prangko));
            }
            $stamp->delete();
            $this->loadStamps();
        }
    }

    public function rotateStamp($stampId)
    {
        $stamp = PostcardStamp::where('id', $stampId)
            ->whereHas('postcard', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->first();

        if ($stamp && file_exists(public_path($stamp->foto_prangko))) {
            $path = public_path($stamp->foto_prangko);
            $imageData = file_get_contents($path);
            $source = @imagecreatefromstring($imageData);

            if ($source === false) {
                return; // Unsupported or corrupt image
            }

            $rotate = imagerotate($source, -90, 0);

            // Detect original format and save accordingly
            $mime = @mime_content_type($path);
            match ($mime) {
                'image/png' => imagepng($rotate, $path),
                'image/gif' => imagegif($rotate, $path),
                'image/webp' => imagewebp($rotate, $path),
                default => imagejpeg($rotate, $path),
            };

            imagedestroy($source);
            imagedestroy($rotate);
            $this->loadStamps();
            $this->dispatch('stampRotated', $stampId);
        }
    }

    public $showContactConflictModal = false;

    public $pendingUpdateData = [];

    public function preUpdate(\App\Services\GeocodingService $geoService)
    {
        $this->validate([
            'postcard_id' => 'nullable',
            'negara' => 'required',
            'alamat' => 'required',
            'tanggal_kirim' => 'required|date',
        ]);

        // Check for contact changes
        $postcard = Postcard::where('id', $this->id)->where('user_id', auth()->id())->firstOrFail();
        $currentContact = $postcard->contact;

        $contactChanged = false;
        if ($currentContact) {
            $isAddressChanged = trim($currentContact->alamat) !== trim($this->alamat);
            $isPhoneChanged = trim($currentContact->nomor_telepon) !== trim($this->nomor_telepon);

            // If details changed AND this contact is used by MORE than just this postcard (or other postcards exist for it)
            if (($isAddressChanged || $isPhoneChanged) && $currentContact->postcards()->count() > 1) {
                $this->showContactConflictModal = true;

                return; // Stop here, wait for user decision
            }
        }

        // If no conflict, proceed with standard update
        $this->performUpdate($geoService, 'update_all');
    }

    public function resolveContactConflict($mode)
    {
        // $mode = 'update_all' (Edit existing Contact) OR 'create_new' (New Contact for this postcard)
        $this->showContactConflictModal = false;
        $geoService = app(\App\Services\GeocodingService::class); // Manually resolve service since this is triggered by UI
        $this->performUpdate($geoService, $mode);
    }

    public function performUpdate(\App\Services\GeocodingService $geoService, $contactMode)
    {
        $postcard = Postcard::where('id', $this->id)->where('user_id', auth()->id())->firstOrFail();

        if ($this->type === 'received') {
            if ($this->mata_uang === 'IDR') {
                $this->biaya_prangko = round((float) ($this->nilai_asal ?? 0));
                $this->kurs_idr = 1;
            } else {
                $this->biaya_prangko = round((float) ($this->nilai_asal ?? 0) * (float) ($this->kurs_idr ?? 1));
            }
        }

        if ($this->newFotoDepanBase64) {
            if ($this->currentFotoDepan && file_exists(public_path($this->currentFotoDepan))) {
                @unlink(public_path($this->currentFotoDepan));
            }
            $this->currentFotoDepan = $this->saveBase64Image($this->newFotoDepanBase64, 'f', 'd');
        }

        if ($this->newFotoBelakangBase64) {
            if ($this->currentFotoBelakang && file_exists(public_path($this->currentFotoBelakang))) {
                @unlink(public_path($this->currentFotoBelakang));
            }
            $this->currentFotoBelakang = $this->saveBase64Image($this->newFotoBelakangBase64, 'b', 'b');
        }

        foreach ($this->newStampsBase64 as $idx => $base64) {
            $path = $this->saveBase64Image($base64, 'stamp', $idx);
            PostcardStamp::create([
                'postcard_id' => $this->id,
                'foto_prangko' => $path,
            ]);
        }

        $country = \App\Models\Country::where('nama_indonesia', $this->negara)
            ->orWhere('nama_inggris', $this->negara)
            ->first();
        $country_id = $country?->id;

        // Geocoding Logic
        $lat = $postcard->contact?->lat ?? 0;
        $lng = $postcard->contact?->lng ?? 0;

        // Check if address actually changed from what is currently in DB (not just what is in form)
        // Note: We use the FORM data ($this->alamat) vs DB data for geocoding trigger
        $addressChanged = ($postcard->contact?->alamat !== $this->alamat) || ($postcard->country?->nama_indonesia !== $this->negara);
        $isZeroCoords = ($lat == 0 && $lng == 0);

        if ($addressChanged || $isZeroCoords) {
            $coords = $geoService->getCoordinates($this->alamat, $this->negara);
            if ($coords['lat'] != 0 || $coords['lng'] != 0) {
                $lat = $coords['lat'];
                $lng = $coords['lng'];
            }
        }

        // SMART CONTACT SYNC
        $contactId = $postcard->contact_id;

        if ($contactMode === 'create_new') {
            // Create NEW contact specific for this postcard
            $newContact = Contact::create([
                'user_id' => auth()->id(),
                'nama_kontak' => $this->nama_kontak, // Can keep same name
                'alamat' => $this->alamat,
                'country_id' => $country_id,
                'nomor_telepon' => $this->nomor_telepon,
                'lat' => $lat,
                'lng' => $lng,
            ]);
            $contactId = $newContact->id;
        } else {
            // Update Existing Contact (Standard)
            // If contact doesn't exist yet, create it
            $contact = Contact::updateOrCreate(
                ['id' => $postcard->contact_id], // Try to find by ID first if exists
                [
                    'user_id' => auth()->id() ?? 1,
                    'nama_kontak' => $this->nama_kontak,
                    'alamat' => $this->alamat,
                    'country_id' => $country_id,
                    'nomor_telepon' => $this->nomor_telepon,
                    'lat' => $lat,
                    'lng' => $lng,
                ]
            );
            $contactId = $contact->id;
        }

        $postcard->update([
            'postcard_id' => $this->postcard_id,
            'contact_id' => $contactId,
            'country_id' => $country_id,
            'tanggal_kirim' => $this->tanggal_kirim,
            'tanggal_terima' => $this->tanggal_terima ?: null,
            'biaya_prangko' => $this->biaya_prangko,
            'deskripsi_gambar' => $this->deskripsi_gambar,
            'nilai_asal' => $this->nilai_asal,
            'mata_uang' => $this->mata_uang,
            'kurs_idr' => $this->kurs_idr,
            'foto_depan' => $this->currentFotoDepan,
            'foto_belakang' => $this->currentFotoBelakang,
        ]);

        return redirect()->route('view', ['id' => $this->id]);
    }

    public function removeNewStamp($index)
    {
        if (isset($this->newStampsBase64[$index])) {
            unset($this->newStampsBase64[$index]);
            $this->newStampsBase64 = array_values($this->newStampsBase64);
        }
    }

    public function refreshGeocoding(\App\Services\GeocodingService $geoService)
    {
        if (empty($this->alamat) || empty($this->negara)) {
            session()->flash('geo_error', 'Address and country are required.');

            return;
        }

        $coords = $geoService->getCoordinates($this->alamat, $this->negara);

        if ($coords['lat'] != 0 || $coords['lng'] != 0) {
            $postcard = Postcard::where('id', $this->id)->where('user_id', auth()->id())->first();
            if ($postcard && $postcard->contact) {
                $postcard->contact->update([
                    'lat' => $coords['lat'],
                    'lng' => $coords['lng'],
                ]);
                session()->flash('geo_success', 'Location updated successfully! ('.round($coords['lat'], 4).', '.round($coords['lng'], 4).')');
            }
        } else {
            session()->flash('geo_error', 'Could not find location. Please check the address.');
        }
    }

    private function saveBase64Image($base64, $prefix, $suffix)
    {
        $image_parts = explode(';base64,', $base64);
        $image_base64 = base64_decode($image_parts[1]);
        $filename = 'uploads/'.$prefix.'_'.time().'_'.$suffix.'.jpg';
        Storage::disk('public')->put($filename, $image_base64);

        return 'storage/'.$filename;
    }

    public function render()
    {
        return view('livewire.edit-postcard')->layout('components.layouts.app', ['title' => 'Edit Postcard']);
    }
}
