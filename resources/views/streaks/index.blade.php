<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-base font-semibold text-slate-900">{{ __('Dashboard') }}</h1>
            <span class="text-xs text-slate-400">{{ __('Today') }}</span>
        </div>
    </x-slot>

    @php
        $todayDate = today()->toDateString();
        $initialStreakState = $streaks->mapWithKeys(function ($streak) use ($todayDate) {
            $todayLog = $streak->streakLogs->first(fn ($log) => $log->date->toDateString() === $todayDate);

            return [
                (string) $streak->id => [
                    'todayStatus' => $todayLog?->status,
                    'stats' => $streak->stats,
                ],
            ];
        });
    @endphp

    <div
        class="mx-auto w-full max-w-md px-4 py-6"
        x-data="streakDashboard({
            showForm: @js($errors->any() || request()->boolean('add')),
            streakIncreased: @js((bool) session('streak_increased')),
            initialStates: @js($initialStreakState),
            csrfToken: @js(csrf_token()),
            hasStreaks: @js($streaks->isNotEmpty()),
        })"
        x-init="init()"
    >
        <div class="pointer-events-none fixed inset-x-0 top-20 z-50 flex justify-center px-4">
            <div
                x-show="showStreakIncreased"
                x-transition:enter="transform transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transform transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-2"
                class="rounded-xl bg-green-100 px-4 py-2 text-sm font-semibold text-green-700 shadow-md"
                x-cloak
            >
                🔥 {{ __('Streak increased!') }}
            </div>
        </div>

        <div class="pointer-events-none fixed inset-x-0 bottom-6 z-50 flex justify-center px-4">
            <div
                x-show="showToast"
                x-transition:enter="transform transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transform transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-2"
                class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-medium text-white shadow-lg"
                x-text="toastMessage"
                x-cloak
            ></div>
        </div>

        <div class="space-y-4">
            @if (session('status'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            <div x-show="showInstallPrompt" x-cloak>
                <button
                    type="button"
                    @click="installApp()"
                    class="inline-flex min-h-10 w-full items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition duration-200 hover:bg-indigo-500 active:scale-95"
                >
                    {{ __('Install App 📲') }}
                </button>
            </div>

            @if ($streaks->isNotEmpty())
                <div class="sticky top-2 z-10">
                    <form method="POST" action="{{ route('streaks.mark-all-done') }}" @submit.prevent="markAllDone($event)">
                        @csrf
                        <button
                            type="submit"
                            :disabled="markingAll"
                            class="inline-flex min-h-10 w-full items-center justify-center rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition duration-200 hover:bg-emerald-500 active:scale-95 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            ✔ {{ __('Mark All Done') }}
                        </button>
                    </form>
                </div>
            @endif

            @if ($streaks->isNotEmpty())
                <div class="mb-6 flex items-center justify-between gap-3">
                    <div>
                        <h1 class="text-xl font-semibold text-slate-900">{{ __('Your Streaks') }}</h1>
                        <p class="text-sm text-slate-500">{{ __('Track consistency day by day.') }}</p>
                    </div>

                    <button
                        type="button"
                        @click="showForm = !showForm"
                        class="inline-flex min-h-10 items-center justify-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition duration-200 hover:bg-indigo-500 active:scale-95"
                    >
                        + {{ __('Add Streak') }}
                    </button>
                </div>
            @else
                <div class="rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-md">
                    <img src="/images/logo.png" alt="KeepTheStreak" class="mx-auto h-12 w-auto">
                    <h1 class="mt-4 text-lg font-semibold text-slate-900">{{ __('Welcome to KeepTheStreak 👋') }}</h1>
                    <p class="mt-1 text-sm text-gray-500">{{ __('Start building consistency today.') }}</p>

                    <button
                        type="button"
                        @click="showForm = true"
                        class="mt-5 inline-flex h-12 w-full items-center justify-center rounded-xl bg-indigo-600 px-5 text-base font-semibold text-white transition duration-200 hover:bg-indigo-500 active:scale-95"
                    >
                        + {{ __('Create Your First Streak') }}
                    </button>

                    <p class="mt-4 text-xs text-gray-400">{{ __('Consistency builds today. Success defines tomorrow.') }}</p>
                </div>
            @endif

            <div x-show="showForm" x-transition class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <form method="POST" action="{{ route('streaks.store') }}" class="space-y-3">
                    @csrf

                    <div>
                        <label for="name" class="mb-1 block text-sm font-medium text-slate-700">{{ __('Streak name') }}</label>
                        <input
                            id="name"
                            name="name"
                            type="text"
                            value="{{ old('name') }}"
                            maxlength="100"
                            required
                            class="w-full rounded-xl border-slate-200 px-3.5 py-2.5 text-sm text-slate-700 shadow-sm focus:border-indigo-400 focus:ring-indigo-400"
                            placeholder="{{ __('e.g. Morning workout') }}"
                        >
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="inline-flex min-h-10 w-full items-center justify-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition duration-200 hover:bg-slate-800 active:scale-95">
                        {{ __('Save') }}
                    </button>
                </form>
            </div>

            @if ($streaks->isNotEmpty())
                <div class="mt-4 space-y-4">
                    @foreach ($streaks as $streak)
                        @php
                            $todayLog = $streak->streakLogs->first(fn ($log) => $log->date->toDateString() === $todayDate);
                            $isMarkedToday = (bool) $todayLog;
                            $stats = $streak->stats;
                            $isBestEver = $stats['longest'] > 0 && $stats['current'] === $stats['longest'];
                        @endphp

                        <div @class([
                            'rounded-2xl border bg-white p-4 shadow-sm',
                            'border-yellow-300' => $isBestEver,
                            'border-slate-200' => ! $isBestEver,
                        ])>
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h2 class="text-base font-bold text-slate-900">{{ $streak->name }}</h2>
                                        <span
                                            class="rounded-full bg-yellow-100 px-2 py-1 text-xs text-yellow-700"
                                            x-show="isBestEver({{ $streak->id }})"
                                            @if (! $isBestEver) style="display: none;" @endif
                                        >
                                            🏆 {{ __('Best Ever') }}
                                        </span>
                                    </div>

                                    <div class="mt-2">
                                        <span
                                            class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700"
                                            x-show="getStatus({{ $streak->id }}) === 'done'"
                                            @if (($todayLog?->status ?? null) !== 'done') style="display: none;" @endif
                                        >
                                            {{ __('Done ✅') }}
                                        </span>
                                        <span
                                            class="inline-flex items-center rounded-full bg-rose-100 px-2.5 py-1 text-xs font-semibold text-rose-700"
                                            x-show="getStatus({{ $streak->id }}) === 'skipped'"
                                            @if (($todayLog?->status ?? null) !== 'skipped') style="display: none;" @endif
                                        >
                                            {{ __('Skipped ❌') }}
                                        </span>
                                    </div>
                                </div>

                                <form method="POST" action="{{ route('streaks.destroy', $streak) }}" onsubmit="return confirm('Delete this streak?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex min-h-10 min-w-10 items-center justify-center rounded-lg bg-rose-50 px-2 text-rose-600 transition duration-200 hover:bg-rose-100 active:scale-95" aria-label="{{ __('Delete streak') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8.5 2a1 1 0 00-1 1v1H5a1 1 0 100 2h.35l.69 9.08A2 2 0 008.03 17h3.94a2 2 0 001.99-1.92L14.65 6H15a1 1 0 100-2h-2.5V3a1 1 0 00-1-1h-3zM9.5 4V3h1v1h-1zM8 8a1 1 0 012 0v5a1 1 0 11-2 0V8zm4-1a1 1 0 00-1 1v5a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </form>
                            </div>

                            <div class="mt-3 rounded-xl border border-slate-200 bg-slate-50 p-3">
                                <div class="space-y-1 text-sm text-gray-500">
                                    <p>🔥 {{ __('Current:') }} <span x-text="getStats({{ $streak->id }}).current">{{ $stats['current'] }}</span> {{ __('days') }}</p>
                                    <p>🏆 {{ __('Best:') }} <span x-text="getStats({{ $streak->id }}).longest">{{ $stats['longest'] }}</span> {{ __('days') }}</p>
                                </div>

                                <div class="mt-3">
                                    <div class="h-2 overflow-hidden rounded-full bg-gray-200">
                                        <div
                                            class="h-2 rounded-full bg-green-500 transition-all duration-300"
                                            style="width: {{ $stats['percentage'] }}%;"
                                            :style="`width: ${getStats({{ $streak->id }}).percentage}%`"
                                        ></div>
                                    </div>
                                    <p class="mt-2 text-sm text-gray-500">
                                        <span x-text="getStats({{ $streak->id }}).percentage">{{ $stats['percentage'] }}</span>% {{ __('consistency') }}
                                    </p>
                                </div>

                                <a href="{{ route('streaks.calendar', $streak->id) }}" class="mt-3 inline-flex min-h-10 items-center justify-center rounded-xl bg-slate-100 px-3 text-sm font-medium text-slate-700 transition duration-200 hover:bg-slate-200 active:scale-95">
                                    {{ __('View Calendar') }}
                                </a>
                            </div>

                            <div class="mt-3 grid grid-cols-2 gap-2">
                                <form method="POST" action="{{ route('streaks.done', $streak->id) }}" @submit.prevent="markDone({{ $streak->id }}, $event)">
                                    @csrf
                                    <button
                                        type="submit"
                                        :disabled="isPending({{ $streak->id }}) || isMarked({{ $streak->id }})"
                                        @disabled($isMarkedToday)
                                        class="inline-flex h-10 w-full items-center justify-center rounded-xl bg-emerald-600 px-3 text-sm font-semibold text-white transition duration-200 hover:bg-emerald-500 active:scale-95 disabled:cursor-not-allowed disabled:opacity-60"
                                    >
                                        ✔ {{ __('Done') }}
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('streaks.skip', $streak->id) }}" @submit.prevent="markSkip({{ $streak->id }}, $event)">
                                    @csrf
                                    <button
                                        type="submit"
                                        :disabled="isPending({{ $streak->id }}) || isMarked({{ $streak->id }})"
                                        @disabled($isMarkedToday)
                                        class="inline-flex h-10 w-full items-center justify-center rounded-xl bg-rose-600 px-3 text-sm font-semibold text-white transition duration-200 hover:bg-rose-500 active:scale-95 disabled:cursor-not-allowed disabled:opacity-60"
                                    >
                                        ✖ {{ __('Skip') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('streakDashboard', (config) => ({
                showForm: config.showForm,
                showStreakIncreased: config.streakIncreased,
                streakStates: config.initialStates ?? {},
                csrfToken: config.csrfToken,
                hasStreaks: config.hasStreaks,
                markingAll: false,
                pendingById: {},
                showToast: false,
                toastMessage: '',
                toastTimer: null,
                reminderTimeout: null,
                installPromptEvent: null,
                showInstallPrompt: false,

                init() {
                    if (this.showStreakIncreased) {
                        setTimeout(() => {
                            this.showStreakIncreased = false;
                        }, 2000);
                    }

                    this.setupDailyReminder();
                    this.setupInstallPrompt();
                },

                getStatus(id) {
                    return this.streakStates[String(id)]?.todayStatus ?? null;
                },

                getStats(id) {
                    return this.streakStates[String(id)]?.stats ?? { current: 0, longest: 0, percentage: 0 };
                },

                isBestEver(id) {
                    const stats = this.getStats(id);
                    return stats.longest > 0 && stats.current === stats.longest;
                },

                isMarked(id) {
                    return this.getStatus(id) !== null;
                },

                isPending(id) {
                    return !!this.pendingById[String(id)];
                },

                applyStreakState(streak) {
                    if (!streak || !streak.id) return;
                    this.streakStates[String(streak.id)] = {
                        todayStatus: streak.today_status,
                        stats: streak.stats,
                    };
                },

                showToastMessage(message) {
                    this.toastMessage = message;
                    this.showToast = true;
                    clearTimeout(this.toastTimer);
                    this.toastTimer = setTimeout(() => {
                        this.showToast = false;
                    }, 1800);
                },

                async sendPost(url) {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': this.csrfToken,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });

                    if (!response.ok) {
                        throw new Error('Request failed');
                    }

                    return response.json();
                },

                async markDone(id, event) {
                    if (this.isPending(id) || this.isMarked(id)) return;

                    this.pendingById[String(id)] = true;

                    try {
                        const data = await this.sendPost(event.target.action);
                        this.applyStreakState(data.streak);
                        if (data.streak_increased) {
                            this.showStreakIncreased = true;
                            setTimeout(() => {
                                this.showStreakIncreased = false;
                            }, 2000);
                        }
                        this.showToastMessage(data.message || 'Marked as done');
                    } catch (error) {
                        event.target.submit();
                    } finally {
                        this.pendingById[String(id)] = false;
                    }
                },

                async markSkip(id, event) {
                    if (this.isPending(id) || this.isMarked(id)) return;

                    this.pendingById[String(id)] = true;

                    try {
                        const data = await this.sendPost(event.target.action);
                        this.applyStreakState(data.streak);
                        this.showToastMessage(data.message || 'Marked as skipped');
                    } catch (error) {
                        event.target.submit();
                    } finally {
                        this.pendingById[String(id)] = false;
                    }
                },

                async markAllDone(event) {
                    if (this.markingAll || !this.hasStreaks) return;

                    this.markingAll = true;

                    try {
                        const data = await this.sendPost(event.target.action);
                        if (Array.isArray(data.streaks)) {
                            data.streaks.forEach((streak) => this.applyStreakState(streak));
                        }
                        this.showToastMessage(data.message || 'Marked as done');
                    } catch (error) {
                        event.target.submit();
                    } finally {
                        this.markingAll = false;
                    }
                },

                setupDailyReminder() {
                    if (!('Notification' in window)) return;

                    const askedKey = 'kts_notification_asked';
                    const enabledKey = 'kts_notification_enabled';

                    if (Notification.permission === 'granted') {
                        localStorage.setItem(enabledKey, '1');
                        this.scheduleReminder();
                        return;
                    }

                    const hasAsked = localStorage.getItem(askedKey) === '1';

                    if (!hasAsked && Notification.permission === 'default') {
                        Notification.requestPermission().then((permission) => {
                            localStorage.setItem(askedKey, '1');
                            if (permission === 'granted') {
                                localStorage.setItem(enabledKey, '1');
                                this.scheduleReminder();
                            } else {
                                localStorage.setItem(enabledKey, '0');
                            }
                        });
                        return;
                    }

                    if (localStorage.getItem(enabledKey) === '1' && Notification.permission === 'granted') {
                        this.scheduleReminder();
                    }
                },

                scheduleReminder() {
                    const scheduleNext = () => {
                        const now = new Date();
                        const next = new Date();
                        next.setHours(20, 0, 0, 0);
                        if (next <= now) {
                            next.setDate(next.getDate() + 1);
                        }

                        const delay = next.getTime() - now.getTime();
                        clearTimeout(this.reminderTimeout);
                        this.reminderTimeout = setTimeout(() => {
                            if (Notification.permission === 'granted') {
                                new Notification('KeepTheStreak', {
                                    body: "Don't break your streak today 🔥",
                                });
                            }
                            scheduleNext();
                        }, delay);
                    };

                    scheduleNext();
                },

                setupInstallPrompt() {
                    window.addEventListener('beforeinstallprompt', (event) => {
                        event.preventDefault();
                        this.installPromptEvent = event;
                        this.showInstallPrompt = true;
                    });
                },

                async installApp() {
                    if (!this.installPromptEvent) return;

                    this.installPromptEvent.prompt();
                    await this.installPromptEvent.userChoice;
                    this.installPromptEvent = null;
                    this.showInstallPrompt = false;
                },
            }));
        });
    </script>
</x-app-layout>
