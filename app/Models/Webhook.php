<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Webhook extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'url',
        'events',
        'is_active',
        'secret',
    ];

    protected $casts = [
        'events' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user that owns the webhook
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all logs for this webhook
     */
    public function logs(): HasMany
    {
        return $this->hasMany(WebhookLog::class);
    }

    /**
     * Get successful logs
     */
    public function successfulLogs(): HasMany
    {
        return $this->hasMany(WebhookLog::class)->where('success', true);
    }

    /**
     * Get failed logs
     */
    public function failedLogs(): HasMany
    {
        return $this->hasMany(WebhookLog::class)->where('success', false);
    }

    /**
     * Check if webhook listens to a specific event
     */
    public function listensTo(string $eventType): bool
    {
        return in_array($eventType, $this->events ?? []);
    }

    /**
     * Get success rate percentage
     */
    public function getSuccessRateAttribute(): float
    {
        $total = $this->logs()->count();
        if ($total === 0) {
            return 0.0;
        }

        $successful = $this->logs()->where('success', true)->count();
        return round(($successful / $total) * 100, 2);
    }

    /**
     * Get total deliveries count
     */
    public function getTotalDeliveriesAttribute(): int
    {
        return $this->logs()->count();
    }
}
