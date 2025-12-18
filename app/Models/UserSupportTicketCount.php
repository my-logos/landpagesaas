<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSupportTicketCount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'year',
        'month',
        'tickets_count',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function incrementForUser(int $userId): void
    {
        $now = now();
        $year = $now->year;
        $month = $now->month;

        $record = static::firstOrNew([
            'user_id' => $userId,
            'year' => $year,
            'month' => $month,
        ]);

        $record->tickets_count = ($record->tickets_count ?? 0) + 1;
        $record->save();
    }

    public static function getCurrentMonthCount(int $userId): int
    {
        $now = now();
        $record = static::where('user_id', $userId)
            ->where('year', $now->year)
            ->where('month', $now->month)
            ->first();

        return $record ? $record->tickets_count : 0;
    }
}

