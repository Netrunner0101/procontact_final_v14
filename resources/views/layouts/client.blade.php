<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Pro Contact') }} - {{ __('Client Area') }}</title>

    <!-- Fonts — Manrope + Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased" style="background: #fbf9f6; min-height: 100vh;">
    <div class="min-h-screen">
        <!-- Glass Navigation -->
        <nav class="sticky top-0 z-50" style="background: rgba(255,255,255,0.70); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border-bottom: 1px solid rgba(197,200,185,0.10);">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{ mobileOpen: false }">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #843728, #c4816e);">
                                    <i class="fas fa-user-circle text-white text-sm"></i>
                                </div>
                                <div>
                                    <h1 class="text-xl font-bold text-gradient" style="font-family: 'Manrope', sans-serif;">Pro Contact</h1>
                                    <span class="text-xs font-medium" style="color: #843728;">{{ __('Client Area') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Links (Desktop) -->
                        <div class="hidden lg:flex lg:ml-10 lg:space-x-1">
                            <a href="{{ route('client.dashboard') }}" class="client-nav-link {{ request()->routeIs('client.dashboard') ? 'client-nav-link-active' : '' }}">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>{{ __('Dashboard') }}</span>
                            </a>
                            <a href="{{ route('client.appointments') }}" class="client-nav-link {{ request()->routeIs('client.appointments*') ? 'client-nav-link-active' : '' }}">
                                <i class="fas fa-calendar-check"></i>
                                <span>{{ __('My appointments') }}</span>
                            </a>
                        </div>
                    </div>

                    <!-- Language Switcher & Settings Dropdown (Desktop) -->
                    <div class="hidden lg:flex lg:items-center lg:ml-6">
                        <!-- Language Switcher -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center gap-1 px-3 py-2 text-sm font-medium rounded-lg hover:bg-white/10 transition-colors">
                                <span>{{ app()->getLocale() === 'fr' ? 'FR' : 'EN' }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-32 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                                <a href="{{ route('lang.switch', 'en') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ app()->getLocale() === 'en' ? 'font-bold' : '' }}">English</a>
                                <a href="{{ route('lang.switch', 'fr') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ app()->getLocale() === 'fr' ? 'font-bold' : '' }}">Français</a>
                            </div>
                        </div>

                        <div class="ml-3 relative">
                            @auth
                                <div class="relative inline-block text-left">
                                    <button type="button" class="inline-flex items-center px-3 py-2 text-sm leading-4 font-medium rounded-lg transition ease-in-out duration-150" id="client-user-menu-button" onclick="toggleClientUserMenu()" style="background: #f5f3f0; color: #1b1c1a;">
                                        <span>{{ Auth::user()->prenom }} {{ Auth::user()->nom }}</span>
                                        <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>

                                    <div id="client-user-menu" class="hidden absolute right-0 mt-2 w-48 rounded-xl py-1 z-50" style="background: #ffffff; box-shadow: 0 20px 40px rgba(27,28,26,0.05);">
                                        <div class="px-4 py-2 text-sm" style="background: #e9e6e3; border-radius: 0.75rem 0.75rem 0 0;">
                                            <div class="font-medium" style="color: #1b1c1a;">{{ Auth::user()->prenom }} {{ Auth::user()->nom }}</div>
                                            <div style="color: #44483e;">{{ Auth::user()->email }}</div>
                                            <div class="text-xs mt-1" style="color: #843728;">{{ __('Client Area') }}</div>
                                        </div>
                                        <form method="POST" action="{{ route('logout') }}" class="block">
                                            @csrf
                                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm transition-colors" style="color: #44483e;" onmouseover="this.style.background='#ffdad6';this.style.color='#410002'" onmouseout="this.style.background='';this.style.color='#44483e'">
                                                {{ __('Log out') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endauth
                        </div>
                    </div>

                    <!-- Mobile Hamburger Button -->
                    <div class="flex items-center lg:hidden">
                        <button @click="mobileOpen = !mobileOpen" class="inline-flex items-center justify-center p-2 rounded-lg transition-colors duration-200" style="color: #75786c;" onmouseover="this.style.background='rgba(245,243,240,0.8)';this.style.color='#1b1c1a'" onmouseout="this.style.background='';this.style.color='#75786c'">
                            <i class="fas fa-bars text-lg" x-show="!mobileOpen"></i>
                            <i class="fas fa-times text-lg" x-show="mobileOpen" x-cloak></i>
                        </button>
                    </div>
                </div>

                <!-- Mobile Navigation Drawer -->
                <div class="lg:hidden"
                     x-show="mobileOpen"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-2"
                     x-cloak
                     @click.away="mobileOpen = false">
                    <div class="pb-4 space-y-1">
                        <!-- Navigation Links -->
                        <a href="{{ route('client.dashboard') }}" class="client-mobile-nav-link {{ request()->routeIs('client.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt w-5 text-center"></i>
                            <span>{{ __('Dashboard') }}</span>
                        </a>
                        <a href="{{ route('client.appointments') }}" class="client-mobile-nav-link {{ request()->routeIs('client.appointments*') ? 'active' : '' }}">
                            <i class="fas fa-calendar-check w-5 text-center"></i>
                            <span>{{ __('My appointments') }}</span>
                        </a>

                        <!-- Divider -->
                        <div class="my-2" style="border-top: 1px solid rgba(197,200,185,0.2);"></div>

                        <!-- Language Switcher -->
                        <div class="flex items-center gap-2 px-3 py-2">
                            <i class="fas fa-globe w-5 text-center" style="color: #75786c;"></i>
                            <a href="{{ route('lang.switch', 'en') }}" class="px-3 py-1 text-sm rounded-lg transition-colors {{ app()->getLocale() === 'en' ? 'font-bold' : '' }}" style="color: {{ app()->getLocale() === 'en' ? '#843728' : '#75786c' }}; {{ app()->getLocale() === 'en' ? 'background: rgba(255,219,209,0.3);' : '' }}">EN</a>
                            <a href="{{ route('lang.switch', 'fr') }}" class="px-3 py-1 text-sm rounded-lg transition-colors {{ app()->getLocale() === 'fr' ? 'font-bold' : '' }}" style="color: {{ app()->getLocale() === 'fr' ? '#843728' : '#75786c' }}; {{ app()->getLocale() === 'fr' ? 'background: rgba(255,219,209,0.3);' : '' }}">FR</a>
                        </div>

                        <!-- Divider -->
                        <div class="my-2" style="border-top: 1px solid rgba(197,200,185,0.2);"></div>

                        <!-- User Section -->
                        @auth
                            <div class="px-3 py-2">
                                <div class="text-sm font-semibold" style="color: #1b1c1a;">{{ Auth::user()->prenom }} {{ Auth::user()->nom }}</div>
                                <div class="text-xs" style="color: #75786c;">{{ Auth::user()->email }}</div>
                            </div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="client-mobile-nav-link w-full" style="color: #ba1a1a;">
                                    <i class="fas fa-sign-out-alt w-5 text-center"></i>
                                    <span>{{ __('Log out') }}</span>
                                </button>
                            </form>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main>
            @if (session('success'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
                    <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium" style="background: #c0f0b8; color: #002204;">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
                    <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium" style="background: #ffdad6; color: #410002;">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <style>
        /* Client Navigation — Warm, glass */
        .client-nav-link {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: #75786c;
            text-decoration: none;
            transition: all 0.25s ease-in-out;
            position: relative;
        }

        .client-nav-link:hover {
            color: #1b1c1a;
            background-color: rgba(245, 243, 240, 0.8);
        }

        .client-nav-link-active {
            color: #843728;
            background-color: rgba(255, 219, 209, 0.3);
            font-weight: 600;
        }

        .client-nav-link-active::after {
            content: '';
            position: absolute;
            bottom: -0.5rem;
            left: 50%;
            transform: translateX(-50%);
            width: 4px;
            height: 4px;
            background-color: #843728;
            border-radius: 50%;
        }

        /* Client Card — Tonal, no borders */
        .client-card {
            background: #ffffff;
            border-radius: 0.75rem;
            box-shadow: 0 2px 8px rgba(27, 28, 26, 0.03);
            border: none;
            transition: all 0.25s ease-in-out;
        }

        .client-card:hover {
            box-shadow: 0 12px 24px rgba(27, 28, 26, 0.04);
            transform: translateY(-2px);
        }

        /* Client Badge */
        .client-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 500;
            border-radius: 9999px;
            gap: 0.25rem;
            border: none;
        }

        .client-badge-primary {
            background-color: #ffdbd1;
            color: #341100;
        }

        .client-badge-success {
            background-color: #c0f0b8;
            color: #002204;
        }

        .client-badge-warning {
            background-color: #ffdfa0;
            color: #2d2000;
        }

        /* Client Button — Terracotta */
        .client-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            font-weight: 600;
            border-radius: 0.375rem;
            border: none;
            cursor: pointer;
            transition: all 0.25s ease-in-out;
            text-decoration: none;
            gap: 0.5rem;
        }

        .client-btn-primary {
            background: linear-gradient(135deg, #843728, #c4816e);
            color: white;
            box-shadow: 0 2px 8px rgba(132, 55, 40, 0.25);
        }

        .client-btn-primary:hover {
            background: linear-gradient(135deg, #6d2a1d, #843728);
            box-shadow: 0 4px 16px rgba(132, 55, 40, 0.35);
            transform: translateY(-1px);
        }

        /* Mobile Navigation Drawer Links */
        .client-mobile-nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.9375rem;
            font-weight: 500;
            color: #75786c;
            text-decoration: none;
            transition: all 0.2s ease;
            width: 100%;
            border: none;
            background: none;
            cursor: pointer;
            text-align: left;
        }

        .client-mobile-nav-link:hover,
        .client-mobile-nav-link.active {
            color: #843728;
            background: rgba(255, 219, 209, 0.3);
        }

        [x-cloak] { display: none !important; }
    </style>

    <script>
        function toggleClientUserMenu() {
            const menu = document.getElementById('client-user-menu');
            menu.classList.toggle('hidden');
            if (!menu.classList.contains('hidden')) {
                menu.classList.add('fade-in-up');
            }
        }

        document.addEventListener('click', function(event) {
            const menu = document.getElementById('client-user-menu');
            const button = document.getElementById('client-user-menu-button');
            if (menu && button && !button.contains(event.target) && !menu.contains(event.target)) {
                menu.classList.add('hidden');
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function() {
                    const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        const originalText = submitBtn.textContent || submitBtn.value;
                        submitBtn.innerHTML = '<div class="spinner inline-block mr-2"></div>{{ __("Loading...") }}';
                        setTimeout(() => {
                            submitBtn.disabled = false;
                            submitBtn.textContent = originalText;
                        }, 5000);
                    }
                });
            });

            const main = document.querySelector('main');
            if (main) {
                main.classList.add('fade-in-up');
            }
        });

        function showClientNotification(message, type = 'info') {
            const colors = {
                success: 'background: #c0f0b8; color: #002204;',
                error: 'background: #ffdad6; color: #410002;',
                info: 'background: #f5dfd0; color: #281810;'
            };
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 z-50 p-4 rounded-xl max-w-sm fade-in-up text-sm font-medium';
            notification.style.cssText = (colors[type] || colors.info) + ' box-shadow: 0 20px 40px rgba(27,28,26,0.05);';
            notification.innerHTML = `
                <div class="flex items-center justify-between">
                    <span>${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4 opacity-60 hover:opacity-100">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            document.body.appendChild(notification);
            setTimeout(() => {
                if (notification.parentElement) notification.remove();
            }, 5000);
        }
    </script>

    @include('partials.idle-timeout-modal')
</body>
</html>
