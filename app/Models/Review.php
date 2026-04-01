<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'external_id',
        'source',
        'author_name',
        'rating',
        'text',
        'sentiment',
        'review_date',
        'is_responded',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'review_date' => 'date',
            'is_responded' => 'boolean',
        ];
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function responses()
    {
        return $this->hasMany(Response::class);
    }

    public function getLatestResponse(): ?Response
    {
        return $this->responses()->latest()->first();
    }

    public function getSentimentFromRating(): string
    {
        if ($this->rating >= 4) {
            return 'positive';
        } elseif ($this->rating === 3) {
            return 'neutral';
        }
        return 'negative';
    }

    public function detectSentiment(): void
    {
        if (!$this->sentiment) {
            $this->sentiment = $this->getSentimentFromRating();
            $this->save();
        }
    }

    public function scopePositive($query)
    {
        return $query->where('sentiment', 'positive');
    }

    public function scopeNeutral($query)
    {
        return $query->where('sentiment', 'neutral');
    }

    public function scopeNegative($query)
    {
        return $query->where('sentiment', 'negative');
    }

    public function scopeUnresponded($query)
    {
        return $query->where('is_responded', false);
    }
}
