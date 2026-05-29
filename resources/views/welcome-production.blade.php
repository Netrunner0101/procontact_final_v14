<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Pro Contact - Professional contact management') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased" style="background: var(--surface); color: var(--on-surface);">

    <div class="min-h-screen flex flex-col">

        <!-- Top bar: brand + language switcher -->
        <header class="w-full">
            <div class="max-w-5xl mx-auto px-6 h-20 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center text-sm font-bold" style="background: var(--primary); color: var(--on-primary);">P</span>
                    <span class="text-xl font-bold tracking-tight" style="color: var(--on-surface);">{{ __('ProContact CRM') }}</span>
                </div>

                <!-- Language Switcher -->
                <div class="flex gap-1 items-center">
                    <a href="{{ route('lang.switch', 'en') }}" class="px-2.5 py-1 text-sm rounded-md {{ app()->getLocale() === 'en' ? 'bg-white font-bold shadow-sm' : 'hover:opacity-80' }} transition-colors" style="color: var(--on-surface);">EN</a>
                    <span style="color: var(--outline-variant);">|</span>
                    <a href="{{ route('lang.switch', 'fr') }}" class="px-2.5 py-1 text-sm rounded-md {{ app()->getLocale() === 'fr' ? 'bg-white font-bold shadow-sm' : 'hover:opacity-80' }} transition-colors" style="color: var(--on-surface);">FR</a>
                </div>
            </div>
        </header>

        <!-- Center: welcome title + login button -->
        <main class="flex-1 flex flex-col items-center justify-center text-center px-6">
            <h1 class="text-4xl sm:text-5xl font-bold tracking-tight mb-8" style="color: var(--on-surface);">
                {{ __('Welcome to ProContact') }}
            </h1>

            <a href="{{ route('login') }}" class="px-10 py-3 rounded-full text-base btn-primary font-semibold shadow-md">
                {{ __('Login') }}
            </a>
        </main>

    </div>

</body>
</html>
