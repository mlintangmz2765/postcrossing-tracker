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

    // The owner's end of the journey: this card's origin, or home
    public $originLat;

    public $originLng;

    public $originName;

    public $originIsHome = true;

    public function mount($id)
    {
        $this->postcardId = $id;

        $this->card = Postcard::with(['contact', 'country', 'origin'])
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (! $this->card) {
            abort(404);
        }

        $origin = $this->card->ownerPlace();
        $this->originLat = $origin->lat;
        $this->originLng = $origin->lng;
        $this->originName = $origin->name;
        $this->originIsHome = $origin->isHome;

        $this->distance = $this->card->distanceKm() ?? 0;

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
