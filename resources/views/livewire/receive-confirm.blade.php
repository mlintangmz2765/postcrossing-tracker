<div class="receive-confirm-wrapper bg-gray-200 flex items-center justify-center min-h-screen p-5" 
     style="font-family: 'Quicksand', sans-serif; background-image: url('{{ asset('images/cardboard.png') }}'); background-repeat: repeat;">
    
    <style>


        
        body { font-family: 'Quicksand', sans-serif; }

        .airmail-border { 
            background: white; 
            padding: 12px; 
            background: repeating-linear-gradient(45deg, #ff4b4b, #ff4b4b 20px, #fff 20px, #fff 40px, #3b82f6 40px, #3b82f6 60px, #fff 60px, #fff 80px); 
            border-radius: 20px; 
            box-shadow: 0 20px 25px rgba(0, 0, 0, 0.15); 
            max-width: 480px; 
            width: 100%; 
        }
        .container-card { 
            background: #fffcf9; 
            border-radius: 12px; 
            overflow: hidden; 
            text-align: center; 
            position: relative; 
            min-height: 400px; 
        }
        .stamp-area { position: absolute; top: 25px; right: 20px; z-index: 10; }
        .stamp { width: 90px; height: 60px; background-image: url('{{ asset('images/prangko.png') }}'); background-size: cover; transform: rotate(3deg); filter: drop-shadow(2px 2px 3px rgba(0,0,0,0.2)); }
        .postmark { position: absolute; width: 110px; top: -15px; right: 35px; opacity: 0.85; transform: rotate(-15deg); pointer-events: none; z-index: 11; }
        
        .airmail-sticker { 
            position: absolute; top: 20px; left: 20px; 
            background: #003399; color: white; padding: 5px 12px; 
            border-radius: 4px; display: flex; align-items: center; gap: 8px; 
            box-shadow: 2px 2px 4px rgba(0,0,0,0.2); 
            border: 1px solid rgba(255,255,255,0.3); transform: rotate(-2deg); 
        }
        
        .header-content { padding-top: 85px; padding-bottom: 15px; }
        h1.cursive-title { font-family: 'Dancing Script', cursive; font-size: 2.8rem; margin: 0; color: #1f2937; font-weight: 700; }
        
        .content-area { padding: 10px 30px 40px 30px; }
        .pc-info-card { background: rgba(0,0,0,0.03); border-left: 4px solid #003399; padding: 15px; margin: 20px 0; text-align: left; }
        .pc-info-item { display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 5px; color: #4b5563; }
        
        .recipient-box { border-bottom: 1px solid #e5e7eb; padding-bottom: 15px; margin-bottom: 25px; }
        .btn-action { 
            background: #1e293b; color: white; border: none; padding: 18px 30px; 
            border-radius: 8px; font-size: 16px; font-weight: 700; cursor: pointer; width: 100%; transition: 0.3s; 
        }
        .btn-action:hover { background: #000; transform: translateY(-2px); }
        
        .badge-confirmed { background: #dcfce7; color: #166534; padding: 10px 20px; border-radius: 50px; font-weight: 700; margin: 20px 0; display: inline-block; }
        .footer-text { font-size: 10px; color: #9ca3af; padding: 20px; border-top: 1px solid #f1f5f9; text-transform: uppercase; letter-spacing: 1px; }
        
        #map-confirm { height: 180px; width: 100%; border-radius: 10px; margin-top: 15px; border: 1px solid #ddd; }
        
        /* Modal Styles */
        .modal-overlay { 
            position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; 
            background: rgba(0,0,0,0.7); backdrop-filter: blur(5px); 
            display: flex; align-items: center; justify-content: center; 
        }
        .modal-box { background: white; padding: 35px; border-radius: 15px; max-width: 400px; width: 90%; text-align: center; }
        .modal-btns { display: flex; gap: 12px; margin-top: 25px; }
    </style>

    <div class="airmail-border" x-data="{ 
        step: 0,
        message: @entangle('message'),
        openModal(s) { this.step = s; },
        closeModal() { this.step = 0; },
        confirmStep1() { this.step = {{ $isSwap ? 2 : 3 }}; if(!{{ $isSwap ? 'true' : 'false' }}) this.submitFinal(); },
        goToStep3() { this.step = 3; },
        submitFinal() { this.$wire.confirm(); this.step = 0; }
    }">
        <div class="container-card">
            <div class="airmail-sticker">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="white">
                    <path d="M21,16L21,14L13,9L13,3.5C13,2.67 12.33,2 11.5,2C10.67,2 10,2.67 10,3.5L10,9L2,14L2,16L10,13.5L10,19L8,20.5L8,22L11.5,21L15,22L15,20.5L13,19L13,13.5L21,16Z" />
                </svg>
                <span style="font-size: 9px; font-weight: bold; text-transform: uppercase;">Par Avion</span>
            </div>

            <div class="stamp-area">
                <div class="stamp"></div>
                <img src="{{ asset('images/postmark.png') }}" class="postmark"> 
            </div>

            <div class="header-content">
                @if ($justConfirmed)
                    <div style="font-size: 50px;">🕊️</div>
                    <h1 class="cursive-title">Received!</h1>
                @elseif ($alreadyConfirmed)
                    <div style="font-size: 50px;">📬</div>
                    <h1 class="cursive-title">Registered</h1>
                @elseif ($postcard)
                    <div style="font-size: 50px;">💌</div>
                    <h1 class="cursive-title">Postcard Arrived</h1>
                @else
                    <div style="font-size: 50px;">❓</div>
                    <h1 class="cursive-title">Not Found</h1>
                @endif
            </div>

            <div class="content-area">
                @if ($justConfirmed)
                    <p class="text-gray-600 mb-4">The world just got a little smaller. Your confirmation has been sent to the sender. Happy Postcrossing!</p>
                    <div class="badge-confirmed">CONFIRMED SAFE</div>
                    
                    <a href="{{ route('gallery') }}" class="btn-action block text-center no-underline" style="margin-top: 15px; background: #2c3e50;">View My Postcard Gallery</a>

                @elseif ($alreadyConfirmed)
                    <p class="text-gray-600 mb-4">This postcard has already been registered in our system.</p>
                    <div class="pc-info-card">
                        <div class="pc-info-item"><span>Registered on:</span> <b>{{ \Carbon\Carbon::parse($postcard->tanggal_terima)->format('M d, Y') }}</b></div>
                        <div class="pc-info-item"><span>Distance Traveled:</span> <b>{{ number_format($distance) }} km</b></div>
                    </div>
                    
                    <a href="{{ route('gallery') }}" class="btn-action block text-center no-underline" style="margin-top: 15px; background: #2c3e50;">View My Postcard Gallery</a>

                @elseif ($postcard)
                    <div class="recipient-box">
                        <small style="color:#64748b;">TO RECIPIENT:</small><br>
                        <strong style="font-size:22px; color:#1e293b;">{{ $postcard->contact?->nama_kontak ?? 'Friend' }}</strong>
                    </div>
                    <p style="font-size: 14px; color: #6b7280; margin-bottom: 20px;">
                        Greetings! A postcard from Indonesia has reached you. Please confirm to notify the sender.
                    </p>
                    <div class="pc-info-card">
                        @if (!$isSwap)
                            <div class="pc-info-item"><span>Postcard ID:</span> <b>{{ $postcard->postcard_id }}</b></div>
                        @endif
                        <div class="pc-info-item"><span>Sent on:</span> <b>{{ \Carbon\Carbon::parse($postcard->tanggal_kirim)->format('F d, Y') }}</b></div>
                        <div class="pc-info-item"><span>Est. Distance:</span> <b>~{{ number_format($distance) }} km</b></div>
                        <div class="pc-info-item"><span>Origin:</span> <b>Yogyakarta, Indonesia 🇮🇩</b></div>
                    </div>

                    <div id="map-confirm" wire:ignore></div> 
                    <button type="button" class="btn-action" style="margin-top: 25px;" @click="openModal(1)">REGISTER NOW</button>

                @else
                    <p>Sorry, the tracking ID or link is invalid. Please check the ID on the postcard again.</p>
                @endif
            </div>

            <div class="footer-text">
                {{ config('app.owner_username') }} Postcard Tracker &bull; {{ date('Y') }}
            </div>
        </div>

        <!-- Modals -->
        <template x-if="step === 1">
            <div class="modal-overlay">
                <div class="modal-box">
                    <h2 class="text-xl font-bold mb-2">Is it you?</h2>
                    <p class="mb-4">Confirming as <b>{{ $postcard->contact?->nama_kontak ?? 'the recipient' }}</b>.</p>
                    <div class="modal-btns">
                        <button class="btn-action bg-emerald-500 hover:bg-emerald-600" @click="confirmStep1()">Yes, confirm!</button>
                        <button @click="closeModal()" class="text-gray-400 hover:text-gray-600">Cancel</button>
                    </div>
                </div>
            </div>
        </template>

        <template x-if="step === 2">
            <div class="modal-overlay">
                <div class="modal-box">
                    <h2 class="text-xl font-bold mb-2">Add a Note?</h2>
                    <p class="mb-4">Would you like to send a message to {{ config('app.owner_name') }}?</p>
                    <div class="modal-btns">
                        <button class="btn-action" @click="goToStep3()">Write Message</button>
                        <button class="btn-action bg-gray-500 hover:bg-gray-600" @click="submitFinal()">No, just register</button>
                    </div>
                </div>
            </div>
        </template>

        <template x-if="step === 3">
            <div class="modal-overlay">
                <div class="modal-box">
                    <h2 class="text-xl font-bold mb-2">Your Message</h2>
                    <textarea x-model="message" rows="4" class="w-full p-3 border-2 border-gray-200 rounded-lg text-sm mb-4" placeholder="I love the stamps! Greetings from..."></textarea>
                    <div class="modal-btns">
                        <button class="btn-action" @click="submitFinal()">SEND & FINISH</button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Conditional Map Script -->
    <script>
        const isSwap = {{ $isSwap ? 'true' : 'false' }};
        const myLat = {{ $myLat }};
        const myLng = {{ $myLng }};
        const targetLat = {{ $targetLat }};
        const targetLng = {{ $targetLng }};
    </script>

    @if ($isChina)
    <script type="text/javascript">
        window._AMapSecurityConfig = { securityJsCode: '{{ config('app.amap_web_key') }}' };
    </script>
    <script type="text/javascript" src="https://webapi.amap.com/maps?v=2.0&key={{ config('app.amap_js_key') }}"></script>
    <script>
        @if($postcard && !$alreadyConfirmed && !$justConfirmed)
        window.onload = function() {
            const container = document.getElementById('map-confirm');
            if (container && typeof AMap !== 'undefined') {
                var map = new AMap.Map('map-confirm', { zoom: 2, center: [105, 10], mapStyle: 'amap://styles/light' });
                var start = new AMap.LngLat(myLng, myLat);
                var end = new AMap.LngLat(targetLng, targetLat);
                new AMap.CircleMarker({ center: start, radius: 5, strokeColor: '#ff4b4b', fillColor: '#ff4b4b', fillOpacity: 0.8 }).setMap(map);
                new AMap.CircleMarker({ center: end, radius: 5, strokeColor: '#4f46e5', fillColor: '#4f46e5', fillOpacity: 0.8 }).setMap(map);
                var polyline = new AMap.Polyline({ path: [start, end], strokeColor: '#4f46e5', strokeWeight: 2, strokeStyle: 'dashed', strokeDasharray: [5, 10] });
                map.add(polyline);
                map.setFitView([polyline], true, [30, 30, 30, 30]);
            }
        };
        @endif
    </script>
    @else
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('app.google_api_key') }}&libraries=marker&callback=initMap&loading=async" async defer></script>
    <script>
        @if($postcard && !$alreadyConfirmed && !$justConfirmed)
        function initMap() {
            var container = document.getElementById('map-confirm');
            if (!container) return;

            var myPos = { lat: myLat, lng: myLng };
            var targetPos = { lat: targetLat, lng: targetLng };
            
            var map = new google.maps.Map(container, {
                zoom: 2, 
                center: myPos, 
                disableDefaultUI: true,
                styles: [ 
                    { "featureType": "all", "elementType": "labels", "stylers": [{ "visibility": "on" }] }, 
                    { "featureType": "water", "stylers": [{ "color": "#e2e8f0" }] } 
                ]
            });

            new google.maps.Marker({
                position: myPos,
                map: map,
                icon: { path: google.maps.SymbolPath.CIRCLE, fillColor: '#ff4b4b', fillOpacity: 0.8, scale: 6, strokeColor: 'white', strokeWeight: 1 }
            });

            new google.maps.Marker({
                position: targetPos,
                map: map,
                icon: { path: google.maps.SymbolPath.CIRCLE, fillColor: '#4f46e5', fillOpacity: 0.8, scale: 6, strokeColor: 'white', strokeWeight: 1 }
            });

            var path = new google.maps.Polyline({ 
                path: [myPos, targetPos], geodesic: true, strokeColor: '#4f46e5', strokeOpacity: 0.8, strokeWeight: 2, 
                icons: [{ icon: { path: 'M 0,-1 0,1', strokeOpacity: 1, scale: 2 }, offset: '0', repeat: '10px' }] 
            });
            path.setMap(map);

            var bounds = new google.maps.LatLngBounds();
            bounds.extend(myPos); 
            bounds.extend(targetPos);
            map.fitBounds(bounds, 30);
        }
        @endif
    </script>
    @endif
</div>
