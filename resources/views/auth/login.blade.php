<x-guest-layout>

    <div class="mb-6 text-center">
        <h1 class="mb-2 text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-100">{{ __('Welcome back') }}</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('Log in to continue your streak.') }}</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-5" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex cursor-pointer items-center">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-900 dark:text-indigo-500 dark:focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-slate-600 dark:text-slate-300">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="mt-6 flex items-center justify-between gap-3">
            @if (Route::has('password.request'))
                <a class="text-sm font-medium text-slate-600 transition hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-white dark:text-slate-300 dark:hover:text-slate-100 dark:focus:ring-offset-slate-900" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="min-w-28">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    <p class="mt-6 text-center text-sm text-slate-600 dark:text-slate-300">
        {{ __("Don't have an account?") }}
        <a href="{{ route('register') }}" class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-300 dark:hover:text-indigo-200">{{ __('Create one') }}</a>
    </p>
</x-guest-layout>
