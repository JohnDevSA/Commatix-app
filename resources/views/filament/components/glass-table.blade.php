{{-- Glass Table Component View --}}
<div {{ $attributes->merge(['class' => 'glass-card rounded-xl overflow-hidden']) }}>
    {{ $slot }}
</div>