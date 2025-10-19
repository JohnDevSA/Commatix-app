@extends('layouts.onboarding-modern')

@section('title', 'Welcome to Commatix!')

@section('content')
<div x-data="celebrationPage()" x-init="init()" class="slide-enter">
    <!-- Confetti Canvas -->
    <canvas id="confetti-canvas" class="fixed inset-0 pointer-events-none z-50"></canvas>

    <!-- Success Card -->
    <div class="question-card p-8 md:p-12 text-center">
        <!-- Animated Success Icon -->
        <div class="mb-8 flex justify-center">
            <div class="relative">
                <!-- Pulsing rings -->
                <div class="absolute inset-0 rounded-full bg-green-400 opacity-25 animate-ping"></div>
                <div class="absolute inset-0 rounded-full bg-green-400 opacity-50 animate-pulse"></div>

                <!-- Success checkmark -->
                <div class="relative w-32 h-32 rounded-full bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center shadow-2xl">
                    <svg class="w-16 h-16 text-white animate-checkmark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Main Heading -->
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4 animate-fade-in">
            🎉 You're all set!
        </h1>

        <p class="text-xl text-gray-600 mb-8 animate-fade-in" style="animation-delay: 0.2s;">
            Welcome to Commatix, <span class="font-semibold text-gray-900">{{ $tenant->name ?? auth()->user()->name }}</span>
        </p>

        <!-- What's Next Section -->
        <div class="max-w-2xl mx-auto mb-8 animate-fade-in" style="animation-delay: 0.4s;">
            <div class="p-6 bg-gradient-to-r from-blue-50 to-purple-50 rounded-2xl border-2 border-blue-200">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">What happens now?</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-left">
                    <!-- Step 1 -->
                    <div class="bg-white rounded-xl p-4 shadow-sm">
                        <div class="w-10 h-10 rounded-lg bg-blue-600 flex items-center justify-center mb-3">
                            <span class="text-white font-bold">1</span>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-2">Explore your dashboard</h3>
                        <p class="text-sm text-gray-600">Get familiar with your new workspace</p>
                    </div>

                    <!-- Step 2 -->
                    <div class="bg-white rounded-xl p-4 shadow-sm">
                        <div class="w-10 h-10 rounded-lg bg-purple-600 flex items-center justify-center mb-3">
                            <span class="text-white font-bold">2</span>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-2">Invite your team</h3>
                        <p class="text-sm text-gray-600">Start collaborating with colleagues</p>
                    </div>

                    <!-- Step 3 -->
                    <div class="bg-white rounded-xl p-4 shadow-sm">
                        <div class="w-10 h-10 rounded-lg bg-green-600 flex items-center justify-center mb-3">
                            <span class="text-white font-bold">3</span>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-2">Launch your first campaign</h3>
                        <p class="text-sm text-gray-600">Put Commatix to work for you</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Setup Summary -->
        <div class="max-w-xl mx-auto mb-8 text-left animate-fade-in" style="animation-delay: 0.6s;">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Your setup summary</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-sm font-medium text-gray-900">Company information</span>
                    </div>
                    <span class="text-xs text-green-600 font-medium">Complete</span>
                </div>

                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-sm font-medium text-gray-900">Team structure</span>
                    </div>
                    <span class="text-xs text-green-600 font-medium">Complete</span>
                </div>

                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-sm font-medium text-gray-900">{{ session('onboarding_step_3.use_case_label', 'Use case configured') }}</span>
                    </div>
                    <span class="text-xs text-green-600 font-medium">Complete</span>
                </div>

                @if(session('onboarding_step_6.plan'))
                    <div class="flex items-center justify-between p-3 bg-gradient-to-r from-purple-50 to-blue-50 rounded-lg border border-purple-200">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                            </svg>
                            <div class="flex-1">
                                <span class="text-sm font-medium text-gray-900">{{ ucfirst(session('onboarding_step_6.plan')) }} plan</span>
                                <div class="text-xs text-purple-600 font-medium">14-day free trial active</div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- CTA Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center animate-fade-in" style="animation-delay: 0.8s;">
            <a href="{{ route('filament.app.pages.dashboard') }}"
               class="btn-primary-modern inline-flex items-center text-lg px-8 py-4 justify-center">
                Go to Dashboard
                <svg class="w-6 h-6 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>

            <a href="{{ route('filament.app.resources.users.index') }}"
               class="btn-secondary-modern inline-flex items-center text-lg px-8 py-4 justify-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Invite Team
            </a>
        </div>

        <!-- Help Section -->
        <div class="mt-12 pt-8 border-t border-gray-200 animate-fade-in" style="animation-delay: 1s;">
            <p class="text-sm text-gray-600 mb-4">Need help getting started?</p>
            <div class="flex flex-wrap gap-4 justify-center">
                <a href="/docs" class="text-sm text-blue-600 hover:text-blue-700 font-medium inline-flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    View Documentation
                </a>
                <a href="/support" class="text-sm text-blue-600 hover:text-blue-700 font-medium inline-flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z"/>
                        <path d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z"/>
                    </svg>
                    Contact Support
                </a>
                <a href="/tutorials" class="text-sm text-blue-600 hover:text-blue-700 font-medium inline-flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                    </svg>
                    Watch Tutorials
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes checkmark {
        0% {
            stroke-dasharray: 0 100;
            stroke-dashoffset: 0;
        }
        100% {
            stroke-dasharray: 100 100;
            stroke-dashoffset: 0;
        }
    }

    .animate-checkmark {
        stroke-dasharray: 100;
        animation: checkmark 0.6s ease-out 0.3s forwards;
    }

    @keyframes fade-in {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in {
        animation: fade-in 0.6s ease-out forwards;
        opacity: 0;
    }
</style>

@push('scripts')
<script>
function celebrationPage() {
    return {
        init() {
            // Start confetti animation
            this.startConfetti();

            // Play success sound (optional)
            // this.playSuccessSound();
        },

        startConfetti() {
            const canvas = document.getElementById('confetti-canvas');
            const ctx = canvas.getContext('2d');

            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;

            const confettiPieces = [];
            const confettiCount = 150;
            const colors = [
                '#3B82F6', // blue
                '#8B5CF6', // purple
                '#10B981', // green
                '#F59E0B', // orange
                '#EC4899', // pink
                '#6366F1', // indigo
            ];

            class ConfettiPiece {
                constructor() {
                    this.x = Math.random() * canvas.width;
                    this.y = Math.random() * canvas.height - canvas.height;
                    this.size = Math.random() * 8 + 5;
                    this.speedY = Math.random() * 3 + 2;
                    this.speedX = Math.random() * 2 - 1;
                    this.color = colors[Math.floor(Math.random() * colors.length)];
                    this.rotation = Math.random() * 360;
                    this.rotationSpeed = Math.random() * 10 - 5;
                }

                update() {
                    this.y += this.speedY;
                    this.x += this.speedX;
                    this.rotation += this.rotationSpeed;

                    if (this.y > canvas.height) {
                        this.y = -10;
                        this.x = Math.random() * canvas.width;
                    }
                }

                draw() {
                    ctx.save();
                    ctx.translate(this.x, this.y);
                    ctx.rotate(this.rotation * Math.PI / 180);
                    ctx.fillStyle = this.color;
                    ctx.fillRect(-this.size / 2, -this.size / 2, this.size, this.size);
                    ctx.restore();
                }
            }

            // Create confetti pieces
            for (let i = 0; i < confettiCount; i++) {
                confettiPieces.push(new ConfettiPiece());
            }

            // Animation loop
            let animationFrameId;
            let startTime = Date.now();
            const duration = 5000; // 5 seconds

            function animate() {
                const elapsed = Date.now() - startTime;

                if (elapsed < duration) {
                    ctx.clearRect(0, 0, canvas.width, canvas.height);

                    confettiPieces.forEach(piece => {
                        piece.update();
                        piece.draw();
                    });

                    animationFrameId = requestAnimationFrame(animate);
                } else {
                    // Fade out
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    cancelAnimationFrame(animationFrameId);
                }
            }

            animate();

            // Handle window resize
            window.addEventListener('resize', () => {
                canvas.width = window.innerWidth;
                canvas.height = window.innerHeight;
            });
        },

        playSuccessSound() {
            // Optional: Play a success sound
            // const audio = new Audio('/sounds/success.mp3');
            // audio.play();
        }
    }
}
</script>
@endpush
