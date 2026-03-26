<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <a href="{{ route('profile.show') }}" class="inline-flex min-h-10 items-center justify-center rounded-xl bg-slate-100 px-3 text-sm font-medium text-slate-700 transition duration-200 hover:bg-slate-200 active:scale-95">
                ← {{ __('Back') }}
            </a>
            <h1 class="text-base font-semibold text-slate-900">{{ __('Change Password') }}</h1>
            <div class="w-[72px]"></div>
        </div>
    </x-slot>

    <div class="mx-auto w-full max-w-md px-4 py-6">
        <div class="space-y-4">
            @if (session('status') === 'password-updated')
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                    {{ __('Password updated successfully.') }}
                </div>
            @endif

            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <form method="post" action="{{ route('profile.password.update') }}" class="space-y-4">
                    @csrf
                    @method('put')

                    <div>
                        <label for="current_password" class="mb-1 block text-sm font-medium text-slate-700">{{ __('Current Password') }}</label>
                        <input
                            id="current_password"
                            name="current_password"
                            type="password"
                            required
                            autocomplete="current-password"
                            class="w-full rounded-xl border-slate-200 px-3.5 py-2.5 text-sm text-slate-700 shadow-sm focus:border-indigo-400 focus:ring-indigo-400"
                        >
                        @error('current_password', 'updatePassword')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="mb-1 block text-sm font-medium text-slate-700">{{ __('New Password') }}</label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            autocomplete="new-password"
                            class="w-full rounded-xl border-slate-200 px-3.5 py-2.5 text-sm text-slate-700 shadow-sm focus:border-indigo-400 focus:ring-indigo-400"
                        >
                        @error('password', 'updatePassword')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-1 block text-sm font-medium text-slate-700">{{ __('Confirm Password') }}</label>
                        <input
                            id="password_confirmation"
                            name="password_confirmation"
                            type="password"
                            required
                            autocomplete="new-password"
                            class="w-full rounded-xl border-slate-200 px-3.5 py-2.5 text-sm text-slate-700 shadow-sm focus:border-indigo-400 focus:ring-indigo-400"
                        >
                    </div>

                    <button type="submit" class="inline-flex h-12 w-full items-center justify-center rounded-xl bg-indigo-600 px-4 text-base font-semibold text-white transition duration-200 hover:bg-indigo-500 active:scale-95">
                        {{ __('Update Password') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
