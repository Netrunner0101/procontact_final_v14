<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Pro Contact - Professional contact management') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="font-sans antialiased" style="background: var(--surface); color: var(--on-surface);">

    <!-- ============================================
         EDITABLE: Navigation
         Change: brand name, nav links, CTA button text
         ============================================ -->
    <nav class="fixed top-0 w-full z-50" x-data="{ mobileOpen: false }">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <!-- EDITABLE: Brand Name -->
            <h1 class="text-2xl font-bold" style="color: var(--on-surface);">Pro Contact</h1>

            <!-- Desktop Nav -->
            <div class="hidden sm:flex items-center gap-3">
                <!-- EDITABLE: Language Switcher -->
                <div class="flex gap-2">
                    <a href="{{ route('lang.switch', 'en') }}" class="px-3 py-1 text-sm rounded-lg {{ app()->getLocale() === 'en' ? 'bg-white font-bold shadow-sm' : 'hover:opacity-80' }} transition-colors" style="color: var(--on-surface);">EN</a>
                    <a href="{{ route('lang.switch', 'fr') }}" class="px-3 py-1 text-sm rounded-lg {{ app()->getLocale() === 'fr' ? 'bg-white font-bold shadow-sm' : 'hover:opacity-80' }} transition-colors" style="color: var(--on-surface);">FR</a>
                </div>
                <!-- EDITABLE: Nav Buttons -->
                <a href="{{ route('login') }}" class="px-5 py-2 rounded-md text-sm btn-secondary">{{ __('Login') }}</a>
                <a href="{{ route('register') }}" class="px-5 py-2 rounded-md text-sm btn-primary">{{ __('Sign Up') }}</a>
            </div>

            <!-- Mobile Hamburger -->
            <button @click="mobileOpen = !mobileOpen" class="sm:hidden p-2 rounded-lg" style="color: var(--outline);">
                <span class="material-symbols-outlined" x-show="!mobileOpen">menu</span>
                <span class="material-symbols-outlined" x-show="mobileOpen" x-cloak>close</span>
            </button>
        </div>

        <!-- Mobile Dropdown -->
        <div class="sm:hidden px-4 pb-4"
             x-show="mobileOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             x-cloak
             @click.away="mobileOpen = false">
            <div class="flex gap-2 mb-3">
                <a href="{{ route('lang.switch', 'en') }}" class="px-3 py-1 text-sm rounded-lg {{ app()->getLocale() === 'en' ? 'bg-white font-bold shadow-sm' : '' }} transition-colors" style="color: var(--on-surface);">EN</a>
                <a href="{{ route('lang.switch', 'fr') }}" class="px-3 py-1 text-sm rounded-lg {{ app()->getLocale() === 'fr' ? 'bg-white font-bold shadow-sm' : '' }} transition-colors" style="color: var(--on-surface);">FR</a>
            </div>
            <div class="flex flex-col gap-2">
                <a href="{{ route('login') }}" class="w-full text-center px-5 py-2.5 rounded-md text-sm btn-secondary">{{ __('Login') }}</a>
                <a href="{{ route('register') }}" class="w-full text-center px-5 py-2.5 rounded-md text-sm btn-primary">{{ __('Sign Up') }}</a>
            </div>
        </div>
    </nav>

    <!-- ============================================
         EDITABLE: Hero Section
         Change: headline, subheadline, description, CTA buttons
         ============================================ -->
    <section class="pt-32 pb-20 px-6">
        <div class="max-w-4xl mx-auto text-center">
            <!-- EDITABLE: Main Headline -->
            <h2 class="text-5xl md:text-6xl font-extrabold leading-tight mb-6" style="font-family: 'Manrope', sans-serif; color: var(--on-surface);">
                {{ __('Manage your contacts') }}<br><span class="text-gradient">{{ __('professionally') }}</span> {{ __('efficiently') }}
            </h2>
            <!-- EDITABLE: Subheadline / Description -->
            <p class="text-lg md:text-xl max-w-2xl mx-auto mb-10" style="color: var(--on-surface-variant);">
                {{ __('Pro Contact simplifies the management of your contacts, appointments and activities. Everything you need, in one place.') }}
            </p>
            <!-- EDITABLE: CTA Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('register') }}" class="px-8 py-3 rounded-md text-base btn-primary">
                    {{ __('Get started for free') }}
                </a>
                <a href="{{ route('login') }}" class="px-8 py-3 rounded-md text-base btn-secondary">
                    {{ __('Log in') }}
                </a>
            </div>
        </div>
    </section>

    <!-- ============================================
         EDITABLE: Features Section
         Change: section title, feature cards (icon, title, description)
         ============================================ -->
    <section class="py-20 px-6">
        <div class="max-w-6xl mx-auto">
            <!-- EDITABLE: Section Heading -->
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold mb-4" style="color: var(--on-surface);">{{ __('Everything to manage your relationships') }}</h2>
                <p class="text-lg max-w-xl mx-auto" style="color: var(--on-surface-variant);">{{ __('Powerful tools to organize and track your professional contacts.') }}</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <!-- EDITABLE: Feature Card 1 -->
                <div class="p-8 rounded-xl transition-all hover:-translate-y-1" style="background: var(--surface-container-lowest); box-shadow: var(--shadow-md);">
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center mb-5" style="background: rgba(132,55,40,0.08);">
                        <span class="material-symbols-outlined" style="color: var(--primary);">group</span>
                    </div>
                    <h3 class="text-xl font-bold mb-3" style="color: var(--on-surface);">{{ __('Contact Management') }}</h3>
                    <p style="color: var(--on-surface-variant);">{{ __('Centralize all your contacts with their information, history and notes in an intuitive interface.') }}</p>
                </div>
                <!-- EDITABLE: Feature Card 2 -->
                <div class="p-8 rounded-xl transition-all hover:-translate-y-1" style="background: var(--surface-container-lowest); box-shadow: var(--shadow-md);">
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center mb-5" style="background: rgba(132,55,40,0.08);">
                        <span class="material-symbols-outlined" style="color: var(--primary);">calendar_month</span>
                    </div>
                    <h3 class="text-xl font-bold mb-3" style="color: var(--on-surface);">{{ __('Appointments') }}</h3>
                    <p style="color: var(--on-surface-variant);">{{ __('Schedule and track your appointments. Receive reminders and share details with your clients.') }}</p>
                </div>
                <!-- EDITABLE: Feature Card 3 -->
                <div class="p-8 rounded-xl transition-all hover:-translate-y-1" style="background: var(--surface-container-lowest); box-shadow: var(--shadow-md);">
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center mb-5" style="background: rgba(132,55,40,0.08);">
                        <span class="material-symbols-outlined" style="color: var(--primary);">bar_chart</span>
                    </div>
                    <h3 class="text-xl font-bold mb-3" style="color: var(--on-surface);">{{ __('Statistics') }}</h3>
                    <p style="color: var(--on-surface-variant);">{{ __('Visualize your performance with dashboards and detailed reports on your activities.') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================
         EDITABLE: CTA Section
         Change: heading, description, button text
         ============================================ -->
    <section class="py-20 px-6">
        <div class="max-w-3xl mx-auto text-center rounded-2xl p-12" style="background: var(--surface-container-lowest); box-shadow: var(--shadow-xl);">
            <!-- EDITABLE: CTA Heading -->
            <h2 class="text-3xl md:text-4xl font-bold mb-4" style="color: var(--on-surface);">{{ __('Ready to get started?') }}</h2>
            <!-- EDITABLE: CTA Description -->
            <p class="text-lg mb-8" style="color: var(--on-surface-variant);">{{ __('Create your account and start managing your professional contacts today.') }}</p>
            <!-- EDITABLE: CTA Button -->
            <a href="{{ route('register') }}" class="inline-block px-8 py-3 rounded-md text-base btn-primary">
                {{ __('Create my account') }}
            </a>
        </div>
    </section>

    <!-- ============================================
         EDITABLE: Footer
         Change: copyright text, links
         ============================================ -->
    <footer class="py-8 px-6" style="border-top: 1px solid rgba(197,200,185,0.2);">
        <div class="max-w-6xl mx-auto text-center">
            <p class="text-sm" style="color: var(--outline);">&copy; {{ date('Y') }} Pro Contact. {{ __('All rights reserved.') }}</p>
        </div>
    </footer>
</body>
</html>
