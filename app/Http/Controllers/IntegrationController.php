<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Review;
use App\Services\GooglePlacesService;
use App\Services\YelpFusionService;
use Illuminate\Http\Request;

class IntegrationController extends Controller
{
    protected GooglePlacesService $googleService;
    protected YelpFusionService $yelpService;

    public function __construct()
    {
        $this->googleService = new GooglePlacesService();
        $this->yelpService = new YelpFusionService();
    }

    /**
     * Get integration status
     */
    public function status(Request $request)
    {
        $googleKey = config('services.google.places_api_key', '');
        $yelpKey = config('services.yelp.fusion_api_key', '');
        
        // Check if keys are real (not placeholder values)
        $googleReal = !empty($googleKey) && 
                      !str_contains($googleKey, 'your_') && 
                      strlen($googleKey) > 20;
        
        $yelpReal = !empty($yelpKey) && 
                    !str_contains($yelpKey, 'your_') && 
                    strlen($yelpKey) > 20;
        
        return response()->json([
            'google_configured' => $this->googleService->isConfigured(),
            'google_has_real_key' => $googleReal,
            'yelp_configured' => $this->yelpService->isConfigured(),
            'yelp_has_real_key' => $yelpReal,
        ]);
    }

    /**
     * Search for Google business
     */
    public function searchGoogle(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
        ]);

        $result = $this->googleService->searchBusiness(
            $validated['name'],
            $validated['address']
        );

        if (!$result) {
            return response()->json([
                'error' => 'Google API not configured or no results found',
            ], 404);
        }

        return response()->json([
            'business' => [
                'place_id' => $result['place_id'] ?? null,
                'name' => $result['name'] ?? 'Unknown',
                'address' => $result['formatted_address'] ?? $validated['address'],
                'rating' => $result['rating'] ?? null,
                'total_reviews' => $result['user_ratings_total'] ?? 0,
            ],
        ]);
    }

    /**
     * Sync reviews from Google
     */
    public function syncGoogle(Request $request)
    {
        $validated = $request->validate([
            'business_id' => 'required|exists:businesses,id',
            'place_id' => 'required|string',
            'send_notifications' => 'boolean',
        ]);

        $business = Business::findOrFail($validated['business_id']);
        $sendNotifications = $validated['send_notifications'] ?? true;

        // Get reviews from Google
        $googleReviews = $this->googleService->getReviews($validated['place_id']);

        if (empty($googleReviews)) {
            return response()->json([
                'message' => 'No reviews found or Google API not configured',
                'imported' => 0,
                'skipped' => 0,
            ]);
        }

        $imported = 0;
        $skipped = 0;
        $newNegativeReviews = [];

        foreach ($googleReviews as $reviewData) {
            $reviewData['business_id'] = $business->id;

            // Check if already exists
            $exists = Review::where('business_id', $business->id)
                ->where('external_id', $reviewData['external_id'])
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            $review = Review::create($reviewData);
            $review->detectSentiment();
            $imported++;

            // Track negative reviews
            if ($review->sentiment === 'negative' && $sendNotifications) {
                $newNegativeReviews[] = $review;
            }
        }

        // Send notifications for negative reviews
        if ($sendNotifications && $imported > 0) {
            $this->sendNegativeAlerts($business, $newNegativeReviews);
        }

        // Update business with Google place ID if not set
        if (!$business->google_place_id) {
            $business->update(['google_place_id' => $validated['place_id']]);
        }

        return response()->json([
            'message' => "Synced {$imported} reviews from Google",
            'imported' => $imported,
            'skipped' => $skipped,
            'total_reviews_found' => count($googleReviews),
            'new_negative_reviews' => count($newNegativeReviews),
        ]);
    }

    /**
     * Search for Yelp business
     */
    public function searchYelp(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
        ]);

        $result = $this->yelpService->searchBusiness(
            $validated['name'],
            $validated['address']
        );

        if (!$result) {
            return response()->json([
                'error' => 'Yelp API not configured or no results found',
            ], 404);
        }

        return response()->json([
            'business' => [
                'yelp_id' => $result['id'] ?? null,
                'name' => $result['name'] ?? 'Unknown',
                'address' => ($result['location']['address1'] ?? '') . ', ' . ($result['location']['city'] ?? ''),
                'rating' => $result['rating'] ?? null,
                'total_reviews' => $result['review_count'] ?? 0,
                'yelp_url' => $result['url'] ?? null,
            ],
        ]);
    }

    /**
     * Sync reviews from Yelp
     */
    public function syncYelp(Request $request)
    {
        $validated = $request->validate([
            'business_id' => 'required|exists:businesses,id',
            'yelp_id' => 'required|string',
            'send_notifications' => 'boolean',
        ]);

        $business = Business::findOrFail($validated['business_id']);
        $sendNotifications = $validated['send_notifications'] ?? true;

        // Get reviews from Yelp
        $yelpReviews = $this->yelpService->getReviews($validated['yelp_id']);

        if (empty($yelpReviews)) {
            return response()->json([
                'message' => 'No reviews found or Yelp API not configured',
                'imported' => 0,
                'skipped' => 0,
            ]);
        }

        $imported = 0;
        $skipped = 0;
        $newNegativeReviews = [];

        foreach ($yelpReviews as $reviewData) {
            $reviewData['business_id'] = $business->id;

            // Check if already exists
            $exists = Review::where('business_id', $business->id)
                ->where('external_id', $reviewData['external_id'])
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            $review = Review::create($reviewData);
            $review->detectSentiment();
            $imported++;

            // Track negative reviews
            if ($review->sentiment === 'negative' && $sendNotifications) {
                $newNegativeReviews[] = $review;
            }
        }

        // Send notifications for negative reviews
        if ($sendNotifications && $imported > 0) {
            $this->sendNegativeAlerts($business, $newNegativeReviews);
        }

        // Update business with Yelp business ID if not set
        if (!$business->yelp_business_id) {
            $business->update(['yelp_business_id' => $validated['yelp_id']]);
        }

        return response()->json([
            'message' => "Synced {$imported} reviews from Yelp",
            'imported' => $imported,
            'skipped' => $skipped,
            'total_reviews_found' => count($yelpReviews),
            'new_negative_reviews' => count($newNegativeReviews),
        ]);
    }

    /**
     * Sync from both Google and Yelp
     */
    public function syncAll(Request $request)
    {
        $validated = $request->validate([
            'business_id' => 'required|exists:businesses,id',
            'send_notifications' => 'boolean',
        ]);

        $business = Business::findOrFail($validated['business_id']);
        $sendNotifications = $validated['send_notifications'] ?? true;

        $totalImported = 0;
        $totalSkipped = 0;
        $allNegativeReviews = [];

        // Sync from Google
        if ($business->google_place_id && $this->googleService->isConfigured()) {
            $result = $this->syncFromGoogle($business, $business->google_place_id, $sendNotifications);
            $totalImported += $result['imported'];
            $totalSkipped += $result['skipped'];
            $allNegativeReviews = array_merge($allNegativeReviews, $result['negative_reviews']);
        }

        // Sync from Yelp
        if ($business->yelp_business_id && $this->yelpService->isConfigured()) {
            $result = $this->syncFromYelp($business, $business->yelp_business_id, $sendNotifications);
            $totalImported += $result['imported'];
            $totalSkipped += $result['skipped'];
            $allNegativeReviews = array_merge($allNegativeReviews, $result['negative_reviews']);
        }

        // Send notifications
        if ($sendNotifications && !empty($allNegativeReviews)) {
            $this->sendNegativeAlerts($business, $allNegativeReviews);
        }

        return response()->json([
            'message' => "Synced {$totalImported} total reviews",
            'imported' => $totalImported,
            'skipped' => $totalSkipped,
            'new_negative_reviews' => count($allNegativeReviews),
        ]);
    }

    /**
     * Sync from Google helper
     */
    protected function syncFromGoogle(Business $business, string $placeId, bool $sendNotifications): array
    {
        $googleReviews = $this->googleService->getReviews($placeId);
        
        return $this->importReviews($business, $googleReviews, $sendNotifications);
    }

    /**
     * Sync from Yelp helper
     */
    protected function syncFromYelp(Business $business, string $yelpId, bool $sendNotifications): array
    {
        $yelpReviews = $this->yelpService->getReviews($yelpId);
        
        return $this->importReviews($business, $yelpReviews, $sendNotifications);
    }

    /**
     * Import reviews helper
     */
    protected function importReviews(Business $business, array $reviews, bool $sendNotifications): array
    {
        $imported = 0;
        $skipped = 0;
        $negativeReviews = [];

        foreach ($reviews as $reviewData) {
            $reviewData['business_id'] = $business->id;

            $exists = Review::where('business_id', $business->id)
                ->where('external_id', $reviewData['external_id'])
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            $review = Review::create($reviewData);
            $review->detectSentiment();
            $imported++;

            if ($review->sentiment === 'negative' && $sendNotifications) {
                $negativeReviews[] = $review;
            }
        }

        return [
            'imported' => $imported,
            'skipped' => $skipped,
            'negative_reviews' => $negativeReviews,
        ];
    }

    /**
     * Send negative review alerts
     */
    protected function sendNegativeAlerts(Business $business, array $reviews): void
    {
        if (empty($reviews) || !$business->user) {
            return;
        }

        try {
            foreach ($reviews as $review) {
                $business->user->sendNegativeReviewAlert($review, $business->name);
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to send negative review alerts: ' . $e->getMessage());
        }
    }
}
