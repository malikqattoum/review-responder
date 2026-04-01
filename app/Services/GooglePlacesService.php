<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GooglePlacesService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://maps.googleapis.com/maps/api/place';

    public function __construct()
    {
        $this->apiKey = config('services.google.places_api_key', env('GOOGLE_PLACES_API_KEY', ''));
    }

    /**
     * Check if Google API is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Search for a business by name and location
     */
    public function searchBusiness(string $name, string $address): ?array
    {
        if (!$this->isConfigured()) {
            return null;
        }

        try {
            $response = Http::timeout(10)->get('https://maps.googleapis.com/maps/api/place/textsearch/json', [
                'query' => "{$name} {$address}",
                'key' => $this->apiKey,
            ]);

            if ($response->successful() && isset($response['results'][0])) {
                return $response['results'][0];
            }
        } catch (\Exception $e) {
            Log::error('Google Places search failed: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Get place details including reviews
     */
    public function getPlaceDetails(string $placeId): ?array
    {
        if (!$this->isConfigured()) {
            return null;
        }

        try {
            $response = Http::timeout(10)->get('https://maps.googleapis.com/maps/api/place/details/json', [
                'place_id' => $placeId,
                'fields' => 'place_id,name,formatted_address,rating,reviews,user_ratings_total,url',
                'key' => $this->apiKey,
            ]);

            if ($response->successful() && isset($response['result'])) {
                return $response['result'];
            }
        } catch (\Exception $e) {
            Log::error('Google Places details failed: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Fetch and format reviews from Google
     */
    public function getReviews(string $placeId): array
    {
        $details = $this->getPlaceDetails($placeId);
        
        if (!$details || !isset($details['reviews'])) {
            return [];
        }

        $reviews = [];
        foreach ($details['reviews'] as $review) {
            $reviews[] = [
                'external_id' => 'google_' . $review['author_name'] . '_' . ($review['time'] ?? time()),
                'source' => 'google',
                'author_name' => $review['author_name'] ?? 'Anonymous',
                'rating' => $review['rating'] ?? 3,
                'text' => $review['text'] ?? '',
                'review_date' => isset($review['time']) 
                    ? date('Y-m-d', $review['time']) 
                    : now()->format('Y-m-d'),
                'google_profile_url' => $review['author_url'] ?? null,
                'google_rating' => $review['rating'] ?? null,
                'google_time' => $review['time'] ?? null,
                'google_language' => $review['language'] ?? 'en',
            ];
        }

        return $reviews;
    }

    /**
     * Get business info
     */
    public function getBusinessInfo(string $placeId): ?array
    {
        return $this->getPlaceDetails($placeId);
    }

    /**
     * Calculate sentiment from rating
     */
    public static function calculateSentiment(int $rating): string
    {
        if ($rating >= 4) {
            return 'positive';
        } elseif ($rating === 3) {
            return 'neutral';
        }
        return 'negative';
    }
}
