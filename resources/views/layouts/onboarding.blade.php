<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Get Started') - Commatix</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Monday.com-inspired glassmorphism effects */
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .glass-card:hover {
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }

        .glass-input {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(148, 163, 184, 0.3);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .glass-input:hover {
            background: rgba(255, 255, 255, 0.9);
            border-color: rgba(148, 163, 184, 0.4);
        }

        .glass-input:focus {
            background: rgba(255, 255, 255, 1);
            border-color: oklch(0.65 0.18 200);
            box-shadow: 0 0 0 4px rgba(100, 149, 237, 0.1);
            transform: translateY(-1px);
        }

        /* Commatix brand colors */
        .bg-commatix-500 {
            background-color: oklch(0.65 0.18 200);
        }

        .bg-commatix-gradient {
            background: linear-gradient(135deg, oklch(0.65 0.18 200) 0%, oklch(0.56 0.15 220) 100%);
        }

        .text-commatix-600 {
            color: oklch(0.56 0.15 200);
        }

        .bg-sa-gold {
            background-color: oklch(0.8 0.12 85);
        }

        /* Monday.com-style progress indicator */
        .step-indicator {
            position: relative;
            padding: 0 20px;
        }

        .step-indicator::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 50px;
            right: 50px;
            height: 3px;
            background: linear-gradient(to right,
                oklch(0.65 0.18 200) 0%,
                oklch(0.65 0.18 200) var(--progress, 0%),
                rgba(148, 163, 184, 0.2) var(--progress, 0%),
                rgba(148, 163, 184, 0.2) 100%
            );
            z-index: 0;
            border-radius: 2px;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .step-circle {
            position: relative;
            z-index: 1;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 16px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }

        .step-circle.completed {
            background: oklch(0.65 0.18 200);
            color: white;
            box-shadow: 0 4px 12px rgba(0, 115, 234, 0.3);
            animation: bounceIn 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        .step-circle.completed:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 16px rgba(0, 115, 234, 0.4);
        }

        .step-circle.current {
            background: white;
            border: 4px solid oklch(0.65 0.18 200);
            color: oklch(0.65 0.18 200);
            box-shadow: 0 0 0 6px rgba(100, 149, 237, 0.15);
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        .step-circle.upcoming {
            background: rgba(148, 163, 184, 0.15);
            color: rgba(71, 85, 105, 0.5);
            border: 2px solid rgba(148, 163, 184, 0.3);
        }

        .step-circle.upcoming:hover {
            background: rgba(148, 163, 184, 0.2);
        }

        @keyframes bounceIn {
            0% { transform: scale(0); opacity: 0; }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); opacity: 1; }
        }

        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 6px rgba(100, 149, 237, 0.15); }
            50% { box-shadow: 0 0 0 8px rgba(100, 149, 237, 0.25); }
        }

        /* Monday.com button styles */
        .btn-monday-primary {
            background: linear-gradient(135deg, oklch(0.65 0.18 200) 0%, oklch(0.56 0.15 220) 100%);
            color: white;
            font-weight: 600;
            padding: 12px 32px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
        }

        .btn-monday-primary:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px) scale(1.02);
        }

        .btn-monday-primary:active {
            transform: translateY(0) scale(0.98);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .btn-monday-secondary {
            background: white;
            color: oklch(0.65 0.18 200);
            font-weight: 600;
            padding: 12px 24px;
            border-radius: 8px;
            border: 2px solid rgba(148, 163, 184, 0.3);
            transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-monday-secondary:hover {
            background: rgba(100, 149, 237, 0.05);
            border-color: oklch(0.65 0.18 200);
            transform: translateY(-1px);
        }
    </style>
</head>
<body class="h-full bg-gradient-to-br from-blue-50 via-white to-purple-50">
    <div class="min-h-full flex flex-col">
        <!-- Header -->
        <header class="bg-white/80 backdrop-blur-md border-b border-gray-200/50 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <svg class="w-8 h-8 text-commatix-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"/>
                        </svg>
                        <h1 class="text-2xl font-bold text-gray-900">Commatix</h1>
                    </div>

                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-600">{{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('filament.app.auth.logout') }}">
                            @csrf
                            <button type="submit" class="text-sm text-gray-500 hover:text-gray-700">
                                Sign Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 py-16">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Progress Steps -->
                <div class="mb-16">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 mb-1">Let's get you set up</h2>
                            <p class="text-sm text-gray-600">We'll have you up and running in just a few minutes</p>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl font-bold text-commatix-600 mb-1">{{ $completionPercentage }}%</div>
                            <div class="text-xs text-gray-500 uppercase tracking-wide">Complete</div>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="w-full bg-gray-200 rounded-full h-3 mb-12 overflow-hidden shadow-inner">
                        <div class="h-full bg-gradient-to-r from-commatix-500 via-commatix-600 to-purple-500 rounded-full transition-all duration-700 ease-out shadow-sm animate-progress-fill"
                             style="width: {{ $completionPercentage }}%"></div>
                    </div>

                    <!-- Step Indicators -->
                    <div class="step-indicator flex items-center justify-between" style="--progress: {{ ($currentStep - 1) * 20 }}%">
                        @foreach([
                            1 => 'Company',
                            2 => 'Team',
                            3 => 'Use Case',
                            4 => 'Integrations',
                            5 => 'POPIA',
                            6 => 'Pricing'
                        ] as $stepNum => $stepLabel)
                            <div class="flex flex-col items-center space-y-3">
                                <div class="step-circle {{ $stepNum < $currentStep ? 'completed' : ($stepNum == $currentStep ? 'current' : 'upcoming') }}"
                                     title="{{ $stepLabel }}">
                                    @if($stepNum < $currentStep)
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    @else
                                        {{ $stepNum }}
                                    @endif
                                </div>
                                <span class="text-xs font-semibold whitespace-nowrap {{ $stepNum == $currentStep ? 'text-gray-900' : ($stepNum < $currentStep ? 'text-commatix-600' : 'text-gray-400') }}">
                                    {{ $stepLabel }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Success/Error Messages -->
                @if(session('success'))
                    <div class="mb-8 p-5 rounded-2xl bg-green-50 border-2 border-green-200 text-green-800 animate-slide-down shadow-md">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-green-500 flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-8 p-5 rounded-2xl bg-red-50 border-2 border-red-200 text-red-800 animate-slide-down shadow-md">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-500 flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Step Content -->
                <div class="glass-card rounded-3xl p-12 animate-scale-in">
                    @yield('content')
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="py-6 text-center text-sm text-gray-500">
            <p>&copy; {{ date('Y') }} Commatix. Proudly South African.</p>
        </footer>
    </div>

    @stack('scripts')
</body>
</html>
