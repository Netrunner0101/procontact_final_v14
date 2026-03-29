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
        <div class="max-w-7xl mx-auto px-4 sm:px-8 h-20 flex items-center justify-between">
            <!-- EDITABLE: Brand Name & Logo -->
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-lg flex items-center justify-center text-sm font-bold" style="background: var(--primary); color: var(--on-primary);">P</span>
                <span class="text-xl font-bold tracking-tight" style="color: var(--on-surface);">{{ __('ProContact CRM') }}</span>
            </div>

            <!-- EDITABLE: Desktop Nav Links -->
            <div class="hidden md:flex items-center gap-8">
                <a href="#features" class="text-sm font-semibold transition-colors" style="color: var(--primary); border-bottom: 2px solid var(--primary); padding-bottom: 2px;">{{ __('Product') }}</a>
                <a href="#personas" class="text-sm font-medium transition-colors hover:opacity-80" style="color: var(--on-surface-variant);">{{ __('Solutions') }}</a>
                <a href="#pricing" class="text-sm font-medium transition-colors hover:opacity-80" style="color: var(--on-surface-variant);">{{ __('Pricing') }}</a>
                <a href="#founder-story" class="text-sm font-medium transition-colors hover:opacity-80" style="color: var(--on-surface-variant);">{{ __('Company') }}</a>
            </div>

            <!-- Desktop Right Side -->
            <div class="hidden md:flex items-center gap-4">
                <!-- EDITABLE: Language Switcher -->
                <div class="flex gap-1">
                    <a href="{{ route('lang.switch', 'en') }}" class="px-2.5 py-1 text-sm rounded-md {{ app()->getLocale() === 'en' ? 'bg-white font-bold shadow-sm' : 'hover:opacity-80' }} transition-colors" style="color: var(--on-surface);">EN</a>
                    <span style="color: var(--outline-variant);">|</span>
                    <a href="{{ route('lang.switch', 'fr') }}" class="px-2.5 py-1 text-sm rounded-md {{ app()->getLocale() === 'fr' ? 'bg-white font-bold shadow-sm' : 'hover:opacity-80' }} transition-colors" style="color: var(--on-surface);">FR</a>
                </div>
                <!-- EDITABLE: Nav Action Buttons -->
                <a href="{{ route('login') }}" class="text-sm font-medium transition-colors hover:opacity-80" style="color: var(--on-surface-variant);">{{ __('Login') }}</a>
                <a href="{{ route('register') }}" class="px-6 py-2.5 rounded-full text-sm btn-primary font-semibold shadow-md">{{ __('Get Started') }}</a>
            </div>

            <!-- Mobile Hamburger -->
            <button @click="mobileOpen = !mobileOpen" class="md:hidden p-2 rounded-lg" style="color: var(--outline);">
                <span class="material-symbols-outlined" x-show="!mobileOpen">menu</span>
                <span class="material-symbols-outlined" x-show="mobileOpen" x-cloak>close</span>
            </button>
        </div>

        <!-- Mobile Dropdown -->
        <div class="md:hidden px-4 pb-4"
             x-show="mobileOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             x-cloak
             @click.away="mobileOpen = false"
             style="background: var(--surface-container-lowest); border-radius: var(--radius-xl); margin: 0 0.5rem; box-shadow: var(--shadow-lg);">
            <!-- EDITABLE: Mobile Nav Links -->
            <div class="flex flex-col gap-1 py-3 px-2">
                <a href="#features" class="px-4 py-2.5 rounded-lg text-sm font-semibold" style="color: var(--primary);" @click="mobileOpen = false">{{ __('Product') }}</a>
                <a href="#personas" class="px-4 py-2.5 rounded-lg text-sm font-medium" style="color: var(--on-surface-variant);" @click="mobileOpen = false">{{ __('Solutions') }}</a>
                <a href="#pricing" class="px-4 py-2.5 rounded-lg text-sm font-medium" style="color: var(--on-surface-variant);" @click="mobileOpen = false">{{ __('Pricing') }}</a>
                <a href="#founder-story" class="px-4 py-2.5 rounded-lg text-sm font-medium" style="color: var(--on-surface-variant);" @click="mobileOpen = false">{{ __('Company') }}</a>
            </div>
            <div style="border-top: 1px solid var(--outline-variant); opacity: 0.3;" class="mx-4"></div>
            <div class="flex gap-2 px-4 py-3">
                <a href="{{ route('lang.switch', 'en') }}" class="px-3 py-1 text-sm rounded-lg {{ app()->getLocale() === 'en' ? 'bg-white font-bold shadow-sm' : '' }} transition-colors" style="color: var(--on-surface);">EN</a>
                <a href="{{ route('lang.switch', 'fr') }}" class="px-3 py-1 text-sm rounded-lg {{ app()->getLocale() === 'fr' ? 'bg-white font-bold shadow-sm' : '' }} transition-colors" style="color: var(--on-surface);">FR</a>
            </div>
            <div class="flex flex-col gap-2 px-4 pb-3">
                <a href="{{ route('login') }}" class="w-full text-center px-5 py-2.5 rounded-full text-sm btn-secondary">{{ __('Login') }}</a>
                <a href="{{ route('register') }}" class="w-full text-center px-5 py-2.5 rounded-full text-sm btn-primary">{{ __('Get Started') }}</a>
            </div>
        </div>
    </nav>

    <!-- ============================================
         EDITABLE: Hero Section
         Change: badge text, headline, French subtitle, description, CTA buttons
         ============================================ -->
    <section class="pt-28 pb-20 lg:pb-28 px-6" style="background: var(--surface);">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">
            <!-- Left Column: Text Content -->
            <div class="lg:col-span-7">
                <!-- EDITABLE: Top Badge -->
                <span class="inline-block py-1.5 px-4 rounded-full text-xs font-bold uppercase tracking-wider mb-6" style="background: var(--primary-container); color: var(--on-primary-container);">
                    {{ __('The client tool for independent professionals') }}
                </span>

                <!-- EDITABLE: Main Headline -->
                <h2 class="text-4xl sm:text-5xl lg:text-7xl font-extrabold leading-[1.1] tracking-tight mb-4" style="font-family: 'Manrope', sans-serif; color: var(--on-surface);">
                    {{ __('All your clients.') }}<br>
                    <span style="color: var(--primary);">{{ __('One place. Zero chaos.') }}</span>
                </h2>

                <!-- EDITABLE: French Subtitle (shown below headline) -->
                <p class="text-2xl sm:text-3xl font-medium mb-8 opacity-60" style="font-family: 'Manrope', sans-serif; color: var(--on-surface-variant);">
                    {{ __('Tous vos clients. Un seul endroit. Zéro chaos.') }}
                </p>

                <!-- EDITABLE: Description -->
                <p class="text-lg lg:text-xl mb-10 max-w-xl leading-relaxed" style="color: var(--on-surface-variant);">
                    {{ __('ProContact is the Belgian-made CRM that actually gets used. Built for solo-preneurs who value time over complex spreadsheets.') }}
                </p>

                <!-- EDITABLE: CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('register') }}" class="px-8 py-4 rounded-full text-lg font-bold btn-primary shadow-lg flex items-center justify-center gap-2">
                        {{ __('Try ProContact free for 14 days') }}
                        <span class="material-symbols-outlined">arrow_forward</span>
                    </a>
                    <a href="#how-it-works" class="px-8 py-4 rounded-full text-lg font-bold btn-secondary flex items-center justify-center" style="border: 2px solid var(--outline-variant);">
                        {{ __('See how it works') }}
                    </a>
                </div>
            </div>

            <!-- Right Column: App Preview -->
            <div class="lg:col-span-5 relative">
                <div class="relative z-10 rounded-3xl overflow-hidden shadow-2xl" style="border: 8px solid rgba(27,28,26,0.05);">
                    <!-- EDITABLE: Hero Image (replace src with your app screenshot) -->
                    <div class="w-full aspect-[9/16] flex items-center justify-center" style="background: linear-gradient(135deg, var(--surface-container-low), var(--surface-container-high));">
                        <div class="text-center px-8">
                            <span class="material-symbols-outlined text-6xl mb-4" style="color: var(--primary); opacity: 0.5;">smartphone</span>
                            <p class="text-sm font-medium" style="color: var(--on-surface-variant);">{{ __('App Preview') }}</p>
                        </div>
                    </div>

                    <!-- EDITABLE: Floating Contact Card Overlay -->
                    <div class="absolute bottom-12 left-6 right-6 p-4 rounded-2xl flex items-center gap-4" style="background: rgba(255,255,255,0.90); backdrop-filter: blur(12px); box-shadow: var(--shadow-xl); border: 1px solid rgba(197,200,185,0.2);">
                        <!-- EDITABLE: Contact initials, name, label -->
                        <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-sm" style="background: var(--tertiary-container); color: var(--on-tertiary-container);">TJ</div>
                        <div>
                            <div class="font-bold text-sm" style="color: var(--on-surface);">Thomas Janssen</div>
                            <div class="text-xs uppercase tracking-tight" style="color: var(--on-surface-variant);">{{ __('Last Contact: Yesterday') }}</div>
                        </div>
                        <div class="ml-auto">
                            <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase" style="background: var(--secondary-container); color: var(--on-secondary-container);">{{ __('Prospect') }}</span>
                        </div>
                    </div>
                </div>
                <!-- Decorative background glow -->
                <div class="absolute -top-12 -right-12 w-64 h-64 rounded-full blur-3xl -z-10" style="background: rgba(132,55,40,0.05);"></div>
            </div>
        </div>
    </section>

    <!-- ============================================
         EDITABLE: Trust Strip
         Change: trust badges (icon, text)
         ============================================ -->
    <div class="py-8" style="background: var(--surface-container-low); border-top: 1px solid rgba(197,200,185,0.1); border-bottom: 1px solid rgba(197,200,185,0.1);">
        <div class="max-w-7xl mx-auto px-8 flex flex-wrap justify-center md:justify-between items-center gap-8 opacity-70 hover:opacity-100 transition-all duration-500">
            <!-- EDITABLE: Trust Badge 1 -->
            <div class="flex items-center gap-3 text-sm font-semibold" style="color: var(--on-surface-variant);">
                <span class="text-2xl">🇧🇪</span> {{ __('Made in Belgium') }}
            </div>
            <!-- EDITABLE: Trust Badge 2 -->
            <div class="flex items-center gap-3 text-sm font-semibold" style="color: var(--on-surface-variant);">
                <span class="material-symbols-outlined" style="color: var(--primary);">lock</span> {{ __('GDPR compliant') }}
            </div>
            <!-- EDITABLE: Trust Badge 3 -->
            <div class="flex items-center gap-3 text-sm font-semibold" style="color: var(--on-surface-variant);">
                <span class="material-symbols-outlined" style="color: var(--tertiary);">star</span> {{ __('Join 50+ beta users') }}
            </div>
            <!-- EDITABLE: Trust Badge 4 -->
            <div class="flex items-center gap-3 text-sm font-semibold" style="color: var(--on-surface-variant);">
                <span class="material-symbols-outlined" style="color: var(--primary);">verified</span> {{ __('ISO 27001 Ready') }}
            </div>
        </div>
    </div>

    <!-- ============================================
         EDITABLE: Problem Statement
         Change: section title, pain point cards (icon, title, description)
         ============================================ -->
    <section class="py-24 px-6" style="background: var(--surface);">
        <div class="max-w-7xl mx-auto text-center mb-16">
            <!-- EDITABLE: Section Heading -->
            <h2 class="text-4xl lg:text-5xl font-bold mb-4 tracking-tight" style="font-family: 'Manrope', sans-serif;">{{ __('Sound familiar?') }}</h2>
            <div class="h-1.5 w-24 mx-auto rounded-full" style="background: var(--primary);"></div>
        </div>
        <div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- EDITABLE: Pain Point Card 1 -->
            <div class="p-10 rounded-2xl transition-all hover:translate-y-[-2px]" style="background: var(--surface-container-low);">
                <span class="material-symbols-outlined text-4xl mb-6" style="color: var(--primary);">search_off</span>
                <h3 class="text-2xl font-bold mb-3">{{ __('Scattered everywhere') }}</h3>
                <p class="leading-relaxed" style="color: var(--on-surface-variant);">{{ __('Names in WhatsApp, emails in Gmail, and notes on sticky papers. Finding info takes longer than the job itself.') }}</p>
            </div>
            <!-- EDITABLE: Pain Point Card 2 -->
            <div class="p-10 rounded-2xl transition-all hover:translate-y-[-2px]" style="background: var(--surface-container-low);">
                <span class="material-symbols-outlined text-4xl mb-6" style="color: var(--primary);">notifications_off</span>
                <h3 class="text-2xl font-bold mb-3">{{ __('Forgotten follow-ups') }}</h3>
                <p class="leading-relaxed" style="color: var(--on-surface-variant);">{{ __('"I\'ll call them Monday" becomes Friday, then next month. Potential revenue slips through the cracks daily.') }}</p>
            </div>
            <!-- EDITABLE: Pain Point Card 3 -->
            <div class="p-10 rounded-2xl transition-all hover:translate-y-[-2px]" style="background: var(--surface-container-low);">
                <span class="material-symbols-outlined text-4xl mb-6" style="color: var(--primary);">history_edu</span>
                <h3 class="text-2xl font-bold mb-3">{{ __('Admin eating your evenings') }}</h3>
                <p class="leading-relaxed" style="color: var(--on-surface-variant);">{{ __('Spending 2 hours after dinner just to organize who needs what. Your weekend shouldn\'t be for data entry.') }}</p>
            </div>
            <!-- EDITABLE: Pain Point Card 4 -->
            <div class="p-10 rounded-2xl transition-all hover:translate-y-[-2px]" style="background: var(--surface-container-low);">
                <span class="material-symbols-outlined text-4xl mb-6" style="color: var(--primary);">payments</span>
                <h3 class="text-2xl font-bold mb-3">{{ __('No visibility on payments') }}</h3>
                <p class="leading-relaxed" style="color: var(--on-surface-variant);">{{ __('Unsure which clients still owe you money without checking three different apps and your bank statement.') }}</p>
            </div>
        </div>
    </section>

    <!-- ============================================
         EDITABLE: How It Works
         Change: section title, subtitle, step numbers, step titles, step descriptions
         ============================================ -->
    <section id="how-it-works" class="py-24 px-6 relative overflow-hidden" style="background: var(--surface-container-low);">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-20">
                <!-- EDITABLE: Section Heading -->
                <h2 class="text-4xl font-bold tracking-tight mb-4" style="font-family: 'Manrope', sans-serif;">{{ __('How it works') }}</h2>
                <p style="color: var(--on-surface-variant);" class="max-w-2xl mx-auto">{{ __('Three simple steps to regain your mental space.') }}</p>
            </div>
            <div class="relative">
                <!-- Progress line (desktop only) -->
                <div class="hidden lg:block absolute top-1/2 left-0 w-full h-0.5 -translate-y-1/2 -z-0" style="background: rgba(197,200,185,0.3);"></div>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 relative z-10">
                    <!-- EDITABLE: Step 1 -->
                    <div class="p-8 rounded-3xl flex flex-col items-center text-center" style="background: var(--surface); border: 1px solid rgba(197,200,185,0.1); box-shadow: var(--shadow-sm);">
                        <div class="w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold mb-6" style="background: var(--primary); color: var(--on-primary);">1</div>
                        <h3 class="text-xl font-bold mb-4">{{ __('Add contacts') }}</h3>
                        <p style="color: var(--on-surface-variant);">{{ __('Import from phone or email in one click. ProContact auto-organizes them by business type.') }}</p>
                    </div>
                    <!-- EDITABLE: Step 2 -->
                    <div class="p-8 rounded-3xl flex flex-col items-center text-center" style="background: var(--surface); border: 1px solid rgba(197,200,185,0.1); box-shadow: var(--shadow-sm);">
                        <div class="w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold mb-6" style="background: var(--primary); color: var(--on-primary);">2</div>
                        <h3 class="text-xl font-bold mb-4">{{ __('Track interactions') }}</h3>
                        <p style="color: var(--on-surface-variant);">{{ __('Log calls, notes, and milestones. Every conversation history available at your fingertips.') }}</p>
                    </div>
                    <!-- EDITABLE: Step 3 -->
                    <div class="p-8 rounded-3xl flex flex-col items-center text-center" style="background: var(--surface); border: 1px solid rgba(197,200,185,0.1); box-shadow: var(--shadow-sm);">
                        <div class="w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold mb-6" style="background: var(--primary); color: var(--on-primary);">3</div>
                        <h3 class="text-xl font-bold mb-4">{{ __('Never miss a follow-up') }}</h3>
                        <p style="color: var(--on-surface-variant);">{{ __('Set smart reminders. Get a gentle nudge when a high-value client hasn\'t heard from you.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================
         EDITABLE: Features Section
         Change: section title, feature cards (icon, title, description)
         ============================================ -->
    <section id="features" class="py-20 px-6">
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
