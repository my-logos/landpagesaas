<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'wallet_balance',
        // optional: store a simple role label if you want quick checks
        'role',
        'is_active',
        'additional_sales_enabled',
        'performance_mode',
        'include_session_data',
        'include_location_data',
        'include_device_type',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string,string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'wallet_balance' => 'decimal:2',
        'additional_sales_enabled' => 'boolean',
        'include_session_data' => 'boolean',
        'include_location_data' => 'boolean',
        'include_device_type' => 'boolean',
    ];

    /**
     * Relationship: user has many subscriptions
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(\App\Models\Subscription::class);
    }

    /**
     * Relationship: user has one subscription (get latest active or any)
     */
    public function subscription(): HasOne
    {
        return $this->hasOne(\App\Models\Subscription::class)
            ->orderBy('created_at', 'desc');
    }

    /**
     * Relationship: user has many products
     */
    public function products(): HasMany
    {
        return $this->hasMany(\App\Models\Product::class);
    }

    /**
     * Relationship: user has many pages
     */
    public function pages(): HasMany
    {
        return $this->hasMany(\App\Models\Page::class);
    }

    /**
     * Relationship: user has many orders
     */
    public function orders(): HasMany
    {
        return $this->hasMany(\App\Models\Order::class);
    }

    /**
     * Relationship: user has many transactions
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(\App\Models\Transaction::class);
    }

    /**
     * Relationship: user has many payments
     */
    public function payments(): HasMany
    {
        return $this->hasMany(\App\Models\Payment::class);
    }

    /**
     * Relationship: user has many invoices
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(\App\Models\Invoice::class);
    }

    /**
     * Relationship: user has many webhooks
     */
    public function webhooks(): HasMany
    {
        return $this->hasMany(\App\Models\Webhook::class);
    }

    /**
     * Quick helper to check admin
     */
    public function isAdmin(): bool
    {
        if (method_exists($this, 'hasRole')) {
            try {
                return $this->hasRole('admin');
            } catch (\Throwable $e) {
                // ignore and fall back
            }
        }

        return ($this->role ?? '') === 'admin';
    }
}
