@props(['value' => null, 'label' => null])
<div {{ $attributes->merge(['class' => 'bg-surface rounded-lg p-4 text-center']) }}>
    <p class="text-2xl font-bold text-secondary tabular-nums">{{ $value }}</p>
    <p class="text-xs text-body-muted mt-1">{{ $label }}</p>
</div>