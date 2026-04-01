<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Business extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'address',
        'location_slug',
        'google_place_id',
        'yelp_business_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function teamMembers(): HasMany
    {
        return $this->hasMany(TeamMember::class);
    }

    public function getPositiveReviewsCount(): int
    {
        return $this->reviews()->where('sentiment', 'positive')->count();
    }

    public function getNegativeReviewsCount(): int
    {
        return $this->reviews()->where('sentiment', 'negative')->count();
    }

    public function getAverageRating(): float
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    public function getAllMembers(): \Illuminate\Support\Collection
    {
        // Owner + team members
        $owner = collect([$this->user]);
        
        $members = $this->teamMembers()
            ->with('user')
            ->accepted()
            ->get()
            ->map(function ($tm) {
                $tm->user->team_role = $tm->role;
                return $tm->user;
            });
        
        return $owner->merge($members);
    }
}
