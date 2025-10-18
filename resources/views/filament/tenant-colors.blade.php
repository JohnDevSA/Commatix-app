{{-- Dynamic Tenant Color Injection --}}
@if($tenant ?? null)
<style id="tenant-colors">
    :root {
        /* Tenant-specific color (can be customized per tenant) */
        --tenant-color: {{ $tenant->brand_color ?? 'oklch(0.65 0.18 230)' }};
        --tenant-color-light: {{ $tenant->brand_color_light ?? 'oklch(0.85 0.18 230)' }};
        --tenant-color-dark: {{ $tenant->brand_color_dark ?? 'oklch(0.45 0.18 230)' }};
    }

    /* Tenant accent classes */
    .tenant-accent {
        background-color: var(--tenant-color) !important;
    }

    .tenant-accent-light {
        background-color: var(--tenant-color-light) !important;
    }

    .tenant-accent-border {
        border-color: var(--tenant-color) !important;
    }

    .tenant-accent-text {
        color: var(--tenant-color) !important;
    }

    /* Tenant badge in navigation */
    .tenant-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: var(--tenant-color-light);
        border-left: 3px solid var(--tenant-color);
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--tenant-color-dark);
    }

    .tenant-badge::before {
        content: '';
        display: inline-block;
        width: 0.5rem;
        height: 0.5rem;
        border-radius: 50%;
        background-color: var(--tenant-color);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.5;
        }
    }
</style>
@endif
