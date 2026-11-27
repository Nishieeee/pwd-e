<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'PWD-E — Inclusive Job Directory' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        (function () {
            try {
                var raw = localStorage.getItem('pwde_scale');
                var parsed = raw !== null ? parseInt(raw, 10) : 100;
                var scale = (!isNaN(parsed) && parsed >= 90 && parsed <= 150) ? parsed : 100;
                window.__pwdeInitialScale = scale;
                document.documentElement.style.fontSize = scale + '%';
                document.documentElement.classList.toggle('a11y-large-text', scale >= 125);
            } catch (e) {}
        })();
    </script>
</head>
<body class="bg-white text-dark-neutral antialiased">
    <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 focus:z-50 focus:bg-primary-dark focus:text-white focus:px-3 focus:py-2 focus:rounded">Skip to main content</a>
    <x-site-header />
    <x-accessibility-panel />

    {{ $slot }}

    <x-site-footer />
</body>
</html>