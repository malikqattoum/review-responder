<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function reviews()
    {
        return $this->hasMany(Review::class);
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
}
