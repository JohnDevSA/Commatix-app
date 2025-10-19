import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/filament/**/*.blade.php', // Add Filament views
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
        './app/Filament/**/*.php', // Include Filament resources
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // South African inspired glassmorphism colors
                'commatix': {
                    50: 'oklch(0.98 0.02 200)',
                    100: 'oklch(0.95 0.05 200)',
                    200: 'oklch(0.90 0.08 200)',
                    300: 'oklch(0.82 0.12 200)',
                    400: 'oklch(0.74 0.15 200)',
                    500: 'oklch(0.65 0.18 200)', // Primary
                    600: 'oklch(0.56 0.15 200)',
                    700: 'oklch(0.47 0.12 200)',
                    800: 'oklch(0.38 0.09 200)',
                    900: 'oklch(0.29 0.06 200)',
                    950: 'oklch(0.20 0.03 200)',
                },
                'sa-gold': {
                    500: 'oklch(0.8 0.12 85)', // South African accent
                },
                'tenant': {
                    blue: 'oklch(0.65 0.18 230)',
                    green: 'oklch(0.75 0.16 140)',
                    orange: 'oklch(0.75 0.15 45)',
                }
            },
            backdropBlur: {
                'xs': '2px',
            },
            // Monday.com-inspired spacing (8px grid)
            spacing: {
                '18': '4.5rem',   // 72px
                '22': '5.5rem',   // 88px
            },
            // Monday.com-inspired border radius
            borderRadius: {
                'xl': '12px',
                '2xl': '16px',
                '3xl': '24px',
            },
            // Monday.com-inspired shadows
            boxShadow: {
                'sm': '0 2px 4px rgba(0,0,0,0.04)',
                'md': '0 4px 12px rgba(0,0,0,0.08)',
                'lg': '0 8px 24px rgba(0,0,0,0.12)',
                'xl': '0 12px 32px rgba(0,0,0,0.16)',
                'button': '0 4px 8px rgba(0,0,0,0.1)',
                'button-hover': '0 8px 16px rgba(0,0,0,0.15)',
            },
            // Monday.com smooth animations
            animation: {
                'fade-in': 'fadeIn 0.3s ease-in-out',
                'slide-up': 'slideUp 0.3s ease-out',
                'slide-down': 'slideDown 0.3s ease-out',
                'scale-in': 'scaleIn 0.2s ease-out',
                'bounce-subtle': 'bounceSubtle 0.5s ease-out',
                'pulse-ring': 'pulseRing 2s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                'progress-fill': 'progressFill 0.5s ease-out',
                'glass-float': 'glassFloat 6s ease-in-out infinite',
                'metric-up': 'metricUp 0.6s ease-out',
                'checkmark': 'checkmark 0.4s ease-out',
                'confetti': 'confetti 0.6s ease-out',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                slideUp: {
                    '0%': { transform: 'translateY(20px)', opacity: '0' },
                    '100%': { transform: 'translateY(0)', opacity: '1' },
                },
                slideDown: {
                    '0%': { transform: 'translateY(-20px)', opacity: '0' },
                    '100%': { transform: 'translateY(0)', opacity: '1' },
                },
                scaleIn: {
                    '0%': { transform: 'scale(0.95)', opacity: '0' },
                    '100%': { transform: 'scale(1)', opacity: '1' },
                },
                bounceSubtle: {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-10px)' },
                },
                pulseRing: {
                    '0%': { transform: 'scale(1)', opacity: '1' },
                    '50%': { transform: 'scale(1.05)', opacity: '0.7' },
                    '100%': { transform: 'scale(1)', opacity: '1' },
                },
                progressFill: {
                    '0%': { width: '0%' },
                },
                glassFloat: {
                    '0%, 100%': { transform: 'translateY(0px)' },
                    '50%': { transform: 'translateY(-5px)' },
                },
                metricUp: {
                    '0%': { transform: 'translateY(20px) scale(0.8)', opacity: '0' },
                    '100%': { transform: 'translateY(0) scale(1)', opacity: '1' },
                },
                checkmark: {
                    '0%': { strokeDashoffset: '100' },
                    '100%': { strokeDashoffset: '0' },
                },
                confetti: {
                    '0%': { transform: 'translateY(0) rotate(0deg)', opacity: '1' },
                    '100%': { transform: 'translateY(-100px) rotate(360deg)', opacity: '0' },
                },
            },
            // Monday.com smooth transitions
            transitionTimingFunction: {
                'smooth': 'cubic-bezier(0.4, 0, 0.2, 1)',
            },
            transitionDuration: {
                '400': '400ms',
            },
            // Gradient backgrounds (using hex for compatibility)
            backgroundImage: {
                'commatix-gradient': 'linear-gradient(135deg, #3B82F6 0%, #6366F1 100%)',
                'commatix-gradient-vibrant': 'linear-gradient(135deg, #3B82F6 0%, #F59E0B 100%)',
                'commatix-text': 'linear-gradient(135deg, #2563EB 0%, #7C3AED 50%, #DB2777 100%)',
            }
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
    ],
};