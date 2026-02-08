<?php

namespace App\Livewire;

use App\Models\Postcard;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

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
        $myLat = (float) config('app.home_lat');
        $myLng = (float) config('app.home_lng');

        $this->statsSent = $this->getSummaryStats('sent', $user_id, $myLat, $myLng);
        $this->statsReceived = $this->getSummaryStats('received', $user_id, $myLat, $myLng);

        $this->recentNotifications = Postcard::with(['contact', 'country'])
            ->where('user_id', $user_id)
            ->where('type', 'sent')
            ->whereNotNull('tanggal_terima')
            ->where('notif_read', 0)
            ->limit(5)
            ->get();

        $this->prepareChartData($user_id);
        $this->prepareMapData($user_id);
    }

    private function getSummaryStats($type, $user_id, $lat, $lng)
    {
        $cards = Postcard::with(['contact', 'country'])
            ->where('user_id', $user_id)
            ->where('type', $type)
            ->get();

        $totalKm = 0;
        $totalCountries = $cards->unique('country_id')->count();
        $totalPeople = $cards->unique('contact_id')->count();
        $totalPc = $cards->filter(fn ($c) => str_contains($c->postcard_id ?? '', '-'))->count();
        $totalSwap = $cards->filter(fn ($c) => ! str_contains($c->postcard_id ?? '', '-') || empty($c->postcard_id))->count();

        foreach ($cards as $card) {
            $lat_c = $card->contact?->lat;
            $lng_c = $card->contact?->lng;
            if ($lat_c && $lng_c && is_numeric($lat_c) && is_numeric($lng_c)) {
                $totalKm += $this->calculateDistance($lat, $lng, (float) $lat_c, (float) $lng_c);
            }
        }

        return [
            'countries' => $totalCountries,
            'people' => $totalPeople,
            'cards' => $cards->count(),
            'pc' => $totalPc,
            'swap' => $totalSwap,
            'km' => $totalKm,
            'laps' => $totalKm / 40075,
        ];
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    private function prepareChartData($user_id)
    {
        $labels = [];
        $sentData = [];
        $receivedData = [];

        // Pre-fetch all relevant cards for the last 12 months for optimization
        $cutoff = Carbon::now()->subMonths(11)->startOfMonth();
        $allCards = Postcard::where('user_id', $user_id)
            ->where(function ($q) use ($cutoff) {
                $q->where('tanggal_kirim', '>=', $cutoff)
                    ->orWhere('tanggal_terima', '>=', $cutoff);
            })->get();

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $m = $date->format('Y-m');
            $labels[] = $date->format('M Y');

            $sentData[] = $allCards->where('type', 'sent')
                ->filter(fn ($c) => $c->tanggal_kirim?->format('Y-m') === $m)
                ->count();

            $receivedData[] = $allCards->where('type', 'received')
                ->filter(function ($c) use ($m) {
                    return ($c->tanggal_terima?->format('Y-m') === $m) ||
                           ($c->tanggal_kirim?->format('Y-m') === $m);
                })
                ->count();
        }

        // Top Countries
        $sentCountries = Postcard::with('country')
            ->where('user_id', $user_id)
            ->where('type', 'sent')
            ->get()
            ->groupBy(fn ($c) => $c->country?->nama_inggris ?? $c->country?->nama_indonesia ?? 'Unknown')
            ->map(fn ($group) => $group->count())
            ->sortByDesc(fn ($count) => $count)
            ->toArray();

        $receivedCountries = Postcard::with('country')
            ->where('user_id', $user_id)
            ->where('type', 'received')
            ->get()
            ->groupBy(fn ($c) => $c->country?->nama_inggris ?? $c->country?->nama_indonesia ?? 'Unknown')
            ->map(fn ($group) => $group->count())
            ->sortByDesc(fn ($count) => $count)
            ->toArray();

        $this->chartData = [
            'labels' => $labels,
            'sent' => $sentData,
            'received' => $receivedData,
            'sentCountries' => $sentCountries,
            'receivedCountries' => $receivedCountries,
        ];
    }

    private function prepareMapData($user_id)
    {
        $this->mapMarkers = Postcard::with(['contact', 'country'])
            ->where('user_id', $user_id)
            ->whereHas('contact', function ($q) {
                $q->whereNotNull('lat');
            })
            ->get()
            ->filter(fn ($c) => is_numeric($c->contact?->lat) && $c->contact?->lat != 0)
            ->map(function ($m) {
                return [
                    'lat' => (float) $m->contact?->lat,
                    'lng' => (float) $m->contact?->lng,
                    'color' => ($m->type == 'sent') ? '#007bff' : '#28a745',
                    'content' => '<b>'.htmlspecialchars($m->contact?->nama_kontak ?? '').'</b><br>'.htmlspecialchars($m->contact?->alamat ?? ''),
                ];
            })
            ->values()
            ->toArray();
    }

    public function markAsRead($id)
    {
        Postcard::where('id', $id)->where('user_id', Auth::id())->update(['notif_read' => 1]);
        $this->mount();
    }

    public function render()
    {
        return view('livewire.dashboard')->layout('components.layouts.app', ['title' => 'Dashboard - Postcard Tracker']);
    }
}
