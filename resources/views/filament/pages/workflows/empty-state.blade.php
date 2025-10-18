{{--
  Commatix Workflows - Empty State Component

  Beautiful, engaging empty state following Commatix design system
  with glassmorphism aesthetic and South African UX standards.
--}}

<div class="flex items-center justify-center min-h-[60vh] p-6 animate-fade-in">
    <div class="max-w-2xl w-full text-center">

        {{-- Glass Card Container --}}
        <div class="glass-card p-12 animate-slide-up">

            {{-- Icon with Floating Animation --}}
            <div class="flex justify-center mb-8">
                <div class="relative">
                    {{-- Background Glow --}}
                    <div class="absolute inset-0 bg-commatix-500/20 rounded-full blur-2xl animate-pulse"></div>

                    {{-- Icon Container --}}
                    <div class="relative bg-gradient-to-br from-commatix-500 to-commatix-600
                                rounded-full p-8 shadow-2xl animate-glass-float">
                        <svg class="w-16 h-16 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Heading --}}
            <h2 class="text-3xl font-bold text-commatix-950 dark:text-commatix-50 mb-4">
                No Workflow Templates Yet
            </h2>

            {{-- Description --}}
            <p class="text-base text-commatix-700 dark:text-commatix-300 mb-8 max-w-lg mx-auto leading-relaxed">
                Get started by creating your first custom workflow template, or browse our
                library of industry-standard templates designed for South African businesses.
            </p>

            {{-- Action Buttons (Right-aligned in their container) --}}
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                {{-- Primary Action --}}
                <a href="{{ route('filament.app.resources.tenant-workflow-templates.create') }}"
                   class="inline-flex items-center gap-2 px-6 py-3
                          bg-commatix-500 hover:bg-commatix-600
                          text-white font-medium rounded-lg
                          shadow-lg hover:shadow-xl
                          transition-all duration-200
                          hover:scale-105 active:scale-95
                          focus:outline-none focus:ring-2 focus:ring-commatix-500 focus:ring-offset-2">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Create Your First Workflow</span>
                </a>

                {{-- Secondary Action --}}
                <a href="{{ route('filament.app.resources.tenant-workflow-templates.index') }}"
                   class="inline-flex items-center gap-2 px-6 py-3
                          bg-white dark:bg-commatix-900
                          hover:bg-commatix-50 dark:hover:bg-commatix-800
                          text-commatix-700 dark:text-commatix-300
                          font-medium rounded-lg
                          border border-commatix-300 dark:border-commatix-700
                          shadow hover:shadow-lg
                          transition-all duration-200
                          hover:scale-105 active:scale-95
                          focus:outline-none focus:ring-2 focus:ring-commatix-500 focus:ring-offset-2">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                    <span>Browse Template Library</span>
                </a>
            </div>

            {{-- Helpful Tips Section --}}
            <div class="mt-12 pt-8 border-t border-commatix-200 dark:border-commatix-800">
                <h3 class="text-sm font-semibold text-commatix-900 dark:text-commatix-100 mb-4">
                    Quick Tips to Get Started
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-left">
                    {{-- Tip 1 --}}
                    <div class="flex gap-3 p-4 rounded-lg bg-commatix-50/50 dark:bg-commatix-900/50
                                border border-commatix-100 dark:border-commatix-800">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 rounded-full bg-commatix-500/20 flex items-center justify-center">
                                <span class="text-commatix-600 dark:text-commatix-400 font-bold text-sm">1</span>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-commatix-900 dark:text-commatix-100 mb-1">
                                Choose Your Starting Point
                            </h4>
                            <p class="text-xs text-commatix-600 dark:text-commatix-400">
                                Create from scratch or copy an industry template
                            </p>
                        </div>
                    </div>

                    {{-- Tip 2 --}}
                    <div class="flex gap-3 p-4 rounded-lg bg-commatix-50/50 dark:bg-commatix-900/50
                                border border-commatix-100 dark:border-commatix-800">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 rounded-full bg-commatix-500/20 flex items-center justify-center">
                                <span class="text-commatix-600 dark:text-commatix-400 font-bold text-sm">2</span>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-commatix-900 dark:text-commatix-100 mb-1">
                                Add Milestones
                            </h4>
                            <p class="text-xs text-commatix-600 dark:text-commatix-400">
                                Break your workflow into manageable steps
                            </p>
                        </div>
                    </div>

                    {{-- Tip 3 --}}
                    <div class="flex gap-3 p-4 rounded-lg bg-commatix-50/50 dark:bg-commatix-900/50
                                border border-commatix-100 dark:border-commatix-800">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 rounded-full bg-commatix-500/20 flex items-center justify-center">
                                <span class="text-commatix-600 dark:text-commatix-400 font-bold text-sm">3</span>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-commatix-900 dark:text-commatix-100 mb-1">
                                Publish & Share
                            </h4>
                            <p class="text-xs text-commatix-600 dark:text-commatix-400">
                                Make it available to your team members
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

<style>
    /* Custom pulse animation for glow effect */
    @keyframes pulse {
        0%, 100% {
            opacity: 0.6;
        }
        50% {
            opacity: 0.3;
        }
    }
</style>
