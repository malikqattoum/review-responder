<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class YelpFusionService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://api.yelp.com/v3';

    public function __construct()
    {
        $this->apiKey = config('services.yelp.fusion_api_key', env('YELP_FUSION_API_KEY', ''));
    }

    /**
     * Check if Yelp API is configured
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
            // Extract city/state from address for better search
            $location = $this->extractLocation($address);
            
            $response = Http::timeout(10)
                ->withHeaders(['Authorization' => 'Bearer ' . $this->apiKey])
                ->get("{$this->baseUrl}/businesses/search", [
                    'term' => $name,
                    'location' => $location,
                    'limit' => 1,
                ]);

            if ($response->successful() && isset($response['businesses'][0])) {
                return $response['businesses'][0];
            }
        } catch (\Exception $e) {
            Log::error('Yelp Fusion search failed: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Get business by Yelp ID
     */
    public function getBusiness(string $yelpId): ?array
    {
        if (!$this->isConfigured()) {
            return null;
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders(['Authorization' => 'Bearer ' . $this->apiKey])
                ->get("{$this->baseUrl}/businesses/{$yelpId}");

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            Log::error('Yelp Fusion business lookup failed: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Get business reviews
     */
    public function getReviews(string $yelpId): array
    {
        if (!$this->isConfigured()) {
            return [];
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders(['Authorization' => 'Bearer ' . $this->apiKey])
                ->get("{$this->baseUrl}/businesses/{$yelpId}/reviews");

            if ($response->successful() && isset($response['reviews'])) {
                return $this->formatReviews($response['reviews']);
            }
        } catch (\Exception $e) {
            Log::error('Yelp Fusion reviews lookup failed: ' . $e->getMessage());
        }

        return [];
    }

    /**
     * Search and get reviews in one call
     */
    public function searchAndGetReviews(string $name, string $address): array
    {
        $business = $this->searchBusiness($name, $address);
        
        if (!$business || !isset($business['id'])) {
            return [];
        }

        return $this->getReviews($business['id']);
    }

    /**
     * Format Yelp reviews to our format
     */
    protected function formatReviews(array $yelpReviews): array
    {
        $reviews = [];
        
        foreach ($yelpReviews as $review) {
            $rating = $review['rating'] ?? 3;
            
            $reviews[] = [
                'external_id' => 'yelp_' . ($review['id'] ?? uniqid()),
                'source' => 'yelp',
                'author_name' => $review['user']['name'] ?? $review['user']['id'] ?? 'Anonymous',
                'rating' => $rating,
                'text' => $review['text'] ?? '',
                'review_date' => isset($review['time_created']) 
                    ? date('Y-m-d', strtotime($review['time_created'])) 
                    : now()->format('Y-m-d'),
                'yelp_business_id' => $review['business_id'] ?? null,
                'yelp_rating' => $rating,
                'yelp_url' => $review['url'] ?? null,
                'yelp_user_url' => $review['user']['profile_url'] ?? null,
            ];
        }

        return $reviews;
    }

    /**
     * Extract location string from full address
     */
    protected function extractLocation(string $address): string
    {
        // Try to extract City, State format
        if (preg_match('/([A-Za-z\s]+),\s*([A-Z]{2})\s*(\d{5})?/', $address, $matches)) {
            return $matches[1] . ', ' . $matches[2];
        }
        
        // If full address, try to use just city/state
        if (preg_match('/([A-Za-z\s]+,\s*[A-Z]{2})/', $address, $matches)) {
            return $matches[1];
        }

        // Fallback to full address
        return $address;
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
