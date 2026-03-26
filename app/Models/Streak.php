<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
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
        return $this->logs();
    }

    public function logs(): HasMany
    {
        return $this->hasMany(StreakLog::class);
    }

    public function getStatsAttribute(): array
    {
        $today = today()->startOfDay();
        $startDate = ($this->created_at ?? $today)->copy()->startOfDay();
        if ($startDate->gt($today)) {
            $startDate = $today->copy();
        }

        $logs = match (true) {
            $this->relationLoaded('logs') => $this->logs->sortBy('date')->values(),
            $this->relationLoaded('streakLogs') => $this->streakLogs->sortBy('date')->values(),
            default => $this->logs()->orderBy('date')->get(),
        };
        $logs = $logs->filter(fn (StreakLog $log) => $log->date->lte($today))->values();

        return [
            'current' => $this->calculateCurrentStreak($logs, $startDate, $today),
            'longest' => $this->calculateLongestStreak($logs, $startDate, $today),
            'percentage' => $this->calculateSuccessPercentage($logs, $startDate, $today),
        ];
    }

    private function calculateCurrentStreak(Collection $logs, Carbon $startDate, Carbon $today): int
    {
        $logsByDate = $logs->keyBy(fn (StreakLog $log) => $log->date->toDateString());
        $cursor = $today->copy();
        $current = 0;

        while ($cursor->gte($startDate)) {
            $entry = $logsByDate->get($cursor->toDateString());

            if (! $entry || $entry->status !== 'done') {
                break;
            }

            $current++;
            $cursor->subDay();
        }

        return $current;
    }

    private function calculateLongestStreak(Collection $logs, Carbon $startDate, Carbon $today): int
    {
        $logsByDate = $logs->keyBy(fn (StreakLog $log) => $log->date->toDateString());
        $cursor = $startDate->copy();
        $running = 0;
        $longest = 0;

        while ($cursor->lte($today)) {
            $entry = $logsByDate->get($cursor->toDateString());

            if (! $entry || $entry->status !== 'done') {
                $running = 0;
            } else {
                $running++;
            }

            $longest = max($longest, $running);
            $cursor->addDay();
        }

        return $longest;
    }

    private function calculateSuccessPercentage(Collection $logs, Carbon $startDate, Carbon $today): int
    {
        $totalDays = $startDate->diffInDays($today) + 1;

        if ($totalDays === 0) {
            return 0;
        }

        $doneDays = $logs
            ->filter(fn (StreakLog $log) => $log->status === 'done' && $log->date->between($startDate, $today))
            ->count();

        return (int) round(($doneDays / $totalDays) * 100);
    }
}
