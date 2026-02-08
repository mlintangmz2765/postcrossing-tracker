<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class StampGallery extends Component
{
    public $sliderStamps = [];

    public $galleryStamps = [];

    public function mount()
    {
        $user_id = Auth::id();

        $this->galleryStamps = DB::table('postcard_stamps')
            ->join('postcards', 'postcard_stamps.postcard_id', '=', 'postcards.id')
            ->leftJoin('countries', 'postcards.country_id', '=', 'countries.id')
            ->select('postcard_stamps.foto_prangko', 'postcards.id', DB::raw('COALESCE(countries.nama_inggris, countries.nama_indonesia) as negara'), 'postcards.postcard_id')
            ->where('postcards.user_id', $user_id)
            ->orderBy(DB::raw('COALESCE(countries.nama_inggris, countries.nama_indonesia)'), 'asc')
            ->get()
            ->toArray();
    }

    public function render()
    {
        return view('livewire.stamp-gallery')->layout('components.layouts.app', ['title' => 'Stamp Collection Library']);
    }
}
