<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'month',
        'reviews_used',
        'reviews_limit',
    ];

    protected function casts(): array
    {
        return [
            'reviews_used' => 'integer',
            'reviews_limit' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getRemainingAttribute(): int
    {
        return max(0, $this->reviews_limit - $this->reviews_used);
    }

    public function isLimitReached(): bool
    {
        return $this->reviews_used >= $this->reviews_limit;
    }
}
