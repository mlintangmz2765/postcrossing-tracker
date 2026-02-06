<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
        // Reset pagination when filtering
        $this->resetPage();
    }

    public function render()
    {
        $user_id = Auth::id();
        $myLat = (float) config('app.home_lat');
        $myLng = (float) config('app.home_lng');

        // Base Query
        $query = DB::table('postcards')
            ->where('user_id', $user_id)
            ->where('type', $this->type);

        // Apply Filters
        if ($this->start_kirim && $this->end_kirim) {
            $query->whereBetween('tanggal_kirim', [$this->start_kirim, $this->end_kirim]);
        }
        if ($this->start_terima && $this->end_terima) {
            $query->whereBetween('tanggal_terima', [$this->start_terima, $this->end_terima]);
        }
        if ($this->filter_negara) {
            $query->where('negara', 'like', '%' . $this->filter_negara . '%');
        }

        if ($this->filter_kategori === 'postcrossing') {
            $query->where('postcard_id', 'like', '%-%');
        } elseif ($this->filter_kategori === 'swap') {
            $query->where(function($q) {
                $q->where('postcard_id', 'not like', '%-%')
                  ->orWhereNull('postcard_id')
                  ->orWhere('postcard_id', '');
            });
        }

        if ($this->filter_status === 'arrived') {
            $query->whereNotNull('tanggal_terima')->where('tanggal_terima', '!=', '0000-00-00');
        } elseif ($this->filter_status === 'travelling') {
            $query->where(function($q) {
                $q->whereNull('tanggal_terima')->orWhere('tanggal_terima', '0000-00-00');
            });
        }

        // Sorting
        // Distance Formula
        $distanceRaw = "(6371 * acos(cos(radians($myLat)) * cos(radians(lat)) * cos(radians(lng) - radians($myLng)) + sin(radians($myLat)) * sin(radians(lat))))";
        
        // We select it to use in view
        $query->select('*', DB::raw("$distanceRaw as jarak_hitung"));

        if ($this->sort === 'tanggal_terima') {
            $query->orderByDesc('tanggal_terima')->orderByDesc('tanggal_kirim');
        } elseif ($this->sort === 'jarak_desc') {
            $query->orderByRaw("$distanceRaw DESC");
        } elseif ($this->sort === 'jarak_asc') {
            $query->orderByRaw("$distanceRaw ASC");
        } else {
            // Default: tanggal_kirim DESC
            $query->orderByDesc('tanggal_kirim');
        }

        // Pagination
        $rows = $query->paginate($this->perPage);

        // Calculate Grand Totals
        // Optimization: Run a separate simple aggregation query with same filters
        $totalQuery = DB::table('postcards')->where('user_id', $user_id)->where('type', $this->type);
        // ... apply exact same filters ... (abstracting filter logic would be better but keeping it simple/inline for now)
         if ($this->start_kirim && $this->end_kirim) $totalQuery->whereBetween('tanggal_kirim', [$this->start_kirim, $this->end_kirim]);
         if ($this->start_terima && $this->end_terima) $totalQuery->whereBetween('tanggal_terima', [$this->start_terima, $this->end_terima]);
         if ($this->filter_negara) $totalQuery->where('negara', 'like', '%' . $this->filter_negara . '%');
         if ($this->filter_kategori === 'postcrossing') $totalQuery->where('postcard_id', 'like', '%-%');
         elseif ($this->filter_kategori === 'swap') $totalQuery->where(function($q) { $q->where('postcard_id', 'not like', '%-%')->orWhereNull('postcard_id')->orWhere('postcard_id', ''); });
         if ($this->filter_status === 'arrived') $totalQuery->whereNotNull('tanggal_terima')->where('tanggal_terima', '!=', '0000-00-00');
         elseif ($this->filter_status === 'travelling') $totalQuery->where(function($q) { $q->whereNull('tanggal_terima')->orWhere('tanggal_terima', '0000-00-00'); });

        $totalCost = $totalQuery->sum('biaya_prangko');

        return view('livewire.dashboard-table', [
            'rows' => $rows,
            'totalCost' => $totalCost
        ]);
    }

    public function getDuration($start, $end)
    {
        if (empty($end) || $end == '0000-00-00') {
            return "-";
        }
        $start = \Carbon\Carbon::parse($start);
        $end = \Carbon\Carbon::parse($end);
        return $start->diffInDays($end) . " days";
    }

    public function delete($id)
    {
        if (Auth::check()) {
            DB::table('postcards')->where('id', $id)->where('user_id', Auth::id())->delete();
            // Optional: delete stamps/images
        }
    }
}
