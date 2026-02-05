<div class="view-wrapper paper-texture py-10 px-4">
    <style>
        .view-container {
            max-width: 900px; margin: 0 auto;
            background: white; padding: 10px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
        }

        .view-inner {
            background: #fff;
            padding: 30px;
            border-radius: 4px;
        }

        /* HEADER */
        .header-section { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px dashed #f1f5f9; padding-bottom: 20px; margin-bottom: 30px; }
        .header-title { margin: 0; color: var(--pc-blue); font-family: 'Dancing Script', cursive; font-size: 2.8rem; }
        .header-title a { text-decoration: none; color: inherit; }
        .header-title a:hover { text-decoration: underline; }
        .status-badge { padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: bold; text-transform: uppercase; font-family: 'Special Elite'; }
        
        /* PHOTO GRID */
        .photo-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px; }
        .photo-box { 
            background: #fafafa; padding: 15px; border-radius: 4px; border: 1px solid #eee; 
            text-align: center; min-height: 250px; display: flex; flex-direction: column; justify-content: center; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        .photo-box img { 
            width: 100%; border: 6px solid white; box-shadow: 0 5px 15px rgba(0,0,0,0.1); border-radius: 2px;
            max-height: 400px; object-fit: contain; cursor: pointer; transition: 0.3s; 
        }
        .photo-box img:hover { transform: scale(1.02); }
        .photo-label { display: block; margin-top: 15px; font-weight: bold; font-size: 12px; color: var(--pc-blue); font-family: 'Special Elite'; letter-spacing: 1px; }

        /* STAMPS */
        .stamp-section { margin-top: 25px; padding-top: 25px; border-top: 2px dashed #f1f5f9; }
        .stamp-gallery { display: flex; gap: 15px; flex-wrap: wrap; margin-top: 15px; }
        .stamp-item { background: white; padding: 5px; border: 1px solid #ddd; box-shadow: 0 4px 8px rgba(0,0,0,0.08); transition: 0.2s; cursor: pointer; rotate: -2deg; }
        .stamp-item:nth-child(even) { rotate: 2deg; }
        .stamp-item:hover { rotate: 0deg; transform: translateY(-5px); }
        .stamp-item img { width: 80px; height: 80px; object-fit: cover; border: 1px solid #eee; }

        /* MESSAGES */
        .msg-box { background: #f0fdf4; padding: 20px; border-radius: 15px; border: 2px solid #bbf7d0; margin-bottom: 20px; line-height: 1.6; position: relative; color: #166534; font-style: italic; }
        .msg-box::before { content: '💬'; position: absolute; top: -15px; left: 20px; background: white; font-size: 20px; border: 1px solid #bbf7d0; border-radius: 50%; padding: 5px; }
        .desc-box { background: #fdf6e3; padding: 20px; border-radius: 8px; border: 1px solid #eee; margin-bottom: 30px; line-height: 1.6; color: var(--pc-ink); position: relative; }
        .desc-box::before { content: 'NOTES'; position: absolute; top: -10px; left: 20px; background: var(--pc-blue); color: white; font-family: 'Special Elite'; font-size: 10px; padding: 2px 8px; border-radius: 4px; }

        /* MAP */
        #google-map-view { height: 400px; width: 100%; border-radius: 8px; margin-bottom: 30px; border: 2px solid white; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }

        /* INFO GRID */
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }
        .info-item { margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #f8fafc; }
        .info-item label { display: block; font-size: 11px; font-weight: bold; color: #94a3b8; text-transform: uppercase; margin-bottom: 6px; font-family: 'Special Elite'; letter-spacing: 1px; }
        .info-item span { font-size: 15px; font-weight: 500; color: #1e293b; word-wrap: break-word; font-family: 'Quicksand', sans-serif; }
        
        /* QR CODE */
        .qr-area { text-align: center; background: white; padding: 20px; border: 2px solid #eee; border-radius: 8px; display: inline-block; box-shadow: 0 10px 20px rgba(0,0,0,0.05); }

        /* Lightbox */
        .lightbox { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.9); align-items: center; justify-content: center; }
        .lightbox img { max-width: 90%; max-height: 90%; border: 10px solid white; box-shadow: 0 0 50px rgba(0,0,0,0.5); }
        .close-lightbox { position: absolute; top: 20px; right: 30px; color: white; font-size: 40px; font-weight: bold; cursor: pointer; }

        @media(max-width: 768px) {
            .photo-grid, .info-grid { grid-template-columns: 1fr; }
        }
    </style>

    <div class="view-container airmail-border">
        <div class="view-inner">
        <!-- Dashboard Button Removed as per request (or keeps legacy 'Back to Dashboard'?) -->
        <!-- User said "hapus tombol ke postcard gallery", assuming they meant the big button at bottom. -->
        <!-- Legacy had a "Dashboard" back link at top. I'll keep a small text link or remove entire nav if they want pure view. -->


        <!-- HEADER -->
        <div class="header-section">
            <div>
                <h2 class="header-title">
                    @php
                        $pcUrl = "#";
                        if (!empty($card->postcard_id) && str_contains($card->postcard_id, '-')) {
                            $isTravelling = (empty($card->tanggal_terima) || $card->tanggal_terima == '0000-00-00');
                            $urlPrefix = ($card->type == 'sent' && $isTravelling) ? 'travelingpostcard' : 'postcards';
                            $pcUrl = "https://www.postcrossing.com/" . $urlPrefix . "/" . $card->postcard_id;
                        }
                    @endphp

                    @if($pcUrl !== "#")
                        <a href="{{ $pcUrl }}" target="_blank" title="Buka di Postcrossing">{{ $card->postcard_id }}</a>
                    @else
                        {{ $card->postcard_id ?: 'Direct Swap' }}
                    @endif
                </h2>
                <span class="status-badge" style="background: {{ $card->type == 'sent' ? '#eff6ff' : '#f0fdf4' }}; color: {{ $card->type == 'sent' ? '#1d4ed8' : '#15803d' }};">
                    @if($card->type == 'sent') <i class="bi bi-send"></i> SENT @else <i class="bi bi-box-arrow-in-down"></i> RECEIVED @endif
                </span>
            </div>
            <div>
                <a href="{{ route('edit', ['id' => $card->id]) }}" style="color: var(--post-blue); font-weight: bold; text-decoration: none;">
                    <i class="bi bi-pencil-square"></i> Edit Data
                </a>
            </div>
        </div>

        <!-- IMAGES -->
        <div class="photo-grid">
            <div class="photo-box">
                @if(!empty($card->foto_depan))
                    <img src="{{ asset($card->foto_depan) }}" class="cursor-pointer" onclick="openLightbox(this.src)">
                @else
                    <div style="color:#94a3b8; font-style:italic; font-size:14px; margin: auto;">No front side image available</div>
                @endif
                <span class="photo-label">FRONT SIDE</span>
            </div>
            <div class="photo-box">
                @if(!empty($card->foto_belakang))
                    <img src="{{ asset($card->foto_belakang) }}" class="cursor-pointer" onclick="openLightbox(this.src)">
                @else
                    <div style="color:#94a3b8; font-style:italic; margin: auto;">No back side image available</div>
                @endif
                <span class="photo-label">BACK SIDE</span>
            </div>
        </div>

        <!-- STAMPS -->
        @if(count($stamps) > 0)
        <div class="stamp-section">
            <label style="font-size: 11px; font-weight: bold; color: #94a3b8; text-transform: uppercase; font-family: 'Special Elite';">Stamp Collection</label>
            <div class="stamp-gallery">
                @foreach($stamps as $st)
                <div class="stamp-item" onclick="openLightbox('{{ asset($st->foto_prangko) }}')">
                    <img src="{{ asset($st->foto_prangko) }}">
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- MESSAGES -->
        <div style="margin-top: 25px;">
            @if(!empty($card->pesan_penerima))
            <div class="msg-box">
                <label style="font-size: 11px; font-weight: bold; color: #15803d; text-transform: uppercase;">Message from Recipient</label><br>
                <span>"{{ $card->pesan_penerima }}"</span>
            </div>
            @endif

            @if(!empty($card->deskripsi_gambar))
            <div class="desc-box">
                <label style="font-size: 11px; font-weight: bold; color: #b45309; text-transform: uppercase;">Card Description</label><br>
                <span>{{ $card->deskripsi_gambar }}</span>
            </div>
            @endif
        </div>

        <!-- MAP -->
        <label style="font-size: 11px; font-weight: bold; color: #94a3b8; text-transform: uppercase; margin-top:20px; display:block; font-family: 'Special Elite';">Travel Visualization</label>
        <div id="google-map-view"></div>

        <!-- FULL DETAILS GRID -->
        <div class="info-grid">
            <!-- Left Info -->
            <div>
                <div class="info-item">
                    <label>Country</label>
                    <span>{{ $card->negara }}</span>
                </div>
                <div class="info-item">
                    <label>{{ $card->type == 'sent' ? 'Recipient' : 'Sender' }}</label>
                    <span>{{ $card->nama_kontak }}</span>
                </div>
                <div class="info-item">
                    <label>Full Address</label>
                    <span>{!! nl2br(e($card->alamat)) !!}</span>
                </div>
                <div class="info-item">
                    <label>Phone Number</label>
                    <span>{{ $card->nomor_telepon ?: '-' }}</span>
                </div>
                 <div class="info-item">
                    <label>Coordinates</label>
                    <span>{{ (float)$card->lat }}, {{ (float)$card->lng }}</span>
                </div>
            </div>

            <!-- Right Info -->
            <div>
                <div class="info-item">
                    <label>Sent Date</label>
                    <span>{{ \Carbon\Carbon::parse($card->tanggal_kirim)->format('d F Y') }}</span>
                </div>
                <div class="info-item">
                    <label>Received Date</label>
                    <span>{{ (!empty($card->tanggal_terima) && $card->tanggal_terima != '0000-00-00') ? \Carbon\Carbon::parse($card->tanggal_terima)->format('d F Y') : 'Travelling / Not Yet Received' }}</span>
                </div>
                <div class="info-item">
                    <label>Duration</label>
                    <span>{{ $travelTime }} days</span>
                </div>
                <div class="info-item">
                    <label>Est. Distance</label>
                    <span style="color:var(--post-blue); font-weight:bold;">{{ number_format($distance, 0, ',', '.') }} KM</span>
                </div>
                <div class="info-item">
                    <label>Postage Cost / Value</label>
                    <span>
                        @if($card->type == 'received' && !empty($card->mata_uang) && $card->nilai_asal > 0)
                            <span style="font-size: 13px; color: #64748b; display: block;">
                                {{ $card->mata_uang }} {{ number_format($card->nilai_asal, 2) }}
                                <small>(Rate: {{ number_format($card->kurs_idr, 0, ',', '.') }})</small>
                            </span>
                        @endif
                        <strong style="color: var(--post-blue);">Rp {{ number_format($card->biaya_prangko, 0, ',', '.') }}</strong>
                    </span>
                </div>
            </div>
        </div>

        <!-- QR SECTION (Sent Only) -->
        @if($card->type == 'sent')
        <div style="border-top: 2px solid #f1f5f9; margin-top: 30px; padding-top: 30px; text-align: center;">
            <div class="qr-area" id="qr-download-area">
                <p style="font-size: 13px; font-weight: 700; color: #1e293b; margin: 0 0 5px 0; text-transform: uppercase;">Received this postcard?</p>
                <p style="font-size: 11px; color: #64748b; margin: 0 0 15px 0; max-width: 200px; line-height: 1.3;">Please scan to notify the sender that it has arrived safely.</p>
                <div id="qrcode" style="display: flex; justify-content: center;"></div>
                <p style="font-size: 11px; margin: 10px 0 0 0; font-family: monospace; font-weight: bold; color: #1e293b;">{{ $card->postcard_id }}</p>
            </div>
            <div style="margin-top: 15px;">
                <button onclick="downloadQR()" style="background: #22c55e; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; cursor: pointer; font-size: 13px;">
                    <i class="bi bi-download"></i> DOWNLOAD LABEL
                </button>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- LIGHTBOX -->
    <div id="lightbox" class="lightbox" onclick="closeLightbox()">
        <span class="close-lightbox">&times;</span>
        <img id="lightbox-img" src="" onclick="event.stopPropagation()">
    </div>

    <!-- SCRIPTS -->
    <script src="{{ asset('vendor/qrcode/qrcode.min.js') }}"></script>
    <script src="{{ asset('vendor/html-to-image/html-to-image.js') }}"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_API_KEY') }}&libraries=marker&callback=initViewMap&loading=async" async defer></script>

    <script>
        function openLightbox(src) {
            document.getElementById('lightbox-img').src = src;
            document.getElementById('lightbox').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
        function closeLightbox() {
            document.getElementById('lightbox').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Map using Google Maps
        async function initViewMap() {
            const myPos = { lat: {{ $originLat }}, lng: {{ $originLng }} };
            const targetPos = { lat: {{ $card->lat }}, lng: {{ $card->lng }} };

            const { Map } = await google.maps.importLibrary("maps");

            const map = new Map(document.getElementById('google-map-view'), {
                zoom: 3,
                center: myPos,
                streetViewControl: false,
                styles: [
                    { "elementType": "geometry", "stylers": [{ "color": "#ebe3cd" }] },
                    { "elementType": "labels.text.fill", "stylers": [{ "color": "#523735" }] },
                    { "featureType": "water", "elementType": "geometry.fill", "stylers": [{ "color": "#b9d3c2" }] }
                ]
            });

            // Standard Marker (Allows programmatic styles)
            new google.maps.Marker({
                position: myPos,
                map: map,
                icon: { path: google.maps.SymbolPath.CIRCLE, fillColor: '#ff4b4b', fillOpacity: 0.8, scale: 8, strokeColor: 'white', strokeWeight: 2 },
                title: 'My Location (Yogyakarta)'
            });

            new google.maps.Marker({
                position: targetPos,
                map: map,
                icon: { path: google.maps.SymbolPath.CIRCLE, fillColor: '#007bff', fillOpacity: 0.8, scale: 8, strokeColor: 'white', strokeWeight: 2 },
                title: 'Postcard Location'
            });

            // Polyline
            new google.maps.Polyline({
                path: [myPos, targetPos],
                geodesic: true,
                strokeColor: '#007bff',
                strokeOpacity: 0.8,
                strokeWeight: 3,
                icons: [{ icon: { path: 'M 0,-1 0,1', strokeOpacity: 1, scale: 2 }, offset: '0', repeat: '10px' }]
            }).setMap(map);

            // Fit bounds
            const bounds = new google.maps.LatLngBounds();
            bounds.extend(myPos);
            bounds.extend(targetPos);
            map.fitBounds(bounds, 50);
        }

        // QR Code
        @if($card->type == 'sent')
            document.addEventListener('DOMContentLoaded', () => {
                new QRCode(document.getElementById("qrcode"), {
                    text: "{{ route('receive.confirm', ['uid' => $card->uid]) }}",
                    width: 128, height: 128
                });
            });

            window.downloadQR = function() {
                var node = document.getElementById('qr-download-area');
                if (!node) { alert('Node not found'); return; }

                htmlToImage.toPng(node)
                    .then(function (dataUrl) {
                        var link = document.createElement('a');
                        link.download = 'Label_{{ $card->uid }}.png';
                        link.href = dataUrl;
                        link.click();
                    })
                    .catch(function (error) {
                        console.error('oops, something went wrong!', error);
                        alert('Gagal: ' + error);
                    });
            }
        @endif
    </script>
</div>
