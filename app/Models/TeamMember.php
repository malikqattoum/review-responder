<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'user_id',
        'role',
        'invite_email',
        'invite_token',
        'invited_at',
        'accepted_at',
    ];

    protected function casts(): array
    {
        return [
            'invited_at' => 'datetime',
            'accepted_at' => 'datetime',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isPending(): bool
    {
        return $this->accepted_at === null;
    }

    public function isAccepted(): bool
    {
        return $this->accepted_at !== null;
    }

    public function generateInviteToken(): string
    {
        $this->invite_token = bin2hex(random_bytes(32));
        $this->invited_at = now();
        $this->save();
        return $this->invite_token;
    }

    public function accept(int $userId): void
    {
        $this->user_id = $userId;
        $this->accepted_at = now();
        $this->invite_token = null;
        $this->save();
    }

    public function scopePending($query)
    {
        return $query->whereNull('accepted_at');
    }

    public function scopeAccepted($query)
    {
        return $query->whereNotNull('accepted_at');
    }
}
