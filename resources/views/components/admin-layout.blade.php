<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0f172a">
    <link rel="icon" type="image/png" href="/images/logo.png">
    <link rel="apple-touch-icon" href="/images/logo.png">

    <title>{{ config('app.name', 'KeepTheStreak') }} Admin</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @include('layouts.partials.theme-head')

    @php
        $hasViteAssets = file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot'));
    @endphp

    @if ($hasViteAssets)
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script>
            window.tailwind = window.tailwind || {};
            window.tailwind.config = {
                darkMode: 'class',
            };
        </script>
        <script src="https://cdn.tailwindcss.com"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @endif
</head>
<body class="bg-slate-100 font-sans antialiased text-slate-900 dark:bg-slate-950 dark:text-slate-100">
    <div class="min-h-screen bg-[radial-gradient(circle_at_top_left,_rgba(99,102,241,0.16),_transparent_30%),radial-gradient(circle_at_bottom_right,_rgba(14,165,233,0.12),_transparent_28%)] dark:bg-[radial-gradient(circle_at_top_left,_rgba(99,102,241,0.24),_transparent_28%),radial-gradient(circle_at_bottom_right,_rgba(14,165,233,0.18),_transparent_26%)]">
        <header class="border-b border-slate-200/80 bg-white/85 backdrop-blur dark:border-slate-800 dark:bg-slate-950/85">
            <div class="mx-auto flex w-full max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <img src="/images/logo.png" alt="KeepTheStreak" class="h-11 w-auto rounded-xl">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-500">{{ __('Admin') }}</p>
                        <h1 class="text-lg font-semibold text-slate-900 dark:text-slate-100">{{ config('app.name', 'KeepTheStreak') }}</h1>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <x-theme-toggle class="px-3" />

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="inline-flex min-h-10 items-center justify-center rounded-xl bg-slate-100 px-4 text-sm font-semibold text-slate-700 transition duration-200 hover:bg-slate-200 active:scale-95 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                            {{ __('Logout') }}
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <main class="mx-auto w-full max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            {{ $slot }}
        </main>
    </div>
</body>
</html>
