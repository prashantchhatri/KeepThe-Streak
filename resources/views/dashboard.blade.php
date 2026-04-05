<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-slate-900 dark:text-slate-100">
            {{ __('Dashboard') }}
        </h2>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ __('Your KeepTheStreak workspace.') }}</p>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center shadow-sm shadow-slate-200 sm:p-14 dark:border-slate-700 dark:bg-slate-900 dark:shadow-black/20">
                <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-100">{{ __('Dashboard placeholder') }}</h3>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                    {{ __('Widgets, streak stats, and activity cards will be added here next.') }}
                </p>
                <p class="mt-4 text-xs font-medium uppercase tracking-[0.16em] text-indigo-500">
                    {{ __('Coming soon') }}
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
