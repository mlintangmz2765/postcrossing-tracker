<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Intervention\Image\Facades\Image;

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
    
    // Currency fields (for received)
    public $nilai_asal;
    public $mata_uang = 'IDR';
    public $kurs_idr = 1;

    // Images
    public $currentFotoDepan;
    public $currentFotoBelakang;
    
    // JS Scanner will fill these Base64 strings
    public $newFotoDepanBase64; 
    public $newFotoBelakangBase64;
    public $newStampsBase64 = []; // Array of Base64 strings

    public $existingStamps = [];

    protected $listeners = ['refreshStamps' => '$refresh'];

    public function mount($id)
    {
        $this->id = $id;
        $data = DB::table('postcards')->where('id', $id)->where('user_id', auth()->id())->first();

        if (!$data) {
            return redirect()->route('dashboard');
        }

        $this->type = $data->type;
        $this->postcard_id = $data->postcard_id;
        $this->nama_kontak = $data->nama_kontak;
        $this->negara = $data->negara;
        $this->nomor_telepon = $data->nomor_telepon;
        $this->alamat = $data->alamat;
        $this->tanggal_kirim = $data->tanggal_kirim;
        $this->tanggal_terima = $data->tanggal_terima;
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
        $this->existingStamps = DB::table('postcard_stamps')->where('postcard_id', $this->id)->get();
    }

    public function deletePostcard()
    {
        $data = DB::table('postcards')->where('id', $this->id)->where('user_id', auth()->id())->first();
        
        if ($data) {
            // Delete associated images
            if ($data->foto_depan && file_exists(public_path($data->foto_depan))) {
                @unlink(public_path($data->foto_depan));
            }
            if ($data->foto_belakang && file_exists(public_path($data->foto_belakang))) {
                @unlink(public_path($data->foto_belakang));
            }
            
            // Delete stamps and their images
            $stamps = DB::table('postcard_stamps')->where('postcard_id', $this->id)->get();
            foreach ($stamps as $stamp) {
                if (file_exists(public_path($stamp->foto_prangko))) {
                    @unlink(public_path($stamp->foto_prangko));
                }
            }
            DB::table('postcard_stamps')->where('postcard_id', $this->id)->delete();
            
            // Delete postcard record
            DB::table('postcards')->where('id', $this->id)->delete();
        }
        
        return redirect()->route('home');
    }

    public function deleteStamp($stampId)
    {
        $stamp = DB::table('postcard_stamps')
            ->join('postcards', 'postcard_stamps.postcard_id', '=', 'postcards.id')
            ->where('postcard_stamps.id', $stampId)
            ->where('postcards.user_id', auth()->id())
            ->select('postcard_stamps.*')
            ->first();
        if ($stamp) {
            if (file_exists(public_path($stamp->foto_prangko))) {
                @unlink(public_path($stamp->foto_prangko));
            }
            DB::table('postcard_stamps')->where('id', $stampId)->delete();
            $this->loadStamps();
        }
    }
    
    public function rotateStamp($stampId)
    {
        // Simple rotation logic using GD
        $stamp = DB::table('postcard_stamps')
            ->join('postcards', 'postcard_stamps.postcard_id', '=', 'postcards.id')
            ->where('postcard_stamps.id', $stampId)
            ->where('postcards.user_id', auth()->id())
            ->select('postcard_stamps.*')
            ->first();
        if ($stamp && file_exists(public_path($stamp->foto_prangko))) {
            $path = public_path($stamp->foto_prangko);
            $source = imagecreatefromjpeg($path);
            $rotate = imagerotate($source, -90, 0); // Rotate 90 deg clockwise (negative in GD?)
            imagejpeg($rotate, $path);
            imagedestroy($source);
            imagedestroy($rotate);
            $this->loadStamps(); // Refresh UI
            $this->dispatch('stampRotated', $stampId); // Notify frontend to reload image
        }
    }

    public function update(\App\Services\GeocodingService $geoService)
    {
        $this->validate([
            'postcard_id' => 'required',
            'negara' => 'required',
            'alamat' => 'required',
            'tanggal_kirim' => 'required|date',
        ]);

        // Process Base64 Images if present
        if ($this->newFotoDepanBase64) {
            if ($this->currentFotoDepan && file_exists(public_path($this->currentFotoDepan))) @unlink(public_path($this->currentFotoDepan));
            $this->currentFotoDepan = $this->saveBase64Image($this->newFotoDepanBase64, 'f', 'd');
        }

        if ($this->newFotoBelakangBase64) {
            if ($this->currentFotoBelakang && file_exists(public_path($this->currentFotoBelakang))) @unlink(public_path($this->currentFotoBelakang));
            $this->currentFotoBelakang = $this->saveBase64Image($this->newFotoBelakangBase64, 'b', 'b');
        }

        // Process New Stamps
        foreach ($this->newStampsBase64 as $idx => $base64) {
             $path = $this->saveBase64Image($base64, 'stamp', $idx);
             DB::table('postcard_stamps')->insert([
                 'postcard_id' => $this->id,
                 'foto_prangko' => $path
             ]);
        }

        // Handle Coordinates - Geocode if address/country changed
        $lat = null;
        $lng = null;
        $original = DB::table('postcards')->where('id', $this->id)->where('user_id', auth()->id())->first();
        
        if ($original && ($original->alamat !== $this->alamat || $original->negara !== $this->negara)) {
            $coords = $geoService->getCoordinates($this->alamat, $this->negara);
            if ($coords['lat'] != 0) {
                $lat = $coords['lat'];
                $lng = $coords['lng'];
            }
        }

        $updateData = [
            'postcard_id' => $this->postcard_id,
            'nama_kontak' => $this->nama_kontak,
            'negara' => $this->negara,
            'nomor_telepon' => $this->nomor_telepon,
            'alamat' => $this->alamat,
            'tanggal_kirim' => $this->tanggal_kirim,
            'tanggal_terima' => $this->tanggal_terima ?: null,
            'biaya_prangko' => $this->biaya_prangko,
            'deskripsi_gambar' => $this->deskripsi_gambar,
            'nilai_asal' => $this->nilai_asal,
            'mata_uang' => $this->mata_uang,
            'kurs_idr' => $this->kurs_idr,
            'foto_depan' => $this->currentFotoDepan,
            'foto_belakang' => $this->currentFotoBelakang,
        ];

        if ($lat !== null && $lng !== null) {
            $updateData['lat'] = $lat;
            $updateData['lng'] = $lng;
        }

        DB::table('postcards')->where('id', $this->id)->where('user_id', auth()->id())->update($updateData);

        // Sync Contact
        $this->syncContact();

        return redirect()->route('view', ['id' => $this->id]);
    }

    private function saveBase64Image($base64, $prefix, $suffix)
    {
        $image_parts = explode(";base64,", $base64);
        $image_base64 = base64_decode($image_parts[1]);
        $filename = 'uploads/' . $prefix . '_' . time() . '_' . $suffix . '.jpg';
        
        // Use Storage facade for safer file writing
        \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $image_base64);
        
        return 'storage/' . $filename;
    }

    private function syncContact()
    {
        $exists = DB::table('contacts')
            ->where('nama_kontak', $this->nama_kontak)
            ->where('user_id', auth()->id() ?? 1)
            ->first();
        
        $data = [
            'alamat' => $this->alamat,
            'negara' => $this->negara,
            'nomor_telepon' => $this->nomor_telepon,
        ];

        if ($exists) {
            DB::table('contacts')->where('id', $exists->id)->update($data);
        } else {
            DB::table('contacts')->insert(array_merge($data, [
                'user_id' => auth()->id() ?? 1,
                'nama_kontak' => $this->nama_kontak
            ]));
        }
    }



    public function render()
    {
        return view('livewire.edit-postcard')->layout('components.layouts.app', ['title' => 'Edit Postcard']);
    }
}
