<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StreakLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'streak_id',
        'date',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function streak(): BelongsTo
    {
        return $this->belongsTo(Streak::class);
    }
}
