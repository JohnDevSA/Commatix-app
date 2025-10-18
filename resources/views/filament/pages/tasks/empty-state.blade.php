{{--
  Commatix Tasks - Empty State Component

  Task-focused empty state with productivity theme,
  following Commatix design system and South African UX standards.
--}}

<div class="flex items-center justify-center min-h-[60vh] p-6 animate-fade-in">
    <div class="max-w-2xl w-full text-center">

        {{-- Glass Card Container --}}
        <div class="glass-card p-12 animate-slide-up">

            {{-- Icon with Floating Animation --}}
            <div class="flex justify-center mb-8">
                <div class="relative">
                    {{-- Background Glow --}}
                    <div class="absolute inset-0 bg-tenant-green/20 rounded-full blur-2xl animate-pulse"></div>

                    {{-- Icon Container with Checkmark Theme --}}
                    <div class="relative bg-gradient-to-br from-green-500 to-green-600
                                rounded-full p-8 shadow-2xl animate-glass-float">
                        <svg class="w-16 h-16 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Heading --}}
            <h2 class="text-3xl font-bold text-commatix-950 dark:text-commatix-50 mb-4">
                Your Task List is Empty
            </h2>

            {{-- Description --}}
            <p class="text-base text-commatix-700 dark:text-commatix-300 mb-8 max-w-lg mx-auto leading-relaxed">
                Tasks are automatically created from workflow templates. Start a workflow or create a standalone task to get organized.
            </p>

            {{-- Action Buttons --}}
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                {{-- Primary Action: Create Task --}}
                <a href="{{ route('filament.app.resources.tasks.create') }}"
                   class="inline-flex items-center gap-2 px-6 py-3
                          bg-green-500 hover:bg-green-600
                          text-white font-medium rounded-lg
                          shadow-lg hover:shadow-xl
                          transition-all duration-200
                          hover:scale-105 active:scale-95
                          focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>Create a Task</span>
                </a>

                {{-- Secondary Action: View Workflows --}}
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
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                    </svg>
                    <span>Browse Workflows</span>
                </a>
            </div>

            {{-- Helpful Information Section --}}
            <div class="mt-12 pt-8 border-t border-commatix-200 dark:border-commatix-800">
                <h3 class="text-sm font-semibold text-commatix-900 dark:text-commatix-100 mb-6">
                    How Tasks Work in Commatix
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-left">
                    {{-- Info Card 1: Workflow Tasks --}}
                    <div class="flex gap-4 p-5 rounded-lg bg-gradient-to-br from-commatix-50/80 to-commatix-100/50
                                dark:from-commatix-900/50 dark:to-commatix-800/30
                                border border-commatix-200 dark:border-commatix-700
                                hover:shadow-md transition-shadow duration-200">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-lg bg-commatix-500/20 flex items-center justify-center">
                                <svg class="w-6 h-6 text-commatix-600 dark:text-commatix-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-commatix-900 dark:text-commatix-100 mb-2">
                                Workflow-Generated Tasks
                            </h4>
                            <p class="text-xs text-commatix-600 dark:text-commatix-400 leading-relaxed">
                                Start a workflow to automatically create tasks for each milestone. Tasks are assigned based on divisions and approval groups.
                            </p>
                        </div>
                    </div>

                    {{-- Info Card 2: Standalone Tasks --}}
                    <div class="flex gap-4 p-5 rounded-lg bg-gradient-to-br from-green-50/80 to-green-100/50
                                dark:from-green-900/20 dark:to-green-800/10
                                border border-green-200 dark:border-green-700/50
                                hover:shadow-md transition-shadow duration-200">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-lg bg-green-500/20 flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0118 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.25m-12 0A2.25 2.25 0 005.25 18.75h13.5A2.25 2.25 0 0021 16.5v-13.5" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-commatix-900 dark:text-commatix-100 mb-2">
                                Standalone Tasks
                            </h4>
                            <p class="text-xs text-commatix-600 dark:text-commatix-400 leading-relaxed">
                                Create individual tasks for quick to-dos or one-off assignments. Perfect for ad-hoc work outside of formal workflows.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Quick Stats / Features --}}
                <div class="mt-8 flex flex-wrap justify-center gap-6 text-xs text-commatix-600 dark:text-commatix-400">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" />
                        </svg>
                        <span>Priority Management</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" />
                        </svg>
                        <span>Due Date Tracking</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" />
                        </svg>
                        <span>Assignment to Users/Divisions</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" />
                        </svg>
                        <span>Progress Tracking</span>
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
