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
            animation: {
                'fade-in': 'fadeIn 0.3s ease-in-out',
                'slide-up': 'slideUp 0.3s ease-out',
                'glass-float': 'glassFloat 6s ease-in-out infinite',
                'metric-up': 'metricUp 0.6s ease-out',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                slideUp: {
                    '0%': { transform: 'translateY(10px)', opacity: '0' },
                    '100%': { transform: 'translateY(0)', opacity: '1' },
                },
                glassFloat: {
                    '0%, 100%': { transform: 'translateY(0px)' },
                    '50%': { transform: 'translateY(-5px)' },
                },
                metricUp: {
                    '0%': { transform: 'translateY(20px) scale(0.8)', opacity: '0' },
                    '100%': { transform: 'translateY(0) scale(1)', opacity: '1' },
                }
            }
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
    ],
};