<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'stripe_customer_id',
        'subscription_status',
        'subscription_tier',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function businesses()
    {
        return $this->hasMany(Business::class);
    }

    public function subscriptionUsages()
    {
        return $this->hasMany(SubscriptionUsage::class);
    }

    public function isPro(): bool
    {
        return $this->subscription_tier === 'pro' && $this->subscription_status === 'active';
    }

    public function getCurrentMonthUsage(): ?SubscriptionUsage
    {
        return $this->subscriptionUsages()
            ->where('month', now()->format('Y-m'))
            ->first();
    }

    public function getReviewsLimit(): int
    {
        return $this->isPro() ? PHP_INT_MAX : 10;
    }

    public function canGenerateResponse(): bool
    {
        $usage = $this->getCurrentMonthUsage();
        
        if (!$usage) {
            return true; // First request this month
        }

        return $usage->reviews_used < $usage->reviews_limit;
    }

    public function incrementUsage(): void
    {
        $month = now()->format('Y-m');
        $usage = $this->subscriptionUsages()->firstOrCreate(
            ['month' => $month],
            ['reviews_used' => 0, 'reviews_limit' => $this->getReviewsLimit()]
        );
        
        $usage->increment('reviews_used');
    }
}
