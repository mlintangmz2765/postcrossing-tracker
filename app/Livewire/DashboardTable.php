<?php

namespace App\Livewire;

use App\Models\Postcard;
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

    public $sort = 'tanggal_kirim';

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

        // Filters
        if ($this->filter_negara) {
            $searchTerm = '%'.$this->filter_negara.'%';
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('country', function ($sq) use ($searchTerm) {
                    $sq->where('nama_indonesia', 'like', $searchTerm)
                        ->orWhere('nama_inggris', 'like', $searchTerm);
                })
                    ->orWhereHas('contact', function ($sq) use ($searchTerm) {
                        $sq->where('nama_kontak', 'like', $searchTerm)
                            ->orWhere('alamat', 'like', $searchTerm)
                            ->orWhere('nomor_telepon', 'like', $searchTerm);
                    })
                    ->orWhere('postcard_id', 'like', $searchTerm)
                    ->orWhere('deskripsi_gambar', 'like', $searchTerm);
            });
        }

        // Handle Sorting
        if (str_starts_with($this->sort, 'jarak')) {
            $allRows = $query->get()->each(function ($row) use ($myLat, $myLng) {
                if ($row->contact && $row->contact->lat && $row->contact->lng) {
                    $row->jarak_hitung = $this->calculateDistance($myLat, $myLng, $row->contact->lat, $row->contact->lng);
                } else {
                    $row->jarak_hitung = 0;
                }
            });

            if ($this->sort === 'jarak_desc') {
                $allRows = $allRows->sortByDesc('jarak_hitung');
            } else {
                $allRows = $allRows->sort(function ($a, $b) {
                    if ($a->jarak_hitung == 0 && $b->jarak_hitung == 0) {
                        return 0;
                    }
                    if ($a->jarak_hitung == 0) {
                        return 1;
                    }
                    if ($b->jarak_hitung == 0) {
                        return -1;
                    }

                    return $a->jarak_hitung <=> $b->jarak_hitung;
                });
            }

            $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage();
            $rows = new \Illuminate\Pagination\LengthAwarePaginator(
                $allRows->forPage($currentPage, $this->perPage)->values(),
                $allRows->count(),
                $this->perPage,
                $currentPage,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        } else {
            if ($this->sort === 'tanggal_terima') {
                $query->orderByDesc('tanggal_terima')->orderByDesc('tanggal_kirim');
            } else {
                $query->orderByDesc('tanggal_kirim')->orderByDesc('id');
            }

            $rows = $query->paginate($this->perPage);

            $rows->getCollection()->each(function ($row) use ($myLat, $myLng) {
                if ($row->contact && $row->contact->lat && $row->contact->lng) {
                    $row->jarak_hitung = $this->calculateDistance($myLat, $myLng, $row->contact->lat, $row->contact->lng);
                } else {
                    $row->jarak_hitung = 0;
                }
            });
        }

        $totalCost = Postcard::where('user_id', $user_id)->where('type', $this->type)->sum('biaya_prangko');

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
