<!DOCTYPE html>
<html lang="en" class="h-full bg-gradient-to-br from-blue-50 via-white to-purple-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Get Started') - Commatix</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        /* Monday.com-inspired minimal progress bar */
        .progress-bar-container {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: rgba(148, 163, 184, 0.2);
            z-index: 100;
        }

        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #3B82F6 0%, #8B5CF6 100%);
            transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Monday.com card styling */
        .question-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        /* Modern input styling */
        .modern-input {
            width: 100%;
            padding: 16px 20px;
            font-size: 18px;
            border: 2px solid #E5E7EB;
            border-radius: 12px;
            transition: all 0.2s ease;
            background: white;
        }

        .modern-input:hover {
            border-color: #C7D2FE;
            background: #FAFAFA;
        }

        .modern-input:focus {
            outline: none;
            border-color: #6366F1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
            background: white;
        }

        /* Monday-style primary button */
        .btn-primary-modern {
            background: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 100%);
            color: white;
            font-weight: 600;
            font-size: 16px;
            padding: 16px 32px;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .btn-primary-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
        }

        .btn-primary-modern:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
        }

        .btn-primary-modern:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        /* Secondary button */
        .btn-secondary-modern {
            background: white;
            color: #6B7280;
            font-weight: 600;
            font-size: 16px;
            padding: 16px 32px;
            border-radius: 12px;
            border: 2px solid #E5E7EB;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-secondary-modern:hover {
            border-color: #C7D2FE;
            background: #F9FAFB;
        }

        /* Slide transitions */
        .slide-enter {
            animation: slideInRight 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .slide-exit {
            animation: slideOutLeft 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideOutLeft {
            from {
                opacity: 1;
                transform: translateX(0);
            }
            to {
                opacity: 0;
                transform: translateX(-30px);
            }
        }

        /* Option card for selections */
        .option-card {
            padding: 20px;
            border: 2px solid #E5E7EB;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            background: white;
        }

        .option-card:hover {
            border-color: #C7D2FE;
            background: #FAFAFA;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
        }

        .option-card.selected {
            border-color: #6366F1;
            background: #EEF2FF;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        /* Pulse animation for progress steps */
        @keyframes pulseRing {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.4);
            }
            50% {
                box-shadow: 0 0 0 8px rgba(99, 102, 241, 0);
            }
        }

        .pulse-ring {
            animation: pulseRing 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
</head>
<body class="h-full overflow-hidden">
    <!-- Fixed Progress Bar -->
    <div class="progress-bar-container">
        <div class="progress-bar" style="width: {{ $progressPercentage ?? 0 }}%"></div>
    </div>

    <!-- Main Container -->
    <div class="h-full flex flex-col">
        <!-- Minimal Header -->
        <header class="flex-shrink-0 pt-8 pb-4 px-4">
            <div class="max-w-2xl mx-auto flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <svg class="w-8 h-8" style="color: #3B82F6;" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"/>
                    </svg>
                    <span class="text-xl font-bold text-gray-900">Commatix</span>
                </div>

                <button type="button"
                        onclick="if(confirm('Are you sure you want to exit? Your progress will be saved.')) { window.location.href='{{ route('filament.app.auth.logout') }}'; }"
                        class="text-sm text-gray-500 hover:text-gray-700 transition-colors">
                    Exit
                </button>
            </div>
        </header>

        <!-- Content Area -->
        <main class="flex-1 overflow-y-auto">
            <div class="max-w-2xl mx-auto px-4 py-8">
                @yield('content')
            </div>
        </main>

        <!-- Progress Indicator (Minimal) -->
        <footer class="flex-shrink-0 py-6 px-4">
            <div class="max-w-2xl mx-auto text-center">
                <div class="text-sm text-gray-500 mb-2">
                    Step {{ $currentStep ?? 1 }} of {{ $totalSteps ?? 6 }}
                </div>
                <div class="flex items-center justify-center space-x-2">
                    @for($i = 1; $i <= ($totalSteps ?? 6); $i++)
                        <div class="h-2 rounded-full transition-all duration-300 {{ $i <= ($currentStep ?? 1) ? 'w-8 bg-gradient-to-r from-blue-500 to-purple-600' : 'w-2 bg-gray-300' }}"></div>
                    @endfor
                </div>
            </div>
        </footer>
    </div>

    @stack('scripts')
</body>
</html>
