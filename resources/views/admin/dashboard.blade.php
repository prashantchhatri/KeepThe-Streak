<x-admin-layout>
    <section class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-indigo-500">{{ __('User Administration') }}</p>
            <h2 class="mt-2 text-3xl font-semibold tracking-tight text-slate-900 dark:text-slate-100">{{ __('Admin Dashboard') }}</h2>
            <p class="mt-2 max-w-2xl text-sm text-slate-500 dark:text-slate-400">
                {{ __('View all registered users, track their latest login, and suspend access when needed. The super admin account is protected from deletion and suspension.') }}
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white/85 px-5 py-4 shadow-sm shadow-slate-200/60 backdrop-blur dark:border-slate-800 dark:bg-slate-900/85 dark:shadow-black/20">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400 dark:text-slate-500">{{ __('Super Admin') }}</p>
            <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ __('Prashant Chhatri') }}</p>
            <p class="text-sm text-slate-500 dark:text-slate-400">{{ \App\Models\User::SUPER_ADMIN_EMAIL }}</p>
        </div>
    </section>

    <div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400 dark:text-slate-500">{{ __('Total Users') }}</p>
            <p class="mt-3 text-3xl font-semibold text-slate-900 dark:text-slate-100">{{ $totalUsers }}</p>
        </div>
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm dark:border-emerald-900/60 dark:bg-emerald-950/30">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-600 dark:text-emerald-300">{{ __('Active') }}</p>
            <p class="mt-3 text-3xl font-semibold text-emerald-700 dark:text-emerald-200">{{ $activeUsers }}</p>
        </div>
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm dark:border-amber-900/60 dark:bg-amber-950/30">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-amber-600 dark:text-amber-300">{{ __('Suspended') }}</p>
            <p class="mt-3 text-3xl font-semibold text-amber-700 dark:text-amber-200">{{ $suspendedUsers }}</p>
        </div>
        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-5 shadow-sm dark:border-rose-900/60 dark:bg-rose-950/30">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-rose-600 dark:text-rose-300">{{ __('Deleted') }}</p>
            <p class="mt-3 text-3xl font-semibold text-rose-700 dark:text-rose-200">{{ $deletedUsers }}</p>
        </div>
    </div>

    <div class="space-y-4">
        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 dark:border-emerald-900/80 dark:bg-emerald-950/40 dark:text-emerald-300">
                {{ session('status') }}
            </div>
        @endif

        @if (session('admin-error'))
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700 dark:border-rose-900/80 dark:bg-rose-950/40 dark:text-rose-300">
                {{ session('admin-error') }}
            </div>
        @endif

        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-lg shadow-slate-200/60 dark:border-slate-800 dark:bg-slate-900 dark:shadow-black/20">
            <div class="border-b border-slate-200 px-5 py-4 dark:border-slate-800">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">{{ __('Registered Users') }}</h3>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ __('Desktop table plus mobile cards for quick admin review.') }}</p>
            </div>

            <div class="hidden overflow-x-auto lg:block">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                    <thead class="bg-slate-50/80 dark:bg-slate-950/60">
                        <tr class="text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-400 dark:text-slate-500">
                            <th class="px-5 py-4">{{ __('Name') }}</th>
                            <th class="px-5 py-4">{{ __('Email') }}</th>
                            <th class="px-5 py-4">{{ __('Last Login') }}</th>
                            <th class="px-5 py-4">{{ __('Status') }}</th>
                            <th class="px-5 py-4">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @foreach ($users as $user)
                            @php
                                $statusLabel = $user->dashboardStatus();
                                $statusClasses = match ($statusLabel) {
                                    'Deleted' => 'bg-rose-100 text-rose-700 dark:bg-rose-950/50 dark:text-rose-300',
                                    'Suspended' => 'bg-amber-100 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300',
                                    default => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300',
                                };
                            @endphp
                            <tr class="align-top">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100 text-sm font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-200">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-900 dark:text-slate-100">{{ $user->name }}</p>
                                            <p class="text-sm text-slate-500 dark:text-slate-400">
                                                {{ $user->isAdmin() ? __('Admin account') : __('Registered user') }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300">{{ $user->email }}</td>
                                <td class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300">
                                    {{ $user->last_login_at?->format('d M Y, h:i A') ?? __('Never') }}
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses }}">
                                        {{ __($statusLabel) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    @if ($user->isAdmin())
                                        <span class="inline-flex rounded-xl bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                            {{ __('Protected') }}
                                        </span>
                                    @elseif ($user->trashed())
                                        <span class="inline-flex rounded-xl bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                                            {{ __('No actions') }}
                                        </span>
                                    @else
                                        <form method="POST" action="{{ route('admin.users.status', $user) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="{{ $user->isSuspended() ? \App\Models\User::STATUS_ACTIVE : \App\Models\User::STATUS_SUSPENDED }}">
                                            <button type="submit" class="inline-flex min-h-10 items-center justify-center rounded-xl px-4 text-sm font-semibold text-white transition duration-200 active:scale-95 {{ $user->isSuspended() ? 'bg-emerald-600 hover:bg-emerald-500' : 'bg-amber-600 hover:bg-amber-500' }}">
                                                {{ $user->isSuspended() ? __('Activate') : __('Suspend') }}
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="grid gap-4 p-4 lg:hidden">
                @foreach ($users as $user)
                    @php
                        $statusLabel = $user->dashboardStatus();
                        $statusClasses = match ($statusLabel) {
                            'Deleted' => 'bg-rose-100 text-rose-700 dark:bg-rose-950/50 dark:text-rose-300',
                            'Suspended' => 'bg-amber-100 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300',
                            default => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300',
                        };
                    @endphp
                    <article class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4 dark:border-slate-800 dark:bg-slate-950/40">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h4 class="text-base font-semibold text-slate-900 dark:text-slate-100">{{ $user->name }}</h4>
                                <p class="mt-1 break-all text-sm text-slate-500 dark:text-slate-400">{{ $user->email }}</p>
                            </div>
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses }}">
                                {{ __($statusLabel) }}
                            </span>
                        </div>

                        <div class="mt-4 grid gap-3 text-sm text-slate-600 dark:text-slate-300">
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-slate-500 dark:text-slate-400">{{ __('Role') }}</span>
                                <span class="font-medium">{{ $user->isAdmin() ? __('Admin') : __('User') }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-slate-500 dark:text-slate-400">{{ __('Last login') }}</span>
                                <span class="font-medium">{{ $user->last_login_at?->format('d M Y, h:i A') ?? __('Never') }}</span>
                            </div>
                        </div>

                        <div class="mt-4">
                            @if ($user->isAdmin())
                                <span class="inline-flex rounded-xl bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                    {{ __('Protected admin account') }}
                                </span>
                            @elseif ($user->trashed())
                                <span class="inline-flex rounded-xl bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                                    {{ __('Deleted users cannot be restored from here') }}
                                </span>
                            @else
                                <form method="POST" action="{{ route('admin.users.status', $user) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="{{ $user->isSuspended() ? \App\Models\User::STATUS_ACTIVE : \App\Models\User::STATUS_SUSPENDED }}">
                                    <button type="submit" class="inline-flex min-h-11 w-full items-center justify-center rounded-xl px-4 text-sm font-semibold text-white transition duration-200 active:scale-95 {{ $user->isSuspended() ? 'bg-emerald-600 hover:bg-emerald-500' : 'bg-amber-600 hover:bg-amber-500' }}">
                                        {{ $user->isSuspended() ? __('Activate User') : __('Suspend User') }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </div>
</x-admin-layout>
