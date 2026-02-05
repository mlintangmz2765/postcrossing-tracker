<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Dashboard extends Component
{
    public $statsSent = [];
    public $statsReceived = [];
    public $chartData = [];
    public $mapMarkers = [];
    public $recentNotifications = [];

    public function mount()
    {
        $user_id = Auth::id();
        $myLat = -7.756378; // Yogyakarta
        $myLng = 110.376618;

        // 1. Stats Calculation
        $this->statsSent = $this->getSummaryStats('sent', $user_id, $myLat, $myLng);
        $this->statsReceived = $this->getSummaryStats('received', $user_id, $myLat, $myLng);

        // 2. Notifications
        $this->recentNotifications = DB::table('postcards')
            ->where('user_id', $user_id)
            ->where('type', 'sent')
            ->whereNotNull('tanggal_terima')
            ->where('notif_read', 0)
            ->limit(5)
            ->get(); // Using 'sent' arrived as notification based on legacy logic roughly

        // 3. Chart Data
        $this->prepareChartData($user_id);

        // 4. Map Markers
        $this->prepareMapData($user_id);
    }

    private function getSummaryStats($type, $user_id, $lat, $lng)
    {
        // Legacy Formula: 6371 * acos(cos(radians($myLat)) * cos(radians(lat)) * cos(radians(lng) - radians($myLng)) + sin(radians($myLat)) * sin(radians(lat)))
        $distanceQuery = "SUM(6371 * acos(cos(radians($lat)) * cos(radians(lat)) * cos(radians(lng) - radians($lng)) + sin(radians($lat)) * sin(radians(lat)))) as total_km";

        $res = DB::table('postcards')
            ->selectRaw("
                COUNT(*) as total_cards,
                COUNT(DISTINCT negara) as total_countries,
                COUNT(DISTINCT nama_kontak) as total_people,
                SUM(CASE WHEN postcard_id LIKE '%-%' THEN 1 ELSE 0 END) as total_pc,
                SUM(CASE WHEN (postcard_id NOT LIKE '%-%' OR postcard_id IS NULL OR postcard_id = '') THEN 1 ELSE 0 END) as total_swap,
                $distanceQuery
            ")
            ->where('user_id', $user_id)
            ->where('type', $type)
            ->whereNotNull('lat')
            ->where('lat', '!=', 0)
            ->first();

        $km = $res->total_km ?? 0;
        
        return [
            'countries' => $res->total_countries ?? 0,
            'people'    => $res->total_people ?? 0,
            'cards'     => $res->total_cards ?? 0,
            'pc'        => $res->total_pc ?? 0,
            'swap'      => $res->total_swap ?? 0,
            'km'        => $km,
            'laps'      => $km / 40075 // Earth Circumference ~40,075km
        ];
    }

    private function prepareChartData($user_id)
    {
        // Line Chart: Last 12 Months
        $labels = [];
        $sentData = [];
        $receivedData = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $m = $date->format('Y-m');
            $labels[] = $date->format('M Y');

            $sentData[] = DB::table('postcards')
                ->where('user_id', $user_id)
                ->where('type', 'sent')
                ->where('tanggal_kirim', 'like', "$m%")
                ->count();

            $receivedData[] = DB::table('postcards')
                ->where('user_id', $user_id)
                ->where('type', 'received')
                ->where(function($q) use ($m) {
                    $q->where('tanggal_terima', 'like', "$m%")
                      ->orWhere(function($sub) use ($m) {
                          $sub->where('type', 'received')->where('tanggal_kirim', 'like', "$m%");
                      });
                })
                ->count();
        }

        // Doughnut Charts: Top Countries
        $sentCountries = DB::table('postcards')
            ->select('negara', DB::raw('count(*) as total'))
            ->where('user_id', $user_id)
            ->where('type', 'sent')
            ->groupBy('negara')
            ->orderByDesc('total')
            // ->limit(10) // Removed to show all countries
            ->pluck('total', 'negara')
            ->toArray();

        $receivedCountries = DB::table('postcards')
            ->select('negara', DB::raw('count(*) as total'))
            ->where('user_id', $user_id)
            ->where('type', 'received')
            ->groupBy('negara')
            ->orderByDesc('total')
            // ->limit(10) // Removed
            ->pluck('total', 'negara')
            ->toArray();

        $this->chartData = [
            'labels' => $labels,
            'sent' => $sentData,
            'received' => $receivedData,
            'sentCountries' => $sentCountries,
            'receivedCountries' => $receivedCountries
        ];
    }

    private function prepareMapData($user_id)
    {
        $this->mapMarkers = DB::table('postcards')
            ->select('lat', 'lng', 'type', 'alamat', 'nama_kontak')
            ->where('user_id', $user_id)
            ->whereNotNull('lat')
            ->where('lat', '!=', 0)
            ->get()
            ->map(function($m) {
                return [
                    'lat' => (float)$m->lat,
                    'lng' => (float)$m->lng,
                    'color' => ($m->type == 'sent') ? '#007bff' : '#28a745', // Legacy: Blue=Sent, Green=Received
                    'content' => "<b>".htmlspecialchars($m->nama_kontak)."</b><br>".htmlspecialchars($m->alamat)
                ];
            })
            ->toArray();
    }

    public function markAsRead($id)
    {
        DB::table('postcards')->where('id', $id)->update(['notif_read' => 1]);
        $this->mount(); // Refresh
    }

    public function render()
    {
        return view('livewire.dashboard')->layout('components.layouts.app', ['title' => 'Dashboard - Postcard Tracker']);
    }
}
