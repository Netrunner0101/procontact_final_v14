<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Pro Contact')</title>

    <!-- Fonts — Manrope (headlines) + Inter (body) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Livewire Styles -->
    @livewireStyles
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen">
        <!-- Glass Navigation Bar -->
        <nav class="sticky top-0 z-50" style="background: rgba(255,255,255,0.70); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border-bottom: 1px solid rgba(197,200,185,0.10);">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{ mobileOpen: false }">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group">
                                <div class="w-9 h-9 rounded-lg flex items-center justify-center group-hover:scale-105 transition-transform duration-200" style="background: linear-gradient(135deg, #843728, #c4816e);">
                                    <i class="fas fa-address-book text-white text-sm"></i>
                                </div>
                                <span class="text-xl font-bold text-gradient" style="font-family: 'Manrope', sans-serif;">Pro Contact</span>
                            </a>
                        </div>

                        <!-- Navigation Links (Desktop) -->
                        <div class="hidden lg:flex lg:ml-10 lg:space-x-1">
                            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'nav-link-active' : '' }}">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>{{ __('Dashboard') }}</span>
                            </a>
                            <a href="{{ route('activites.index') }}" class="nav-link {{ request()->routeIs('activites.*') ? 'nav-link-active' : '' }}">
                                <i class="fas fa-briefcase"></i>
                                <span>{{ __('Activities') }}</span>
                            </a>
                            <a href="{{ route('contacts.index') }}" class="nav-link {{ request()->routeIs('contacts.*') ? 'nav-link-active' : '' }}">
                                <i class="fas fa-users"></i>
                                <span>{{ __('Contacts') }}</span>
                            </a>
                            <a href="{{ route('rendez-vous.index') }}" class="nav-link {{ request()->routeIs('rendez-vous.*') ? 'nav-link-active' : '' }}">
                                <i class="fas fa-calendar-alt"></i>
                                <span>{{ __('Appointments') }}</span>
                            </a>
                            <a href="{{ route('notes.index') }}" class="nav-link {{ request()->routeIs('notes.*') ? 'nav-link-active' : '' }}">
                                <i class="fas fa-sticky-note"></i>
                                <span>{{ __('Notes') }}</span>
                            </a>
                            <a href="{{ route('rappels.index') }}" class="nav-link {{ request()->routeIs('rappels.*') ? 'nav-link-active' : '' }}">
                                <i class="fas fa-bell"></i>
                                <span>{{ __('Reminders') }}</span>
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
                                    <button type="button" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200" id="user-menu-button" onclick="toggleUserMenu()" style="background: #f5f3f0; color: #1b1c1a;">
                                        <div class="w-7 h-7 rounded-full flex items-center justify-center mr-2" style="background: linear-gradient(135deg, #843728, #c4816e);">
                                            <i class="fas fa-user text-white text-xs"></i>
                                        </div>
                                        <span>{{ Auth::user()->prenom }} {{ Auth::user()->nom }}</span>
                                        <svg class="ml-2 -mr-0.5 h-4 w-4 opacity-60" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>

                                    <div id="user-menu" class="hidden absolute right-0 mt-2 w-56 rounded-xl py-1 z-50" style="background: #ffffff; box-shadow: 0 20px 40px rgba(27,28,26,0.05);">
                                        <div class="px-4 py-3" style="background: #e9e6e3; border-radius: 0.75rem 0.75rem 0 0;">
                                            <div class="font-semibold" style="color: #1b1c1a;">{{ Auth::user()->prenom }} {{ Auth::user()->nom }}</div>
                                            <div class="text-sm mt-0.5" style="color: #44483e;">{{ Auth::user()->email }}</div>
                                            @if(Auth::user()->last_login_at)
                                                <div class="text-xs mt-1" style="color: #75786c;">
                                                    {{ __('Last login:') }} {{ Auth::user()->last_login_at->diffForHumans() }}
                                                </div>
                                            @endif
                                        </div>
                                        <a href="{{ route('profile.show') }}" class="flex items-center px-4 py-2.5 text-sm transition-colors" style="color: #44483e;" onmouseover="this.style.background='#ffdbd1';this.style.color='#341100'" onmouseout="this.style.background='';this.style.color='#44483e'">
                                            <i class="fas fa-user mr-3" style="color: #75786c;"></i>
                                            {{ __('My Profile') }}
                                        </a>
                                        <form method="POST" action="{{ route('logout') }}" class="block">
                                            @csrf
                                            <button type="submit" class="flex items-center w-full text-left px-4 py-2.5 text-sm transition-colors" style="color: #44483e;" onmouseover="this.style.background='#ffdad6';this.style.color='#410002'" onmouseout="this.style.background='';this.style.color='#44483e'">
                                                <i class="fas fa-sign-out-alt mr-3" style="color: #75786c;"></i>
                                                {{ __('Log out') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @else
                                <a href="{{ route('login') }}" style="color: #44483e;" class="hover:opacity-80 transition-colors">
                                    {{ __('Log in') }}
                                </a>
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
                        <a href="{{ route('dashboard') }}" class="mobile-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt w-5 text-center"></i>
                            <span>{{ __('Dashboard') }}</span>
                        </a>
                        <a href="{{ route('activites.index') }}" class="mobile-nav-link {{ request()->routeIs('activites.*') ? 'active' : '' }}">
                            <i class="fas fa-briefcase w-5 text-center"></i>
                            <span>{{ __('Activities') }}</span>
                        </a>
                        <a href="{{ route('contacts.index') }}" class="mobile-nav-link {{ request()->routeIs('contacts.*') ? 'active' : '' }}">
                            <i class="fas fa-users w-5 text-center"></i>
                            <span>{{ __('Contacts') }}</span>
                        </a>
                        <a href="{{ route('rendez-vous.index') }}" class="mobile-nav-link {{ request()->routeIs('rendez-vous.*') ? 'active' : '' }}">
                            <i class="fas fa-calendar-alt w-5 text-center"></i>
                            <span>{{ __('Appointments') }}</span>
                        </a>
                        <a href="{{ route('notes.index') }}" class="mobile-nav-link {{ request()->routeIs('notes.*') ? 'active' : '' }}">
                            <i class="fas fa-sticky-note w-5 text-center"></i>
                            <span>{{ __('Notes') }}</span>
                        </a>
                        <a href="{{ route('rappels.index') }}" class="mobile-nav-link {{ request()->routeIs('rappels.*') ? 'active' : '' }}">
                            <i class="fas fa-bell w-5 text-center"></i>
                            <span>{{ __('Reminders') }}</span>
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
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center" style="background: linear-gradient(135deg, #843728, #c4816e);">
                                        <i class="fas fa-user text-white text-xs"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold" style="color: #1b1c1a;">{{ Auth::user()->prenom }} {{ Auth::user()->nom }}</div>
                                        <div class="text-xs" style="color: #75786c;">{{ Auth::user()->email }}</div>
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('profile.show') }}" class="mobile-nav-link">
                                <i class="fas fa-user-cog w-5 text-center"></i>
                                <span>{{ __('My Profile') }}</span>
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="mobile-nav-link w-full" style="color: #ba1a1a;">
                                    <i class="fas fa-sign-out-alt w-5 text-center"></i>
                                    <span>{{ __('Log out') }}</span>
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="mobile-nav-link">
                                <i class="fas fa-sign-in-alt w-5 text-center"></i>
                                <span>{{ __('Log in') }}</span>
                            </a>
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
        /* Navigation Link Styles — Glass Nav */
        .nav-link {
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

        .nav-link:hover {
            color: #1b1c1a;
            background-color: rgba(245, 243, 240, 0.8);
        }

        .nav-link-active {
            color: #843728 !important;
            background-color: rgba(255, 219, 209, 0.3);
        }

        .nav-link-active::after {
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

        /* Mobile Navigation Drawer Links */
        .mobile-nav-link {
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

        .mobile-nav-link:hover,
        .mobile-nav-link.active {
            color: #843728;
            background: rgba(255, 219, 209, 0.3);
        }

        [x-cloak] { display: none !important; }

        /* Enhanced Animations */
        .fade-in-up {
            animation: fadeInUp 0.3s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Loading Spinner — Terracotta */
        .spinner {
            border: 2px solid #e9e6e3;
            border-top: 2px solid #843728;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>

    <script>
        function toggleUserMenu() {
            const menu = document.getElementById('user-menu');
            menu.classList.toggle('hidden');
            if (!menu.classList.contains('hidden')) {
                menu.classList.add('fade-in-up');
            }
        }

        document.addEventListener('click', function(event) {
            const menu = document.getElementById('user-menu');
            const button = document.getElementById('user-menu-button');
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

        function showNotification(message, type = 'success') {
            const colors = {
                success: 'background: #c0f0b8; color: #002204;',
                error: 'background: #ffdad6; color: #410002;',
                info: 'background: #f5dfd0; color: #281810;'
            };
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 z-50 px-4 py-3 rounded-xl shadow-lg max-w-sm fade-in-up text-sm font-medium';
            notification.style.cssText = colors[type] || colors.info;
            notification.innerHTML = `
                <div class="flex items-center justify-between gap-3">
                    <span>${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="opacity-60 hover:opacity-100">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            document.body.appendChild(notification);
            setTimeout(() => { if (notification.parentElement) notification.remove(); }, 5000);
        }
    </script>

    @livewireScripts
</body>
</html>
