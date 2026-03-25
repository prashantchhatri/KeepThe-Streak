<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#4f46e5">
    <link rel="manifest" href="/manifest.json">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 font-sans antialiased text-slate-900">
    <div class="min-h-screen bg-gradient-to-b from-slate-100 via-slate-50 to-indigo-50/40">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="mx-auto mb-6 w-full max-w-md px-4">
                <div class="rounded-xl border border-slate-200 bg-white/90 px-4 py-3 shadow-sm">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main class="pb-24">
            {{ $slot }}
        </main>

        <footer class="mx-auto mt-10 mb-24 w-full max-w-md border-t border-gray-200 px-4 pt-4 text-center text-xs text-gray-400">
            {{ __('Idea by') }}
            <span class="font-medium text-gray-600">Prashant Chhatri</span>
        </footer>

        <nav class="fixed inset-x-0 bottom-0 z-40 border-t border-slate-200 bg-white/95 backdrop-blur">
            <div class="mx-auto flex w-full max-w-md items-center justify-around px-4 py-2">
                <a href="{{ route('dashboard') }}" @class([
                    'inline-flex min-h-10 flex-col items-center justify-center rounded-lg px-3 text-xs font-medium transition duration-200',
                    'text-indigo-600' => request()->routeIs('dashboard'),
                    'text-slate-500 hover:text-slate-700' => ! request()->routeIs('dashboard'),
                ])>
                    {{ __('Dashboard') }}
                </a>

                <a href="{{ route('dashboard', ['add' => 1]) }}" class="inline-flex min-h-10 items-center justify-center rounded-xl bg-indigo-600 px-4 text-sm font-semibold text-white transition duration-200 hover:bg-indigo-500 active:scale-95">
                    + {{ __('Add') }}
                </a>

                <a href="{{ route('profile.edit') }}" @class([
                    'inline-flex min-h-10 flex-col items-center justify-center rounded-lg px-3 text-xs font-medium transition duration-200',
                    'text-indigo-600' => request()->routeIs('profile.*'),
                    'text-slate-500 hover:text-slate-700' => ! request()->routeIs('profile.*'),
                ])>
                    {{ __('Profile') }}
                </a>
            </div>
        </nav>
    </div>
</body>
</html>
