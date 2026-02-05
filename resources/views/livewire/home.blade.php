
    <style>
        /* --- 1. SETUP FONT LOKAL --- */
        @font-face {
            font-family: 'Dancing Script';
            src: url('{{ asset('fonts/dancing-script.woff2') }}') format('woff2');
            font-weight: 700;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'Special Elite';
            src: url('{{ asset('fonts/special-elite.woff2') }}') format('woff2');
            font-weight: 400;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'Quicksand';
            src: url('{{ asset('fonts/quicksand.woff2') }}') format('woff2');
            font-weight: 400 700;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'Ma Shan Zheng';
            src: url('{{ asset('fonts/ma-shan-zheng.woff2') }}') format('woff2');
            font-weight: 400;
            font-style: normal;
            font-display: swap;
        }

        /* --- 2. CSS UTAMA --- */
        :root {
            --primary: #2c3e50;
            --accent-blue: #457b9d;
            --accent-red: #e63946;
            --paper-bg: #fdf6e3;
            --paper-texture: #f4e9d7;
            --text-main: #333;
        }

        .home-wrapper {
            font-family: 'Quicksand', sans-serif;
            background-color: var(--paper-bg);
            /* Pola kertas halus */
            background-image: linear-gradient(rgba(0,0,0,0.02) 1px, transparent 1px),
            linear-gradient(90deg, rgba(0,0,0,0.02) 1px, transparent 1px);
            background-size: 20px 20px;
            color: var(--text-main);
            margin: 0;
            line-height: 1.8;
            overflow-x: hidden;
            min-height: 100vh;
            -webkit-font-smoothing: auto;
            -moz-osx-font-smoothing: auto;
        }
        
        /* Helper for legacy text-danger */
        .text-danger { color: var(--accent-red); }

        /* --- DEKORASI PAR AVION (Strip Atas) --- */
        .par-avion-strip {
            position: fixed; top: 0; left: 0; right: 0; height: 10px;
            background: repeating-linear-gradient(
                -45deg,
                var(--accent-blue) 0, var(--accent-blue) 15px,
                #fff 15px, #fff 25px,
                var(--accent-red) 25px, var(--accent-red) 40px,
                #fff 40px, #fff 50px
            );
            z-index: 2000;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        /* --- NAVIGATION (Top Right) --- */
        .top-nav-legacy {
            position: absolute;
            top: 25px;
            right: 25px;
            z-index: 100;
            display: flex;
            gap: 15px;
        }

        .nav-link-legacy {
            text-decoration: none;
            color: #fff;
            font-family: 'Special Elite', monospace;
            font-size: 0.9rem;
            padding: 8px 15px;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(4px);
            border-radius: 4px;
            border: 1px solid rgba(255,255,255,0.3);
            transition: 0.3s;
        }

        .nav-link-legacy:hover {
            background: var(--accent-red);
            border-color: var(--accent-red);
        }

        /* --- HERO SECTION --- */
        .hero {
            position: relative;
            /* Gambar lokal home.jpg */
            background: linear-gradient(rgba(44, 62, 80, 0.7), rgba(44, 62, 80, 0.6)), 
                        url('{{ asset('images/home.jpg') }}'); 
            
            background-size: cover;
            background-position: center;
            background-attachment: fixed; /* Efek Parallax */
            color: white;
            padding: 140px 20px 100px;
            text-align: center;
            border-bottom: 5px solid white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .hero h1 {
            font-family: 'Dancing Script', cursive;
            font-size: 5rem;
            margin: 0;
            text-shadow: 3px 3px 0px rgba(0,0,0,0.3);
            letter-spacing: 2px;
            font-weight: 700;
        }

        .hero h1.lang-zh {
            font-family: 'Ma Shan Zheng', cursive;
            font-weight: 400;
        }

        .hero p {
            font-family: 'Special Elite', monospace;
            font-size: 1.3rem;
            max-width: 700px;
            margin: 15px auto 40px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
            letter-spacing: 1px;
        }

        /* --- TOMBOL UTAMA (Public Gallery) --- */
        .btn-gallery {
            display: inline-block;
            background: #fff;
            color: var(--primary);
            font-family: 'Special Elite', monospace;
            font-size: 1.1rem;
            font-weight: bold;
            text-transform: uppercase;
            padding: 15px 40px;
            text-decoration: none;
            border: 2px dashed var(--primary);
            border-radius: 2px;
            box-shadow: 5px 5px 0px rgba(0,0,0,0.2);
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
            line-height: 1.2; /* Fix vertical alignment */
        }
        
        .btn-gallery:hover {
            transform: translate(-3px, -3px);
            box-shadow: 8px 8px 0px var(--accent-red);
            border-color: var(--accent-red);
            color: var(--accent-red);
        }
        
        .btn-gallery::before {
             content: none; /* Legacy CSS content was invalid/broken, so no icon */
        }

        /* --- CONTENT SECTION --- */
        .section-legacy {
            padding: 60px 20px;
            max-width: 1000px;
            margin: auto;
        }

        /* Card Style: Amplop Vintage */
        .card-info {
            background: #fff;
            padding: 50px;
            position: relative;
            margin-top: -60px; /* Overlap dengan hero */
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            border: 1px solid #e0e0e0;
            /* Efek kertas bertumpuk */
            border-radius: 2px;
        }
        
        .card-info::before {
            content: "";
            position: absolute;
            top: 5px; left: 5px; right: -5px; bottom: -5px;
            background: #f8f8f8;
            border: 1px solid #ddd;
            z-index: -1;
            border-radius: 2px;
        }

        .card-info h2 {
            font-family: 'Dancing Script', cursive;
            color: var(--primary);
            font-size: 2.5rem;
            margin-top: 0;
            margin-bottom: 20px;
            border-bottom: 2px dashed var(--accent-blue);
            display: inline-block;
            padding-bottom: 5px;
            font-weight: 700;
        }

        .about-postcrossing {
            margin-bottom: 50px;
            text-align: left;
        }

        .about-postcrossing p {
            font-size: 1.05rem;
            color: #555;
        }

        .card-info strong {
            color: var(--accent-red);
            font-family: 'Special Elite', monospace;
        }

        /* --- DEKORASI PRANGKO CSS --- */
        .stamp-decor {
            position: absolute;
            top: -20px;
            right: 20px;
            width: 80px;
            height: 90px;
            background: white;
            border: 4px solid var(--paper-bg);
            /* Gerigi Prangko */
            background-image: 
                radial-gradient(var(--paper-bg) 30%, transparent 30%),
                radial-gradient(var(--paper-bg) 30%, transparent 30%);
            background-size: 10px 10px;
            background-position: 0 0, 5px 5px;
            box-shadow: 2px 4px 10px rgba(0,0,0,0.2);
            transform: rotate(10deg);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }
        
        .stamp-decor::after {
            content: "IDN";
            font-family: 'Special Elite', monospace;
            font-weight: bold;
            color: #ccc;
            font-size: 1.5rem;
        }

        /* --- OFFER BOX --- */
        .offer-box {
            background: #fff;
            padding: 30px;
            border: 2px solid var(--primary);
            position: relative;
            margin-top: 50px;
            text-align: center;
        }

        /* Strip merah biru di pinggir box offer */
        .offer-box::after {
            content: "";
            position: absolute;
            bottom: 5px; left: 5px; right: 5px; height: 5px;
            background: repeating-linear-gradient(
                45deg,
                var(--accent-red), var(--accent-red) 10px,
                #fff 10px, #fff 20px,
                var(--accent-blue) 20px, var(--accent-blue) 30px,
                #fff 30px, #fff 40px
            );
        }

        .btn-contact {
            display: inline-block;
            background: var(--primary);
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 50px;
            font-family: 'Special Elite', monospace;
            font-size: 0.95rem;
            transition: 0.3s;
            margin-top: 20px;
            border: 2px solid var(--primary);
        }

        .btn-contact:hover {
            background: white;
            color: var(--primary);
        }

        /* --- LANG SWITCHER (Bottom Right) --- */
        .lang-switcher {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #fff;
            padding: 5px;
            border-radius: 50px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            display: flex;
            gap: 0;
            border: 2px solid var(--primary);
            z-index: 1000;
        }

        .lang-btn {
            border: none;
            background: none;
            padding: 10px 15px;
            border-radius: 50px;
            cursor: pointer;
            font-family: 'Special Elite', monospace;
            font-weight: bold;
            font-size: 12px;
            color: var(--primary);
            transition: 0.3s;
        }

        .lang-btn.active {
            background: var(--primary);
            color: white;
        }

        /* --- FOOTER --- */
        .footer-legacy {
            padding: 50px 20px;
            text-align: center;
            font-family: 'Special Elite', monospace;
            color: #777;
            font-size: 0.85rem;
            border-top: 1px solid #ddd;
            background: #fff;
        }

        /* Mobile Responsive */
        @media (max-width: 600px) {
            .hero { padding-top: 120px; }
            .hero h1 { font-size: 3rem; }
            .card-info { padding: 30px 20px; }
            .stamp-decor { display: none; } /* Hide decorative stamp on mobile */
            .top-nav-legacy { top: 20px; right: 20px; gap: 10px; }
            .nav-link-legacy { padding: 6px 12px; font-size: 0.8rem; }
        }
    </style>
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.css') }}">

<div class="home-wrapper">
    <div class="par-avion-strip"></div>

    <nav class="top-nav-legacy">
        <a href="{{ route('gallery', ['china' => $isChina ? 1 : null]) }}" class="nav-link-legacy" style="background:rgba(230, 57, 70, 0.8);">Public Gallery</a>
        <a href="{{ route('login') }}" id="nav-admin" class="nav-link-legacy">Admin Login</a> 
    </nav>

    <header class="hero">
        <h1 id="hero-title">Hello from Indonesia!</h1>
        <p id="hero-desc">Menjelajahi dunia, satu kartu pos dalam satu waktu.</p>
        
        <div style="margin-top: 30px;">
            <a href="{{ route('gallery') }}" class="btn-gallery" id="btn-gallery-text">
                Lihat Koleksi Saya
            </a>
        </div>
    </header>

    <main class="section-legacy">
        <div class="card-info">
            <div class="stamp-decor"></div>

            <div class="about-postcrossing">
                <h2 id="title-what">Apa itu Postcrossing?</h2>
                <p id="desc-what">
                    <strong>Postcrossing</strong> adalah sebuah proyek daring yang memungkinkan orang-orang di seluruh dunia untuk saling berkirim dan menerima kartu pos nyata. Konsepnya sederhana: "Untuk setiap kartu pos yang Anda kirim, Anda akan menerima satu kartu pos kembali dari orang asing di belahan dunia lain."
                </p>
                <p id="desc-what-2">
                    Hobi ini bukan sekadar mengoleksi kertas bergambar, melainkan tentang koneksi manusia. Melalui kartu pos, kita bisa mempelajari budaya lain, melihat pemandangan indah dari negara jauh, dan merasakan kehangatan pesan tulisan tangan di tengah dunia yang serba digital.
                </p>
            </div>

            <div class="about-postcrossing">
                <h2 id="title-project">Tentang Proyek Ini</h2>
                <p id="desc-project">
                    Website ini adalah <strong>Postcard Tracker</strong> pribadi saya. Di sini, saya mendokumentasikan setiap lembar kenangan yang saya kirimkan ke berbagai penjuru dunia serta kartu-kartu cantik yang mampir ke kotak pos saya. Saya mencatat statistik jarak tempuh, durasi perjalanan, hingga biaya prangko sebagai bagian dari perjalanan hobi saya.
                </p>
            </div>

            <div class="offer-box">
                <h3 id="offer-title" style="font-family:'Special Elite'; font-size:1.5rem; margin-top:0;">Tertarik Bertukar Kartu Pos?</h3>
                <p id="offer-desc">
                    Saya sangat terbuka untuk melakukan <strong>Direct Swap</strong> (pertukaran langsung). Jika Anda ingin bertukar kartu pos bertema pemandangan Indonesia, budaya, atau apa pun dengan saya, jangan ragu untuk menghubungi!
                </p>
                <a href="mailto:{{ config('app.owner_contact_email') }}?subject=Postcrossing%20Swap%20Request" class="btn-contact" id="btn-contact">
                    <i class="bi bi-envelope-fill"></i> Hubungi Saya via Email
                </a>
            </div>
        </div>
    </main>

    <footer class="footer-legacy">
        <p id="footer-owner">Dikelola dengan <i class="bi bi-heart-fill text-danger"></i> oleh {{ config('app.owner_name') }}</p>
        <p id="footer-disclaimer" style="font-size: 11px; opacity: 0.7; margin-top:10px;">
            Situs ini adalah proyek hobi pribadi. Kami tidak mengumpulkan data pribadi pengunjung publik.<br>
            Postcrossing adalah merek dagang terdaftar. Situs ini tidak berafiliasi secara resmi dengan Postcrossing.com.
        </p>
    </footer>

    <div class="lang-switcher">
        <button class="lang-btn active" onclick="setLang('id')">ID</button>
        <button class="lang-btn" onclick="setLang('en')">EN</button>
        <button class="lang-btn" onclick="setLang('zh')">CN</button>
    </div>

    <script>
        const translations = {
            id: {
                navAdmin: "Admin Login",
                heroTitle: "Hello from Indonesia!",
                heroDesc: "Menjelajahi dunia, satu kartu pos dalam satu waktu.",
                btnGallery: "Lihat Koleksi Saya",
                titleWhat: "Apa itu Postcrossing?",
                descWhat: "<strong>Postcrossing</strong> adalah sebuah proyek daring yang memungkinkan orang-orang di seluruh dunia untuk saling berkirim dan menerima kartu pos nyata. Konsepnya sederhana: 'Untuk setiap kartu pos yang Anda kirim, Anda akan menerima satu kartu pos kembali dari orang asing di belahan dunia lain.'",
                descWhat2: "Hobi ini bukan sekadar mengoleksi kertas bergambar, melainkan tentang koneksi manusia. Melalui kartu pos, kita bisa mempelajari budaya lain, melihat pemandangan indah dari negara jauh, dan merasakan kehangatan pesan tulisan tangan di tengah dunia yang serba digital.",
                titleProject: "Tentang Proyek Ini",
                descProject: "Website ini adalah <strong>Postcard Tracker</strong> pribadi saya. Di sini, saya mendokumentasikan setiap lembar kenangan yang saya kirimkan ke berbagai penjuru dunia serta kartu-kartu cantik yang mampir ke kotak pos saya. Saya mencatat statistik jarak tempuh, durasi perjalanan, hingga biaya prangko.",
                offerTitle: "Tertarik Bertukar Kartu Pos?",
                offerDesc: "Saya sangat terbuka untuk melakukan <strong>Direct Swap</strong> (pertukaran langsung). Jika Anda ingin bertukar kartu pos bertema pemandangan Indonesia, budaya, atau apa pun dengan saya, jangan ragu untuk menghubungi!",
                btnContact: "<i class='bi bi-envelope-fill'></i> Hubungi Saya via Email",
                footerOwner: "Dikelola dengan <i class='bi bi-heart-fill text-danger'></i> oleh {{ config('app.owner_name') }}",
                footerDisclaimer: "Situs ini adalah proyek hobi pribadi. Kami tidak mengumpulkan data pribadi pengunjung publik.<br>Postcrossing adalah merek dagang terdaftar. Situs ini tidak berafiliasi secara resmi dengan Postcrossing.com."
            },
            en: {
                navAdmin: "Admin Login",
                heroTitle: "Hello from Indonesia!",
                heroDesc: "Exploring the world, one postcard at a time.",
                btnGallery: "View My Collection",
                titleWhat: "What is Postcrossing?",
                descWhat: "<strong>Postcrossing</strong> is an online project that allows people to send and receive real postcards from all over the world. The concept is simple: 'For every postcard you send, you will receive one back from a random stranger somewhere in the world.'",
                descWhat2: "This hobby is not just about collecting pieces of paper; it's about human connection. Through postcards, we learn about other cultures, see beautiful sights from far-off lands, and feel the warmth of a handwritten message in a digital world.",
                titleProject: "About This Project",
                descProject: "This website is my personal <strong>Postcard Tracker</strong>. Here, I document every memory I send abroad and every beautiful card that arrives in my mailbox. I keep track of travel distances, durations, and postage costs as part of my hobby journey.",
                offerTitle: "Interested in a Swap?",
                offerDesc: "I am very open to <strong>Direct Swaps</strong>. If you would like to exchange postcards featuring Indonesian landscapes, culture, or anything else, please don't hesitate to reach out!",
                btnContact: "<i class='bi bi-envelope-fill'></i> Contact Me via Email",
                footerOwner: "Managed with <i class='bi bi-heart-fill text-danger'></i> by {{ config('app.owner_name') }}",
                footerDisclaimer: "This is a private hobby project. We do not collect personal data from public visitors.<br>Postcrossing is a registered trademark. This site is not officially affiliated with Postcrossing.com."
            },
            zh: {
                navAdmin: "管理员登录",
                heroTitle: "来自印尼的问候！",
                heroDesc: "探索世界，一次一张明信片。",
                btnGallery: "查看我的收藏",
                titleWhat: "什么是 Postcrossing？",
                descWhat: "<strong>Postcrossing</strong> 是一个在线项目，允许世界各地的人们互相寄送和接收真实的明信片。核心理念很简单：'你每寄出一张明信片，就会收到一张来自世界某个角落的陌生人的明信片。'",
                descWhat2: "这个爱好不仅仅是收集纸片，更是关于人与人之间的联系。通过明信片，我们可以了解不同的文化，欣赏遥远国度的美景，并在数字化世界中感受手写信息的温度。",
                titleProject: "关于本网站",
                descProject: "这个网站是我的个人<strong>明信片追踪器</strong>。在这里，我记录了寄往世界各地的每一份回忆，以及寄到我信箱里的每一张精美卡片。我记录旅途距离、时长和邮资，作为我爱好之旅的一部分。",
                offerTitle: "有兴趣交换吗？",
                offerDesc: "我非常欢迎<strong>直接交换 (Direct Swap)</strong>。如果您想交换印尼风景、文化或任何主题的明信片，请随时与我联系！",
                btnContact: "<i class='bi bi-envelope-fill'></i> 通过电子邮件联系我",
                footerOwner: "由 {{ config('app.owner_name') }} 用 <i class='bi bi-heart-fill text-danger'></i> 管理",
                footerDisclaimer: "这是一个私人爱好项目。我们不收集公众访客的个人数据。<br>Postcrossing 是注册商标。本网站与 Postcrossing.com 官方无关。"
            }
        };

        function setLang(lang) {
            const heroTitle = document.getElementById('hero-title');
            
            // Update Text Content
            document.getElementById('nav-admin').innerText = translations[lang].navAdmin;
            heroTitle.innerText = translations[lang].heroTitle;
            document.getElementById('hero-desc').innerText = translations[lang].heroDesc;
            // No Icon as per legacy match
            document.getElementById('btn-gallery-text').innerText = translations[lang].btnGallery;
            document.getElementById('title-what').innerText = translations[lang].titleWhat;
            document.getElementById('desc-what').innerHTML = translations[lang].descWhat;
            document.getElementById('desc-what-2').innerText = translations[lang].descWhat2;
            document.getElementById('title-project').innerText = translations[lang].titleProject;
            document.getElementById('desc-project').innerHTML = translations[lang].descProject;
            document.getElementById('offer-title').innerText = translations[lang].offerTitle;
            document.getElementById('offer-desc').innerHTML = translations[lang].offerDesc;
            document.getElementById('btn-contact').innerHTML = translations[lang].btnContact;
            document.getElementById('footer-owner').innerHTML = translations[lang].footerOwner;
            document.getElementById('footer-disclaimer').innerHTML = translations[lang].footerDisclaimer;

            // Logika ganti font untuk Mandarin
            if (lang === 'zh') {
                heroTitle.classList.add('lang-zh');
            } else {
                heroTitle.classList.remove('lang-zh');
            }

            // Update Active Button
            document.querySelectorAll('.lang-btn').forEach(btn => {
                btn.classList.remove('active');
                if(btn.innerText.toLowerCase() === (lang === 'zh' ? 'cn' : lang)) {
                    btn.classList.add('active');
                }
            });
            
            localStorage.setItem('preferredLang', lang);
        }

        const savedLang = localStorage.getItem('preferredLang') || 'id';
        setTimeout(() => setLang(savedLang), 100);
    </script>
</div>
