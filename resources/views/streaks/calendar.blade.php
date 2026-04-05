<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <button
                type="button"
                onclick="if (window.history.length > 1) { window.history.back(); } else { window.location.href = '/dashboard'; }"
                class="inline-flex min-h-10 items-center justify-center rounded-xl bg-slate-100 px-3 text-sm font-medium text-slate-700 transition duration-200 hover:bg-slate-200 active:scale-95 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
            >
                ← {{ __('Back') }}
            </button>

            <div class="text-right">
                <h1 class="text-base font-semibold text-slate-900 dark:text-slate-100">{{ __('Calendar') }}</h1>
                <p class="text-xs text-slate-400 dark:text-slate-500">{{ $todayLabel }}</p>
            </div>
        </div>
    </x-slot>

    <div
        class="mx-auto w-full max-w-md px-4 py-6"
        x-data="streakCalendar({
            todayDate: @js($todayDate),
            markUrl: @js(route('streaks.mark-date', $streak->id)),
            csrfToken: @js(csrf_token()),
            initialStatuses: @js($logStatuses),
        })"
    >
        <div x-show="toast.show" class="pointer-events-none fixed inset-x-0 bottom-6 z-50 flex justify-center px-4" x-cloak>
            <div class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-medium text-white shadow-lg dark:bg-slate-100 dark:text-slate-900" x-text="toast.message"></div>
        </div>

        <div class="space-y-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900 dark:shadow-black/20">
                <div class="mb-4">
                    <label for="streak_switcher" class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-400">{{ __('Switch streak') }}</label>
                    <select
                        id="streak_switcher"
                        class="w-full rounded-xl border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 shadow-sm focus:border-indigo-400 focus:ring-indigo-400 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:focus:border-indigo-500 dark:focus:ring-indigo-500"
                        onchange="window.location.href=this.value"
                    >
                        @foreach ($allStreaks as $streakOption)
                            <option value="{{ route('streaks.calendar', ['id' => $streakOption->id, 'month' => $selectedMonth, 'year' => $selectedYear]) }}" @selected($streakOption->id === $streak->id)>
                                {{ $streakOption->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4 flex items-center gap-2">
                    <a
                        href="{{ $canGoPrevious ? route('streaks.calendar', ['id' => $streak->id, 'month' => $previousMonthStart->month, 'year' => $previousMonthStart->year]) : '#' }}"
                        @class([
                            'inline-flex h-11 w-11 items-center justify-center rounded-xl border text-xl font-bold leading-none transition duration-200',
                            'border-slate-200 bg-white text-slate-700 hover:bg-slate-100 active:scale-95 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800' => $canGoPrevious,
                            'pointer-events-none cursor-not-allowed border-slate-100 bg-slate-100 text-slate-300 dark:border-slate-800 dark:bg-slate-800 dark:text-slate-600' => ! $canGoPrevious,
                        ])
                    >
                        &lt;
                    </a>

                    <form method="GET" action="{{ route('streaks.calendar', $streak->id) }}" class="grid flex-1 grid-cols-2 gap-2">
                        <select
                            name="month"
                            class="w-full rounded-xl border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 shadow-sm focus:border-indigo-400 focus:ring-indigo-400 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:focus:border-indigo-500 dark:focus:ring-indigo-500"
                            onchange="this.form.submit()"
                        >
                            @foreach ($monthOptions as $monthOption)
                                <option value="{{ $monthOption['value'] }}" @selected($selectedMonth === $monthOption['value'])>
                                    {{ $monthOption['label'] }}
                                </option>
                            @endforeach
                        </select>

                        <select
                            name="year"
                            class="w-full rounded-xl border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 shadow-sm focus:border-indigo-400 focus:ring-indigo-400 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:focus:border-indigo-500 dark:focus:ring-indigo-500"
                            onchange="this.form.submit()"
                        >
                            @foreach ($yearOptions as $yearOption)
                                <option value="{{ $yearOption }}" @selected($selectedYear === $yearOption)>
                                    {{ $yearOption }}
                                </option>
                            @endforeach
                        </select>
                    </form>

                    <a
                        href="{{ $canGoNext ? route('streaks.calendar', ['id' => $streak->id, 'month' => $nextMonthStart->month, 'year' => $nextMonthStart->year]) : '#' }}"
                        @class([
                            'inline-flex h-11 w-11 items-center justify-center rounded-xl border text-xl font-bold leading-none transition duration-200',
                            'border-slate-200 bg-white text-slate-700 hover:bg-slate-100 active:scale-95 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800' => $canGoNext,
                            'pointer-events-none cursor-not-allowed border-slate-100 bg-slate-100 text-slate-300 dark:border-slate-800 dark:bg-slate-800 dark:text-slate-600' => ! $canGoNext,
                        ])
                    >
                        &gt;
                    </a>
                </div>

                <div class="mb-4 flex items-center justify-between gap-2">
                    <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">{{ $monthLabel }}</h3>
                    <span class="text-xs text-slate-400 dark:text-slate-500">{{ __('Tap to update') }}</span>
                </div>

                <div class="mb-3 flex flex-wrap gap-2 text-xs text-slate-500 dark:text-slate-400">
                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-1 dark:bg-slate-800">
                        <span class="h-2.5 w-2.5 rounded-sm bg-green-500"></span>{{ __('Done') }}
                    </span>
                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-1 dark:bg-slate-800">
                        <span class="h-2.5 w-2.5 rounded-sm bg-red-500"></span>{{ __('Skipped') }}
                    </span>
                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-1 dark:bg-slate-800">
                        <span class="h-2.5 w-2.5 rounded-sm bg-slate-300 dark:bg-slate-600"></span>{{ __('Not marked') }}
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <div class="min-w-[18rem]">
                        <div class="mb-2 grid grid-cols-7 gap-2 text-center text-xs font-medium text-slate-500 dark:text-slate-400">
                            <span>{{ __('Sun') }}</span>
                            <span>{{ __('Mon') }}</span>
                            <span>{{ __('Tue') }}</span>
                            <span>{{ __('Wed') }}</span>
                            <span>{{ __('Thu') }}</span>
                            <span>{{ __('Fri') }}</span>
                            <span>{{ __('Sat') }}</span>
                        </div>

                        <div class="grid grid-cols-7 gap-2">
                            @for ($i = 0; $i < $firstDayOffset; $i++)
                                <div class="h-8 w-8"></div>
                            @endfor

                            @for ($day = 1; $day <= $daysInMonth; $day++)
                                @php
                                    $date = $monthStart->copy()->day($day)->toDateString();
                                    $isFuture = $date > $todayDate;
                                @endphp

                                <button
                                    type="button"
                                    @click="openEditor('{{ $date }}')"
                                    @disabled($isFuture)
                                    :class="dayClass('{{ $date }}', {{ $isFuture ? 'true' : 'false' }})"
                                    :title="dayTitle('{{ $date }}', {{ $isFuture ? 'true' : 'false' }})"
                                    @class([
                                        'flex h-8 w-8 items-center justify-center rounded-md text-xs font-medium transition duration-150',
                                        'active:scale-95' => ! $isFuture,
                                        'ring-2 ring-indigo-300 ring-offset-1 dark:ring-indigo-400 dark:ring-offset-slate-900' => $date === $todayDate,
                                    ])
                                >
                                    {{ $day }}
                                </button>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div
            x-show="editor.open"
            x-cloak
            class="fixed inset-0 z-50 flex items-end justify-center bg-slate-900/40 px-4 pb-20 sm:items-center sm:pb-4 dark:bg-slate-950/75"
            @click.self="closeEditor()"
        >
            <div class="w-full max-w-sm rounded-2xl bg-white p-4 shadow-xl dark:bg-slate-900 dark:shadow-black/30">
                <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ __('Update Day') }}</h2>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400" x-text="editor.dateLabel"></p>
                <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">
                    <span>{{ __('Current:') }}</span>
                    <span x-text="editor.currentLabel"></span>
                </p>

                <div class="mt-4 grid grid-cols-2 gap-2">
                    <button
                        type="button"
                        class="inline-flex h-10 items-center justify-center rounded-xl bg-emerald-600 px-3 text-sm font-semibold text-white transition duration-200 hover:bg-emerald-500 active:scale-95 disabled:cursor-not-allowed disabled:opacity-60"
                        :disabled="editor.saving"
                        @click="markDay('done')"
                    >
                        ✔ {{ __('Done') }}
                    </button>

                    <button
                        type="button"
                        class="inline-flex h-10 items-center justify-center rounded-xl bg-rose-600 px-3 text-sm font-semibold text-white transition duration-200 hover:bg-rose-500 active:scale-95 disabled:cursor-not-allowed disabled:opacity-60"
                        :disabled="editor.saving"
                        @click="markDay('skipped')"
                    >
                        ✖ {{ __('Skipped') }}
                    </button>
                </div>

                <button
                    type="button"
                    class="mt-3 inline-flex min-h-10 w-full items-center justify-center rounded-xl bg-slate-100 px-3 text-sm font-medium text-slate-700 transition duration-200 hover:bg-slate-200 active:scale-95 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
                    @click="closeEditor()"
                >
                    {{ __('Cancel') }}
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('streakCalendar', (config) => ({
                todayDate: config.todayDate,
                markUrl: config.markUrl,
                csrfToken: config.csrfToken,
                statuses: config.initialStatuses ?? {},
                toast: {
                    show: false,
                    message: '',
                    timer: null,
                },
                editor: {
                    open: false,
                    date: null,
                    dateLabel: '',
                    currentLabel: '',
                    saving: false,
                },

                dayClass(date, isFuture) {
                    if (isFuture) return 'bg-slate-100 text-slate-400 opacity-50 dark:bg-slate-800 dark:text-slate-500';

                    const status = this.statuses[date] ?? null;
                    if (status === 'done') return 'bg-green-500 text-white';
                    if (status === 'skipped') return 'bg-red-500 text-white';

                    return 'bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-200';
                },

                dayTitle(date, isFuture) {
                    if (isFuture) return 'Future day';

                    const status = this.statuses[date] ?? null;
                    if (status === 'done') return `${date} - Done (tap to update)`;
                    if (status === 'skipped') return `${date} - Skipped (tap to update)`;

                    return `${date} - Not marked (tap to update)`;
                },

                openEditor(date) {
                    if (date > this.todayDate) return;

                    this.editor.date = date;
                    this.editor.dateLabel = new Date(`${date}T00:00:00`).toLocaleDateString(undefined, {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric',
                    });
                    this.editor.currentLabel = this.statuses[date] === 'done'
                        ? 'Done'
                        : this.statuses[date] === 'skipped'
                            ? 'Skipped'
                            : 'Not marked';
                    this.editor.open = true;
                },

                closeEditor() {
                    if (this.editor.saving) return;
                    this.editor.open = false;
                },

                showToast(message) {
                    this.toast.message = message;
                    this.toast.show = true;
                    clearTimeout(this.toast.timer);
                    this.toast.timer = setTimeout(() => {
                        this.toast.show = false;
                    }, 1800);
                },

                async markDay(status) {
                    if (!this.editor.date || this.editor.saving) return;

                    this.editor.saving = true;

                    try {
                        const response = await fetch(this.markUrl, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': this.csrfToken,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                date: this.editor.date,
                                status,
                            }),
                        });

                        const payload = await response.json();
                        if (!response.ok) {
                            throw new Error(payload.message || 'Could not update day');
                        }

                        this.statuses[this.editor.date] = status;
                        this.closeEditor();
                        this.showToast(payload.message || 'Day updated');
                    } catch (error) {
                        this.showToast(error.message || 'Could not update day');
                    } finally {
                        this.editor.saving = false;
                    }
                },
            }));
        });
    </script>
</x-app-layout>
