<?php

namespace App\Models;

use App\Notifications\NegativeReviewAlert;
use App\Notifications\NewReviewNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'notify_new_reviews',
        'notify_negative_reviews',
        'notification_email',
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
            'notify_new_reviews' => 'boolean',
            'notify_negative_reviews' => 'boolean',
        ];
    }

    public function businesses(): HasMany
    {
        return $this->hasMany(Business::class);
    }

    public function subscriptionUsages(): HasMany
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

    public function getNotificationEmail(): string
    {
        return $this->notification_email ?? $this->email;
    }

    public function sendNewReviewNotification(Review $review, string $businessName): void
    {
        if (!$this->notify_new_reviews) {
            return;
        }

        $this->notify(new NewReviewNotification($review, $businessName));
    }

    public function sendNegativeReviewAlert(Review $review, string $businessName): void
    {
        // Negative reviews always send alerts if enabled
        if (!$this->notify_negative_reviews) {
            return;
        }

        $this->notify(new NegativeReviewAlert($review, $businessName));
    }

    public function routeNotificationForMail(): string
    {
        return $this->getNotificationEmail();
    }
}
