<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Streak extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function streakLogs(): HasMany
    {
        return $this->hasMany(StreakLog::class);
    }

    public function getStatsAttribute(): array
    {
        $logs = $this->relationLoaded('streakLogs')
            ? $this->streakLogs->sortBy('date')->values()
            : $this->streakLogs()->orderBy('date')->get();

        if ($logs->isEmpty()) {
            return [
                'current' => 0,
                'longest' => 0,
                'percentage' => 0,
            ];
        }

        return [
            'current' => $this->calculateCurrentStreak($logs),
            'longest' => $this->calculateLongestStreak($logs),
            'percentage' => $this->calculateSuccessPercentage($logs),
        ];
    }

    private function calculateCurrentStreak(Collection $logs): int
    {
        $logsByDate = $logs->keyBy(fn (StreakLog $log) => $log->date->toDateString());
        $cursor = today();
        $current = 0;

        while (true) {
            $entry = $logsByDate->get($cursor->toDateString());

            if (! $entry || $entry->status !== 'done') {
                break;
            }

            $current++;
            $cursor->subDay();
        }

        return $current;
    }

    private function calculateLongestStreak(Collection $logs): int
    {
        $running = 0;
        $longest = 0;
        $previousDoneDate = null;

        foreach ($logs as $log) {
            if ($log->status !== 'done') {
                $running = 0;
                $previousDoneDate = null;
                continue;
            }

            if ($previousDoneDate && $log->date->isSameDay($previousDoneDate->copy()->addDay())) {
                $running++;
            } else {
                $running = 1;
            }

            $previousDoneDate = $log->date->copy();
            $longest = max($longest, $running);
        }

        return $longest;
    }

    private function calculateSuccessPercentage(Collection $logs): int
    {
        $totalDays = $logs->count();

        if ($totalDays === 0) {
            return 0;
        }

        $doneDays = $logs->where('status', 'done')->count();

        return (int) round(($doneDays / $totalDays) * 100);
    }
}
