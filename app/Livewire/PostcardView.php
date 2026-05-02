<?php

namespace App\Livewire;

use App\Models\Postcard;
use App\Models\PostcardStamp;
use App\Traits\CalculatesDistance;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PostcardView extends Component
{
    use CalculatesDistance;

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
        $this->originLat = (float) config('app.home_lat');
        $this->originLng = (float) config('app.home_lng');
        $this->postcardId = $id;

        $this->card = Postcard::with(['contact', 'country'])
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (! $this->card) {
            abort(404);
        }

        // Calculate Distance
        if ($this->card->contact?->lat && $this->card->contact?->lng && is_numeric($this->card->contact->lat)) {
            $this->distance = $this->calculateDistance($this->originLat, $this->originLng, (float) $this->card->contact->lat, (float) $this->card->contact->lng);
        }

        if ($this->card->tanggal_kirim && $this->card->tanggal_terima && $this->card->tanggal_terima > '2000-01-01') {
            $start = \Carbon\Carbon::parse($this->card->tanggal_kirim);
            $end = \Carbon\Carbon::parse($this->card->tanggal_terima);
            $this->travelTime = $start->diffInDays($end);
        }

        // Fetch Stamps
        $this->stamps = PostcardStamp::where('postcard_id', $this->postcardId)->get();
    }

    // calculateDistance() provided by CalculatesDistance trait

    public function render()
    {
        $title = 'Postcard Details - '.($this->card->postcard_id ?: 'Direct Swap');

        return view('livewire.postcard-view')
            ->layout('components.layouts.app', ['title' => $title]);
    }
}
