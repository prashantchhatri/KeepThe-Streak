<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#4f46e5">
    <link rel="icon" type="image/png" href="/images/logo.png">
    <link rel="apple-touch-icon" href="/images/logo.png">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @include('layouts.partials.theme-head')

    <!-- Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
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
    <div class="relative min-h-screen overflow-hidden bg-gradient-to-b from-slate-100 via-white to-indigo-50 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950">
        <div class="pointer-events-none absolute -left-12 -top-20 h-72 w-72 rounded-full bg-indigo-100/80 blur-3xl dark:bg-indigo-500/10"></div>
        <div class="pointer-events-none absolute -bottom-28 right-0 h-96 w-96 rounded-full bg-slate-200/70 blur-3xl dark:bg-slate-700/30"></div>

        <div class="relative mx-auto flex min-h-screen w-full max-w-md flex-col px-4 py-8">
            <div class="mb-4 flex justify-end">
                <x-theme-toggle />
            </div>

            <div class="flex-1 flex items-center">
                <section class="w-full rounded-2xl border border-slate-200/90 bg-white/90 p-6 shadow-xl shadow-slate-200/60 backdrop-blur sm:p-8 dark:border-slate-800/80 dark:bg-slate-900/90 dark:shadow-black/30">
                    <div class="mb-4 text-center">
                        <a href="/" class="inline-block transform transition hover:scale-105">
                            <img src="/images/logo.png" alt="KeepTheStreak" class="mx-auto h-24 w-auto sm:h-28">
                        </a>
                    </div>

                    {{ $slot }}
                </section>
            </div>

            <footer class="mt-10 mb-4 border-t border-slate-200 pt-4 text-center text-xs text-slate-400 dark:border-slate-800 dark:text-slate-500">
                {{ __('Idea by') }}
                <span class="font-medium text-slate-600 dark:text-slate-300">Prashant Chhatri</span>
            </footer>
        </div>
    </div>
</body>
</html>
