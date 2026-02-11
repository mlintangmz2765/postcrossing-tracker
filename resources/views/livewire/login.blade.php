<div>
    <style>
        .font-special { font-family: 'Special Elite', 'Courier New', monospace; }
        .font-hand { font-family: 'Dancing Script', cursive; }
        .font-body { font-family: 'Quicksand', sans-serif; }

        .login-bg-pattern {
            background-color: #fdf6e3;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23d4c5a9' fill-opacity='0.2'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .airmail-strip {
             height: 10px;
             width: 100%;
             background: repeating-linear-gradient(
                45deg,
                #e63946, #e63946 20px,
                #ffffff 20px, #ffffff 30px,
                #457b9d 30px, #457b9d 50px,
                #ffffff 50px, #ffffff 60px
            );
        }

        .disclaimer-box {
            background: #fff8e1;
            border-left: 4px solid #eec55d;
            padding: 15px;
            font-size: 0.9rem;
            color: #5d4037;
        }

        .input-group { position: relative; margin-bottom: 25px; }
        .input-underline {
            display: block;
            width: 100%;
            border: none;
            border-bottom: 2px solid #ccc;
            background: transparent;
            padding: 10px 5px;
            font-family: 'Special Elite', monospace;
            font-size: 1.1rem;
            transition: all 0.3s;
            border-radius: 0;
        }
        .input-underline:focus {
            outline: none;
            border-bottom-color: #e63946;
            background: rgba(255,255,255,0.5);
        }
        
        .btn-stamp {
            background-color: #2c3e50; 
            color: white;
            font-family: 'Special Elite', monospace; 
            text-transform: uppercase;
            letter-spacing: 2px;
            border: 2px solid #2c3e50;
            transition: all 0.2s;
            position: relative;
            overflow: hidden;
        }
        .btn-stamp:hover {
            background-color: #34495e;
            box-shadow: 0 4px 6px rgba(0,0,0,0.2);
            transform: translateY(-2px);
        }
        .btn-stamp:active {
            transform: translateY(1px);
            box-shadow: none;
        }

        @media (max-width: 640px) {
            .g-recaptcha { transform: scale(0.85); transform-origin: 50% 0; }
        }
    </style>
    
    <style>
        .postcard-container {
            display: flex;
            flex-direction: column;
            width: 100%;
            max-width: 850px;
            margin: 0 auto;
            background: #fff;
            border-radius: 4px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            overflow: hidden;
            position: relative;
            border: 1px solid #ddd;
        }

        .postcard-left {
            width: 100%;
            padding: 30px;
            background-color: #f9f9f9;
            border-bottom: 2px dashed #ddd;
            text-align: center;
        }

        .postcard-right {
            width: 100%;
            padding: 30px;
            background-color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        @media (min-width: 768px) {
            .postcard-container {
                flex-direction: row;
                min-height: 450px;
            }
            .postcard-left {
                width: 50%;
                border-bottom: none;
                border-right: 2px dashed #ddd;
                text-align: left;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }
            .postcard-right {
                width: 50%;
            }
        }

        .btn-login-custom {
            display: block !important;
            width: 100%;
            padding: 12px;
            background-color: #2c3e50;
            color: white;
            font-family: 'Special Elite', monospace;
            text-transform: uppercase;
            letter-spacing: 1px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-align: center;
            margin-top: 20px;
            transition: background 0.3s;
        }
        .btn-login-custom:hover {
            background-color: #34495e;
        }
        .btn-login-custom:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }
    </style>

    <div class="login-bg-pattern min-h-screen flex items-center justify-center p-4">
        <div class="postcard-container">
            
            <div class="airmail-strip absolute top-0 left-0 z-10"></div>
            <div class="airmail-strip absolute bottom-0 left-0 z-10"></div>

            <div class="absolute top-6 right-6 z-20 hidden md:block" style="transform: rotate(5deg);">
                 <div style="display: inline-flex; align-items: center; gap: 8px; border: 2px solid #457b9d; padding: 4px 12px; background: rgba(255,255,255,0.95);">
                    <svg width="24" height="24" viewBox="0 0 24 24">
                        <path d="M21,16L21,14L13,9L13,3.5C13,2.67 12.33,2 11.5,2C10.67,2 10,2.67 10,3.5L10,9L2,14L2,16L10,13.5L10,19L8,20.5L8,22L11.5,21L15,22L15,20.5L13,19L13,13.5L21,16Z" fill="#457b9d" />
                    </svg>
                    <div style="font-family: 'Special Elite', monospace; color: #457b9d; line-height: 1;">
                        <span style="font-size: 1rem; font-weight: bold; display: block;">PAR AVION</span>
                    </div>
                </div>
            </div>

            <div class="postcard-left relative">
                <h2 class="text-3xl text-gray-800 mb-2 font-hand font-bold">Manager Access</h2>
                <p class="subtitle text-gray-500 text-sm mb-6 font-special">Authorized Personnel Only</p>

                <div class="disclaimer-box mb-6 font-body text-left">
                    <strong><i class="bi bi-info-circle-fill"></i> IMPORTANT NOTICE:</strong><br>
                    This portal is for the administration of the personal postcard archive.
                    It is <u>NOT</u> the official Postcrossing.com website.
                    Please do not use your official Postcrossing credentials.
                </div>

                <a href="{{ route('home') }}" class="mt-auto md:mt-0 no-underline text-gray-500 text-sm hover:text-red-500 transition-colors font-special">
                    <i class="bi bi-arrow-left"></i> Return to Home
                </a>
            </div>

            <div class="postcard-right relative z-10">
                
                @if($error)
                    <div class="error-msg text-center w-full mb-6 relative z-10 bg-red-50 border border-red-200 text-red-700 p-3 rounded font-body">
                        {{ $error }}
                    </div>
                @endif

                <form wire:submit.prevent="authenticate" class="w-full relative z-10">
                    <div class="mb-5 text-left">
                        <label class="block mb-2 text-gray-700 font-special uppercase tracking-wider text-xs">Username</label>
                        <input type="text" wire:model="username" 
                               class="w-full p-3 border border-gray-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition font-body bg-gray-50 focus:bg-white" 
                               placeholder="" required>
                    </div>

                    <div class="mb-6 text-left">
                        <label class="block mb-2 text-gray-700 font-special uppercase tracking-wider text-xs">Password</label>
                        <input type="password" wire:model="password" 
                               class="w-full p-3 border border-gray-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition font-body bg-gray-50 focus:bg-white" 
                               placeholder="" required>
                    </div>

                    <div class="flex justify-center mb-4">
                        <div wire:ignore>
                            <div class="g-recaptcha" 
                                 data-sitekey="{{ config('app.recaptcha_site_key') }}" 
                                 data-callback="onRecaptchaSuccess"
                                 data-expired-callback="onRecaptchaExpired">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-login-custom" id="loginBtn" @if(!$recaptchaToken) disabled @endif wire:loading.attr="disabled">
                        <i class="bi bi-box-arrow-in-right"></i> Sign In
                    </button>
                    
                    <div class="text-center mt-6 text-gray-400 text-xs font-special border-t pt-4 w-full">
                        &copy; {{ date('Y') }} Postcard Tracker
                    </div>
                </form>
            </div>
        </div>
    </div>

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
