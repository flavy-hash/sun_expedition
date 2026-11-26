<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        // Sections arrive HTML-escaped; decode once and let {{ }} escape the final values.
        $section = fn (string $name, string $default = '') => html_entity_decode(trim($__env->yieldContent($name, $default)), ENT_QUOTES | ENT_HTML5);
        $seoTitle = \App\Support\Seo::title($section('title'));
        $seoDescription = \Illuminate\Support\Str::limit($section('meta_description', 'Suni Expedition is a locally owned Tanzanian tour operator in Arusha: private safaris, Kilimanjaro climbs, Zanzibar holidays and day trips, planned and guided by our own team.'), 300);
        $seoUrl = \App\Support\Seo::canonical();
        $seoImage = \App\Support\Seo::image($section('og_image') ?: 'images/hero/home.jpg');
    @endphp
    <title>{{ $seoTitle }}</title>
    <meta name="description" content="{{ $seoDescription }}">
    <link rel="canonical" href="{{ $seoUrl }}">

    {{-- How the page looks when shared on WhatsApp, Facebook, X… --}}
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:type" content="{{ $section('og_type', 'website') }}">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:url" content="{{ $seoUrl }}">
    <meta property="og:image" content="{{ $seoImage }}">
    <meta name="twitter:card" content="summary_large_image">

    {{-- Ownership checks for Google Search Console and Bing Webmaster Tools (set in .env). --}}
    @if (filled(config('services.search_console.google')))
        <meta name="google-site-verification" content="{{ config('services.search_console.google') }}">
    @endif
    @if (filled(config('services.search_console.bing')))
        <meta name="msvalidate.01" content="{{ config('services.search_console.bing') }}">
    @endif

    <script type="application/ld+json">{!! json_encode(\App\Support\Seo::organization(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
    @stack('structured-data')

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicon-180.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=Instrument+Serif:ital@0;1&family=Space+Grotesk:wght@300;400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Google Website Translator: powers the language switcher in partials.language-switcher.
         Our own CSS hides its default banner/toolbar so only our custom button is visible. --}}
    <div id="google_translate_element" class="notranslate" style="display:none"></div>
    <script>
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                pageLanguage: 'en',
                includedLanguages: 'zh-CN,fr,it,es,ar',
                autoDisplay: false,
            }, 'google_translate_element');
        }
    </script>
    <script src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit" async></script>
</head>
<body class="flex min-h-screen flex-col bg-cream text-ink font-sans">

    @include('partials.nav')

    @php
        $toast = match (true) {
            (bool) session('inquirySent') => __('Thank you — your trip request has been sent. We reply within one business day.'),
            (bool) session('reviewSubmitted') => __('Thank you for your review! It will appear on the site once our team has checked it.'),
            default => null,
        };
    @endphp

    @if ($toast)
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)" x-transition
             class="fixed inset-x-4 top-20 z-[80] mx-auto w-fit rounded-2xl border border-forest/10 bg-white px-5 py-2.5 text-sm text-ink shadow-lg sm:rounded-full">
            <div class="flex items-center gap-3">
                <span class="h-2 w-2 flex-shrink-0 rounded-full bg-orange"></span>
                <p>{{ $toast }}</p>
                <button @click="show = false" class="text-muted hover:text-ink">&times;</button>
            </div>
        </div>
    @endif

    <main class="flex-1">
        @yield('content')
    </main>

    @include('partials.footer')
    @include('partials.whatsapp-float')
    @include('partials.language-switcher')
    @include('partials.bottom-nav')

</body>
</html>
