<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-base font-semibold text-slate-900 dark:text-slate-100">{{ __('Profile') }}</h1>
            <span class="text-xs text-slate-400 dark:text-slate-500">{{ __('Account') }}</span>
        </div>
    </x-slot>

    <div class="mx-auto w-full max-w-md px-4 py-6">
        <div class="space-y-4">
            @if (session('status') === 'profile-updated')
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 dark:border-emerald-900/80 dark:bg-emerald-950/40 dark:text-emerald-300">
                    {{ __('Profile updated successfully.') }}
                </div>
            @endif

            @if (session('status') === 'password-updated')
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 dark:border-emerald-900/80 dark:bg-emerald-950/40 dark:text-emerald-300">
                    {{ __('Password updated successfully.') }}
                </div>
            @endif

            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900 dark:shadow-black/20">
                <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ __('User Info') }}</h2>
                <dl class="mt-3 space-y-3 text-sm">
                    <div>
                        <dt class="text-slate-500 dark:text-slate-400">{{ __('Name') }}</dt>
                        <dd class="font-medium text-slate-900 dark:text-slate-100">{{ $user->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500 dark:text-slate-400">{{ __('Email') }}</dt>
                        <dd class="break-all font-medium text-slate-900 dark:text-slate-100">{{ $user->email }}</dd>
                    </div>
                </dl>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900 dark:shadow-black/20">
                <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ __('Actions') }}</h2>
                <div class="mt-3 space-y-3">
                    <a href="{{ route('profile.edit') }}" class="inline-flex h-12 w-full items-center justify-center rounded-xl bg-indigo-600 px-4 text-base font-semibold text-white transition duration-200 hover:bg-indigo-500 active:scale-95">
                        {{ __('Edit Profile') }}
                    </a>

                    <a href="{{ route('profile.password') }}" class="inline-flex h-12 w-full items-center justify-center rounded-xl bg-slate-100 px-4 text-base font-semibold text-slate-700 transition duration-200 hover:bg-slate-200 active:scale-95 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                        {{ __('Change Password') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
