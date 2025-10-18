{{--
  Commatix Glass Theme for Filament 4

  This theme applies the Commatix design system to Filament admin panels.
  See DESIGN_SYSTEM.md for complete documentation.

  Features:
  - OKLCH color space for perceptual uniformity
  - Glassmorphism aesthetic
  - South African UX standards
  - Dark mode support
  - Accessible contrast ratios (WCAG 2.1 AA)
--}}

<style>
    /* ============================================
       Commatix Design System - CSS Variables
       ============================================ */

    :root {
        /* Commatix Primary Colors (OKLCH) */
        --commatix-50: oklch(0.98 0.02 200);
        --commatix-100: oklch(0.95 0.05 200);
        --commatix-200: oklch(0.90 0.08 200);
        --commatix-300: oklch(0.82 0.12 200);
        --commatix-400: oklch(0.74 0.15 200);
        --commatix-500: oklch(0.65 0.18 200); /* Primary brand color */
        --commatix-600: oklch(0.56 0.15 200);
        --commatix-700: oklch(0.47 0.12 200);
        --commatix-800: oklch(0.38 0.09 200);
        --commatix-900: oklch(0.29 0.06 200);
        --commatix-950: oklch(0.20 0.03 200);

        /* South African Gold Accent */
        --sa-gold-500: oklch(0.8 0.12 85);

        /* Map to Filament CSS variables */
        --fi-primary-50: var(--commatix-50);
        --fi-primary-100: var(--commatix-100);
        --fi-primary-200: var(--commatix-200);
        --fi-primary-300: var(--commatix-300);
        --fi-primary-400: var(--commatix-400);
        --fi-primary-500: var(--commatix-500);
        --fi-primary-600: var(--commatix-600);
        --fi-primary-700: var(--commatix-700);
        --fi-primary-800: var(--commatix-800);
        --fi-primary-900: var(--commatix-900);
        --fi-primary-950: var(--commatix-950);

        /* Filament color shades */
        --fi-color-primary-50: var(--commatix-50);
        --fi-color-primary-100: var(--commatix-100);
        --fi-color-primary-200: var(--commatix-200);
        --fi-color-primary-300: var(--commatix-300);
        --fi-color-primary-400: var(--commatix-400);
        --fi-color-primary-500: var(--commatix-500);
        --fi-color-primary-600: var(--commatix-600);
        --fi-color-primary-700: var(--commatix-700);
        --fi-color-primary-800: var(--commatix-800);
        --fi-color-primary-900: var(--commatix-900);
        --fi-color-primary-950: var(--commatix-950);
    }

    /* ============================================
       Glassmorphism Background
       ============================================ */

    /* Main content area - subtle glass background */
    .fi-main {
        background: linear-gradient(135deg,
            rgba(255, 255, 255, 0.05) 0%,
            rgba(255, 255, 255, 0.02) 100%
        );
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
    }

    /* Sidebar glass effect */
    .fi-sidebar {
        background: rgba(255, 255, 255, 0.8) !important;
        backdrop-filter: blur(15px) !important;
        -webkit-backdrop-filter: blur(15px) !important;
        border-right: 1px solid rgba(0, 0, 0, 0.08) !important;
    }

    /* Dark mode adjustments */
    .dark .fi-main {
        background: linear-gradient(135deg,
            rgba(0, 0, 0, 0.3) 0%,
            rgba(0, 0, 0, 0.5) 100%
        );
    }

    .dark .fi-sidebar {
        background: rgba(0, 0, 0, 0.7) !important;
        border-right-color: rgba(255, 255, 255, 0.1) !important;
    }

    /* ============================================
       Enhanced Filament Components
       ============================================ */

    /* Cards and sections with glass effect */
    .fi-section,
    .fi-card {
        background: rgba(255, 255, 255, 0.7) !important;
        backdrop-filter: blur(10px) !important;
        -webkit-backdrop-filter: blur(10px) !important;
        border: 1px solid rgba(255, 255, 255, 0.18) !important;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1) !important;
        transition: all 0.3s ease !important;
    }

    .fi-section:hover,
    .fi-card:hover {
        box-shadow: 0 12px 48px rgba(0, 0, 0, 0.15) !important;
        transform: translateY(-2px) !important;
    }

    .dark .fi-section,
    .dark .fi-card {
        background: rgba(0, 0, 0, 0.6) !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3) !important;
    }

    /* Form inputs with glass styling */
    .fi-input,
    .fi-select,
    .fi-textarea {
        background: rgba(255, 255, 255, 0.8) !important;
        backdrop-filter: blur(8px) !important;
        -webkit-backdrop-filter: blur(8px) !important;
        border: 1px solid rgba(0, 0, 0, 0.1) !important;
        transition: all 0.2s ease !important;
    }

    .fi-input:focus,
    .fi-select:focus,
    .fi-textarea:focus {
        background: rgba(255, 255, 255, 0.95) !important;
        border-color: var(--commatix-500) !important;
        box-shadow: 0 0 0 3px rgba(101, 102, 241, 0.1) !important;
        outline: none !important;
    }

    .dark .fi-input,
    .dark .fi-select,
    .dark .fi-textarea {
        background: rgba(0, 0, 0, 0.4) !important;
        border-color: rgba(255, 255, 255, 0.15) !important;
    }

    .dark .fi-input:focus,
    .dark .fi-select:focus,
    .dark .fi-textarea:focus {
        background: rgba(0, 0, 0, 0.6) !important;
    }

    /* ============================================
       South African UX: Right-aligned Buttons
       ============================================ */

    /* Form actions always right-aligned */
    .fi-fo-actions,
    .fi-form-actions,
    [class*="fi-ac-action-group"] {
        display: flex !important;
        justify-content: flex-end !important;
        gap: 0.75rem !important;
    }

    /* ============================================
       Typography Enhancements
       ============================================ */

    /* Ensure Figtree font is applied */
    body,
    .fi-body {
        font-family: 'Figtree', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    /* Heading styles */
    .fi-header-heading,
    .fi-section-header-heading {
        font-weight: 700;
        color: var(--commatix-950);
    }

    .dark .fi-header-heading,
    .dark .fi-section-header-heading {
        color: var(--commatix-50);
    }

    /* ============================================
       Status Badges & Pills
       ============================================ */

    .fi-badge {
        font-weight: 500;
        border-radius: 9999px;
        padding: 0.25rem 0.75rem;
        font-size: 0.75rem;
        border: 1px solid transparent;
    }

    /* Success badge (Active, Completed) */
    .fi-badge-success {
        background-color: rgba(16, 185, 129, 0.1) !important;
        color: rgb(5, 150, 105) !important;
        border-color: rgba(16, 185, 129, 0.3) !important;
    }

    /* Warning badge (Pending, Trial) */
    .fi-badge-warning {
        background-color: rgba(245, 158, 11, 0.1) !important;
        color: rgb(217, 119, 6) !important;
        border-color: rgba(245, 158, 11, 0.3) !important;
    }

    /* Danger badge (Failed, Suspended) */
    .fi-badge-danger {
        background-color: rgba(239, 68, 68, 0.1) !important;
        color: rgb(220, 38, 38) !important;
        border-color: rgba(239, 68, 68, 0.3) !important;
    }

    /* Info badge (Draft, Inactive) */
    .fi-badge-info {
        background-color: var(--commatix-100) !important;
        color: var(--commatix-800) !important;
        border-color: var(--commatix-300) !important;
    }

    /* ============================================
       Animations & Transitions
       ============================================ */

    /* Respect user motion preferences */
    @media (prefers-reduced-motion: reduce) {
        * {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
    }

    /* ============================================
       Accessibility Enhancements
       ============================================ */

    /* Focus indicators */
    *:focus-visible {
        outline: 2px solid var(--commatix-500) !important;
        outline-offset: 2px !important;
    }

    /* High contrast mode support */
    @media (prefers-contrast: high) {
        .fi-section,
        .fi-card,
        .fi-input,
        .fi-select,
        .fi-textarea {
            border-width: 2px !important;
        }
    }

    /* ============================================
       Performance Optimizations
       ============================================ */

    /* Use GPU acceleration for animations */
    .fi-section,
    .fi-card {
        will-change: transform;
        transform: translateZ(0);
    }

    /* Fallback for browsers without backdrop-filter */
    @supports not (backdrop-filter: blur(10px)) {
        .fi-section,
        .fi-card {
            background: rgba(255, 255, 255, 0.95) !important;
        }

        .fi-input,
        .fi-select,
        .fi-textarea {
            background: rgba(255, 255, 255, 0.98) !important;
        }

        .dark .fi-section,
        .dark .fi-card {
            background: rgba(0, 0, 0, 0.85) !important;
        }

        .dark .fi-input,
        .dark .fi-select,
        .dark .fi-textarea {
            background: rgba(0, 0, 0, 0.7) !important;
        }
    }
</style>