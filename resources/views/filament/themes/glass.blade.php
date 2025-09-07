{{-- Custom Glass Theme for Filament --}}
@php
    // This theme will be registered with Filament
@endphp

<style>
    :root {
        --fi-primary-50: oklch(0.98 0.02 200);
        --fi-primary-100: oklch(0.95 0.05 200);
        --fi-primary-200: oklch(0.90 0.08 200);
        --fi-primary-300: oklch(0.82 0.12 200);
        --fi-primary-400: oklch(0.74 0.15 200);
        --fi-primary-500: oklch(0.65 0.18 200);
        --fi-primary-600: oklch(0.56 0.15 200);
        --fi-primary-700: oklch(0.47 0.12 200);
        --fi-primary-800: oklch(0.38 0.09 200);
        --fi-primary-900: oklch(0.29 0.06 200);
        --fi-primary-950: oklch(0.20 0.03 200);
    }

    /* Apply glass styling globally */
    .fi-main {
        background: linear-gradient(135deg, rgba(255,255,255,0.1), rgba(255,255,255,0.05));
        backdrop-filter: blur(20px);
    }
</style>