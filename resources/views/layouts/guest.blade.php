<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="/images/logo.png">
    <link rel="apple-touch-icon" href="/images/logo.png">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-slate-900">
    <div class="relative min-h-screen overflow-hidden bg-gradient-to-b from-slate-100 via-white to-indigo-50">
        <div class="pointer-events-none absolute -left-12 -top-20 h-72 w-72 rounded-full bg-indigo-100/80 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-28 right-0 h-96 w-96 rounded-full bg-slate-200/70 blur-3xl"></div>

        <div class="relative mx-auto flex min-h-screen w-full max-w-md flex-col px-4 py-8">
            <div class="flex-1 flex items-center">
                <section class="w-full rounded-2xl border border-slate-200/90 bg-white/90 p-6 shadow-xl shadow-slate-200/60 backdrop-blur sm:p-8">
                    <div class="mb-4 text-center">
                        <a href="/" class="inline-block transform transition hover:scale-105">
                            <img src="/images/logo.png" alt="KeepTheStreak" class="mx-auto h-24 w-auto sm:h-28">
                        </a>
                    </div>

                    {{ $slot }}
                </section>
            </div>

            <footer class="mt-10 mb-4 border-t border-gray-200 pt-4 text-center text-xs text-gray-400">
                {{ __('Idea by') }}
                <span class="font-medium text-gray-600">Prashant Chhatri</span>
            </footer>
        </div>
    </div>
</body>
</html>
