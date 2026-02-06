<div class="statistics-wrapper">
    <!-- Local Styles -->
    <style>
        :root {
            --paper: #fdf6e3;
        }
        .statistics-wrapper {
            background-color: var(--paper);
            background-image:
                linear-gradient(#eee 1.1px, transparent 1.1px),
                linear-gradient(90deg, #eee 1.1px, transparent 1.1px);
            background-size: 30px 30px;
            min-height: 100vh;
            font-family: 'Quicksand', sans-serif;
        }

        .stats-header {
            font-family: 'Special Elite', cursive;
            font-size: 3rem; /* Increased size */
            color: #2c3e50;
            margin-bottom: 30px;
            border-bottom: 3px double #d4c5b0;
            padding-bottom: 10px;
            display: inline-block;
            text-shadow: 1px 1px 0px #fff;
        }

        .vintage-table-card {
            background-color: #ffffff;
            position: relative;
            padding: 25px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            margin-bottom: 40px;
            border: 1px solid #d4c5b0;
        }

        /* Top Border Airmail Stripe */
        .vintage-table-card::before {
            content: "";
            position: absolute; top: 0; left: 0; right: 0; height: 8px;
            background: repeating-linear-gradient(45deg, #e63946, #e63946 15px, #ffffff 15px, #ffffff 30px, #457b9d 30px, #457b9d 45px, #ffffff 45px, #ffffff 60px);
            z-index: 2;
        }

        .table-title {
            font-family: 'Special Elite', cursive;
            font-size: 1.4rem;
            margin-bottom: 20px;
            display: flex; align-items: center; gap: 10px;
        }
        
        /* Table Styles */
        .vintage-table {
            width: 100%; border-collapse: collapse; font-family: 'Quicksand', sans-serif; font-size: 0.95rem;
        }
        .vintage-table th {
            text-align: left; padding: 12px; background: #f8fafc;
            border-bottom: 2px solid #e2e8f0; color: #64748b; font-weight: 700;
            text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.5px;
        }
        .vintage-table td {
            padding: 12px; border-bottom: 1px solid #e2e8f0; color: #334155;
        }
        .vintage-table tr:last-child td { border-bottom: none; }
        .vintage-table tr:hover { background-color: #ffffef; } /* Subtle highlight */

        .badge-pc { background: #e0f2fe; color: #0369a1; padding: 2px 6px; border-radius: 4px; font-size: 0.75rem; font-weight: bold; }
        .badge-swap { background: #fef3c7; color: #92400e; padding: 2px 6px; border-radius: 4px; font-size: 0.75rem; font-weight: bold; }

        .btn-back-dash {
             display: inline-block; margin-bottom: 20px;
             text-decoration: none; color: #fff; font-family: 'Special Elite', cursive;
             background: #1e293b; padding: 10px 20px; border-radius: 4px; 
             box-shadow: 3px 3px 0px #457b9d; transition: 0.2s;
        }
        .btn-back-dash:hover { transform: translate(-2px, -2px); box-shadow: 5px 5px 0px #457b9d; color:white;}
    </style>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ activeTab: 'sent' }">
        <div class="stats-actions">
        
        <h1 class="stats-header">Detailed Statistics</h1>

        <!-- Tabs Navigation: Full Width -->
        <div class="flex space-x-1 rounded-xl bg-gray-200 p-1 mb-6 w-full font-bold shadow-inner" style="font-family: 'Special Elite', cursive;">
            <button @click="activeTab = 'sent'" 
                :class="{ 'bg-white shadow text-blue-700': activeTab === 'sent', 'text-gray-600 hover:bg-white/[0.12] hover:text-blue-600': activeTab !== 'sent' }"
                class="flex-1 rounded-lg py-3 px-6 text-lg font-medium leading-5 ring-white ring-opacity-60 ring-offset-2 ring-offset-blue-400 focus:outline-none focus:ring-2 transition duration-200">
                <i class="bi bi-send-fill mr-1"></i> SENT STATISTICS
            </button>
            <button @click="activeTab = 'received'" 
                :class="{ 'bg-white shadow text-green-700': activeTab === 'received', 'text-gray-600 hover:bg-white/[0.12] hover:text-green-600': activeTab !== 'received' }"
                class="flex-1 rounded-lg py-3 px-6 text-lg font-medium leading-5 ring-white ring-opacity-60 ring-offset-2 ring-offset-blue-400 focus:outline-none focus:ring-2 transition duration-200">
                <i class="bi bi-inbox-fill mr-1"></i> RECEIVED STATISTICS
            </button>
        </div>
        
        <!-- SENT STATISTICS TAB -->
        <div x-show="activeTab === 'sent'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
            <div class="vintage-table-card">
                <h3 class="table-title" style="color: #007bff;">
                    <i class="bi bi-send"></i> Sent Statistics per Country
                </h3>
                <div class="overflow-x-auto">
                    <table class="vintage-table">
                        <thead>
                            <tr>
                                <th>Country</th>
                                <th>Total Sent</th>
                                <th>Type Split</th>
                                <th>Arrived</th>
                                <th>Avg Days</th>
                                <th>Min / Max</th>
                                <th>Total Cost</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sentStats as $s)
                            <tr>
                                <td class="font-bold">{{ $s->negara }}</td>
                                <td class="text-center">{{ $s->total_dikirim }}</td>
                                <td>
                                    <span class="badge-pc">PC: {{ $s->pc_count }}</span>
                                    <span class="badge-swap">Swap: {{ $s->swap_count }}</span>
                                </td>
                                <td class="text-center">{{ $s->sudah_sampai }}</td>
                                <td class="text-center">{{ $s->avg_days ? round($s->avg_days, 1) : '-' }}</td>
                                <td class="text-center">
                                    <span class="text-green-600">{{ $s->min_days ?? '-' }}</span> / 
                                    <span class="text-red-600">{{ $s->max_days ?? '-' }}</span>
                                </td>
                                <td class="font-bold text-right">Rp {{ number_format($s->total_biaya, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- RECEIVED STATISTICS TAB -->
        <div x-show="activeTab === 'received'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" style="display: none;">
            <div class="vintage-table-card">
                <h3 class="table-title" style="color: #28a745;">
                    <i class="bi bi-inbox"></i> Received Statistics per Country
                </h3>
                <div class="overflow-x-auto">
                    <table class="vintage-table">
                        <thead>
                            <tr>
                                <th>Country</th>
                                <th>Total Received</th>
                                <th>Type Split</th>
                                <th>Avg Days</th>
                                <th>Min / Max</th>
                                <th>Avg Value</th>
                                <th>Est. Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($receivedStats as $r)
                            <tr>
                                <td class="font-bold">{{ $r->negara }}</td>
                                <td class="text-center">{{ $r->total_diterima }}</td>
                                <td>
                                    <span class="badge-pc">PC: {{ $r->pc_count }}</span>
                                    <span class="badge-swap">Swap: {{ $r->swap_count }}</span>
                                </td>
                                <td class="text-center">{{ $r->avg_days ? round($r->avg_days, 1) : '-' }}</td>
                                <td class="text-center">
                                    <span class="text-green-600">{{ $r->min_days ?? '-' }}</span> / 
                                    <span class="text-red-600">{{ $r->max_days ?? '-' }}</span>
                                </td>
                                <td class="font-bold text-right text-gray-600">Rp {{ number_format($r->avg_nilai, 0, ',', '.') }}</td>
                                <td class="font-bold text-right">Rp {{ number_format($r->total_nilai, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
