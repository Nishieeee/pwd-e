@props(['tone' => 'tint'])
@php
$classes = match($tone) {
    'green' => 'bg-secondary-tint text-secondary',
    'white' => 'bg-white text-primary-dark border border-primary-border',
    default => 'bg-primary-tint text-primary-dark',
};
@endphp
<span {{ $attributes->merge(['class' => "inline-block $classes text-sm font-medium px-4 py-2 rounded-full"]) }}>{{ $slot }}</span>