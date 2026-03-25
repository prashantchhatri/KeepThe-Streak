<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <button
                type="button"
                onclick="if (window.history.length > 1) { window.history.back(); } else { window.location.href = '/dashboard'; }"
                class="inline-flex min-h-10 items-center justify-center rounded-xl bg-slate-100 px-3 text-sm font-medium text-slate-700 transition duration-200 hover:bg-slate-200 active:scale-95"
            >
                ← {{ __('Back') }}
            </button>
            <h1 class="text-base font-semibold text-slate-900">{{ __('Calendar') }}</h1>
            <div class="w-[72px]"></div>
        </div>
    </x-slot>

    <div class="mx-auto w-full max-w-md px-4 py-6">
        <div class="mb-4 text-center">
            <h2 class="text-sm font-medium text-slate-500">{{ $monthLabel }}</h2>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <h3 class="mb-4 text-base font-bold text-slate-900">{{ $streak->name }}</h3>

            <div class="mb-3 flex flex-wrap gap-2 text-xs text-gray-500">
                <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2 py-1">
                    <span class="h-2.5 w-2.5 rounded-sm bg-green-500"></span>{{ __('Done') }}
                </span>
                <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2 py-1">
                    <span class="h-2.5 w-2.5 rounded-sm bg-red-500"></span>{{ __('Skipped') }}
                </span>
                <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2 py-1">
                    <span class="h-2.5 w-2.5 rounded-sm bg-gray-300"></span>{{ __('No entry') }}
                </span>
            </div>

            <div class="overflow-x-auto">
                <div class="min-w-[18rem]">
                    <div class="mb-2 grid grid-cols-7 gap-2 text-center text-xs font-medium text-gray-500">
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
                                $status = optional($logsByDate->get($date))->first()?->status;
                                $cellClasses = match ($status) {
                                    'done' => 'bg-green-500 text-white',
                                    'skipped' => 'bg-red-500 text-white',
                                    default => 'bg-gray-200 text-gray-600',
                                };
                            @endphp

                            <div class="flex h-8 w-8 items-center justify-center rounded-md text-xs {{ $cellClasses }}" title="{{ $date }}">
                                {{ $day }}
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
