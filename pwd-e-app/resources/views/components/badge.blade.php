@props(['tone' => 'blue'])
@php
$classes = match($tone) {
    'green' => 'bg-secondary-tint text-secondary',
    'navy' => 'bg-primary-tint text-primary-dark',
    default => 'bg-primary-tint text-primary',
};
@endphp
<span {{ $attributes->merge(['class' => "inline-block text-xs font-medium $classes px-3 py-1 rounded-full"]) }}>{{ $slot }}</span>