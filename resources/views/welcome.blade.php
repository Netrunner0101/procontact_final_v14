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
         EDITABLE: Features Bento Grid
         Change: section title, feature cards (icon, title, description)
         Grid layout: first card spans 2 cols, others are 1 col, security card spans 2 cols
         ============================================ -->
    <section id="features" class="py-24 px-6" style="background: var(--surface);">
        <div class="max-w-7xl mx-auto">
            <!-- EDITABLE: Section Heading -->
            <h2 class="text-4xl font-bold mb-12 tracking-tight text-center lg:text-left" style="font-family: 'Manrope', sans-serif;">{{ __('Engineered for simplicity') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">

                <!-- EDITABLE: Feature - Works Anywhere (large card, spans 2 cols) -->
                <div class="md:col-span-2 lg:col-span-2 p-8 rounded-3xl flex flex-col justify-between" style="background: var(--surface-container-low);">
                    <div>
                        <span class="material-symbols-outlined text-4xl mb-6" style="color: var(--primary);">devices</span>
                        <h3 class="text-2xl font-bold mb-4">{{ __('Works anywhere') }}</h3>
                        <p style="color: var(--on-surface-variant);">{{ __('Your office is everywhere. Access your data on mobile while on-site, or on desktop while doing invoicing.') }}</p>
                    </div>
                    <div class="mt-8 rounded-xl h-48 flex items-center justify-center" style="background: var(--surface-container);">
                        <span class="material-symbols-outlined text-6xl" style="color: var(--outline-variant);">devices</span>
                    </div>
                </div>

                <!-- EDITABLE: Feature - Every Client Detail (accent card) -->
                <div class="p-8 rounded-3xl" style="background: var(--primary); color: var(--on-primary); box-shadow: 0 12px 24px rgba(132,55,40,0.1);">
                    <span class="material-symbols-outlined text-4xl mb-6">contact_page</span>
                    <h3 class="text-xl font-bold mb-4">{{ __('Every client detail') }}</h3>
                    <p class="opacity-80">{{ __('Custom fields for VAT numbers, preferred coffee, or kids\' names. Personal service matters.') }}</p>
                </div>

                <!-- EDITABLE: Feature - Follow-ups on Time -->
                <div class="p-8 rounded-3xl" style="background: var(--surface-container-low);">
                    <span class="material-symbols-outlined text-4xl mb-6" style="color: var(--primary);">alarm_on</span>
                    <h3 class="text-xl font-bold mb-4">{{ __('Follow-ups on time') }}</h3>
                    <p style="color: var(--on-surface-variant);">{{ __('Automated reminders that don\'t feel robotic. Stay top of mind without the effort.') }}</p>
                </div>

                <!-- EDITABLE: Feature - Stats & Export -->
                <div class="p-8 rounded-3xl" style="background: var(--surface-container-low);">
                    <span class="material-symbols-outlined text-4xl mb-6" style="color: var(--primary);">monitoring</span>
                    <h3 class="text-xl font-bold mb-4">{{ __('Stats & Export') }}</h3>
                    <p style="color: var(--on-surface-variant);">{{ __('Export to Excel or PDF for your accountant in seconds. See your growth trajectory.') }}</p>
                </div>

                <!-- EDITABLE: Feature - Private & Secure (spans 2 cols) -->
                <div class="md:col-span-2 p-8 rounded-3xl flex flex-col md:flex-row gap-8 items-center" style="background: var(--tertiary-container); color: var(--on-tertiary-container);">
                    <div class="flex-1">
                        <span class="material-symbols-outlined text-4xl mb-6">lock_person</span>
                        <h3 class="text-xl font-bold mb-4">{{ __('Private & Secure') }}</h3>
                        <p class="opacity-80">{{ __('Your data belongs to you. No tracking, no reselling. Hosted on European soil with military-grade encryption.') }}</p>
                    </div>
                    <div class="flex-1 w-full h-32 rounded-2xl flex items-center justify-center" style="background: rgba(0,0,0,0.06);">
                        <span class="material-symbols-outlined text-6xl opacity-50">shield_lock</span>
                    </div>
                </div>

                <!-- EDITABLE: Feature - Client Portal -->
                <div class="p-8 rounded-3xl" style="background: var(--surface-container-low);">
                    <span class="material-symbols-outlined text-4xl mb-6" style="color: var(--primary);">group_add</span>
                    <h3 class="text-xl font-bold mb-4">{{ __('Client portal') }}</h3>
                    <p style="color: var(--on-surface-variant);">{{ __('Let clients update their own details and view shared documents in a professional branded portal.') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================
         EDITABLE: Persona Segmentation
         Change: section title, persona cards (icon, title, description, bullet points)
         ============================================ -->
    <section id="personas" class="py-24 px-6" style="background: var(--surface-container-lowest);">
        <div class="max-w-7xl mx-auto">
            <div class="mb-16">
                <!-- EDITABLE: Section Heading -->
                <h2 class="text-4xl font-bold mb-4" style="font-family: 'Manrope', sans-serif;">{{ __('Made for you') }}</h2>
                <p style="color: var(--on-surface-variant);">{{ __('Built for the unique needs of the Belgian professional ecosystem.') }}</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- EDITABLE: Persona Card 1 - Freelancers -->
                <div class="p-8 rounded-3xl transition-transform hover:-translate-y-2" style="background: var(--surface); border: 1px solid rgba(197,200,185,0.1);">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-6" style="background: var(--surface-container);">
                        <span class="material-symbols-outlined" style="color: var(--primary);">design_services</span>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">{{ __('Freelancers') }}</h3>
                    <p class="mb-6" style="color: var(--on-surface-variant);">{{ __('Manage diverse client portfolios, track project milestones, and ensure prompt payments without the corporate bloat.') }}</p>
                    <!-- EDITABLE: Persona features list -->
                    <ul class="space-y-3 text-sm font-semibold" style="color: var(--on-surface-variant);">
                        <li class="flex items-center gap-2"><span class="material-symbols-outlined text-lg" style="color: var(--primary);">check_circle</span> {{ __('Project timeline tracking') }}</li>
                        <li class="flex items-center gap-2"><span class="material-symbols-outlined text-lg" style="color: var(--primary);">check_circle</span> {{ __('Portfolio integration') }}</li>
                    </ul>
                </div>
                <!-- EDITABLE: Persona Card 2 - Tradespeople -->
                <div class="p-8 rounded-3xl transition-transform hover:-translate-y-2" style="background: var(--surface); border: 1px solid rgba(197,200,185,0.1);">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-6" style="background: var(--surface-container);">
                        <span class="material-symbols-outlined" style="color: var(--primary);">construction</span>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">{{ __('Tradespeople') }}</h3>
                    <p class="mb-6" style="color: var(--on-surface-variant);">{{ __('Electricians, plumbers, and decorators who need site details, address history, and recurring maintenance logs.') }}</p>
                    <ul class="space-y-3 text-sm font-semibold" style="color: var(--on-surface-variant);">
                        <li class="flex items-center gap-2"><span class="material-symbols-outlined text-lg" style="color: var(--primary);">check_circle</span> {{ __('Address & site notes') }}</li>
                        <li class="flex items-center gap-2"><span class="material-symbols-outlined text-lg" style="color: var(--primary);">check_circle</span> {{ __('Photo attachments') }}</li>
                    </ul>
                </div>
                <!-- EDITABLE: Persona Card 3 - Side Hustlers -->
                <div class="p-8 rounded-3xl transition-transform hover:-translate-y-2" style="background: var(--surface); border: 1px solid rgba(197,200,185,0.1);">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-6" style="background: var(--surface-container);">
                        <span class="material-symbols-outlined" style="color: var(--primary);">rocket_launch</span>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">{{ __('Side Hustlers') }}</h3>
                    <p class="mb-6" style="color: var(--on-surface-variant);">{{ __('Growing your dream while working a 9-to-5? ProContact keeps you organized in minutes, not hours.') }}</p>
                    <ul class="space-y-3 text-sm font-semibold" style="color: var(--on-surface-variant);">
                        <li class="flex items-center gap-2"><span class="material-symbols-outlined text-lg" style="color: var(--primary);">check_circle</span> {{ __('Quick-entry mobile app') }}</li>
                        <li class="flex items-center gap-2"><span class="material-symbols-outlined text-lg" style="color: var(--primary);">check_circle</span> {{ __('Low-cost starter plan') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================
         EDITABLE: Founder Story
         Change: founder image, quote text, founder name, founder title
         ============================================ -->
    <section id="founder-story" class="py-24 px-6" style="background: var(--surface);">
        <div class="max-w-4xl mx-auto">
            <div class="p-12 lg:p-20 rounded-[3rem] relative overflow-hidden" style="background: var(--surface-container-low);">
                <!-- Decorative quote mark -->
                <div class="absolute top-0 right-0 p-12 opacity-5">
                    <span class="material-symbols-outlined" style="font-size: 10rem;">format_quote</span>
                </div>
                <div class="relative z-10 text-center">
                    <!-- EDITABLE: Founder Image (replace with actual photo) -->
                    <div class="mb-8 flex justify-center">
                        <div class="w-24 h-24 rounded-full flex items-center justify-center shadow-lg" style="background: var(--surface-container-high); border: 4px solid var(--surface);">
                            <span class="material-symbols-outlined text-4xl" style="color: var(--primary);">person</span>
                        </div>
                    </div>
                    <!-- EDITABLE: Founder Quote -->
                    <blockquote class="text-2xl md:text-3xl font-semibold leading-snug mb-8 italic" style="font-family: 'Manrope', sans-serif; color: var(--on-surface);">
                        "{{ __('I spent years looking for a CRM that didn\'t feel like a cockpit of a Boeing 747. I just wanted to remember my clients and what we talked about. So, I built ProContact right here in Belgium.') }}"
                    </blockquote>
                    <!-- EDITABLE: Founder Name & Title -->
                    <cite class="block not-italic">
                        <span class="font-bold text-lg" style="color: var(--primary);">Eric D.</span><br>
                        <span class="text-sm uppercase tracking-widest" style="color: var(--on-surface-variant);">{{ __('Tech Consultant & Founder') }}</span>
                    </cite>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================
         EDITABLE: Pricing Section
         Change: section title, toggle labels, plan names, prices, features, CTA buttons
         ============================================ -->
    <section id="pricing" class="py-24 px-6" style="background: var(--surface-container-low);" x-data="{ yearly: true }">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <!-- EDITABLE: Section Heading -->
                <h2 class="text-4xl font-bold mb-4 tracking-tight" style="font-family: 'Manrope', sans-serif;">{{ __('Simple, honest pricing') }}</h2>
                <!-- EDITABLE: Billing Toggle -->
                <div class="flex items-center justify-center gap-4 mt-8">
                    <span class="font-semibold" :class="yearly ? '' : 'opacity-100'" :style="yearly ? 'color: var(--on-surface-variant)' : 'color: var(--on-surface)'">{{ __('Monthly') }}</span>
                    <button @click="yearly = !yearly" class="w-14 h-7 rounded-full relative p-1 transition-all" :style="yearly ? 'background: rgba(132,55,40,0.2)' : 'background: rgba(132,55,40,0.2)'">
                        <div class="w-5 h-5 rounded-full transition-all" :class="yearly ? 'ml-auto' : 'ml-0'" style="background: var(--primary);"></div>
                    </button>
                    <span class="font-semibold" :style="yearly ? 'color: var(--primary)' : 'color: var(--on-surface-variant)'">
                        {{ __('Yearly') }}
                        <span class="text-xs px-2 py-0.5 rounded-full ml-1" style="background: var(--tertiary-container); color: var(--on-tertiary-container);">-20%</span>
                    </span>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                <!-- EDITABLE: Plan 1 - Free Trial -->
                <div class="p-10 rounded-3xl" style="background: var(--surface); border: 1px solid rgba(197,200,185,0.2);">
                    <!-- EDITABLE: Plan name -->
                    <h3 class="text-xl font-bold mb-2">{{ __('Free Trial') }}</h3>
                    <!-- EDITABLE: Price -->
                    <div class="text-4xl font-extrabold mb-6" style="font-family: 'Manrope', sans-serif;">€0 <span class="text-sm font-normal" style="color: var(--on-surface-variant);">/ {{ __('14 days') }}</span></div>
                    <!-- EDITABLE: Plan description -->
                    <p class="mb-8" style="color: var(--on-surface-variant);">{{ __('Full access to explore every feature. No credit card required.') }}</p>
                    <!-- EDITABLE: Plan features -->
                    <ul class="space-y-4 mb-10">
                        <li class="flex items-center gap-3"><span class="material-symbols-outlined" style="color: var(--primary);">check</span> {{ __('Unlimited contacts') }}</li>
                        <li class="flex items-center gap-3"><span class="material-symbols-outlined" style="color: var(--primary);">check</span> {{ __('Mobile app access') }}</li>
                        <li class="flex items-center gap-3"><span class="material-symbols-outlined" style="color: var(--primary);">check</span> {{ __('Email support') }}</li>
                    </ul>
                    <!-- EDITABLE: Plan CTA -->
                    <a href="{{ route('register') }}" class="block w-full py-4 rounded-full font-bold text-center transition-all" style="border: 2px solid var(--primary); color: var(--primary);">{{ __('Get Started') }}</a>
                </div>
                <!-- EDITABLE: Plan 2 - Pro Plan -->
                <div class="p-10 rounded-3xl relative" style="background: var(--surface-container-lowest); border: 2px solid var(--primary); box-shadow: var(--shadow-xl);">
                    <!-- EDITABLE: Popular badge -->
                    <div class="absolute top-0 right-10 -translate-y-1/2 px-4 py-1 rounded-full text-xs font-bold uppercase" style="background: var(--primary); color: var(--on-primary);">{{ __('Most Popular') }}</div>
                    <h3 class="text-xl font-bold mb-2">{{ __('Pro Plan') }}</h3>
                    <!-- EDITABLE: Price (changes with toggle) -->
                    <div class="text-4xl font-extrabold mb-6" style="font-family: 'Manrope', sans-serif;">
                        <span x-show="!yearly">€19</span>
                        <span x-show="yearly" x-cloak>€15</span>
                        <span class="text-sm font-normal" style="color: var(--on-surface-variant);">/ {{ __('month') }}</span>
                    </div>
                    <p class="mb-8" style="color: var(--on-surface-variant);">{{ __('The complete toolbox for professionals ready to grow.') }}</p>
                    <ul class="space-y-4 mb-10">
                        <li class="flex items-center gap-3"><span class="material-symbols-outlined" style="color: var(--primary);">check</span> {{ __('Everything in Trial') }}</li>
                        <li class="flex items-center gap-3"><span class="material-symbols-outlined" style="color: var(--primary);">check</span> {{ __('Client portal access') }}</li>
                        <li class="flex items-center gap-3"><span class="material-symbols-outlined" style="color: var(--primary);">check</span> {{ __('Advanced reporting & Export') }}</li>
                        <li class="flex items-center gap-3"><span class="material-symbols-outlined" style="color: var(--primary);">check</span> {{ __('Priority human support') }}</li>
                    </ul>
                    <a href="{{ route('register') }}" class="block w-full py-4 rounded-full font-bold text-center btn-primary shadow-lg transition-all">{{ __('Go Pro') }}</a>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================
         EDITABLE: FAQ Section
         Change: section title, questions and answers
         ============================================ -->
    <section class="py-24 px-6" style="background: var(--surface);" x-data="{ open: null }">
        <div class="max-w-3xl mx-auto">
            <!-- EDITABLE: Section Heading -->
            <h2 class="text-3xl font-bold mb-12 text-center" style="font-family: 'Manrope', sans-serif;">{{ __('Frequently Asked Questions') }}</h2>
            <div class="space-y-4">
                <!-- EDITABLE: FAQ Item 1 -->
                <div class="rounded-2xl p-6 cursor-pointer transition-colors" style="border: 1px solid rgba(197,200,185,0.3);" :style="open === 1 ? 'background: var(--surface-container-low)' : ''" @click="open = open === 1 ? null : 1">
                    <div class="flex justify-between items-center">
                        <h4 class="font-bold text-lg">{{ __('Can I import my data from Excel?') }}</h4>
                        <span class="material-symbols-outlined transition-transform" :class="open === 1 ? 'rotate-180' : ''">expand_more</span>
                    </div>
                    <p x-show="open === 1" x-collapse x-cloak class="mt-4 leading-relaxed" style="color: var(--on-surface-variant);">
                        {{ __('Yes, we offer a simple CSV import tool. You can bring all your existing client data in seconds.') }}
                    </p>
                </div>
                <!-- EDITABLE: FAQ Item 2 -->
                <div class="rounded-2xl p-6 cursor-pointer transition-colors" style="border: 1px solid rgba(197,200,185,0.3);" :style="open === 2 ? 'background: var(--surface-container-low)' : ''" @click="open = open === 2 ? null : 2">
                    <div class="flex justify-between items-center">
                        <h4 class="font-bold text-lg">{{ __('Is my data hosted in Europe?') }}</h4>
                        <span class="material-symbols-outlined transition-transform" :class="open === 2 ? 'rotate-180' : ''">expand_more</span>
                    </div>
                    <p x-show="open === 2" x-collapse x-cloak class="mt-4 leading-relaxed" style="color: var(--on-surface-variant);">
                        {{ __('Absolutely. All data is hosted on secure servers within the EU, fully compliant with GDPR.') }}
                    </p>
                </div>
                <!-- EDITABLE: FAQ Item 3 -->
                <div class="rounded-2xl p-6 cursor-pointer transition-colors" style="border: 1px solid rgba(197,200,185,0.3);" :style="open === 3 ? 'background: var(--surface-container-low)' : ''" @click="open = open === 3 ? null : 3">
                    <div class="flex justify-between items-center">
                        <h4 class="font-bold text-lg">{{ __('Is there a mobile app?') }}</h4>
                        <span class="material-symbols-outlined transition-transform" :class="open === 3 ? 'rotate-180' : ''">expand_more</span>
                    </div>
                    <p x-show="open === 3" x-collapse x-cloak class="mt-4 leading-relaxed" style="color: var(--on-surface-variant);">
                        {{ __('Yes, ProContact works perfectly as a PWA on your phone, giving you full access on the go.') }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================
         EDITABLE: Final CTA Banner
         Change: heading, description, CTA buttons, subtext
         ============================================ -->
    <section class="py-24 px-8">
        <div class="max-w-7xl mx-auto rounded-[3rem] p-12 lg:p-24 text-center relative overflow-hidden" style="background: linear-gradient(135deg, #843728, #a24e3d); color: var(--on-primary); box-shadow: var(--shadow-xl);">
            <!-- Decorative elements -->
            <div class="absolute top-0 left-0 w-64 h-64 rounded-full -translate-x-1/2 -translate-y-1/2 blur-3xl" style="background: rgba(255,255,255,0.1);"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 rounded-full translate-x-1/3 translate-y-1/3 blur-3xl" style="background: rgba(255,255,255,0.05);"></div>
            <div class="relative z-10 max-w-2xl mx-auto">
                <!-- EDITABLE: CTA Heading -->
                <h2 class="text-4xl lg:text-5xl font-extrabold mb-8 tracking-tight" style="font-family: 'Manrope', sans-serif;">{{ __('Ready to stop losing clients?') }}</h2>
                <!-- EDITABLE: CTA Description -->
                <p class="text-xl opacity-90 mb-12">{{ __('Join hundreds of Belgian professionals who have reclaimed their time and organized their business with ProContact.') }}</p>
                <!-- EDITABLE: CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <a href="{{ route('register') }}" class="px-10 py-5 rounded-full font-bold text-xl transition-all shadow-xl hover:scale-105" style="background: var(--surface); color: var(--primary);">{{ __('Start your free trial') }}</a>
                    <a href="#" class="px-10 py-5 rounded-full font-bold text-xl transition-all border-2 border-white hover:bg-white/10" style="color: var(--on-primary);">{{ __('Book a demo') }}</a>
                </div>
                <!-- EDITABLE: Subtext -->
                <p class="mt-8 text-sm opacity-70">{{ __('No credit card required. Cancel anytime.') }}</p>
            </div>
        </div>
    </section>

    <!-- ============================================
         EDITABLE: Footer
         Change: copyright text, links
         ============================================ -->
    <footer class="py-12 px-8" style="background: var(--surface-container-low); border-top: 1px solid rgba(197,200,185,0.1);">
        <div class="max-w-7xl mx-auto">
            <!-- EDITABLE: Footer Top Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:flex lg:justify-between items-center pt-8">
                <div class="mb-8 lg:mb-0">
                    <!-- EDITABLE: Footer Brand -->
                    <div class="text-lg font-black mb-2" style="color: var(--on-surface);">{{ __('ProContact CRM') }}</div>
                    <p class="text-sm" style="color: var(--outline);">{{ __('Built for the modern professional.') }}</p>
                </div>
                <!-- EDITABLE: Footer Links -->
                <div class="flex flex-wrap gap-8 lg:gap-12 text-sm font-medium uppercase tracking-wide">
                    <a href="#" class="transition-colors hover:opacity-80" style="color: var(--outline);">{{ __('Privacy Policy') }}</a>
                    <a href="#" class="transition-colors hover:opacity-80" style="color: var(--outline);">{{ __('Terms of Service') }}</a>
                    <a href="#" class="transition-colors hover:opacity-80" style="color: var(--outline);">{{ __('Cookie Settings') }}</a>
                    <a href="#" class="transition-colors hover:opacity-80" style="color: var(--outline);">{{ __('Contact Support') }}</a>
                </div>
            </div>
            <!-- EDITABLE: Footer Copyright -->
            <div class="mt-12 text-center lg:text-left text-sm" style="color: var(--outline-variant);">
                &copy; {{ date('Y') }} ProContact CRM. {{ __('Made with precision in Belgium.') }}
            </div>
        </div>
    </footer>
</body>
</html>
