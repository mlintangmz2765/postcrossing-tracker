<div class="stamp-gallery-wrapper">
    <style>
        :root {
            --primary: #4f46e5;
            --post-red: #e63946;
            --post-blue: #457b9d;
            --paper: #fdf6e3;
            --stamp-w: 180px;
        }

        .stamp-gallery-wrapper {
            background-color: var(--paper);
            background-image: linear-gradient(#eee 1.1px, transparent 1.1px);
            background-size: 100% 1.8em;
            min-height: 100vh;
            padding-bottom: 100px;
            font-family: 'Quicksand', sans-serif;
            overflow-x: hidden;
            width: 100%;
        }

        /* Decoration: Airmail Top Border REMOVED as requested */
        /* .stamp-gallery-wrapper::before { content: ""; ... } */

        .stamp-header { text-align: center; margin: 40px 0 30px; position: relative; }
        .stamp-header h1 { font-family: 'Dancing Script', cursive; font-size: 4.5rem; margin: 0; color: #1e293b; text-shadow: 2px 2px #fff; }
        .stamp-header p { font-family: 'Special Elite', cursive; color: #64748b; font-size: 1rem; margin-top: -10px; }

        /* Badge Positioned to the side (Absolute) */
        .airmail-badge {
            position: absolute; 
            top: 10px; right: 0; /* Align to the right of the header area */
            border: 3px solid var(--post-blue); color: var(--post-blue);
            padding: 5px 15px; font-family: 'Special Elite', cursive;
            transform: rotate(5deg); font-size: 1rem;
            border-radius: 5px; opacity: 0.8; 
            max-width: 150px;
        }
        
    </style>

    <header class="stamp-header" style="max-width: 600px; margin: 40px auto 30px;">
        <p>Happy Postcrossing!</p>
        <h1>Stamp Collection</h1>
        <div class="airmail-badge">BY AIR MAIL<br>PAR AVION</div>
    </header>

    <!-- Marquee Slider -->
    <style>
        .marquee-container {
            width: 100%;
            overflow: hidden;
            background: #1a1a1a;
            padding: 35px 0;
            box-shadow: inset 0 0 50px rgba(0,0,0,0.5);
            margin-bottom: 60px;
            border-top: 2px dashed #444;
            border-bottom: 2px dashed #444;
        }

        .marquee-content {
            display: flex;
            animation: scroll var(--durasi) linear infinite;
            width: max-content;
        }
        .marquee-content:hover { animation-play-state: paused; }

        @keyframes scroll {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }

        .stamp-card-slider {
            width: var(--stamp-w); height: 140px; margin: 0 15px;
            background: white; padding: 10px;
            /* Simple border, removed serrated hack */
            border: 1px solid #ccc; border-radius: 2px;
            box-shadow: 3px 3px 10px rgba(0,0,0,0.3);
            flex-shrink: 0;
            transform: rotate(calc(var(--r) * 1deg));
            cursor: pointer; transition: 0.3s;
            display: flex; align-items: center; justify-content: center; position: relative;
        }
        
        .stamp-card-slider img { max-width: 100%; max-height: 100%; object-fit: contain; z-index: 1; }
        .stamp-card-slider:hover { transform: scale(1.1) rotate(0deg); z-index: 10; box-shadow: 0 10px 20px rgba(0,0,0,0.5); }

        /* --- GRID STYLES RESTORED --- */
        .gallery-grid {
            display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 30px;
            max-width: 1200px; margin: 0 auto; padding: 0 20px;
        }

        .gallery-item {
            display: block; /* Ensure anchor behaves as block */
            background: white; padding: 15px; border-radius: 2px;
            box-shadow: 3px 3px 10px rgba(0,0,0,0.1); transition: 0.4s;
            text-decoration: none; color: inherit; border: 1px solid #d1d5db;
            position: relative;
        }
        .gallery-item:hover { transform: translateY(-10px) rotate(2deg); box-shadow: 15px 15px 30px rgba(0,0,0,0.1); }

        .stamp-wrapper {
            width: 100%; height: 180px; display: flex; align-items: center; justify-content: center;
            background: #f8fafc; border: 1px solid #eee; position: relative; overflow: hidden;
        }
        .stamp-wrapper img {
            max-width: 85%; max-height: 85%; object-fit: contain;
            filter: grayscale(20%) contrast(1.1); transition: 0.5s;
        }
        .gallery-item:hover img { filter: grayscale(0%); scale: 1.1; }

        .postmark {
            position: absolute; bottom: -10px; right: -10px; width: 80px; height: 80px;
            border: 2px double #888; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-family: 'Special Elite', cursive; font-size: 8px; color: #888; text-align: center;
            transform: rotate(-20deg); opacity: 0.4; z-index: 2; pointer-events: none;
        }

        .stamp-info { margin-top: 15px; }
        .stamp-country { font-weight: 700; color: #1e293b; font-family: 'Special Elite', cursive; }
        .stamp-id { font-size: 0.7rem; color: #94a3b8; text-transform: uppercase; margin-top: 5px; }

        .section-title {
            font-family: 'Special Elite', cursive; font-size: 1.8rem; margin-bottom: 30px;
            color: #334155; display: flex; align-items: center; gap: 15px;
            max-width: 1200px; margin: 30px auto; padding: 0 20px;
        }
        .section-title::after {
            content: ""; flex: 1; height: 2px; background: repeating-linear-gradient(90deg, #ccc, #ccc 5px, transparent 5px, transparent 10px);
        }
    </style>
    <div class="marquee-container">
        @php
            $count = count($sliderStamps);
            $duplicateStamps = array_merge($sliderStamps, $sliderStamps); // Duplicate for seamless loop
            $speed = 50; 
            $duration = $count > 0 ? ($count * 200) / $speed : 10;
        @endphp
        
        <div class="marquee-content" style="--durasi: {{ $duration }}s">
            @foreach($duplicateStamps as $st)
                @php $random_rot = rand(-5, 5); @endphp
                <div class="stamp-card-slider" style="--r: {{ $random_rot }}" onclick="window.location.href='{{ route('view', ['id' => $st->id]) }}'">
                    <img src="{{ asset($st->foto_prangko) }}" loading="lazy" alt="Stamp">
                </div>
            @endforeach
        </div>
    </div>

    <h3 class="section-title">International Philately</h3>

    <div class="gallery-grid">
        @foreach($galleryStamps as $g)
            <a href="{{ route('view', ['id' => $g->id]) }}" class="gallery-item">
                <div class="stamp-wrapper">
                    <div class="postmark">CHECKED<br>{{ date('d.m.Y') }}<br>PASSED</div>
                    <img src="{{ asset($g->foto_prangko) }}" loading="lazy" alt="Stamp">
                </div>
                <div class="stamp-info">
                    <div class="stamp-country">📍 {{ $g->negara }}</div>
                    <div class="stamp-id">{{ $g->postcard_id ?: 'PHIL-'.rand(1000,9999) }}</div>
                </div>
            </a>
        @endforeach
    </div>
</div>
