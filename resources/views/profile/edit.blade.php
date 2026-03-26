<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <a href="{{ route('profile.show') }}" class="inline-flex min-h-10 items-center justify-center rounded-xl bg-slate-100 px-3 text-sm font-medium text-slate-700 transition duration-200 hover:bg-slate-200 active:scale-95">
                ← {{ __('Back') }}
            </a>
            <h1 class="text-base font-semibold text-slate-900">{{ __('Edit Profile') }}</h1>
            <div class="w-[72px]"></div>
        </div>
    </x-slot>

    <div class="mx-auto w-full max-w-md px-4 py-6">
        <div class="space-y-4">
            @if (session('status') === 'profile-updated')
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                    {{ __('Profile updated successfully.') }}
                </div>
            @endif

            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
                    @csrf
                    @method('patch')

                    <div>
                        <label for="name" class="mb-1 block text-sm font-medium text-slate-700">{{ __('Name') }}</label>
                        <input
                            id="name"
                            name="name"
                            type="text"
                            value="{{ old('name', $user->name) }}"
                            required
                            autofocus
                            autocomplete="name"
                            class="w-full rounded-xl border-slate-200 px-3.5 py-2.5 text-sm text-slate-700 shadow-sm focus:border-indigo-400 focus:ring-indigo-400"
                        >
                        @error('name')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="mb-1 block text-sm font-medium text-slate-700">{{ __('Email') }}</label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email', $user->email) }}"
                            required
                            autocomplete="username"
                            class="w-full rounded-xl border-slate-200 px-3.5 py-2.5 text-sm text-slate-700 shadow-sm focus:border-indigo-400 focus:ring-indigo-400"
                        >
                        @error('email')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="inline-flex h-12 w-full items-center justify-center rounded-xl bg-indigo-600 px-4 text-base font-semibold text-white transition duration-200 hover:bg-indigo-500 active:scale-95">
                        {{ __('Save Changes') }}
                    </button>
                </form>
            </div>

            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 shadow-sm">
                <h2 class="text-sm font-semibold text-rose-800">{{ __('Delete Account') }}</h2>
                <p class="mt-1 text-xs text-rose-700">{{ __('This action is permanent.') }}</p>

                <form method="post" action="{{ route('profile.destroy') }}" class="mt-3 space-y-3" onsubmit="return confirm('Delete your account permanently?');">
                    @csrf
                    @method('delete')

                    <div>
                        <label for="delete_password" class="mb-1 block text-sm font-medium text-rose-800">{{ __('Current Password') }}</label>
                        <input
                            id="delete_password"
                            name="password"
                            type="password"
                            required
                            autocomplete="current-password"
                            class="w-full rounded-xl border-rose-200 px-3.5 py-2.5 text-sm text-slate-700 shadow-sm focus:border-rose-400 focus:ring-rose-300"
                        >
                        @error('password', 'userDeletion')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="inline-flex min-h-10 w-full items-center justify-center rounded-xl bg-rose-600 px-4 py-2.5 text-sm font-semibold text-white transition duration-200 hover:bg-rose-500 active:scale-95">
                        {{ __('Delete Account') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
