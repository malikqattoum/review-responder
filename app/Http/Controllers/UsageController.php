<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UsageController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $currentMonth = now()->format('Y-m');

        $usage = $user->subscriptionUsages()
            ->where('month', $currentMonth)
            ->first();

        if (!$usage) {
            $usage = $user->subscriptionUsages()->create([
                'month' => $currentMonth,
                'reviews_used' => 0,
                'reviews_limit' => $user->getReviewsLimit(),
            ]);
        }

        return response()->json([
            'usage' => [
                'month' => $usage->month,
                'reviews_used' => $usage->reviews_used,
                'reviews_limit' => $usage->reviews_limit,
                'remaining' => $usage->remaining,
                'is_limit_reached' => $usage->isLimitReached(),
                'is_pro' => $user->isPro(),
            ],
        ]);
    }
}
