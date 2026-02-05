<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PostcardGallery extends Component
{
    public $cards = [];

    public function mount()
    {
        $user_id = Auth::id();
        $this->cards = DB::table('postcards')
            ->select('id', 'postcard_id', 'foto_depan', 'foto_belakang', 'deskripsi_gambar', 'negara')
            ->where('user_id', $user_id)
            ->whereNotNull('foto_depan')
            ->where('foto_depan', '!=', '')
            ->orderByDesc('tanggal_kirim')
            ->get()
            ->toArray();

        foreach ($this->cards as &$card) {
            // Ratio Front
            $ratioFront = "4/3";
            $pathF = public_path($card->foto_depan);
            if (!empty($card->foto_depan) && file_exists($pathF)) {
                $sizeF = @getimagesize($pathF);
                if ($sizeF) {
                    $ratioFront = $sizeF[0] . "/" . $sizeF[1];
                }
            }
            $card->ratioFront = $ratioFront;

            // Ratio Back
            $ratioBack = $ratioFront; // Default same
            $pathB = !empty($card->foto_belakang) ? public_path($card->foto_belakang) : null;
            if ($pathB && file_exists($pathB)) {
                $sizeB = @getimagesize($pathB);
                if ($sizeB) {
                    $ratioBack = $sizeB[0] . "/" . $sizeB[1];
                }
            }
            $card->ratioBack = $ratioBack;
        }
    }

    public function render()
    {
        return view('livewire.postcard-gallery')->layout('components.layouts.app', ['title' => 'Archive Gallery - Postcard Tracker']);
    }
}
