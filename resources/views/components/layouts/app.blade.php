<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ $title ?? 'Postcard Tracker - My Postcrossing Journey' }}</title>
        
        <!-- Local Assets Only -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles

        <style>
            /* Global Font Imports if not already loaded by app.css */

            
            body { font-family: 'Quicksand', sans-serif; }
            h1, h2, h3, h4, h5, h6, .vintage-font { font-family: 'Special Elite', monospace; }

            /* Vintage Nav Styles */
            .vintage-nav {
                background-color: #2c3e50; /* Dark Vintage Blue */
                color: white;
                position: relative;
                box-shadow: 0 4px 10px rgba(0,0,0,0.2);
                z-index: 50;
            }
            
            .vintage-nav::after {
                content: "";
                display: block;
                height: 6px;
                width: 100%;
                background: repeating-linear-gradient(
                    45deg,
                    #e63946, #e63946 10px,
                    #fff 10px, #fff 20px,
                    #457b9d 20px, #457b9d 30px,
                    #fff 30px, #fff 40px
                );
            }

            .nav-container {
                max-width: 1280px;
                margin: 0 auto;
                padding: 0 20px;
                height: 70px;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            .nav-brand {
                font-family: 'Special Elite', monospace;
                font-size: 1.5rem;
                color: #fff;
                text-decoration: none;
                display: flex;
                align-items: center;
                gap: 10px;
                text-shadow: 2px 2px 0px rgba(0,0,0,0.3);
            }
            
            .nav-brand:hover { color: #f1f1f1; }

            .nav-links {
                display: flex;
                gap: 5px;
            }

            .nav-item {
                font-family: 'Special Elite', monospace;
                color: #ecf0f1;
                text-decoration: none;
                padding: 8px 15px;
                border-radius: 4px;
                font-size: 0.9rem;
                transition: all 0.3s;
                border: 1px solid transparent;
            }

            .nav-item:hover, .nav-item.active {
                background: rgba(255, 255, 255, 0.1);
                border-color: rgba(255, 255, 255, 0.3);
                color: #fff;
                transform: translateY(-2px);
            }
             
            .nav-item.active {
                background: #e63946; /* Accent Red */
                border-color: #e63946;
                box-shadow: 2px 2px 5px rgba(0,0,0,0.3);
            }

            .btn-new-entry {
                background: #f1c40f; /* Vintage Yellow */
                color: #2c3e50;
                font-weight: bold;
                border: 2px dashed #2c3e50;
            }
            
            .btn-new-entry:hover {
                background: #f39c12;
                color: #fff;
                border-color: #fff;
            }
            
            .user-menu {
                display: flex;
                align-items: center;
                gap: 10px;
                font-size: 0.8rem;
                color: #bdc3c7;
            }
        </style>
    </head>
    <body class="bg-gray-50 antialiased">
        <div class="min-h-screen relative">
            
            <!-- Navbar (Hide on public pages & login) -->
            @if(request()->routeIs('home') || request()->routeIs('gallery') || request()->routeIs('receive.confirm') || request()->routeIs('login'))
                <!-- Pages with their own nav or no nav -->
            @else
                <nav class="vintage-nav">
                    <div class="nav-container">
                        <div class="flex items-center">
                            <a href="{{ route('dashboard') }}" class="nav-brand">
                                <img src="{{ asset('logo.png') }}" alt="Postcrossing" style="height: 40px; vertical-align: middle;">
                            </a>
                        </div>
                        
                        <div class="nav-links hidden sm:flex">
                            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                            <a href="{{ route('postcard.gallery') }}" class="nav-item {{ request()->routeIs('postcard.gallery') ? 'active' : '' }}">
                                <i class="bi bi-images"></i> Postcard Gallery
                            </a>
                            <a href="{{ route('stamps') }}" class="nav-item {{ request()->routeIs('stamps') ? 'active' : '' }}">
                                <i class="bi bi-envelope-paper"></i> Stamps
                            </a>
                            <a href="{{ route('postcard.import') }}" class="nav-item {{ request()->routeIs('postcard.import') ? 'active' : '' }}">
                                <i class="bi bi-box-arrow-in-down"></i> Import
                            </a>
                            <a href="{{ route('stats') }}" class="nav-item {{ request()->routeIs('stats') ? 'active' : '' }}">
                                <i class="bi bi-bar-chart-fill"></i> Stats
                            </a>
                            <!-- Future Routes -->
                            <a href="{{ route('gallery') }}" class="nav-item {{ request()->routeIs('gallery') ? 'active' : '' }}" target="_blank">
                                <i class="bi bi-globe"></i> Public Gallery
                            </a>
                        </div>

                        <div class="flex items-center gap-4">
                             <a href="{{ route('postcard.register') }}" class="nav-item btn-new-entry">
                                + New Entry
                            </a>
                            
                            @auth
                                <div class="user-menu">
                                    <span class="hidden md:inline">{{ Auth::user()->username }}</span>
                                    <!-- Logout Form -->
                                    <form method="POST" action="{{ route('logout') }}" x-data>
                                        @csrf
                                        <a href="{{ route('logout') }}" @click.prevent="$root.submit();" class="nav-item" style="padding: 5px 10px; font-size: 0.8rem; background:rgba(0,0,0,0.3);">
                                            <i class="bi bi-box-arrow-right"></i>
                                        </a>
                                    </form>
                                </div>
                            @endauth
                        </div>
                    </div>
                </nav>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        @livewireScripts
        <!-- Local Bootstrap Icons -->
        <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.css') }}">
    </body>
</html>
