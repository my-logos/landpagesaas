<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sender_name',
        'sender_email',
        'sender_phone',
        'subject',
        'message',
        'order_number',
        'order_id',
        'status',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(MessageReply::class);
    }

    public function markAsRead(): void
    {
        if (!$this->read_at) {
            $this->update([
                'read_at' => now(),
                'status' => 'read',
            ]);
        }
    }

    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }

    public function hasReplies(): bool
    {
        return $this->replies()->count() > 0;
    }
}
