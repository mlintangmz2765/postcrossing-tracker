<?php

namespace App\Livewire;

use App\Models\Postcard;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class DashboardTable extends Component
{
    use WithPagination;

    public $type = 'sent'; // 'sent' or 'received'

    // Filters
    public $start_kirim = '';

    public $end_kirim = '';

    public $start_terima = '';

    public $end_terima = '';

    public $filter_negara = '';

    public $filter_kategori = '';

    public $filter_status = '';

    public $sort = 'tanggal_kirim'; // default

    public $perPage = 20;

    public function mount($type = 'sent')
    {
        $this->type = $type;
    }

    public function updating($property)
    {
        $this->resetPage();
    }

    public function render()
    {
        $user_id = Auth::id();
        $myLat = (float) config('app.home_lat');
        $myLng = (float) config('app.home_lng');

        $query = Postcard::with(['contact', 'country'])
            ->where('user_id', $user_id)
            ->where('type', $this->type);

        if ($this->start_kirim && $this->end_kirim) {
            $query->whereBetween('tanggal_kirim', [$this->start_kirim, $this->end_kirim]);
        }
        if ($this->start_terima && $this->end_terima) {
            $query->whereBetween('tanggal_terima', [$this->start_terima, $this->end_terima]);
        }

        // SQL JOIN for Country Search
        if ($this->filter_negara) {
            $searchTerm = $this->filter_negara;
            $query->whereHas('country', function ($q) use ($searchTerm) {
                $q->where('nama_indonesia', 'like', '%'.$searchTerm.'%')
                    ->orWhere('nama_inggris', 'like', '%'.$searchTerm.'%');
            });
        }

        if ($this->filter_kategori === 'postcrossing') {
            $query->where('postcard_id', 'like', '%-%');
        } elseif ($this->filter_kategori === 'swap') {
            $query->where(function ($q) {
                $q->where('postcard_id', 'not like', '%-%')
                    ->orWhereNull('postcard_id')
                    ->orWhere('postcard_id', '');
            });
        }

        if ($this->filter_status === 'arrived') {
            $query->whereNotNull('tanggal_terima')->where('tanggal_terima', '!=', '0000-00-00');
        } elseif ($this->filter_status === 'travelling') {
            $query->where(function ($q) {
                $q->whereNull('tanggal_terima')->orWhere('tanggal_terima', '0000-00-00');
            });
        }

        $allRecords = $query->get();

        if ($this->filter_negara) {
            $searchTerm = strtolower($this->filter_negara);
            $allRecords = $allRecords->filter(function ($row) use ($searchTerm) {
                // Country search already handled by SQL whereHas, but adding others here
                return str_contains(strtolower((string) $row->contact?->nama_kontak), $searchTerm) ||
                       str_contains(strtolower((string) $row->contact?->alamat), $searchTerm) ||
                       str_contains(strtolower((string) $row->contact?->nomor_telepon), $searchTerm) ||
                       str_contains(strtolower((string) $row->postcard_id), $searchTerm) ||
                       str_contains(strtolower((string) $row->deskripsi_gambar), $searchTerm);
            });
        }

        if (str_starts_with($this->sort, 'jarak')) {
            $allRecords->each(function ($row) use ($myLat, $myLng) {
                $row->jarak_hitung = ($row->contact?->lat && $row->contact?->lng && is_numeric($row->contact->lat))
                    ? $this->calculateDistance($myLat, $myLng, (float) $row->contact->lat, (float) $row->contact->lng)
                    : 0;
            });

            if ($this->sort === 'jarak_desc') {
                $allRecords = $allRecords->sortByDesc('jarak_hitung');
            } else {
                $allRecords = $allRecords->sortBy('jarak_hitung');
            }
        } elseif ($this->sort === 'tanggal_terima') {
            $allRecords = $allRecords->sortByDesc('tanggal_kirim')
                ->sortByDesc('tanggal_terima');
        } else {
            // Default: tanggal_kirim DESC
            $allRecords = $allRecords->sortByDesc('tanggal_kirim');
        }

        $page = $this->getPage();
        $rows = new LengthAwarePaginator(
            $allRecords->forPage($page, $this->perPage),
            $allRecords->count(),
            $this->perPage,
            $page,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        // Ensure distance is calculated for result page if not done in sort step
        if (! str_starts_with($this->sort, 'jarak')) {
            $rows->getCollection()->each(function ($row) use ($myLat, $myLng) {
                $row->jarak_hitung = ($row->contact?->lat && $row->contact?->lng && is_numeric($row->contact->lat))
                    ? $this->calculateDistance($myLat, $myLng, (float) $row->contact->lat, (float) $row->contact->lng)
                    : 0;
            });
        }

        // Grand Total based on PHP-filtered records
        $totalCost = $allRecords->sum('biaya_prangko');

        return view('livewire.dashboard-table', [
            'rows' => $rows,
            'totalCost' => $totalCost,
        ]);
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

    public function getDuration($start, $end)
    {
        if (! $start || empty($end) || $end == '0000-00-00') {
            return '-';
        }
        try {
            $end = \Carbon\Carbon::parse($end);
            $start = \Carbon\Carbon::parse($start);

            return $start->diffInDays($end).' days';
        } catch (\Exception $e) {
            return '-';
        }
    }

    public function delete($id)
    {
        Postcard::where('id', $id)->where('user_id', Auth::id())->delete();
    }
}
