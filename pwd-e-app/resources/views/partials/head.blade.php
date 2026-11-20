<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>
    {{ filled($title ?? null) ? $title.' - '.config('app.name', 'Laravel') : config('app.name', 'Laravel') }}
</title>

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">
<link rel="manifest" href="/manifest.webmanifest">
<meta name="theme-color" content="#244F73">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="PWD-E">

@fonts

@vite(['resources/css/app.css', 'resources/js/app.js'])

{{-- The custom UI is light-only; force light appearance so Flux dark-mode tokens don't render white-on-white. --}}
<script>window.localStorage.setItem('flux.appearance', 'light');</script>
@fluxAppearance

{{-- Apply persisted accessibility settings early to avoid FOUC. --}}
@php
    $a11y = auth()->user()?->display_settings ?? [];
@endphp
<script>
    (function () {
        var s = @json($a11y);
        var h = document.documentElement;
        if (s.high_contrast) h.classList.add('a11y-high-contrast');
        if (s.large_text) h.classList.add('a11y-large-text');
        if (s.dyslexia_font) h.classList.add('a11y-dyslexia');
        if (s.reduced_motion) h.classList.add('a11y-reduced-motion');
        if (s.predictive_text === false) h.classList.add('a11y-no-predictive');
    })();

    // Service Worker Registration for PWA / Low-Bandwidth Resilience
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('/sw.js').catch(function () {});
        });
    }
</script>
