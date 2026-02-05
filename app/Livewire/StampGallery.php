<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StampGallery extends Component
{
    public $sliderStamps = [];
    public $galleryStamps = [];

    public function mount()
    {
        $user_id = Auth::id();

        // Slider: Random order
        $this->sliderStamps = DB::table('postcard_stamps')
            ->join('postcards', 'postcard_stamps.postcard_id', '=', 'postcards.id')
            ->select('postcard_stamps.foto_prangko', 'postcards.id')
            ->where('postcards.user_id', $user_id)
            ->inRandomOrder()
            ->get()
            ->toArray();

        // Gallery: Ordered by Country
        $this->galleryStamps = DB::table('postcard_stamps')
            ->join('postcards', 'postcard_stamps.postcard_id', '=', 'postcards.id')
            ->select('postcard_stamps.foto_prangko', 'postcards.id', 'postcards.negara', 'postcards.postcard_id')
            ->where('postcards.user_id', $user_id)
            ->orderBy('postcards.negara', 'asc')
            ->get()
            ->toArray();
    }

    public function render()
    {
        return view('livewire.stamp-gallery')->layout('components.layouts.app', ['title' => 'Stamp Collection Library']);
    }
}
