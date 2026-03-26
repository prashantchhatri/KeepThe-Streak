<?php

namespace App\Http\Controllers;

use App\Models\Streak;
use App\Models\StreakLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class StreakController extends Controller
{
    public function index(Request $request): View
    {
        $streaks = auth()->user()
            ->streaks()
            ->with([
                'logs' => fn ($query) => $query->orderBy('date'),
            ])
            ->latest()
            ->get();
        $streaks->each->append('stats');
        $totalStreaks = $streaks->count();
        $chartData = $this->buildDailyCompletionChart($streaks, $totalStreaks);

        return view('streaks.index', compact('streaks', 'chartData', 'totalStreaks'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);

        $request->user()->streaks()->create([
            'name' => $validated['name'],
            'is_active' => true,
        ]);

        return redirect()->route('dashboard')->with('status', 'Streak created successfully.');
    }

    public function calendar(Request $request, int $id): View
    {
        $streak = $request->user()->streaks()->findOrFail($id);
        $allStreaks = $request->user()->streaks()->latest()->get(['id', 'name']);

        $today = today()->startOfDay();
        $todayMonthStart = $today->copy()->startOfMonth();
        $selectedMonth = (int) $request->integer('month', $today->month);
        $selectedYear = (int) $request->integer('year', $today->year);
        $selectedMonth = $selectedMonth >= 1 && $selectedMonth <= 12 ? $selectedMonth : $today->month;
        $selectedYear = $selectedYear >= 2000 && $selectedYear <= $today->year ? $selectedYear : $today->year;

        $monthStart = Carbon::create($selectedYear, $selectedMonth, 1)->startOfMonth();
        if ($monthStart->gt($todayMonthStart)) {
            $monthStart = $todayMonthStart->copy();
            $selectedMonth = $monthStart->month;
            $selectedYear = $monthStart->year;
        }

        $monthEnd = $monthStart->copy()->endOfMonth();
        $todayDate = $today->toDateString();

        $streakStartMonth = $streak->created_at->copy()->startOfMonth();
        $baselineHistoryMonth = $todayMonthStart->copy()->subMonths(12);
        $minimumBrowseMonth = $streakStartMonth->lt($baselineHistoryMonth)
            ? $streakStartMonth
            : $baselineHistoryMonth;
        $previousMonthStart = $monthStart->copy()->subMonth();
        $nextMonthStart = $monthStart->copy()->addMonth();
        $canGoPrevious = $previousMonthStart->gte($minimumBrowseMonth);
        $canGoNext = $nextMonthStart->lte($todayMonthStart);

        $yearOptions = range($today->year, $minimumBrowseMonth->year);
        $monthOptions = collect(range(1, 12))
            ->map(fn (int $month) => [
                'value' => $month,
                'label' => Carbon::create($today->year, $month, 1)->format('F'),
            ])
            ->all();

        $logStatuses = $streak->logs()
            ->whereBetween('date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->orderBy('date')
            ->get()
            ->mapWithKeys(fn (StreakLog $log) => [$log->date->toDateString() => $log->status])
            ->all();

        return view('streaks.calendar', [
            'streak' => $streak,
            'allStreaks' => $allStreaks,
            'monthStart' => $monthStart,
            'monthLabel' => $monthStart->format('F Y'),
            'daysInMonth' => $monthStart->daysInMonth,
            'firstDayOffset' => $monthStart->dayOfWeek,
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
            'monthOptions' => $monthOptions,
            'yearOptions' => $yearOptions,
            'previousMonthStart' => $previousMonthStart,
            'nextMonthStart' => $nextMonthStart,
            'canGoPrevious' => $canGoPrevious,
            'canGoNext' => $canGoNext,
            'todayDate' => $todayDate,
            'todayLabel' => today()->format('d M Y'),
            'logStatuses' => $logStatuses,
        ]);
    }

    public function markDone(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $streak = $request->user()
            ->streaks()
            ->with([
                'logs' => fn ($query) => $query->orderBy('date'),
            ])
            ->findOrFail($id);
        $oldCurrent = $streak->stats['current'];

        StreakLog::updateOrCreate(
            ['streak_id' => $streak->id, 'date' => today()->toDateString()],
            ['status' => 'done']
        );

        $streak->unsetRelation('logs');
        $streak->load([
            'logs' => fn ($query) => $query->orderBy('date'),
        ]);
        $newCurrent = $streak->stats['current'];

        if ($newCurrent > $oldCurrent) {
            $request->session()->flash('streak_increased', true);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Marked as done',
                'streak_increased' => $newCurrent > $oldCurrent,
                'streak' => $this->formatStreakState($streak, today()->toDateString()),
            ]);
        }

        return redirect()->route('dashboard')->with('status', 'Marked as done');
    }

    public function markSkipped(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $streak = $request->user()
            ->streaks()
            ->with([
                'logs' => fn ($query) => $query->orderBy('date'),
            ])
            ->findOrFail($id);

        StreakLog::updateOrCreate(
            ['streak_id' => $streak->id, 'date' => today()->toDateString()],
            ['status' => 'skipped']
        );

        $streak->unsetRelation('logs');
        $streak->load([
            'logs' => fn ($query) => $query->orderBy('date'),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Marked as skipped',
                'streak' => $this->formatStreakState($streak, today()->toDateString()),
            ]);
        }

        return redirect()->route('dashboard')->with('status', 'Marked as skipped');
    }

    public function markAllDone(Request $request): JsonResponse|RedirectResponse
    {
        $today = today()->toDateString();
        $streakIds = $request->user()->streaks()->pluck('id');

        foreach ($streakIds as $streakId) {
            StreakLog::updateOrCreate(
                ['streak_id' => $streakId, 'date' => $today],
                ['status' => 'done']
            );
        }

        if ($request->expectsJson()) {
            $streaks = $request->user()
                ->streaks()
                ->with([
                    'logs' => fn ($query) => $query->orderBy('date'),
                ])
                ->get();

            return response()->json([
                'message' => 'Marked as done',
                'streaks' => $streaks
                    ->map(fn (Streak $streak) => $this->formatStreakState($streak, $today))
                    ->values(),
            ]);
        }

        return redirect()->route('dashboard')->with('status', 'Marked as done');
    }

    public function markDate(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $streak = $request->user()->streaks()->findOrFail($id);

        $validated = $request->validate([
            'date' => ['required', 'date_format:Y-m-d'],
            'status' => ['required', 'in:done,skipped'],
        ]);

        $date = Carbon::createFromFormat('Y-m-d', $validated['date'])->startOfDay();
        if ($date->isFuture()) {
            if (! $request->expectsJson()) {
                return redirect()->back()->withErrors(['date' => 'Future dates cannot be marked.']);
            }

            return response()->json([
                'message' => 'Future dates cannot be marked.',
            ], 422);
        }

        $streakStart = $streak->created_at->copy()->startOfDay();
        if ($date->lt($streakStart)) {
            if (! $request->expectsJson()) {
                return redirect()->back()->withErrors(['date' => 'Date is before this streak was created.']);
            }

            return response()->json([
                'message' => 'Date is before this streak was created.',
            ], 422);
        }

        StreakLog::updateOrCreate(
            ['streak_id' => $streak->id, 'date' => $date->toDateString()],
            ['status' => $validated['status']]
        );

        $streak->unsetRelation('logs');
        $streak->load([
            'logs' => fn ($query) => $query->orderBy('date'),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Day updated',
                'date' => $date->toDateString(),
                'status' => $validated['status'],
                'streak' => $this->formatStreakState($streak, today()->toDateString()),
            ]);
        }

        return redirect()->back()->with('status', 'Day updated');
    }

    public function destroy(Request $request, Streak $streak): RedirectResponse
    {
        abort_unless($streak->user_id === $request->user()->id, 403);

        $streak->delete();

        return redirect()->route('dashboard')->with('status', 'Streak deleted successfully.');
    }

    public function delete(Request $request, int $id): RedirectResponse
    {
        $streak = $request->user()->streaks()->findOrFail($id);
        $streak->delete();

        return redirect()->route('dashboard')->with('status', 'Streak deleted successfully.');
    }

    private function formatStreakState(Streak $streak, string $todayDate): array
    {
        return [
            'id' => $streak->id,
            'today_status' => optional(
                $streak->logs->first(fn (StreakLog $log) => $log->date->toDateString() === $todayDate)
            )->status,
            'stats' => $streak->stats,
        ];
    }

    private function buildDailyCompletionChart(Collection $streaks, int $totalStreaks): array
    {
        $weekStart = today()->copy()->startOfWeek(Carbon::MONDAY);
        $dates = collect(range(0, 6))
            ->map(fn (int $offset) => $weekStart->copy()->addDays($offset))
            ->values();

        if ($totalStreaks === 0) {
            return [];
        }

        $streakIds = $streaks->pluck('id');
        $doneCountByDate = StreakLog::query()
            ->whereIn('streak_id', $streakIds)
            ->where('status', 'done')
            ->whereDate('date', '>=', $dates->first()->toDateString())
            ->whereDate('date', '<=', $dates->last()->toDateString())
            ->selectRaw('DATE(date) as log_date, COUNT(*) as done_count')
            ->groupByRaw('DATE(date)')
            ->pluck('done_count', 'log_date');

        return $dates->map(function (Carbon $date) use ($doneCountByDate, $totalStreaks) {
            $dateKey = $date->toDateString();

            return [
                'date_key' => $dateKey,
                'date' => $date->format('d M'),
                'weekday' => $date->format('D'),
                'done' => (int) ($doneCountByDate[$dateKey] ?? 0),
                'total' => $totalStreaks,
            ];
        })->all();
    }
}
