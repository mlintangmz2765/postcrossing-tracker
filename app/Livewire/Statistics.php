<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Statistics extends Component
{
    public $sentStats = [];
    public $receivedStats = [];

    public function mount()
    {
        $user_id = Auth::id();

        // queries mirroring legacy stats_pivot.php

        // SENT Statistics
        $this->sentStats = DB::table('postcards')
            ->select(
                'negara',
                DB::raw('COUNT(*) as total_dikirim'),
                DB::raw("SUM(CASE WHEN postcard_id LIKE '%-%' THEN 1 ELSE 0 END) as pc_count"),
                DB::raw("SUM(CASE WHEN postcard_id NOT LIKE '%-%' OR postcard_id IS NULL OR postcard_id = '' THEN 1 ELSE 0 END) as swap_count"),
                DB::raw("SUM(CASE WHEN tanggal_terima IS NOT NULL AND tanggal_terima > '2000-01-01' THEN 1 ELSE 0 END) as sudah_sampai"),
                DB::raw("AVG(CASE WHEN tanggal_terima IS NOT NULL AND tanggal_terima > '2000-01-01' THEN DATEDIFF(tanggal_terima, tanggal_kirim) END) as avg_days"),
                DB::raw("MIN(CASE WHEN tanggal_terima IS NOT NULL AND tanggal_terima > '2000-01-01' THEN DATEDIFF(tanggal_terima, tanggal_kirim) END) as min_days"),
                DB::raw("MAX(CASE WHEN tanggal_terima IS NOT NULL AND tanggal_terima > '2000-01-01' THEN DATEDIFF(tanggal_terima, tanggal_kirim) END) as max_days"),
                DB::raw("SUM(biaya_prangko) as total_biaya")
            )
            ->where('user_id', $user_id)
            ->where('type', 'sent')
            ->groupBy('negara')
            ->orderByDesc('total_dikirim')
            ->get()
            ->toArray();

        // RECEIVED Statistics
        // Note: For 'received', tanggal_terima is usually set, but we perform similar checks for consistency if needed.
        // The legacy SQL checked total_diterima counts mostly.
        $this->receivedStats = DB::table('postcards')
            ->select(
                'negara',
                DB::raw('COUNT(*) as total_diterima'),
                DB::raw("SUM(CASE WHEN postcard_id LIKE '%-%' THEN 1 ELSE 0 END) as pc_count"),
                DB::raw("SUM(CASE WHEN postcard_id NOT LIKE '%-%' OR postcard_id IS NULL OR postcard_id = '' THEN 1 ELSE 0 END) as swap_count"),
                DB::raw("AVG(CASE WHEN tanggal_terima IS NOT NULL AND tanggal_kirim IS NOT NULL THEN DATEDIFF(tanggal_terima, tanggal_kirim) END) as avg_days"),
                DB::raw("MIN(CASE WHEN tanggal_terima IS NOT NULL AND tanggal_kirim IS NOT NULL THEN DATEDIFF(tanggal_terima, tanggal_kirim) END) as min_days"),
                DB::raw("MAX(CASE WHEN tanggal_terima IS NOT NULL AND tanggal_kirim IS NOT NULL THEN DATEDIFF(tanggal_terima, tanggal_kirim) END) as max_days"),
                DB::raw("SUM(biaya_prangko) as total_nilai"),
                DB::raw("AVG(biaya_prangko) as avg_nilai")
            )
            ->where('user_id', $user_id)
            ->where('type', 'received')
            ->groupBy('negara')
            ->orderByDesc('total_diterima')
            ->get()
            ->toArray();
    }

    public function render()
    {
        return view('livewire.statistics')->layout('components.layouts.app', ['title' => 'Postcard Statistics & Insights']);
    }
}
