@props([
    'status' => 'available',
    'message' => null,
    'tooltip' => null,
    'strikethrough' => false,
])

@php
    $component = new \App\Filament\Components\FeatureStatusBadge(
        status: $status,
        message: $message,
        tooltip: $tooltip,
        strikethrough: $strikethrough
    );

    $color = $component->getColor();
    $icon = $component->getIcon();
    $displayMessage = $component->getMessage();
    $cssClasses = $component->getCssClasses();
    $tooltipText = $tooltip ?? $displayMessage;
@endphp

<span
    {{ $attributes->merge(['class' => $cssClasses]) }}
    @if($tooltip)
        x-data="{ tooltip: false }"
        x-tooltip="{
            content: '{{ $tooltipText }}',
            theme: 'dark',
        }"
    @endif
>
    @if($icon)
        <x-filament::icon
            :icon="$icon"
            class="h-4 w-4"
        />
    @endif

    <span @if($strikethrough) class="line-through" @endif>
        {{ $displayMessage }}
    </span>
</span>
