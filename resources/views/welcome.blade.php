<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Pro Contact - Professional contact management') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; background: #fbf9f6; color: #1b1c1a; }
        h1, h2, h3 { font-family: 'Manrope', sans-serif; }
        .btn-primary { background: linear-gradient(135deg, #843728, #c4816e); color: #ffffff; font-weight: 600; letter-spacing: 0.02em; transition: all 0.25s; }
        .btn-primary:hover { background: linear-gradient(135deg, #6d2a1d, #843728); box-shadow: 0 4px 16px rgba(132,55,40,0.35); transform: translateY(-1px); }
        .btn-secondary { background: #ffffff; border: 1px solid rgba(197,200,185,0.4); color: #1b1c1a; font-weight: 600; transition: all 0.25s; }
        .btn-secondary:hover { background: #f5f3f0; box-shadow: 0 2px 8px rgba(27,28,26,0.05); }
        .feature-card { background: #ffffff; border-radius: 0.75rem; box-shadow: 0 4px 20px rgba(27,28,26,0.04); transition: all 0.3s; }
        .feature-card:hover { box-shadow: 0 8px 30px rgba(27,28,26,0.08); transform: translateY(-2px); }
        .gradient-text { background: linear-gradient(135deg, #843728, #c4816e); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="fixed top-0 w-full z-50" style="background: rgba(251,249,246,0.9); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(197,200,185,0.2);">
        <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
            <h1 class="text-2xl font-bold" style="color: #1b1c1a;">Pro Contact</h1>
            <div class="flex items-center gap-3">
                <div class="flex gap-2">
                    <a href="{{ route('lang.switch', 'en') }}" class="px-3 py-1 text-sm rounded-lg {{ app()->getLocale() === 'en' ? 'bg-white text-gray-900 font-bold shadow-sm' : 'text-gray-500 hover:text-gray-900' }} transition-colors">EN</a>
                    <a href="{{ route('lang.switch', 'fr') }}" class="px-3 py-1 text-sm rounded-lg {{ app()->getLocale() === 'fr' ? 'bg-white text-gray-900 font-bold shadow-sm' : 'text-gray-500 hover:text-gray-900' }} transition-colors">FR</a>
                </div>
            </div>
            {{-- Waitlist mode: auth buttons hidden temporarily
            <div class="flex items-center gap-3">
                <a href="{{ route('login') }}" class="px-5 py-2 rounded-md text-sm btn-secondary">{{ __('Login') }}</a>
                <a href="{{ route('register') }}" class="px-5 py-2 rounded-md text-sm btn-primary">{{ __('Sign Up') }}</a>
            </div>
            --}}
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-32 pb-20 px-6">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-5xl md:text-6xl font-extrabold leading-tight mb-6" style="color: #1b1c1a;">
                {{ __('Manage your contacts') }}<br><span class="gradient-text">{{ __('professionally') }}</span> {{ __('efficiently') }}
            </h2>
            <p class="text-lg md:text-xl max-w-2xl mx-auto mb-10" style="color: #44483e;">
                {{ __('Pro Contact simplifies the management of your contacts, appointments and activities. Everything you need, in one place.') }}
            </p>
            <div class="max-w-xl mx-auto">
                <iframe data-tally-src="https://tally.so/embed/2EL9Je?alignLeft=1&hideTitle=1&transparentBackground=1&dynamicHeight=1" loading="lazy" width="100%" height="722" frameborder="0" marginheight="0" marginwidth="0" title="Pro Contact"></iframe>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 px-6">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold mb-4" style="color: #1b1c1a;">{{ __('Everything to manage your relationships') }}</h2>
                <p class="text-lg max-w-xl mx-auto" style="color: #44483e;">{{ __('Powerful tools to organize and track your professional contacts.') }}</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="feature-card p-8">
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center mb-5" style="background: rgba(132,55,40,0.08);">
                        <svg class="w-6 h-6" fill="none" stroke="#843728" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3" style="color: #1b1c1a;">{{ __('Contact Management') }}</h3>
                    <p style="color: #44483e;">{{ __('Centralize all your contacts with their information, history and notes in an intuitive interface.') }}</p>
                </div>
                <!-- Feature 2 -->
                <div class="feature-card p-8">
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center mb-5" style="background: rgba(132,55,40,0.08);">
                        <svg class="w-6 h-6" fill="none" stroke="#843728" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3" style="color: #1b1c1a;">{{ __('Appointments') }}</h3>
                    <p style="color: #44483e;">{{ __('Schedule and track your appointments. Receive reminders and share details with your clients.') }}</p>
                </div>
                <!-- Feature 3 -->
                <div class="feature-card p-8">
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center mb-5" style="background: rgba(132,55,40,0.08);">
                        <svg class="w-6 h-6" fill="none" stroke="#843728" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3" style="color: #1b1c1a;">{{ __('Statistics') }}</h3>
                    <p style="color: #44483e;">{{ __('Visualize your performance with dashboards and detailed reports on your activities.') }}</p>
                </div>
            </div>
        </div>
    </section>


    <script>var d=document,w="https://tally.so/widgets/embed.js",v=function(){"undefined"!=typeof Tally?Tally.loadEmbeds():d.querySelectorAll("iframe[data-tally-src]:not([src])").forEach((function(e){e.src=e.dataset.tallySrc}))};if("undefined"!=typeof Tally)v();else if(d.querySelector('script[src="'+w+'"]')==null){var s=d.createElement("script");s.src=w,s.onload=v,s.onerror=v,d.body.appendChild(s);}</script>

    <!-- Footer -->
    <footer class="py-8 px-6" style="border-top: 1px solid rgba(197,200,185,0.2);">
        <div class="max-w-6xl mx-auto text-center">
            <p class="text-sm" style="color: #75786c;">&copy; {{ date('Y') }} Pro Contact. {{ __('All rights reserved.') }}</p>
        </div>
    </footer>
</body>
</html>
