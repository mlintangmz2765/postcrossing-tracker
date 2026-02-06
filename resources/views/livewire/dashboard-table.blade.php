<div>
    <style>
        .filter-inline {
            display: flex; gap: 10px; align-items: flex-end; flex-wrap: wrap; 
            margin-bottom: 20px; padding: 15px; 
            background: #fdfdfd; border-radius: 8px; border: 1px solid #eee;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .filter-group label {
            font-size: 0.7rem; font-weight: bold; color: #666; display: block; margin-bottom: 2px;
            text-transform: uppercase; letter-spacing: 0.5px;
        }
        .filter-control {
            padding: 6px 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 0.85rem;
            background: white; color: #333; outline: none; transition: border 0.2s;
        }
        .filter-control:focus { border-color: #aaa; }
        
        .vintage-table {
            width: 100%; border-collapse: separate; border-spacing: 0 2px; font-size: 0.85rem;
        }
        .vintage-table th {
            text-align: left; padding: 12px 10px; 
            background-color: #f4f6f7; color: #576574; 
            border-bottom: 2px solid #dde1e5;
            text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px;
        }
        .vintage-table td {
            padding: 10px; background: white; border-bottom: 1px solid #f0f0f0; vertical-align: middle;
        }
        .vintage-table tr:hover td { background-color: #fafafa; }
        
        .badge { display: inline-block; padding: 3px 6px; border-radius: 4px; font-size: 0.75rem; background:#e9ecef; color: #495057; font-weight:600; white-space: nowrap; }
        
        .id-link { font-weight: bold; color: #2c3e50; text-decoration: none; }
        .id-link:hover { text-decoration: underline; color: #e63946; }
        
        .thumb-img { width: 45px; height: 30px; object-fit: cover; border-radius: 3px; border: 1px solid #ccc; cursor: pointer; transition: transform 0.2s; }
        .thumb-img:hover { transform: scale(3); z-index: 10; position: relative; box-shadow: 0 5px 15px rgba(0,0,0,0.3); }

        .btn-action { color: #666; margin-right: 5px; font-size: 1rem; transition: color 0.2s; }
        .btn-action:hover { color: #000; }
        
        .total-row td { background: #f8f9fa; font-weight: bold; font-size: 0.9rem; color: #2c3e50; border-top: 2px solid #ddd; }
    </style>

    <!-- Filters -->
    <div class="filter-inline">
        <div class="filter-group">
            <label>Sent Date</label>
            <div class="flex items-center gap-2">
                <input type="date" wire:model.live="start_kirim" class="filter-control">
                <span class="text-gray-400">-</span>
                <input type="date" wire:model.live="end_kirim" class="filter-control">
            </div>
        </div>
        <div class="filter-group">
            <label>Received Date</label>
            <div class="flex items-center gap-2">
                <input type="date" wire:model.live="start_terima" class="filter-control">
                <span class="text-gray-400">-</span>
                <input type="date" wire:model.live="end_terima" class="filter-control">
            </div>
        </div>
        <div class="filter-group">
            <label>Category</label>
            <select wire:model.live="filter_kategori" class="filter-control">
                <option value="">All</option>
                <option value="postcrossing">Postcrossing</option>
                <option value="swap">Direct Swap</option>
            </select>
        </div>
        <div class="filter-group">
            <label>Status</label>
            <select wire:model.live="filter_status" class="filter-control">
                <option value="">All</option>
                <option value="arrived">Arrived</option>
                <option value="travelling">Travelling</option>
            </select>
        </div>
        <div class="filter-group">
            <label>Sort By</label>
            <select wire:model.live="sort" class="filter-control">
                <option value="tanggal_kirim">Date Sent</option>
                <option value="tanggal_terima">Date Received</option>
                <option value="jarak_desc">Distance (Far)</option>
                <option value="jarak_asc">Distance (Near)</option>
            </select>
        </div>
        <div class="filter-group">
            <label>Country / Search</label>
            <input type="text" wire:model.live.debounce.500ms="filter_negara" class="filter-control" placeholder="Search country...">
        </div>
    </div>

    <!-- Table -->
    <div style="overflow-x: auto;">
        <table class="vintage-table">
            <thead>
                <tr>
                    @if($type === 'received') <th>Photo</th> @endif
                    <th>Postcard ID</th>
                    <th>Date Sent</th>
                    <th>Date Rcvd</th>
                    <th>Duration</th>
                    <th>Distance</th>
                    <th>Description</th>
                    <th>{{ $type === 'sent' ? 'Recipient' : 'Sender' }}</th>
                    <th>Address</th>
                    <th>Country</th>
                    @if($type === 'sent')
                        <th>Phone</th>
                    @else
                        <th>Origin Cost</th>
                        <th>Rate</th>
                    @endif
                    <th>Total (IDR)</th>
                    <th>Actions</th>
                    <th style="width: 80px;"></th> 
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                    <tr>
                        @if($type === 'received')
                            <td>
                                @if($row->foto_depan)
                                    <img src="{{ asset($row->foto_depan) }}" class="thumb-img">
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>
                        @endif
                        <td>
                            @php
                                $isPostcrossing = strpos($row->postcard_id, '-') !== false;
                                $isTravelling = empty($row->tanggal_terima) || $row->tanggal_terima == '0000-00-00';
                                $url = "https://www.postcrossing.com/" . ($type == 'sent' && $isTravelling ? 'travelingpostcard' : 'postcards') . "/" . $row->postcard_id;
                            @endphp
                            
                            @if($isPostcrossing)
                                <a href="{{ $url }}" target="_blank" class="id-link">{{ $row->postcard_id }}</a>
                            @else
                                <span class="font-bold text-gray-700">{{ $row->postcard_id ?: '-' }}</span>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($row->tanggal_kirim)->format('Y-m-d') }}</td>
                        <td>{{ ($row->tanggal_terima && $row->tanggal_terima != '0000-00-00') ? \Carbon\Carbon::parse($row->tanggal_terima)->format('Y-m-d') : '-' }}</td>
                        <td>
                            <span class="badge">{{ $this->getDuration($row->tanggal_kirim, $row->tanggal_terima) }}</span>
                        </td>
                        <td>{{ number_format($row->jarak_hitung) }} km</td>
                        <td style="max-width: 250px; word-wrap: break-word;" title="{{ $row->deskripsi_gambar }}">
                            {{ $row->deskripsi_gambar }}
                        </td>
                        <td>{{ $row->nama_kontak }}</td>
                        <td style="font-size: 0.75rem; max-width: 200px; word-wrap: break-word;" title="{{ $row->alamat }}">
                            {{ $row->alamat }}
                        </td>
                        <td>{{ $row->negara }}</td>
                        @if($type === 'sent')
                            <td>{{ $row->nomor_telepon ?: '-' }}</td>
                        @else
                            <td>
                                {{ $row->mata_uang }} {{ number_format($row->nilai_asal, 2) }}
                            </td>
                            <td>{{ number_format($row->kurs_idr) }}</td>
                        @endif
                        <td class="font-bold font-mono">
                            {{ number_format($row->biaya_prangko) }}
                        </td>
                        <td class="text-center">
                            <div class="flex items-center justify-center gap-2">
                                <!-- View -->
                                <a href="{{ route('view', ['id' => $row->id]) }}" class="text-blue-500 hover:text-blue-700" title="View"><i class="bi bi-eye"></i></a>
                                <!-- Edit -->
                                <a href="{{ route('edit', ['id' => $row->id]) }}" class="text-yellow-500 hover:text-yellow-700" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                <!-- Delete -->
                                <button wire:click="delete({{ $row->id }})" wire:confirm="Are you sure you want to delete this postcard?" class="text-red-500 hover:text-red-700" title="Delete"><i class="bi bi-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $type === 'sent' ? '12' : '13' }}" class="text-center py-8 text-gray-500 italic">
                            No postcards found matching these filters.
                        </td>
                    </tr>
                @endforelse
                
                <!-- Totals Row -->
                <tr class="total-row">
                    <td colspan="{{ $type === 'sent' ? '10' : '11' }}" style="text-align: right; padding-right: 15px;">TOTAL:</td>
                    <td colspan="2" style="color: #d63031;">Rp {{ number_format($totalCost) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="mt-4">
        {{ $rows->links('livewire.custom-pagination', data: ['scrollTo' => false]) }}
    </div>
</div>
