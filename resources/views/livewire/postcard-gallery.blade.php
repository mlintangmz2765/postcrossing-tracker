<div class="gallery-wrapper">

    <style>
        :root { 
            --post-red: #e63946;
            --post-blue: #457b9d;
            --paper: #fdf6e3;
            --text-dark: #1e293b;
        }
        
        /* Page Background */
        .gallery-wrapper {
            background-color: var(--paper);
            background-image: 
                linear-gradient(#e5e5e5 1.1px, transparent 1.1px), 
                linear-gradient(90deg, #e5e5e5 1.1px, transparent 1.1px);
            background-size: 30px 30px;
            min-height: 100vh;
            padding-bottom: 50px;
            font-family: 'Quicksand', sans-serif;
            /* Ensure it breaks out or covers nicely */
            width: 100%;
        }

        .gallery-header { 
            text-align: center; margin: 0 auto 60px; position: relative; padding-top: 40px;
        }
        .gallery-header h1 { 
            font-family: 'Dancing Script', cursive; font-size: 5.5rem; margin: 0; 
            color: #1e293b; text-shadow: 4px 4px 0px #fff;
            filter: drop-shadow(2px 2px 2px rgba(0,0,0,0.1));
        }

        .paravion-badge {
            display: inline-flex; align-items: center; gap: 15px;
            border: 3px solid var(--post-blue); padding: 8px 25px;
            margin-top: 5px; transform: rotate(-1.5deg); 
            background: rgba(255,255,255,0.8);
            box-shadow: 3px 3px 0px var(--post-blue);
        }

        .paravion-text {
            font-family: 'Special Elite', cursive; color: var(--post-blue);
            line-height: 1.1; text-align: left;
        }
        .paravion-text span { font-size: 1.5rem; font-weight: bold; display: block; }
        .paravion-text small { font-size: 0.8rem; letter-spacing: 3px; }

        .gallery-grid { 
            display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); 
            gap: 50px; max-width: 1300px; margin: 0 auto; padding-bottom: 50px;
        }

        .card-container { 
            perspective: 2000px; cursor: pointer; position: relative; 
            /* Default Aspect Ratio */
            aspect-ratio: 4/3;
            transition: aspect-ratio 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .card-inner { 
            position: relative; width: 100%; height: 100%;
            transition: transform 0.8s cubic-bezier(0.34, 1.56, 0.64, 1); transform-style: preserve-3d; 
        }
        .card-container.is-flipped .card-inner { transform: rotateY(180deg); }

        .card-front, .card-back { 
            position: absolute; width: 100%; height: 100%; 
            backface-visibility: hidden; border-radius: 4px; 
            background: #fff; 
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            border: 12px solid white; box-sizing: border-box; overflow: hidden;
            display: flex; flex-direction: column;
        }

        .card-back { 
            transform: rotateY(180deg); background-color: #f3ece0;
            background-image: url('https://www.transparenttextures.com/patterns/cardboard-flat.png'); /* We might need to cache this pattern locally */
        }

        .card-front img, .card-back img { 
            width: 100%; height: 100%; object-fit: cover; display: block; 
            filter: contrast(1.05) saturate(1.1);
        }

        /* INFO OVERLAY */
        .info-overlay { 
            position: absolute; bottom: 0; left: 0; right: 0; 
            background: linear-gradient(transparent, rgba(0,0,0,0.9));
            color: #fff; padding: 60px 20px 20px; 
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94); 
            display: flex; flex-direction: column; gap: 10px;
            opacity: 0; transform: translateY(100%); pointer-events: none;
        }
        
        .card-front:hover .info-overlay { 
            opacity: 1; transform: translateY(0); pointer-events: auto; 
        }

        .info-overlay h4 { margin: 0; font-family: 'Special Elite', cursive; font-size: 20px; text-shadow: 1px 1px 2px #000; }
        
        .btn-view-card {
            background: var(--post-red); color: white; text-decoration: none;
            font-family: 'Special Elite', cursive; font-size: 12px;
            padding: 10px; text-align: center; border-radius: 3px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.4); transition: 0.3s;
        }

        .postmark-decor {
            position: absolute; top: 30px; right: 30px; width: 90px; height: 90px;
            border: 4px double rgba(0,0,0,0.08); border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 10px; text-align: center; color: rgba(0,0,0,0.15);
            transform: rotate(-20deg); pointer-events: none;
            font-family: 'Special Elite', cursive; line-height: 1.3;
            z-index: 5;
        }
        
        .btn-back-dash {
             position: fixed; bottom: 30px; left: 30px;
             text-decoration: none; color: #fff; font-family: 'Special Elite', cursive;
             background: #1e293b; padding: 12px 25px; border-radius: 5px; 
             box-shadow: 4px 4px 0px #457b9d; z-index: 100; transition: 0.3s;
        }
        .btn-back-dash:hover { transform: translate(-3px, -3px); box-shadow: 7px 7px 0px #457b9d; color:white;}

    </style>



    <div class="gallery-header">
        <h1>Postcard Gallery</h1>
        <div class="paravion-badge">
            <svg width="45" height="45" viewBox="0 0 24 24" fill="var(--post-blue)">
                <path d="M21,16L21,14L13,9L13,3.5C13,2.67 12.33,2 11.5,2C10.67,2 10,2.67 10,3.5L10,9L2,14L2,16L10,13.5L10,19L8,20.5L8,22L11.5,21L15,22L15,20.5L13,19L13,13.5L21,16Z" />
            </svg>
            <div class="paravion-text">
                <span>PAR AVION</span>
                <small>BY AIR MAIL</small>
            </div>
        </div>
    </div>

    <div class="gallery-grid">
        @foreach($cards as $card)
            <div class="card-container" 
                 data-ratio-front="{{ $card->ratioFront }}" 
                 data-ratio-back="{{ $card->ratioBack }}" 
                 style="aspect-ratio: {{ $card->ratioFront }};" 
                 onclick="toggleFlip(this)">
                <div class="card-inner">
                    <!-- Front -->
                    <div class="card-front">
                        <img src="{{ asset($card->foto_depan) }}" loading="lazy" alt="Front">
                        <div class="info-overlay">
                            <h4>{{ $card->country?->nama_inggris ?? $card->country?->nama_indonesia ?? 'Unknown' }}</h4>
                            <p style="font-size: 11px; opacity: 0.9; margin: 0; font-family: 'Special Elite'; letter-spacing: 1px;">
                                {{ $card->postcard_id ?: 'DIRECT SWAP' }}
                            </p>
                            <a href="{{ route('view', ['id' => $card->id]) }}" class="btn-view-card" onclick="event.stopPropagation();">VIEW FULL DETAILS</a>
                        </div>
                    </div>

                    <!-- Back -->
                    <div class="card-back">
                        <div class="postmark-decor">CHECKED & PASSED<br>OFFICIAL POST<br>{{ date('M Y') }}</div>
                        @if($card->foto_belakang)
                            <img src="{{ asset($card->foto_belakang) }}" loading="lazy" alt="Back">
                        @else
                            <div style="flex:1; display:flex; align-items:center; justify-content:center; color:#94a3b8; font-family:'Special Elite'; font-style:italic; font-size:13px; padding:30px; text-align:center;">
                                [ No handwritten message scan available ]
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
    function toggleFlip(card) {
        const isFlipped = card.classList.toggle('is-flipped');
        
        // Dynamic Ratio Switching
        const ratioFront = card.getAttribute('data-ratio-front');
        const ratioBack = card.getAttribute('data-ratio-back');
        
        card.style.aspectRatio = isFlipped ? ratioBack : ratioFront;
    }
</script>
