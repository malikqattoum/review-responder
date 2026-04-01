<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BusinessController extends Controller
{
    public function index(Request $request)
    {
        $businesses = $request->user()->businesses()->get();

        return response()->json([
            'businesses' => $businesses,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'google_place_id' => 'nullable|string|max:255',
            'yelp_business_id' => 'nullable|string|max:255',
        ]);

        $validated['location_slug'] = Str::slug($request->name) . '-' . uniqid();

        $business = $request->user()->businesses()->create($validated);

        return response()->json([
            'business' => $business,
            'message' => 'Business created successfully',
        ], 201);
    }

    public function show(Request $request, Business $business)
    {
        $this->authorize('view', $business);

        $business->load(['reviews' => function ($query) {
            $query->orderBy('review_date', 'desc')->limit(10);
        }]);

        return response()->json([
            'business' => $business,
        ]);
    }

    public function update(Request $request, Business $business)
    {
        $this->authorize('update', $business);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'address' => 'nullable|string|max:500',
            'google_place_id' => 'nullable|string|max:255',
            'yelp_business_id' => 'nullable|string|max:255',
        ]);

        $business->update($validated);

        return response()->json([
            'business' => $business,
            'message' => 'Business updated successfully',
        ]);
    }

    public function destroy(Request $request, Business $business)
    {
        $this->authorize('delete', $business);

        $business->delete();

        return response()->json([
            'message' => 'Business deleted successfully',
        ]);
    }
}
