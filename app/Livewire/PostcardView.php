<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PostcardView extends Component
{
    public $postcardId;
    public $card;
    public $stamps = [];

    public $distance = 0;
    public $travelTime = 0;

    // Origin (Home)
    public $originLat;
    public $originLng;

    public function mount($id)
    {
        $this->originLat = (float) env('HOME_LAT', 0);
        $this->originLng = (float) env('HOME_LNG', 0);
        $this->postcardId = $id;
        
        $this->card = DB::table('postcards')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$this->card) {
            abort(404);
        }

        // Calculate Distance
        if ($this->card->lat && $this->card->lng) {
            $this->distance = $this->calculateDistance($this->originLat, $this->originLng, $this->card->lat, $this->card->lng);
        }

        if ($this->card->tanggal_kirim && $this->card->tanggal_terima && $this->card->tanggal_terima > '2000-01-01') {
            $start = \Carbon\Carbon::parse($this->card->tanggal_kirim);
            $end = \Carbon\Carbon::parse($this->card->tanggal_terima);
            $this->travelTime = $start->diffInDays($end);
        }

        // Fetch Stamps
        $this->stamps = DB::table('postcard_stamps')->where('postcard_id', $this->postcardId)->get();
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        return round($miles * 1.609344);
    }

    public function render()
    {
        $title = 'Postcard Details - ' . ($this->card->postcard_id ?: 'Direct Swap');
        return view('livewire.postcard-view')
            ->layout('components.layouts.app', ['title' => $title]);
    }
}
