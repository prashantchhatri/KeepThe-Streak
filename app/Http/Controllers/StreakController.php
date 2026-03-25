<?php

namespace App\Http\Controllers;

use App\Models\Streak;
use App\Models\StreakLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StreakController extends Controller
{
    public function index(Request $request): View
    {
        $streaks = $request->user()
            ->streaks()
            ->with([
                'streakLogs' => fn ($query) => $query->orderBy('date'),
            ])
            ->latest()
            ->get();
        $streaks->each->append('stats');

        return view('streaks.index', compact('streaks'));
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

        $monthStart = today()->startOfMonth();
        $monthEnd = today()->endOfMonth();

        $logsByDate = $streak->streakLogs()
            ->whereBetween('date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->orderBy('date')
            ->get()
            ->groupBy(fn (StreakLog $log) => $log->date->toDateString());

        return view('streaks.calendar', [
            'streak' => $streak,
            'monthStart' => $monthStart,
            'monthLabel' => $monthStart->format('F Y'),
            'daysInMonth' => $monthStart->daysInMonth,
            'firstDayOffset' => $monthStart->dayOfWeek,
            'logsByDate' => $logsByDate,
        ]);
    }

    public function markDone(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $streak = $request->user()
            ->streaks()
            ->with([
                'streakLogs' => fn ($query) => $query->orderBy('date'),
            ])
            ->findOrFail($id);
        $oldCurrent = $streak->stats['current'];

        StreakLog::updateOrCreate(
            ['streak_id' => $streak->id, 'date' => today()->toDateString()],
            ['status' => 'done']
        );

        $streak->unsetRelation('streakLogs');
        $streak->load([
            'streakLogs' => fn ($query) => $query->orderBy('date'),
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
                'streakLogs' => fn ($query) => $query->orderBy('date'),
            ])
            ->findOrFail($id);

        StreakLog::updateOrCreate(
            ['streak_id' => $streak->id, 'date' => today()->toDateString()],
            ['status' => 'skipped']
        );

        $streak->unsetRelation('streakLogs');
        $streak->load([
            'streakLogs' => fn ($query) => $query->orderBy('date'),
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
                    'streakLogs' => fn ($query) => $query->orderBy('date'),
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

    public function destroy(Request $request, Streak $streak): RedirectResponse
    {
        abort_unless($streak->user_id === $request->user()->id, 403);

        $streak->delete();

        return redirect()->route('dashboard')->with('status', 'Streak deleted successfully.');
    }

    private function formatStreakState(Streak $streak, string $todayDate): array
    {
        return [
            'id' => $streak->id,
            'today_status' => optional(
                $streak->streakLogs->first(fn (StreakLog $log) => $log->date->toDateString() === $todayDate)
            )->status,
            'stats' => $streak->stats,
        ];
    }
}
