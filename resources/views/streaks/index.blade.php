<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-base font-semibold text-slate-900">{{ __('Dashboard') }}</h1>
            <span class="text-xs text-slate-400">{{ now()->format('d M Y') }}</span>
        </div>
    </x-slot>

    @php
        $todayDate = today()->toDateString();
        $hasStreaks = $streaks->count() > 0;
        $initialStreakState = $streaks->mapWithKeys(function ($streak) use ($todayDate) {
            $todayLog = $streak->logs->first(fn ($log) => $log->date->toDateString() === $todayDate);

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
            hasStreaks: @js($hasStreaks),
            todayDate: @js($todayDate),
            chartData: @js($chartData ?? []),
            totalStreaks: @js($totalStreaks ?? 0),
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

            @if ($hasStreaks)
                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="mb-3 flex items-center justify-between gap-3">
                        <h2 class="text-sm font-semibold text-slate-900">{{ __('Your Consistency (Last 7 Days)') }}</h2>
                        <span class="text-xs text-slate-400">{{ __('Last 7 days') }}</span>
                    </div>
                    <div class="h-32">
                        <canvas x-ref="consistencyChartCanvas" class="h-32 w-full"></canvas>
                    </div>
                </div>

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

            @if ($hasStreaks)
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

            @if ($hasStreaks)
                <div class="mt-4 space-y-4">
                    @foreach ($streaks as $streak)
                        @php
                            $todayLog = $streak->logs->first(fn ($log) => $log->date->toDateString() === $todayDate);
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

                                <form method="POST" action="{{ route('streaks.delete', $streak->id) }}" onsubmit="return confirm('Delete this streak?');">
                                    @csrf
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

        <div class="fixed inset-x-0 bottom-20 z-[60] flex justify-center px-4">
            <button
                type="button"
                @click="installApp()"
                x-show="showInstallPrompt"
                class="inline-flex min-h-10 items-center justify-center rounded-full bg-indigo-600 px-4 py-2 text-xs font-semibold text-white shadow-md transition duration-200 hover:bg-indigo-500 active:scale-95"
                :title="installUnsupported ? 'Use Chrome or Edge to install this app' : 'Install app'"
            >
                <span x-text="installUnsupported ? 'Install Help' : 'Install App 📲'"></span>
            </button>
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
                todayDate: config.todayDate ?? null,
                chartData: config.chartData ?? [],
                totalStreaks: Number(config.totalStreaks ?? 0),
                chartResizeTimer: null,
                markingAll: false,
                pendingById: {},
                showToast: false,
                toastMessage: '',
                toastTimer: null,
                reminderTimeout: null,
                installPromptEvent: null,
                showInstallPrompt: false,
                installUnsupported: false,

                init() {
                    if (this.showStreakIncreased) {
                        setTimeout(() => {
                            this.showStreakIncreased = false;
                        }, 2000);
                    }

                    this.$nextTick(() => {
                        this.renderConsistencyChart();
                    });
                    window.addEventListener('resize', () => {
                        clearTimeout(this.chartResizeTimer);
                        this.chartResizeTimer = setTimeout(() => this.renderConsistencyChart(), 120);
                    });
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

                renderConsistencyChart() {
                    if (!this.hasStreaks || !this.$refs.consistencyChartCanvas || !Array.isArray(this.chartData) || this.chartData.length === 0) {
                        return;
                    }

                    const canvas = this.$refs.consistencyChartCanvas;
                    if (!(canvas instanceof HTMLCanvasElement) || !canvas.isConnected) return;

                    const ctx = canvas.getContext('2d');
                    if (!ctx) return;

                    const doneData = this.chartData.map((item) => Number(item.done ?? 0));
                    const width = canvas.clientWidth;
                    const height = canvas.clientHeight;
                    if (width <= 0 || height <= 0) return;

                    const dpr = window.devicePixelRatio || 1;
                    canvas.width = Math.floor(width * dpr);
                    canvas.height = Math.floor(height * dpr);
                    ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
                    ctx.clearRect(0, 0, width, height);

                    const yMax = Math.max(this.totalStreaks, ...doneData, 1);
                    const padding = { top: 10, right: 8, bottom: 30, left: 24 };
                    const chartWidth = width - padding.left - padding.right;
                    const chartHeight = height - padding.top - padding.bottom;
                    if (chartWidth <= 0 || chartHeight <= 0) return;

                    const xStep = chartWidth / this.chartData.length;
                    const barWidth = Math.max(8, Math.min(20, xStep - 8));
                    const totalLineY = padding.top + chartHeight - (this.totalStreaks / yMax) * chartHeight;

                    ctx.strokeStyle = '#e2e8f0';
                    ctx.lineWidth = 1;
                    for (let level = 0; level <= yMax; level++) {
                        const y = padding.top + chartHeight - (level / yMax) * chartHeight;
                        ctx.beginPath();
                        ctx.moveTo(padding.left, y);
                        ctx.lineTo(width - padding.right, y);
                        ctx.stroke();
                    }

                    ctx.strokeStyle = '#94a3b8';
                    ctx.setLineDash([4, 3]);
                    ctx.beginPath();
                    ctx.moveTo(padding.left, totalLineY);
                    ctx.lineTo(width - padding.right, totalLineY);
                    ctx.stroke();
                    ctx.setLineDash([]);

                    this.chartData.forEach((item, index) => {
                        const done = Math.max(0, Math.min(yMax, Number(item.done ?? 0)));
                        const xCenter = padding.left + index * xStep + xStep / 2;
                        const barHeight = (done / yMax) * chartHeight;
                        const x = xCenter - barWidth / 2;
                        const y = padding.top + chartHeight - barHeight;

                        ctx.fillStyle = '#6366f1';
                        const radius = 6;
                        ctx.beginPath();
                        ctx.moveTo(x, y + radius);
                        ctx.arcTo(x, y, x + radius, y, radius);
                        ctx.arcTo(x + barWidth, y, x + barWidth, y + radius, radius);
                        ctx.lineTo(x + barWidth, padding.top + chartHeight);
                        ctx.lineTo(x, padding.top + chartHeight);
                        ctx.closePath();
                        ctx.fill();

                        ctx.fillStyle = '#94a3b8';
                        ctx.font = '9px sans-serif';
                        ctx.textAlign = 'center';
                        ctx.fillText(item.weekday ?? '', xCenter, height - 6);
                    });
                },

                refreshTodayDoneChart() {
                    if (!this.hasStreaks || !Array.isArray(this.chartData) || this.chartData.length === 0) {
                        return;
                    }

                    const streakIds = Object.keys(this.streakStates);
                    if (streakIds.length === 0) return;

                    const doneCount = streakIds.reduce((carry, streakId) => {
                        return carry + (this.getStatus(streakId) === 'done' ? 1 : 0);
                    }, 0);

                    const targetIndex = this.chartData.findIndex((item) => item.date_key === this.todayDate);
                    const indexToUpdate = targetIndex >= 0 ? targetIndex : this.chartData.length - 1;
                    this.chartData[indexToUpdate].done = doneCount;
                    this.$nextTick(() => {
                        this.renderConsistencyChart();
                    });
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
                        this.refreshTodayDoneChart();
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
                        this.refreshTodayDoneChart();
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
                        this.refreshTodayDoneChart();
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
                    if (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone) {
                        this.showInstallPrompt = false;
                        return;
                    }

                    // Keep CTA visible unless app is already installed in standalone mode.
                    this.showInstallPrompt = true;
                    this.installUnsupported = true;

                    window.addEventListener('beforeinstallprompt', (event) => {
                        event.preventDefault();
                        this.installPromptEvent = event;
                        this.installUnsupported = false;
                        this.showInstallPrompt = true;
                    });
                },

                async installApp() {
                    if (!this.installPromptEvent) {
                        this.showToastMessage('Install is available in Chrome/Edge. Open there to install.');
                        return;
                    }

                    this.installPromptEvent.prompt();
                    await this.installPromptEvent.userChoice;
                    this.installPromptEvent = null;
                    this.showInstallPrompt = false;
                },
            }));
        });
    </script>
</x-app-layout>
