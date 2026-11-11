@props(['eyebrow' => null, 'title' => null, 'intro' => null, 'eyebrowTone' => 'tint'])
<div {{ $attributes->merge(['class' => 'text-center']) }}>
    @if($eyebrow)<x-eyebrow-pill :tone="$eyebrowTone">{{ $eyebrow }}</x-eyebrow-pill>@endif
    @if($title)<h2 class="font-heading text-primary-dark text-3xl font-bold mt-4">{{ $title }}</h2>@endif
    @if($intro)<p class="text-body-muted mt-3 max-w-2xl mx-auto">{{ $intro }}</p>@endif
</div>