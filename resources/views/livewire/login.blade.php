
<div>
    <style>
        @font-face {
            font-family: 'Special Elite';
            src: url('{{ asset('fonts/special-elite.woff2') }}') format('woff2');
            font-weight: 400;
            font-style: normal;
        }
        @font-face {
            font-family: 'Quicksand';
            src: url('{{ asset('fonts/quicksand.woff2') }}') format('woff2');
            font-weight: 400 700;
        }
        
        .login-wrapper {
            background-color: #fdf6e3;
            background-image: 
                linear-gradient(rgba(0,0,0,0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0,0,0,0.02) 1px, transparent 1px);
            background-size: 20px 20px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Quicksand', sans-serif;
            padding: 20px;
        }

        .postcard-container {
            width: 100%;
            max-width: 850px;
            background: #fff;
            padding: 40px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
            border-radius: 4px;
            position: relative;
            border: 1px solid #e0e0e0;
            display: flex;
            gap: 40px;
            overflow: hidden;
            flex-wrap: wrap;
        }

        /* Airmail Strip Border effect */
        .postcard-container::before {
             content: "";
             position: absolute;
             top: 0; left: 0; right: 0; height: 8px;
             background: repeating-linear-gradient(
                45deg,
                #e63946, #e63946 15px,
                #fff 15px, #fff 25px,
                #457b9d 25px, #457b9d 40px,
                #fff 40px, #fff 50px
            );
        }

        .postcard-container::after {
             content: "";
             position: absolute;
             bottom: 0; left: 0; right: 0; height: 8px;
             background: repeating-linear-gradient(
                -45deg,
                #e63946, #e63946 15px,
                #fff 15px, #fff 25px,
                #457b9d 25px, #457b9d 40px,
                #fff 40px, #fff 50px
            );
        }

        .left-panel {
            flex: 1;
            min-width: 300px;
            border-right: 2px dashed #ddd;
            padding-right: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .right-panel {
            flex: 1;
            min-width: 300px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        h2 {
            font-family: 'Special Elite', monospace;
            font-size: 2rem;
            color: #2c3e50;
            margin-bottom: 5px;
            border-bottom: 2px solid #e63946;
            display: inline-block;
            padding-bottom: 5px;
        }

        .subtitle {
            font-family: 'Special Elite', monospace;
            color: #7f8c8d;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }

        /* Stamp Decoration */
        .stamp-box {
            width: 100px;
            height: 120px;
            background: #fff;
            border: 3px solid #fdf6e3;
            background-image: radial-gradient(#fdf6e3 30%, transparent 30%);
            background-size: 10px 10px;
            background-position: 5px 5px;
            box-shadow: 2px 2px 5px rgba(0,0,0,0.1);
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }
        .stamp-box img { width: 80%; height: auto; opacity: 0.8; }

        .disclaimer-box {
            background: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
            padding: 15px;
            border-radius: 4px;
            font-size: 0.8rem;
            margin-bottom: 20px;
            font-family: 'Quicksand', sans-serif;
            line-height: 1.4;
        }

        form input {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 2px solid #ddd;
            border-radius: 4px;
            font-family: 'Special Elite', monospace;
            font-size: 1rem;
            transition: 0.3s;
            background: #fafafa;
        }

        form input:focus {
            outline: none;
            border-color: #457b9d;
            background: #fff;
            box-shadow: 2px 2px 0px rgba(69, 123, 157, 0.2);
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: #2c3e50;
            color: white;
            border: none;
            border-radius: 4px;
            font-family: 'Special Elite', monospace;
            font-size: 1.1rem;
            cursor: pointer;
            transition: 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-login:disabled {
            background: #95a5a6;
            cursor: not-allowed;
        }

        .btn-login:hover:not(:disabled) {
            background: #e63946;
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(0,0,0,0.1);
        }

        .error-msg {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 0.9rem;
            border: 1px solid #f5c6cb;
        }

        /* Postmark effect */
        .postmark {
            position: absolute;
            top: 20%;
            right: 15%;
            width: 150px;
            opacity: 0.1;
            transform: rotate(-15deg);
            pointer-events: none;
        }

        /* Responsive */
        @media (max-width: 800px) {
            .postcard-container { flex-direction: column; gap: 20px; }
            .left-panel { border-right: none; border-bottom: 2px dashed #ddd; padding-right: 0; padding-bottom: 20px; }
            .stamp-box { display: none; }
        }
    </style>
    
    <div class="login-wrapper">
        <div class="postcard-container">
            <!-- Stamp Decoration on Top Right -->
            <div class="stamp-box">
                <i class="bi bi-person-fill" style="font-size: 3rem; color: #ccc;"></i>
            </div>
            
            <img src="{{ asset('images/postmark.png') }}" class="postmark" onerror="this.style.display='none'">

            <div class="left-panel">
                <h2>Manager Access</h2>
                <p class="subtitle">Authorized Access Only</p>

                <div class="disclaimer-box">
                    <strong><i class="bi bi-exclamation-triangle-fill"></i> IMPORTANT NOTICE:</strong><br>
                    This is a private personal website. It is <u>NOT</u> the official Postcrossing.com website. 
                    Do not enter your official Postcrossing account credentials here.
                </div>

                <a href="{{ route('home') }}" class="text-decoration-none text-muted" style="font-size: 0.85rem; font-family:'Special Elite';">
                    <i class="bi bi-arrow-left"></i> Back to Public Page
                </a>
            </div>

            <div class="right-panel">
                @if($error)
                    <div class="error-msg text-center w-100">
                        {{ $error }}
                    </div>
                @endif

                <form wire:submit.prevent="authenticate" class="w-100">
                    <div class="mb-3">
                        <label class="d-none">Username</label>
                        <input type="text" wire:model="username" placeholder="Username / Access ID" required>
                    </div>

                    <div class="mb-3">
                        <label class="d-none">Password</label>
                        <input type="password" wire:model="password" placeholder="Secret Key" required>
                    </div>

                    <div class="d-flex justify-content-center mb-3">
                    <div wire:ignore>
                            <div class="g-recaptcha" 
                                 data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}" 
                                 data-callback="onRecaptchaSuccess"
                                 data-expired-callback="onRecaptchaExpired">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-login" id="loginBtn">
                        <i class="bi bi-stamp"></i> Sign In
                    </button>
                    
                    <div class="text-center mt-3 text-muted" style="font-size: 0.7rem;">
                        &copy; {{ date('Y') }} Postcard Tracker Project
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ReCaptcha Script -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script>
        function onRecaptchaSuccess(token) {
            @this.set('recaptchaToken', token);
            document.getElementById('loginBtn').disabled = false;
        }
        function onRecaptchaExpired() {
            @this.set('recaptchaToken', null);
            document.getElementById('loginBtn').disabled = true;
        }
    </script>
</div>
