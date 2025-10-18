<!-- Custom styles for Commatix - South African UX standards & Glassmorphism Design System -->
<style id="commatix-custom-styles">
    /* ===========================================
       South African UX: Right-aligned form buttons
       =========================================== */

    /* Target all footers and form action areas */
    footer,
    [class*="fi-fo-actions"],
    [class*="fi-form-actions"],
    .fi-ac-action-group {
        display: flex !important;
        justify-content: flex-end !important;
    }

    /* Ensure buttons are also flexed to the right in custom forms */
    form > div.flex {
        display: flex !important;
        justify-content: flex-end !important;
    }

    /* ===========================================
       Glassmorphism Design System
       =========================================== */

    /* Glass Card - Sections and containers */
    .glass-card {
        background: rgba(255, 255, 255, 0.7) !important;
        backdrop-filter: blur(10px) !important;
        -webkit-backdrop-filter: blur(10px) !important;
        border: 1px solid rgba(255, 255, 255, 0.18) !important;
        border-radius: 12px !important;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1) !important;
        transition: all 0.3s ease !important;
    }

    .glass-card:hover {
        box-shadow: 0 12px 48px rgba(0, 0, 0, 0.15) !important;
        transform: translateY(-2px) !important;
    }

    /* Dark mode glass card */
    .dark .glass-card {
        background: rgba(0, 0, 0, 0.6) !important;
        backdrop-filter: blur(15px) !important;
        -webkit-backdrop-filter: blur(15px) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3) !important;
    }

    /* Glass Input - Form fields */
    .glass-input {
        background: rgba(255, 255, 255, 0.8) !important;
        backdrop-filter: blur(8px) !important;
        -webkit-backdrop-filter: blur(8px) !important;
        border: 1px solid rgba(0, 0, 0, 0.1) !important;
        border-radius: 8px !important;
        transition: all 0.2s ease !important;
    }

    .glass-input:focus {
        background: rgba(255, 255, 255, 0.95) !important;
        border-color: oklch(0.65 0.18 200) !important;
        box-shadow: 0 0 0 3px rgba(101, 102, 241, 0.1) !important;
    }

    /* Dark mode glass input */
    .dark .glass-input {
        background: rgba(0, 0, 0, 0.4) !important;
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
    }

    .dark .glass-input:focus {
        background: rgba(0, 0, 0, 0.6) !important;
        border-color: oklch(0.65 0.18 200) !important;
    }

    /* Apply to Filament form inputs automatically */
    input[type="text"].glass-input,
    input[type="email"].glass-input,
    input[type="password"].glass-input,
    input[type="number"].glass-input,
    input[type="tel"].glass-input,
    input[type="date"].glass-input,
    input[type="time"].glass-input,
    textarea.glass-input,
    select.glass-input {
        background: rgba(255, 255, 255, 0.8) !important;
        backdrop-filter: blur(8px) !important;
    }

    /* Prominent Glass - Modals and popovers */
    .glass-prominent {
        background: rgba(255, 255, 255, 0.85) !important;
        backdrop-filter: blur(20px) !important;
        -webkit-backdrop-filter: blur(20px) !important;
        border: 1px solid rgba(255, 255, 255, 0.25) !important;
        border-radius: 16px !important;
        box-shadow: 0 12px 48px rgba(0, 0, 0, 0.15) !important;
    }

    /* ===========================================
       Performance Optimization
       =========================================== */

    /* Respect user motion preferences */
    @media (prefers-reduced-motion: reduce) {
        .glass-card,
        .glass-input,
        .glass-prominent,
        .animate-fade-in,
        .animate-slide-up,
        .animate-glass-float,
        .animate-metric-up {
            animation: none !important;
            transition: none !important;
            transform: none !important;
        }
    }

    /* Fallback for browsers without backdrop-filter support */
    @supports not (backdrop-filter: blur(10px)) {
        .glass-card {
            background: rgba(255, 255, 255, 0.95) !important;
        }

        .glass-input {
            background: rgba(255, 255, 255, 0.98) !important;
        }

        .dark .glass-card {
            background: rgba(0, 0, 0, 0.85) !important;
        }

        .dark .glass-input {
            background: rgba(0, 0, 0, 0.7) !important;
        }
    }
</style>
