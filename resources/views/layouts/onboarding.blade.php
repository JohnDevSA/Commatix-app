<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Get Started') - Commatix</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Glassmorphism effects */
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .glass-input {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(148, 163, 184, 0.2);
            transition: all 0.3s ease;
        }

        .glass-input:focus {
            background: rgba(255, 255, 255, 0.9);
            border-color: oklch(0.65 0.18 200);
            box-shadow: 0 0 0 3px rgba(100, 149, 237, 0.1);
        }

        /* Commatix brand colors */
        .bg-commatix-500 {
            background-color: oklch(0.65 0.18 200);
        }

        .text-commatix-600 {
            color: oklch(0.56 0.15 200);
        }

        .bg-sa-gold {
            background-color: oklch(0.8 0.12 85);
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }

        /* Progress indicator */
        .step-indicator {
            position: relative;
        }

        .step-indicator::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 2px;
            background: rgba(148, 163, 184, 0.3);
            z-index: 0;
        }

        .step-circle {
            position: relative;
            z-index: 1;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .step-circle.completed {
            background: oklch(0.65 0.18 200);
            color: white;
        }

        .step-circle.current {
            background: white;
            border: 3px solid oklch(0.65 0.18 200);
            color: oklch(0.65 0.18 200);
            box-shadow: 0 0 0 4px rgba(100, 149, 237, 0.1);
        }

        .step-circle.upcoming {
            background: rgba(148, 163, 184, 0.2);
            color: rgba(71, 85, 105, 0.6);
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
        <main class="flex-1 py-12">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Progress Steps -->
                <div class="mb-12">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Setup Progress</h2>
                        <span class="text-sm font-medium text-commatix-600">
                            {{ $completionPercentage }}% Complete
                        </span>
                    </div>

                    <!-- Progress Bar -->
                    <div class="w-full bg-gray-200 rounded-full h-2 mb-8 overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-commatix-500 to-purple-500 rounded-full transition-all duration-500"
                             style="width: {{ $completionPercentage }}%"></div>
                    </div>

                    <!-- Step Indicators -->
                    <div class="step-indicator flex items-center justify-between">
                        @foreach([
                            1 => 'Company',
                            2 => 'Team',
                            3 => 'Use Case',
                            4 => 'Integrations',
                            5 => 'POPIA',
                            6 => 'Pricing'
                        ] as $stepNum => $stepLabel)
                            <div class="flex flex-col items-center">
                                <div class="step-circle {{ $stepNum < $currentStep ? 'completed' : ($stepNum == $currentStep ? 'current' : 'upcoming') }}">
                                    @if($stepNum < $currentStep)
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    @else
                                        {{ $stepNum }}
                                    @endif
                                </div>
                                <span class="mt-2 text-xs font-medium {{ $stepNum == $currentStep ? 'text-gray-900' : 'text-gray-500' }}">
                                    {{ $stepLabel }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Success/Error Messages -->
                @if(session('success'))
                    <div class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200 text-green-800 animate-fade-in">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            {{ session('success') }}
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 text-red-800 animate-fade-in">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            {{ session('error') }}
                        </div>
                    </div>
                @endif

                <!-- Step Content -->
                <div class="glass-card rounded-2xl p-8 animate-fade-in">
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
