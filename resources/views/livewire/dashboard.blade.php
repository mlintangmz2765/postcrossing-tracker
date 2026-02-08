<div class="dashboard-wrapper paper-texture">
    <style>
        .dashboard-wrapper {
            min-height: 100vh;
            padding: 30px 20px;
            font-family: 'Quicksand', sans-serif;
        }

        .section-title {
            font-family: 'Dancing Script', cursive;
            font-size: 3rem;
            color: var(--pc-ink);
            border-bottom: 2px dashed #eee;
            padding-bottom: 10px;
            margin-bottom: 30px;
            display: block;
            text-align: left;
            padding-left: 10px;
        }


        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .stats-card {
            background: #fff;
            padding: 25px;
            border-radius: 2px;
            box-shadow: 2px 2px 8px rgba(0,0,0,0.1);
            position: relative;
            border: 1px solid #eee;
            background-image: linear-gradient(rgba(0,0,0,0.01) 1px, transparent 1px);
            background-size: 100% 25px;
        }

        .stats-card::before {
            content: "";
            position: absolute;
            top: -10px; left: 50%; transform: translateX(-50%) rotate(-2deg);
            width: 90px; height: 20px;
            background: rgba(0,0,0,0.05); /* Tape effect */
            border: 1px solid rgba(0,0,0,0.03);
        }

        .stats-header {
            font-family: 'Special Elite', monospace;
            font-weight: bold;
            font-size: 1.3rem;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 2px dashed #eee;
            padding-bottom: 15px;
        }

        .stat-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px dotted #eee;
            font-size: 0.95rem;
        }

        .stat-row:last-child { border-bottom: none; }
        .stat-value { font-family: 'Special Elite', monospace; font-weight: bold; font-size: 1.2rem; }


        #dashboard-map { width: 100%; height: 500px; background: #f8f8f8; border-radius: 4px; border: 1px solid #eee; }


        .notif-alert {
            background: #fdf6e3;
            color: var(--pc-ink);
            padding: 15px 25px;
            margin-bottom: 25px;
            border: 1px solid #eee;
            border-left: 5px solid var(--pc-red);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: 'Special Elite', monospace;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            border-radius: 4px;
        }


        .charts-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-bottom: 40px;
        }
        
        .vintage-container {
            background-color: #ffffff;
            position: relative;
            padding: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            margin-bottom: 40px;
        }

        .chart-box {
            background: #ffffff;
            padding: 20px;
            border: 1px solid #eee;
            border-radius: 4px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.03);
        }
        
        .chart-box h4 {
            font-family: 'Special Elite', monospace;
            color: var(--pc-blue);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
            border-bottom: 1px dotted #eee;
            padding-bottom: 10px;
            padding-left: 15px;
        }

        @media (max-width: 900px) {
            .charts-row { grid-template-columns: 1fr; }
        }
    </style>

    <div class="max-w-7xl mx-auto py-10">
        
        <!-- Welcome Message -->
        <h2 class="section-title">
            <i class="bi bi-journal-album"></i> My Travel Log
        </h2>

        @foreach($recentNotifications as $notif)
            <div class="notif-alert">
                <div>
                    <i class="bi bi-envelope-open"></i> 
                    News! Postcard <b>{{ $notif->postcard_id }}</b> has arrived in <b>{{ $notif->country?->nama_inggris ?? $notif->country?->nama_indonesia ?? 'its destination' }}</b>!
                </div>
                <button wire:click="markAsRead({{ $notif->id }})" class="text-sm underline hover:text-green-800">
                    Dismiss <i class="bi bi-check"></i>
                </button>
            </div>
        @endforeach

        <div class="vintage-container airmail-border mb-10">
             <h3 style="margin-top:15px; color:#444; font-family:'Special Elite'; margin-bottom:15px; text-align: left; padding-left: 15px;">
                Distribution Map <span style="font-size:0.7em; color: #94a3b8;">(🔵 SENT | 🟢 RECEIVED)</span>
            </h3>
            <div id="dashboard-map"></div>
        </div>

        <div class="stats-container">
            <!-- SENT Stats -->
            <div class="stats-card">
                <div class="stats-header" style="color: var(--pc-blue);">
                    <i class="bi bi-send"></i> SENT LOGS
                </div>
                <div class="stat-row">
                    <span><i class="bi bi-people text-blue-500 mr-2"></i> Receivers</span> 
                    <span class="stat-value">{{ $statsSent['people'] }}</span>
                </div>
                <div class="stat-row">
                    <span><i class="bi bi-globe text-blue-500 mr-2"></i> Countries</span>
                    <span class="stat-value">{{ $statsSent['countries'] }}</span>
                </div>
                <div class="stat-row">
                    <span><i class="bi bi-postcard text-blue-500 mr-2"></i> Total Cards</span>
                    <span class="stat-value">{{ $statsSent['cards'] }}</span>
                </div>
                <div class="stat-row">
                    <span><i class="bi bi-geo-alt text-blue-500 mr-2"></i> Distance</span>
                    <span class="stat-value">{{ number_format($statsSent['km'], 0, ',', '.') }} km</span>
                </div>
            </div>

            <!-- RECEIVED Stats -->
            <div class="stats-card">
                <div class="stats-header" style="color: var(--pc-red);">
                    <i class="bi bi-box-arrow-in-down"></i> RECEIVED LOGS
                </div>
                <div class="stat-row">
                    <span><i class="bi bi-people text-red-500 mr-2"></i> Senders</span>
                    <span class="stat-value">{{ $statsReceived['people'] }}</span>
                </div>
                <div class="stat-row">
                    <span><i class="bi bi-globe text-red-500 mr-2"></i> Countries</span>
                    <span class="stat-value">{{ $statsReceived['countries'] }}</span>
                </div>
                <div class="stat-row">
                    <span><i class="bi bi-postcard text-red-500 mr-2"></i> Total Cards</span>
                    <span class="stat-value">{{ $statsReceived['cards'] }}</span>
                </div>
                <div class="stat-row">
                    <span><i class="bi bi-geo-alt text-red-500 mr-2"></i> Distance</span>
                    <span class="stat-value">{{ number_format($statsReceived['km'], 0, ',', '.') }} km</span>
                </div>
            </div>
        </div>

        <div class="vintage-container airmail-border mb-10">
             <h3 style="margin-top:15px; color:#444; font-family:'Special Elite'; text-align: left; margin-bottom: 20px; padding-left: 15px;">Monthly Postcard Trends</h3>
             <div style="height: 300px; padding: 10px;"><canvas id="lineChart"></canvas></div>
        </div>

        <div class="charts-row">
            <div class="chart-box">
                <h4 class="font-bold text-gray-600 mb-2">Top Countries (Sent)</h4>
                <div style="height: 300px;"><canvas id="doughnutChartSent"></canvas></div>
            </div>
            <div class="chart-box">
                <h4 class="font-bold text-gray-600 mb-2">Top Countries (Received)</h4>
                <div style="height: 300px;"><canvas id="doughnutChartReceived"></canvas></div>
            </div>
        </div>

        <div x-data="{ activeTab: 'sent' }" class="mb-10">
            <div class="flex w-full mb-0">
                <button @click="activeTab = 'sent'" 
                        :class="activeTab === 'sent' ? 'bg-white border-t-4 border-blue-500 text-blue-600' : 'bg-gray-100 text-gray-500 border-t-4 border-transparent hover:bg-gray-50'"
                        class="flex-1 py-4 font-bold text-center border-x border-b tracking-widest uppercase transition-all shadow-sm z-10 relative">
                    <i class="bi bi-send mr-2"></i> SENT
                </button>
                <button @click="activeTab = 'received'" 
                        :class="activeTab === 'received' ? 'bg-white border-t-4 border-green-500 text-green-600' : 'bg-gray-100 text-gray-500 border-t-4 border-transparent hover:bg-gray-50'"
                        class="flex-1 py-4 font-bold text-center border-x border-b tracking-widest uppercase transition-all shadow-sm z-10 relative">
                    <i class="bi bi-box-arrow-in-down mr-2"></i> RECEIVED
                </button>
            </div>

            <div class="bg-white border border-t-0 p-5 rounded-b-lg shadow-sm">
                <div x-show="activeTab === 'sent'" x-transition>
                    <livewire:dashboard-table type="sent" wire:key="table-sent" />
                </div>
                <div x-show="activeTab === 'received'" x-transition style="display: none;">
                    <livewire:dashboard-table type="received" wire:key="table-received" />
                </div>
            </div>
        </div>

    </div>

    <script src="{{ asset('vendor/chartjs/chart.js') }}"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('app.google_api_key') }}&callback=initDashboardMap&loading=async" async defer></script>

    <script>
        const markersData = @json($mapMarkers);
        let dashboardMap;
        let mapMarkers = [];

        function initDashboardMap() {
            dashboardMap = new google.maps.Map(document.getElementById('dashboard-map'), {
                zoom: 2,
                center: { lat: 20, lng: 10 },
                streetViewControl: false,
                styles: [
                    { "elementType": "geometry", "stylers": [{ "color": "#ebe3cd" }] },
                    { "elementType": "labels.text.fill", "stylers": [{ "color": "#523735" }] },
                    { "featureType": "water", "elementType": "geometry.fill", "stylers": [{ "color": "#b9d3c2" }] }
                ]
            });

            const coordsTracker = {};
            const bounds = new google.maps.LatLngBounds();
            const projection = dashboardMap.getProjection();

            // Initial marker placement
            dashboardMap.addListener('projection_changed', function() {
                updateDashboardMarkers(true);
            });

            dashboardMap.addListener('zoom_changed', function() {
                updateDashboardMarkers(false);
            });
        }

        function updateDashboardMarkers(fitMap = false) {
            mapMarkers.forEach(m => m.setMap(null));
            mapMarkers = [];

            const coordsTracker = {};
            const bounds = new google.maps.LatLngBounds();
            const projection = dashboardMap.getProjection();

            if (!projection) return;

            markersData.forEach(m => {
                const lat = parseFloat(m.lat);
                const lng = parseFloat(m.lng);
                const key = lat.toFixed(3) + "," + lng.toFixed(3);
                let finalPos = { lat: lat, lng: lng };

                bounds.extend(new google.maps.LatLng(lat, lng));

                if (coordsTracker[key]) {
                    const scale = Math.pow(2, dashboardMap.getZoom());
                    const count = coordsTracker[key];
                    const angle = count * (2 * Math.PI / 8);
                    const radius = 12 / scale;
                    
                    const point = projection.fromLatLngToPoint(new google.maps.LatLng(lat, lng));
                    const newX = point.x + (Math.cos(angle) * radius);
                    const newY = point.y + (Math.sin(angle) * radius);
                    
                    finalPos = projection.fromPointToLatLng(new google.maps.Point(newX, newY));
                    coordsTracker[key]++;
                } else {
                    coordsTracker[key] = 1;
                }

                const marker = new google.maps.Marker({
                    position: finalPos,
                    map: dashboardMap,
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        fillColor: m.color,
                        fillOpacity: 0.9,
                        scale: 6,
                        strokeColor: 'white',
                        strokeWeight: 1
                    }
                });

                // InfoWindow on click
                const info = new google.maps.InfoWindow({ content: m.content });
                marker.addListener('click', () => info.open(dashboardMap, marker));

                mapMarkers.push(marker);
            });

            if (fitMap && mapMarkers.length > 0) {
                dashboardMap.fitBounds(bounds);
            }
        }

        document.addEventListener('livewire:initialized', () => {
            const chartData = @json($chartData);
            
            // Line Chart
            new Chart(document.getElementById('lineChart'), {
                type: 'line',
                data: {
                    labels: chartData.labels,
                    datasets: [
                        { label: 'Sent', data: chartData.sent, borderColor: '#007bff', tension: 0.3, fill: false }, // Blue
                        { label: 'Received', data: chartData.received, borderColor: '#28a745', tension: 0.3, fill: false } // Green
                    ]
                },
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
            });

            const dCols = ['#007bff', '#28a745', '#dc3545', '#ffc107', '#17a2b8', '#6610f2', '#6f42c1', '#e83e8c', '#fd7e14', '#20c997'];

            // Doughnut Chart (Sent)
            new Chart(document.getElementById('doughnutChartSent'), {
                type: 'doughnut',
                data: {
                    labels: Object.keys(chartData.sentCountries),
                    datasets: [{
                        data: Object.values(chartData.sentCountries),
                        backgroundColor: dCols
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right' } } }
            });

            // Doughnut Chart (Received)
             new Chart(document.getElementById('doughnutChartReceived'), {
                type: 'doughnut',
                data: {
                    labels: Object.keys(chartData.receivedCountries),
                    datasets: [{
                        data: Object.values(chartData.receivedCountries),
                        backgroundColor: dCols
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right' } } }
            });
        });
    </script>
</div>
