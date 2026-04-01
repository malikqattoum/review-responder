<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with('business');

        if ($request->has('business_id')) {
            $query->where('business_id', $request->business_id);
        }

        if ($request->has('sentiment')) {
            $query->where('sentiment', $request->sentiment);
        }

        if ($request->has('source')) {
            $query->where('source', $request->source);
        }

        if ($request->has('unresponded')) {
            $query->where('is_responded', false);
        }

        $reviews = $query->orderBy('review_date', 'desc')->paginate(20);

        return response()->json([
            'reviews' => $reviews->items(),
            'pagination' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'business_id' => 'required|exists:businesses,id',
            'external_id' => 'nullable|string|max:255',
            'source' => 'required|in:google,yelp,manual',
            'author_name' => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'text' => 'nullable|string',
            'review_date' => 'nullable|date',
        ]);

        // Check if review already exists
        $existingReview = Review::where('business_id', $validated['business_id'])
            ->where('external_id', $validated['external_id'])
            ->first();

        if ($existingReview) {
            return response()->json([
                'message' => 'Review already exists',
                'review' => $existingReview,
            ], 409);
        }

        $review = Review::create($validated);
        $review->detectSentiment();

        return response()->json([
            'review' => $review,
            'message' => 'Review added successfully',
        ], 201);
    }

    public function show(Review $review)
    {
        $review->load(['business', 'responses']);

        return response()->json([
            'review' => $review,
        ]);
    }

    public function update(Request $request, Review $review)
    {
        $validated = $request->validate([
            'is_responded' => 'sometimes|boolean',
            'text' => 'sometimes|nullable|string',
        ]);

        $review->update($validated);

        return response()->json([
            'review' => $review,
            'message' => 'Review updated successfully',
        ]);
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return response()->json([
            'message' => 'Review deleted successfully',
        ]);
    }

    public function import(Request $request)
    {
        $validated = $request->validate([
            'business_id' => 'required|exists:businesses,id',
            'reviews' => 'required|array',
            'reviews.*.external_id' => 'nullable|string',
            'reviews.*.source' => 'required|in:google,yelp,manual',
            'reviews.*.author_name' => 'required|string',
            'reviews.*.rating' => 'required|integer|min:1|max:5',
            'reviews.*.text' => 'nullable|string',
            'reviews.*.review_date' => 'nullable|date',
        ]);

        $imported = 0;
        $skipped = 0;

        foreach ($validated['reviews'] as $reviewData) {
            $reviewData['business_id'] = $validated['business_id'];

            // Check if already exists
            $exists = Review::where('business_id', $validated['business_id'])
                ->where('external_id', $reviewData['external_id'] ?? null)
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            $review = Review::create($reviewData);
            $review->detectSentiment();
            $imported++;
        }

        return response()->json([
            'message' => "Imported {$imported} reviews, skipped {$skipped} duplicates",
            'imported' => $imported,
            'skipped' => $skipped,
        ]);
    }
}
