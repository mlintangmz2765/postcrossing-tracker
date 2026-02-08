<div class="gallery-page-wrapper" x-data="{ 
    modalOpen: false, 
    modalSrc: '', 
    modalCaption: '',
    openModal(src, caption) {
        this.modalSrc = src;
        this.modalCaption = caption;
        this.modalOpen = true;
    }
}" @keydown.escape.window="modalOpen = false">

    <style>




        
        .gallery-page-wrapper {
            background-color: #fdf6e3;
            background-image: linear-gradient(#e5e5e5 1.1px, transparent 1.1px), linear-gradient(90deg, #e5e5e5 1.1px, transparent 1.1px);
            background-size: 30px 30px;
            font-family: 'Quicksand', sans-serif; 
            color: #1e293b; 
            margin: 0; 
            padding: 0; 
            overflow-x: hidden;
            min-height: 100vh;
        }

        /* STAMP SLIDER */
        .stamp-marquee {
            width: 100%; 
            overflow: hidden; 
            background: #1a1a1a; 
            padding: 25px 0; 
            white-space: nowrap; 
            position: relative;
            margin-top: 30px; 
            
            border-top: 4px dotted #fdf6e3;
            border-bottom: 4px dotted #fdf6e3;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.2);
        }

        .marquee-content { 
            display: inline-block; 
            animation: scroll 120s linear infinite; 
        }

        .stamp-marquee:hover .marquee-content {
            animation-play-state: paused;
        }

        .marquee-item {
            display: inline-block; 
            width: 100px; 
            height: 80px; 
            margin: 0 20px;
            background: #fff;
            padding: 6px; 
            box-shadow: 2px 4px 8px rgba(0,0,0,0.5); 
            border-radius: 2px;
            vertical-align: middle; 
            cursor: zoom-in;
            transition: transform 0.3s ease;
        }

        .marquee-item:hover { 
            transform: scale(1.2) rotate(0deg) !important; 
            z-index: 100;
            box-shadow: 0 10px 20px rgba(0,0,0,0.5);
        }

        .marquee-item img { 
            width: 100%; 
            height: 100%; 
            object-fit: contain; 
            display: block;
        }
        @keyframes scroll { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }

        /* MAP CONTAINER */
        #map-container {
            width: 100%; height: 450px; 
            margin-top: 50px; margin-bottom: 50px; 
            border-bottom: 3px double #457b9d;
            box-shadow: inset 0 0 20px rgba(0,0,0,0.1);
        }
        #map { width: 100%; height: 100%; }

        /* HEADER */
        .paravion-top { position: fixed; top: 0; left: 0; right: 0; height: 12px; background: repeating-linear-gradient(45deg, #e63946, #e63946 20px, #fff 20px, #fff 40px, #457b9d 40px, #457b9d 60px, #fff 60px, #fff 80px); z-index: 1000; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .gallery-header { text-align: center; padding: 60px 20px 20px; position: relative; }
        .gallery-header h1 { font-family: 'Dancing Script', cursive; font-size: 5.5rem; margin: 0; color: #1e293b; text-shadow: 4px 4px 0px #fff; font-weight: 700; }
        @media (max-width: 768px) { .gallery-header h1 { font-size: 3.5rem; } }
        
        .paravion-badge { display: inline-flex; align-items: center; gap: 15px; border: 3px solid #457b9d; padding: 8px 25px; margin-top: 10px; transform: rotate(-1.5deg); background: rgba(255,255,255,0.8); box-shadow: 4px 4px 0px #457b9d; }
        .paravion-text { font-family: 'Special Elite', cursive; color: #457b9d; line-height: 1.1; text-align: left; }
        .paravion-text span { font-size: 1.6rem; font-weight: bold; display: block; }
        .paravion-text small { font-size: 0.85rem; letter-spacing: 3px; }

        .filter-container { margin: 30px auto; text-align: center; display: flex; justify-content: center; flex-wrap: wrap; gap: 15px; }
        select.vintage-select { padding: 12px 15px; font-family: 'Special Elite', cursive; background: #fff; border: 2px solid #457b9d; border-radius: 5px; cursor: pointer; outline: none; box-shadow: 3px 3px 0px #457b9d; transition: 0.3s; font-size: 1rem; min-width: 180px; }
        select.vintage-select:hover { transform: translate(-2px, -2px); box-shadow: 5px 5px 0px #457b9d; }

        /* GRID GALLERY */
        .gallery-wrapper { max-width: 1400px; margin: 0 auto; padding: 20px; column-count: 3; column-gap: 2.5rem; }
        @media (max-width: 1100px) { .gallery-wrapper { column-count: 2; } }
        @media (max-width: 600px) { .gallery-wrapper { column-count: 1; } }

        .gallery-item { break-inside: avoid; margin-bottom: 3.5rem; position: relative; scroll-margin-top: 20px; }
        
        /* HIGHLIGHT EFFECT */
        @keyframes flashHighlight {
            0% { transform: scale(1); box-shadow: 0 0 0 rgba(230, 57, 70, 0); }
            50% { transform: scale(1.05); box-shadow: 0 0 30px rgba(230, 57, 70, 0.6); border-color: #e63946; }
            100% { transform: scale(1); box-shadow: 0 0 0 rgba(230, 57, 70, 0); }
        }
        .highlight-item .postcard-card { animation: flashHighlight 2s ease-in-out; position: relative; z-index: 50; border: 2px solid #e63946 !important; }

        /* GENERAL CARD STYLING */
        .postcard-card { 
            background: #fff; padding: 12px; border-radius: 4px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.1), 0 1px 8px rgba(0,0,0,0.05); 
            border: 1px solid #ddd; 
            transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); 
        }
        .postcard-card:hover { transform: translateY(-10px) rotate(1deg); }

        .image-container { position: relative; overflow: hidden; background: #f8f8f8; border-radius: 2px; }
        .postcard-image { width: 100%; height: 100%; object-fit: cover; display: block; filter: sepia(5%) contrast(1.05); }
        
        .glass-overlay { 
            position: absolute; top: 0; left: 0; right: 0; bottom: 0; 
            background: rgba(255, 255, 255, 0.1); 
            backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); 
            display: flex; flex-direction: column; justify-content: center; align-items: center; 
            opacity: 0; transition: all 0.5s ease; padding: 25px; text-align: center; cursor: zoom-in; 
        }
        @media (hover: hover) { .image-container:hover .glass-overlay { opacity: 1; } }
        .image-container:active .glass-overlay { opacity: 1; }

        .overlay-country { 
            font-family: 'Special Elite', cursive; font-size: 1.6rem; 
            background: #fdf6e3; padding: 8px 20px; 
            border: 1px dashed #333; transform: rotate(-3deg); 
            margin-bottom: 12px; box-shadow: 4px 4px 0px rgba(0,0,0,0.1); 
        }
        .overlay-desc { font-size: 0.95rem; line-height: 1.6; color: #000; background: rgba(255,255,255,0.7); padding: 10px; border-radius: 5px; font-style: italic; }

        /* STAMP MODE */
        .stamp-mode-card {
            background: #fff;
            padding: 15px 15px 10px 15px; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            text-align: left;
            border: 1px solid #e0e0e0;
        }
        
        .stamp-mode-image-wrapper {
            background-color: #fff;
            background-image: repeating-linear-gradient(#fff 0, #fff 19px, #f0f4c3 20px);
            padding: 20px;
            border: 1px solid #eee;
            display: flex; justify-content: center; align-items: center;
            margin-bottom: 10px;
            
            /* FIX GEPEK/TUMPUK */
            min-height: 220px; 
        }
        
        .stamp-mode-image {
            width: auto;
            max-width: 80%;
            height: auto;
            max-height: 200px;
            box-shadow: 1px 1px 4px rgba(0,0,0,0.3);
            border: 2px solid white;
            outline: 1px dashed #ccc; 
        }

        .stamp-mode-footer {
            margin-top: 5px;
            font-family: 'Special Elite', monospace;
            font-weight: bold;
            font-size: 1.2rem;
            color: #333;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .stamp-pin {
            color: #e63946; 
            font-size: 1.2rem;
        }

        /* Styling untuk view ALL (Classic) */
        .stamps-row { margin-top: 15px; display: flex; gap: 10px; justify-content: flex-end; flex-wrap: wrap; position: relative; padding-right: 5px; }
        .stamp-box { width: 60px; height: 60px; background: #fff; padding: 4px; border: 1px solid #eee; box-shadow: 2px 2px 5px rgba(0,0,0,0.15); transform: rotate(calc(var(--r) * 1deg)); transition: 0.3s; cursor: zoom-in; }
        .stamp-box:hover { transform: rotate(0deg) scale(1.2); z-index: 10; }
        .stamp-box img { width: 100%; height: 100%; object-fit: contain; }
        .postmark-decor { position: absolute; left: 0; top: -5px; width: 80px; height: 80px; border: 3px double rgba(0,0,0,0.07); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-family: 'Special Elite', cursive; font-size: 9px; color: rgba(0,0,0,0.15); transform: rotate(-20deg); pointer-events: none; line-height: 1.2; text-align: center; }

        /* Modal */
        .modal { display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.85); backdrop-filter: blur(5px); opacity: 0; transition: opacity 0.3s ease; }
        .modal.show { display: flex; align-items: center; justify-content: center; opacity: 1; }
        .modal-content-wrapper { position: relative; max-width: 90%; max-height: 90%; text-align: center; animation: zoomIn 0.3s; }
        .modal-image { max-width: 100%; max-height: 85vh; border: 8px solid #fff; border-radius: 2px; box-shadow: 0 0 20px rgba(0,0,0,0.5); }
        .modal-caption { margin-top: 15px; font-family: 'Special Elite', monospace; color: #fff; font-size: 1.1rem; letter-spacing: 1px; text-shadow: 1px 1px 2px #000; }
        .modal-close { position: absolute; top: -40px; right: 0; color: #f1f1f1; font-size: 40px; font-weight: bold; cursor: pointer; transition: 0.3s; line-height: 1; }
        .modal-close:hover { color: #e63946; }
        @keyframes zoomIn { from {transform:scale(0.8); opacity:0} to {transform:scale(1); opacity:1} }
        .zoomable { cursor: zoom-in; }
    </style>

    <div class="paravion-top"></div>

    <header class="gallery-header">
        <h1>{{ config('app.owner_username') }} Mailbox</h1>
        <div class="paravion-badge">
            <svg width="45" height="45" viewBox="0 0 24 24">
                <path d="M21,16L21,14L13,9L13,3.5C13,2.67 12.33,2 11.5,2C10.67,2 10,2.67 10,3.5L10,9L2,14L2,16L10,13.5L10,19L8,20.5L8,22L11.5,21L15,22L15,20.5L13,19L13,13.5L21,16Z" fill="#457b9d" />
            </svg>
            <div class="paravion-text">
                <span>PAR AVION</span>
                <small>BY AIR MAIL</small>
            </div>
        </div>

        <div id="map-container" wire:ignore><div id="map"></div></div>

        <div class="filter-container">
            <div style="display:contents;">
                
                <select wire:model.live="viewMode" class="vintage-select">
                    <option value="all">👁 View: All Collection</option>
                    <option value="postcard">🖼 View: Postcards Only</option>
                    <option value="stamp">🏵 View: Stamps Only</option>
                </select>

                <select wire:model.live="filterInput" class="vintage-select">
                    <option value="">-- Explore Location --</option>
                    <optgroup label="By Continent">
                        @foreach ($continents as $val)
                            <option value="continent:{{ $val }}">{{ $val }}</option>
                        @endforeach
                    </optgroup>
                    <optgroup label="By Region">
                        @foreach ($subcontinents as $val)
                            <option value="subcontinent:{{ $val }}">{{ $val }}</option>
                        @endforeach
                    </optgroup>
                    <optgroup label="By Country">
                        @foreach ($countries as $val)
                            <option value="country:{{ $val }}">{{ $val }}</option>
                        @endforeach
                    </optgroup>
                </select>

                @if($filterInput)
                    <button wire:click="resetFilter" class="vintage-select" style="color:#e63946; border-color:#e63946; cursor:pointer;">Reset Filter ✖</button>
                @endif
            </div>
        </div>

        @if ($viewMode === 'stamp' && !empty($sliderStamps))
        <div class="stamp-marquee">
            <div class="marquee-content">
                @for($i=0; $i<2; $i++)
                    @foreach ($sliderStamps as $stampData)
                        @php $rot = rand(-6, 6); @endphp
                        <div class="marquee-item" 
                             style="transform: rotate({{ $rot }}deg);"
                             @click="openModal('{{ asset($stampData['img']) }}', '{{ addslashes($stampData['caption']) }}')">
                            <img src="{{ asset($stampData['img']) }}" alt="Stamp" loading="lazy">
                        </div>
                    @endforeach
                @endfor
            </div>
        </div>
        @endif

    </header>

    <main class="gallery-wrapper">
        @if (count($postcards) > 0)
            @foreach ($postcards as $card)
                @php
                    // aspect ratio calculated in backend as 'ratio_style'
                    $imgFile = asset($card['main_image']);
                    $style = $card['ratio_style']; 
                    $displayName = !empty($card['display_country']) ? $card['display_country'] : 'Unknown';
                    $caption = ($viewMode === 'stamp') ? "{$displayName} Stamp" : $card['deskripsi_gambar'];
                    $groupId = "group-" . $card['pid'];
                    
                    // Escaping for JS string safety (Basic)
                    $jsCaption = addslashes($caption); 
                    // To handle newlines properly for JS string, we should replace newlines
                    $jsCaption = str_replace(["\r", "\n"], " ", $jsCaption);
                @endphp
                <article class="gallery-item {{ $groupId }}">
                    
                    @if ($viewMode === 'stamp')
                        <div class="postcard-card stamp-mode-card" @click="openModal('{{ $imgFile }}', '{{ $jsCaption }}')">
                            <div class="stamp-mode-image-wrapper">
                                <img src="{{ $imgFile }}" 
                                     style="{{ $style }}" 
                                     class="stamp-mode-image zoomable" 
                                     alt="Stamp" 
                                     loading="lazy">
                            </div>
                            
                            <div class="stamp-mode-footer">
                                <span class="stamp-pin"></span>
                                <span>{{ $displayName }}</span>
                            </div>
                        </div>
                    @else
                        <div class="postcard-card">
                            <div class="image-container zoomable" style="{{ $style }}" @click="openModal('{{ $imgFile }}', '{{ $jsCaption }}')">
                                <img src="{{ $imgFile }}" class="postcard-image" alt="Item from {{ $displayName }}" loading="lazy">
                                <div class="glass-overlay">
                                    <div class="overlay-country">{{ $displayName }}</div>
                                    <div style="font-family:'Special Elite'; color:#444; font-size:12px; margin-bottom:10px;">{{ $card['postcard_id'] }}</div>
                                    @if (!empty($card['deskripsi_gambar']))
                                        <p class="overlay-desc">{!! nl2br(e($card['deskripsi_gambar'])) !!}</p>
                                    @endif
                                    <div style="margin-top:10px; font-size:0.8rem; color:#444;">(Click to Zoom)</div>
                                </div>
                            </div>

                            @if ($viewMode === 'all' && isset($stampsByCard[$card['pid']]))
                                <div class="stamps-row">
                                    <div class="postmark-decor">CHECKED &<br>PASSED</div>
                                    @foreach ($stampsByCard[$card['pid']] as $stamp)
                                        @php 
                                            $rand = rand(-8, 8); 
                                            $stampCaption = $displayName . " Stamp";
                                            $jsStampCaption = addslashes($stampCaption);
                                        @endphp
                                        <div class="stamp-box zoomable" style="--r: {{ $rand }}" @click="openModal('{{ asset($stamp['foto_prangko']) }}', '{{ $jsStampCaption }}')">
                                            <img src="{{ asset($stamp['foto_prangko']) }}" alt="Stamp" loading="lazy">
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif

                </article>
            @endforeach
        @else
            <div style="text-align:center; grid-column:1/-1; padding:40px; font-family:'Special Elite';">
                <h3>No collection found matching your criteria.</h3>
            </div>
        @endif
    </main>

    <footer style="text-align:center; padding: 80px 20px; opacity: 0.5; font-family: 'Special Elite'; font-size: 0.9rem;">
        <p>&copy; {{ date('Y') }} {{ config('app.owner_username') }} Postcrossing Archive • All Rights Reserved.</p>
    </footer>

    <!-- Alpine Modal -->
    <div x-show="modalOpen" style="display: none;" 
         class="modal show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click.self="modalOpen = false">
         
        <div class="modal-content-wrapper">
            <span class="modal-close" @click="modalOpen = false">&times;</span>
            <img class="modal-image" :src="modalSrc">
            <div class="modal-caption" x-text="modalCaption"></div>
        </div>
    </div>


    <!-- Conditional Map Script Loading -->
    @if ($isChina)
        <script type="text/javascript">
            window._AMapSecurityConfig = { securityJsCode: '{{ config('app.amap_web_key') }}' };
        </script>
        <script src="https://webapi.amap.com/maps?v=2.0&key={{ config('app.amap_js_key') }}"></script>
    @endif

</div>

<script>
    const rawMarkers = {!! $mapMarkers !!};
    let map;
    let markers = [];
    const isChina = {{ $isChina ? 'true' : 'false' }}; 

    function initMap() {
        if (rawMarkers.length === 0) return;

        if (isChina) {
            // GAODE MAPS (AMap) Logic
            map = new AMap.Map('map', { zoom: 3, center: [104, 35], lang: 'en', zooms: [3, 10]});

            rawMarkers.forEach(m => {
                const offsetLat = (Math.random() - 0.5) * 0.0005; 
                const offsetLng = (Math.random() - 0.5) * 0.0005;
                const contentHtml = `<div style="width: 14px; height: 14px; background-color: ${m.color}; border: 2px solid white; border-radius: 50%; box-shadow: 1px 1px 4px rgba(0,0,0,0.4); cursor: pointer;"></div>`;

                const marker = new AMap.Marker({
                    position: new AMap.LngLat(m.lng + offsetLng, m.lat + offsetLat),
                    content: contentHtml,
                    offset: new AMap.Pixel(-7, -7),
                    title: m.country
                });
                marker.on('click', () => { scrollToItem(m.id); });
                map.add(marker);
            });

            if (rawMarkers.length <= 1) {
                if (rawMarkers.length === 1) map.setZoomAndCenter(6, [rawMarkers[0].lng, rawMarkers[0].lat]);
            } else {
                map.setFitView(null, false, [50, 50, 50, 50]);
                setTimeout(() => { if (map.getZoom() > 6) map.setZoom(6); }, 500);
            }

        } else {
            // GOOGLE MAPS Logic
            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 2, 
                center: { lat: 20, lng: 0 }, 
                maxZoom: 10, 
                minZoom: 2, 
                streetViewControl: false,
                styles: [
                    { "elementType": "geometry", "stylers": [{ "color": "#ebe3cd" }] }, 
                    { "elementType": "labels.text.fill", "stylers": [{ "color": "#523735" }] }, 
                    { "elementType": "labels.text.stroke", "stylers": [{ "color": "#f5f1e6" }] }, 
                    { "featureType": "water", "elementType": "geometry.fill", "stylers": [{ "color": "#b9d3c2" }] }
                ]
            });
            
            // Initial markers
            updateMarkers(true);
            
            map.addListener('zoom_changed', function() { 
                updateMarkers(false); 
            });
        }
    }

    function updateMarkers(fitMap = false) {
        if (isChina) return;
        
        // Clear existing markers
        markers.forEach(m => m.setMap(null));
        markers = [];
        
        const coordsTracker = {};
        const bounds = new google.maps.LatLngBounds();
        const projection = map.getProjection();

        if (!projection && !fitMap) return; 

        rawMarkers.forEach(m => {
            const key = m.lat.toFixed(3) + "," + m.lng.toFixed(3);
            let finalPos = { lat: m.lat, lng: m.lng };

            bounds.extend(new google.maps.LatLng(m.lat, m.lng));

            if (coordsTracker[key] && projection) {
                const scale = Math.pow(2, map.getZoom());
                const count = coordsTracker[key];
                const angle = count * (2 * Math.PI / 8); 
                const radius = 12 / scale; 
                
                const point = projection.fromLatLngToPoint(new google.maps.LatLng(m.lat, m.lng));
                const newX = point.x + (Math.cos(angle) * radius);
                const newY = point.y + (Math.sin(angle) * radius);
                
                finalPos = projection.fromPointToLatLng(new google.maps.Point(newX, newY));
                coordsTracker[key]++;
            } else { 
                coordsTracker[key] = 1; 
            }

            const marker = new google.maps.Marker({
                position: finalPos, 
                map: map,
                icon: { 
                    path: google.maps.SymbolPath.CIRCLE, 
                    fillColor: m.color, 
                    fillOpacity: 0.9, 
                    scale: 6, 
                    strokeColor: 'white', 
                    strokeWeight: 2 
                },
                title: m.country
            });

            marker.addListener('click', () => { scrollToItem(m.id); });
            markers.push(marker);
        });

        if (fitMap && markers.length > 0) {
            map.fitBounds(bounds);
            
            google.maps.event.addListenerOnce(map, "idle", function() { 
                if (map.getZoom() > 6) {
                    map.setZoom(6); 
                }
            });
        }
    }

    window.scrollToItem = function(id) {
        const elements = document.querySelectorAll('.group-' + id);
        if(elements.length > 0) {
            elements[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
            elements.forEach(el => {
                el.classList.add('highlight-item');
                setTimeout(() => { el.classList.remove('highlight-item'); }, 2000);
            });
        }
    }

    // Initialize map on page load
    @if ($isChina)
        window.onload = function() { initMap(); };
    @endif
</script>

@if (!$isChina)
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('app.google_api_key') }}&libraries=marker&callback=initMap&loading=async" async defer></script>
@endif

</div>
