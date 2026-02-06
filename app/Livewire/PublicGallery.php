<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Url;
use App\Models\Postcard;
use App\Models\PostcardStamp;
use App\Models\Country;
use Illuminate\Support\Facades\DB;

class PublicGallery extends Component
{
    #[Url(as: 'view')]
    public $viewMode = 'all';

    #[Url(as: 'filter')]
    public $filterInput = '';

    public $isChina = false;

    public function mount()
    {
        // Detect China Viewer
        $this->detectChinaViewer();
        
        // Properties are automatically filled from URL
    }
    
    public function resetFilter()
    {
        $this->filterInput = '';
        // View mode stays as is, or reset if desired. Usually reset just clears filter.
    }

    public function detectChinaViewer()
    {
        // 1. URL Override (Explicit choice)
        if (request()->has('china')) {
            $this->isChina = (request()->query('china') == '1');
            return;
        }

        // 2. Cloudflare IP Detection
        $cfCountry = request()->header('CF-IPCountry');
        if ($cfCountry === 'CN') {
            $this->isChina = true;
            return;
        }

        $this->isChina = false;
    }

    public function render()
    {
        // 1. Get Filter Options
        $continents = Country::whereHas('postcards', function($q) {
            $q->where('type', 'received')->whereNotNull('foto_depan')->where('foto_depan', '!=', '');
        })->distinct()->pluck('benua')->sort();

        $subcontinents = Country::whereHas('postcards', function($q) {
            $q->where('type', 'received')->whereNotNull('foto_depan')->where('foto_depan', '!=', '');
        })->distinct()->pluck('subbenua')->sort();
        
        $countries = Country::whereHas('postcards', function($q) {
            $q->where('type', 'received')->whereNotNull('foto_depan')->where('foto_depan', '!=', '');
        })->distinct()->pluck('nama_inggris')->sort();

        // 2. Query Postcards
        $query = Postcard::with('country_data')
            ->where('type', 'received');

        if ($this->viewMode === 'all' || $this->viewMode === 'postcard') {
            $query->whereNotNull('foto_depan')->where('foto_depan', '!=', '');
        } 
        // Logic for stamp view mode
        // We might need a separate query or handle it in blade. 
        // Let's follow structure: data is $postcards array.

        // Filter Logic
        if (!empty($this->filterInput)) {
            $parts = explode(':', $this->filterInput, 2);
            if (count($parts) === 2) {
                $type = $parts[0];
                $val = $parts[1];
                $query->whereHas('country_data', function($q) use ($type, $val) {
                    if ($type === 'continent') $q->where('benua', $val);
                    if ($type === 'subcontinent') $q->where('subbenua', $val);
                    if ($type === 'country') $q->where('nama_inggris', $val);
                });
            }
        }

        $query->orderBy('tanggal_kirim', 'desc');
        
        // If viewMode is 'stamp', we need stamp data primarily.
        $postcards = [];
        $sliderStamps = [];
        $mapMarkers = [];
        $stampsByCard = [];

        if ($this->viewMode === 'stamp') {
             // Fetch stamp images
             // Eloquent equivalent:
             $stamps = PostcardStamp::with(['postcard.country_data'])
                ->whereHas('postcard', function($q) use ($query) {
                    $q->mergeConstraintsFrom($query);
                })->get();
             
             foreach($stamps as $s) {
                 $p = $s->postcard;
                 if(!$p) continue;
                 $cName = $p->country_data->nama_inggris ?? $p->negara;
                 
                 // Calculate Aspect Ratio for Stamp
                 $imgRatioStyle = "aspect-ratio: 4/3;";
                 if (!empty($s->foto_prangko)) {
                     $absPath = public_path($s->foto_prangko);
                     if (file_exists($absPath)) {
                         $dims = @getimagesize($absPath);
                         if ($dims && $dims[1] > 0) {
                             $imgRatioStyle = "aspect-ratio: {$dims[0]} / {$dims[1]};";
                         }
                     }
                 }

                 $row = [
                     'main_image' => $s->foto_prangko,
                     'postcard_id' => $p->postcard_id,
                     'pid' => $p->id,
                     'display_country' => $cName,
                     'deskripsi_gambar' => $p->deskripsi_gambar,
                     'lat' => $p->lat,
                     'lng' => $p->lng,
                     'type' => $p->type,
                     'ratio_style' => $imgRatioStyle,
                 ];
                 $postcards[] = $row;
                 $sliderStamps[] = ['img' => $s->foto_prangko, 'caption' => $cName . " Stamp"];
                 
                 if (!empty($p->lat) && !empty($p->lng)) {
                     // Map Marker Logic
                     // Query from existing result
                 }
             }

        } else {
             // View All / Postcard
             $cards = $query->get();
             foreach($cards as $p) {
                 $cName = $p->country_data->nama_inggris ?? $p->negara;
                 
                 // Calculate optimal aspect ratio
                 // Path resolution for local storage
                 $imgRatioStyle = "aspect-ratio: 4/3;"; // Default
                 $relPath = $p->foto_depan;
                 // Path resolution for local storage
                 // We need absolute path for getimagesize
                 if (!empty($relPath)) {
                     $absPath = public_path($relPath);
                     if (file_exists($absPath)) {
                         $dims = @getimagesize($absPath);
                         if ($dims && $dims[1] > 0) {
                             $imgRatioStyle = "aspect-ratio: {$dims[0]} / {$dims[1]};";
                         }
                     }
                 }

                 $row = [
                     'main_image' => $p->foto_depan,
                     'postcard_id' => $p->postcard_id,
                     'pid' => $p->id,
                     'display_country' => $cName,
                     'deskripsi_gambar' => $p->deskripsi_gambar,
                     'lat' => $p->lat,
                     'lng' => $p->lng,
                     'type' => $p->type,
                     'ratio_style' => $imgRatioStyle // Pass to view
                 ];
                 $postcards[] = $row;
                 
                 // Map Markers
                 if (!empty($p->lat) && !empty($p->lng)) {
                     $mapMarkers[$p->id] = [
                         'id' => $p->id,
                         'postcard_id' => $p->postcard_id,
                         'lat' => (float)$p->lat,
                         'lng' => (float)$p->lng,
                         'country' => $cName,
                         'color' => '#e63946'
                     ];
                 }
             }

             // Stamps for 'all' view
             if ($this->viewMode === 'all') {
                 // Fetch all stamps for processed postcards
                 // Optimized:
                 $pids = $cards->pluck('id');
                 $allStamps = PostcardStamp::whereIn('postcard_id', $pids)->get();
                 foreach($allStamps as $s) {
                     $stampsByCard[$s->postcard_id][] = [
                         'postcard_id' => $s->postcard_id,
                         'foto_prangko' => $s->foto_prangko
                     ];
                 }
             }
        }

        // Map Data JSON
        $mapDataJson = json_encode(array_values($mapMarkers));

        return view('livewire.public-gallery', [
            'continents' => $continents,
            'subcontinents' => $subcontinents,
            'countries' => $countries,
            'postcards' => $postcards,
            'sliderStamps' => $sliderStamps,
            'mapMarkers' => $mapDataJson, // JSON string for JS
            'stampsByCard' => $stampsByCard,
            'isChina' => $this->isChina
        ])->layout('components.layouts.app', ['title' => 'Postcard Public Gallery - Virtual Trip']);
    }
}
