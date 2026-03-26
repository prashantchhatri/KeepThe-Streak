<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#4f46e5">
    <link rel="icon" type="image/png" href="/images/logo.png">
    <link rel="apple-touch-icon" href="/images/logo.png">
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
        <main class="pb-28">
            {{ $slot }}
        </main>

        <footer class="mx-auto mt-10 mb-28 w-full max-w-md border-t border-gray-200 px-4 pt-4 text-center text-xs text-gray-400">
            {{ __('Idea by') }}
            <span class="font-medium text-gray-600">Prashant Chhatri</span>
        </footer>

        @php
            $isDashboardRoute = request()->routeIs('dashboard') || request()->routeIs('streaks.*');
            $isProfileRoute = request()->routeIs('profile.*');
        @endphp
        <nav class="fixed inset-x-0 bottom-0 z-40 border-t border-slate-200 bg-white">
            <div class="mx-auto w-full max-w-md">
                <div class="flex h-14 items-center justify-around px-4">
                    <a href="{{ route('dashboard') }}" @class([
                        'inline-flex min-h-10 flex-col items-center justify-center rounded-lg px-3 transition duration-200',
                        'text-indigo-600' => $isDashboardRoute,
                        'text-gray-400 hover:text-gray-600' => ! $isDashboardRoute,
                    ])>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10.707 1.707a1 1 0 00-1.414 0l-7 7A1 1 0 003 10.414V17a2 2 0 002 2h3a1 1 0 001-1v-3a1 1 0 011-1h0a1 1 0 011 1v3a1 1 0 001 1h3a2 2 0 002-2v-6.586a1 1 0 00-.293-.707l-7-7z" />
                        </svg>
                        <span class="text-[11px] font-medium">{{ __('Dashboard') }}</span>
                    </a>

                    <div class="flex flex-col items-center">
                        <a href="{{ route('dashboard', ['add' => 1]) }}" class="-mt-8 inline-flex h-14 w-14 items-center justify-center rounded-full bg-indigo-600 text-white shadow-lg transition duration-200 hover:bg-indigo-500 active:scale-95" aria-label="{{ __('Add Streak') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 4a1 1 0 011 1v4h4a1 1 0 110 2h-4v4a1 1 0 11-2 0v-4H5a1 1 0 110-2h4V5a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                        </a>
                        <span class="text-[11px] text-gray-400">{{ __('Add') }}</span>
                    </div>

                    <a href="{{ route('profile.show') }}" @class([
                        'inline-flex min-h-10 flex-col items-center justify-center rounded-lg px-3 transition duration-200',
                        'text-indigo-600' => $isProfileRoute,
                        'text-gray-400 hover:text-gray-600' => ! $isProfileRoute,
                    ])>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 2a4 4 0 100 8 4 4 0 000-8zM4 16a6 6 0 1112 0H4z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-[11px] font-medium">{{ __('Profile') }}</span>
                    </a>
                </div>
            </div>
        </nav>
    </div>
</body>
</html>
