<?php

namespace App\Livewire;

use App\Models\Postcard;
use App\Support\Money;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Statistics extends Component
{
    public $sentStats = [];

    public $receivedStats = [];

    /**
     * Cards whose cost was recorded in a different base currency; excluded
     * from the cost columns rather than summed as if they were comparable.
     */
    public $otherCurrencyCount = 0;

    public function mount()
    {
        $user_id = Auth::id();
        $base = Money::base();

        $this->sentStats = DB::table('postcards')
            ->leftJoin('countries', 'postcards.country_id', '=', 'countries.id')
            ->select(
                DB::raw('COALESCE(countries.nama_inggris, countries.nama_indonesia) as negara'),
                DB::raw('COUNT(*) as total_dikirim'),
                DB::raw("SUM(CASE WHEN postcard_id LIKE '%-%' THEN 1 ELSE 0 END) as pc_count"),
                DB::raw("SUM(CASE WHEN postcard_id NOT LIKE '%-%' OR postcard_id IS NULL OR postcard_id = '' THEN 1 ELSE 0 END) as swap_count"),
                DB::raw("SUM(CASE WHEN tanggal_terima IS NOT NULL AND tanggal_terima > '2000-01-01' THEN 1 ELSE 0 END) as sudah_sampai"),
                DB::raw("AVG(CASE WHEN tanggal_terima IS NOT NULL AND tanggal_terima > '2000-01-01' THEN DATEDIFF(tanggal_terima, tanggal_kirim) END) as avg_days"),
                DB::raw("MIN(CASE WHEN tanggal_terima IS NOT NULL AND tanggal_terima > '2000-01-01' THEN DATEDIFF(tanggal_terima, tanggal_kirim) END) as min_days"),
                DB::raw("MAX(CASE WHEN tanggal_terima IS NOT NULL AND tanggal_terima > '2000-01-01' THEN DATEDIFF(tanggal_terima, tanggal_kirim) END) as max_days"),
            )
            ->selectRaw('SUM(CASE WHEN postcards.base_currency = ? THEN biaya_prangko END) as total_biaya', [$base])
            ->where('postcards.user_id', $user_id)
            ->where('postcards.type', 'sent')
            ->groupBy(DB::raw('COALESCE(countries.nama_inggris, countries.nama_indonesia)'))
            ->orderByDesc('total_dikirim')
            ->get()
            ->toArray();

        $this->receivedStats = DB::table('postcards')
            ->leftJoin('countries', 'postcards.country_id', '=', 'countries.id')
            ->select(
                DB::raw('COALESCE(countries.nama_inggris, countries.nama_indonesia) as negara'),
                DB::raw('COUNT(*) as total_diterima'),
                DB::raw("SUM(CASE WHEN postcard_id LIKE '%-%' THEN 1 ELSE 0 END) as pc_count"),
                DB::raw("SUM(CASE WHEN postcard_id NOT LIKE '%-%' OR postcard_id IS NULL OR postcard_id = '' THEN 1 ELSE 0 END) as swap_count"),
                DB::raw('AVG(CASE WHEN tanggal_terima IS NOT NULL AND tanggal_kirim IS NOT NULL THEN DATEDIFF(tanggal_terima, tanggal_kirim) END) as avg_days'),
                DB::raw('MIN(CASE WHEN tanggal_terima IS NOT NULL AND tanggal_kirim IS NOT NULL THEN DATEDIFF(tanggal_terima, tanggal_kirim) END) as min_days'),
                DB::raw('MAX(CASE WHEN tanggal_terima IS NOT NULL AND tanggal_kirim IS NOT NULL THEN DATEDIFF(tanggal_terima, tanggal_kirim) END) as max_days'),
            )
            ->selectRaw('SUM(CASE WHEN postcards.base_currency = ? THEN biaya_prangko END) as total_nilai', [$base])
            ->selectRaw('AVG(CASE WHEN postcards.base_currency = ? THEN biaya_prangko END) as avg_nilai', [$base])
            ->where('postcards.user_id', $user_id)
            ->where('postcards.type', 'received')
            ->groupBy(DB::raw('COALESCE(countries.nama_inggris, countries.nama_indonesia)'))
            ->orderByDesc('total_diterima')
            ->get()
            ->toArray();

        $this->otherCurrencyCount = Postcard::where('user_id', $user_id)->count()
            - Postcard::where('user_id', $user_id)->inBaseCurrency()->count();
    }

    public function render()
    {
        return view('livewire.statistics')->layout('components.layouts.app', ['title' => 'Postcard Statistics & Insights']);
    }
}
