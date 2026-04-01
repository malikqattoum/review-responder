<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Review;
use App\Models\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    /**
     * Get overall analytics for a business
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'business_id' => 'nullable|exists:businesses,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $businessId = $validated['business_id'] ?? null;
        $startDate = $validated['start_date'] ?? now()->subDays(30)->format('Y-m-d');
        $endDate = $validated['end_date'] ?? now()->format('Y-m-d');

        $query = Review::query();
        
        if ($businessId) {
            $query->where('business_id', $businessId);
        }
        
        // Handle NULL review_dates by using COALESCE with created_at
        $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('review_date', [$startDate, $endDate])
              ->orWhere(function ($q2) use ($startDate, $endDate) {
                  // Also include reviews with NULL review_date but within created_at range
                  $q2->whereNull('review_date')
                      ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
              });
        });

        $reviews = $query->get();

        // Basic counts
        $totalReviews = $reviews->count();
        $positiveReviews = $reviews->where('sentiment', 'positive')->count();
        $neutralReviews = $reviews->where('sentiment', 'neutral')->count();
        $negativeReviews = $reviews->where('sentiment', 'negative')->count();
        $respondedReviews = $reviews->where('is_responded', true)->count();
        $unrespondedReviews = $totalReviews - $respondedReviews;

        // Average rating
        $averageRating = $reviews->avg('rating') ?? 0;

        // Response rate
        $responseRate = $totalReviews > 0 
            ? round(($respondedReviews / $totalReviews) * 100, 1) 
            : 0;

        // Sentiment distribution (by week)
        $sentimentByWeek = $this->getSentimentByWeek($businessId, $startDate, $endDate);
        
        // Rating distribution
        $ratingDistribution = $this->getRatingDistribution($businessId, $startDate, $endDate);
        
        // Response time metrics
        $responseMetrics = $this->getResponseMetrics($businessId, $startDate, $endDate);
        
        // Recent activity
        $recentActivity = $this->getRecentActivity($businessId, 10);
        
        // Monthly comparison
        $monthlyComparison = $this->getMonthlyComparison($businessId);

        // Insights
        $insights = $this->generateInsights(
            $totalReviews,
            $positiveReviews,
            $negativeReviews,
            $responseRate,
            $averageRating,
            $respondedReviews,
            $unrespondedReviews
        );

        return response()->json([
            'summary' => [
                'total_reviews' => $totalReviews,
                'positive_reviews' => $positiveReviews,
                'neutral_reviews' => $neutralReviews,
                'negative_reviews' => $negativeReviews,
                'responded_reviews' => $respondedReviews,
                'unresponded_reviews' => $unrespondedReviews,
                'average_rating' => round($averageRating, 2),
                'response_rate' => $responseRate,
                'date_range' => [
                    'start' => $startDate,
                    'end' => $endDate,
                ],
            ],
            'sentiment_by_week' => $sentimentByWeek,
            'rating_distribution' => $ratingDistribution,
            'response_metrics' => $responseMetrics,
            'recent_activity' => $recentActivity,
            'monthly_comparison' => $monthlyComparison,
            'insights' => $insights,
        ]);
    }

    /**
     * Get sentiment trends by week
     */
    protected function getSentimentByWeek($businessId, $startDate, $endDate)
    {
        // Use COALESCE to handle NULL review_dates (default to created_at)
        $query = Review::query()
            ->select(
                DB::raw("YEARWEEK(COALESCE(review_date, created_at), 1) as year_week"),
                DB::raw("COUNT(*) as total"),
                DB::raw("SUM(CASE WHEN sentiment = 'positive' THEN 1 ELSE 0 END) as positive"),
                DB::raw("SUM(CASE WHEN sentiment = 'neutral' THEN 1 ELSE 0 END) as neutral"),
                DB::raw("SUM(CASE WHEN sentiment = 'negative' THEN 1 ELSE 0 END) as negative")
            )
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('review_date', [$startDate, $endDate])
                  ->orWhere(function ($q2) use ($startDate, $endDate) {
                      $q2->whereNull('review_date')
                          ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                  });
            })
            ->groupBy(DB::raw("YEARWEEK(COALESCE(review_date, created_at), 1)"))
            ->orderBy('year_week');

        if ($businessId) {
            $query->where('business_id', $businessId);
        }

        $results = $query->get();

        return $results->map(function ($item) {
            $date = date('Y-m-d', strtotime($item->year_week . ' Monday'));
            return [
                'week' => $date,
                'total' => $item->total,
                'positive' => $item->positive,
                'neutral' => $item->neutral,
                'negative' => $item->negative,
            ];
        });
    }

    /**
     * Get rating distribution (1-5 stars)
     */
    protected function getRatingDistribution($businessId, $startDate, $endDate)
    {
        $query = Review::query()
            ->select('rating', DB::raw('COUNT(*) as count'))
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('review_date', [$startDate, $endDate])
                  ->orWhere(function ($q2) use ($startDate, $endDate) {
                      $q2->whereNull('review_date')
                          ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                  });
            })
            ->groupBy('rating')
            ->orderBy('rating', 'desc');

        if ($businessId) {
            $query->where('business_id', $businessId);
        }

        $results = $query->get();

        // Ensure all ratings 1-5 are represented
        $distribution = [
            5 => 0,
            4 => 0,
            3 => 0,
            2 => 0,
            1 => 0,
        ];

        foreach ($results as $item) {
            $distribution[$item->rating] = $item->count;
        }

        return $distribution;
    }

    /**
     * Get response time metrics
     */
    protected function getResponseMetrics($businessId, $startDate, $endDate)
    {
        $query = Response::query()
            ->join('reviews', 'responses.review_id', '=', 'reviews.id')
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('reviews.review_date', [$startDate, $endDate])
                  ->orWhere(function ($q2) use ($startDate, $endDate) {
                      $q2->whereNull('reviews.review_date')
                          ->whereBetween('reviews.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                  });
            });

        if ($businessId) {
            $query->where('reviews.business_id', $businessId);
        }

        $responses = $query->select(
            DB::raw("TIMESTAMPDIFF(HOUR, reviews.created_at, responses.created_at) as response_time_hours")
        )->get();

        $avgHours = $responses->avg('response_time_hours') ?? 0;
        $minHours = $responses->min('response_time_hours') ?? 0;
        $maxHours = $responses->max('response_time_hours') ?? 0;

        // Categorize response times
        $within1Hour = $responses->where('response_time_hours', '<=', 1)->count();
        $within24Hours = $responses->where('response_time_hours', '<=', 24)->count();
        $withinWeek = $responses->where('response_time_hours', '<=', 168)->count();

        $totalResponded = $responses->count();

        return [
            'average_hours' => round($avgHours, 1),
            'min_hours' => round($minHours, 1),
            'max_hours' => round($maxHours, 1),
            'within_1_hour' => $within1Hour,
            'within_24_hours' => $within24Hours,
            'within_week' => $withinWeek,
            'total_responded' => $totalResponded,
        ];
    }

    /**
     * Get recent activity (reviews + responses)
     */
    protected function getRecentActivity($businessId, $limit = 10)
    {
        $query = Review::query()
            ->with(['responses' => function ($q) {
                $q->latest()->limit(1);
            }])
            ->orderBy('created_at', 'desc')
            ->limit($limit);

        if ($businessId) {
            $query->where('business_id', $businessId);
        }

        $reviews = $query->get();

        return $reviews->map(function ($review) {
            return [
                'id' => $review->id,
                'type' => 'review',
                'author' => $review->author_name,
                'rating' => $review->rating,
                'sentiment' => $review->sentiment,
                'is_responded' => $review->is_responded,
                'date' => $review->created_at->toIso8601String(),
                'has_response' => $review->responses->count() > 0,
            ];
        });
    }

    /**
     * Compare current month vs previous month
     */
    protected function getMonthlyComparison($businessId)
    {
        $currentMonth = now()->format('Y-m');
        $previousMonth = now()->subMonth()->format('Y-m');

        $query = Review::query()
            ->select(
                DB::raw("DATE_FORMAT(COALESCE(review_date, created_at), '%Y-%m') as month"),
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN sentiment = 'positive' THEN 1 ELSE 0 END) as positive"),
                DB::raw("SUM(CASE WHEN sentiment = 'negative' THEN 1 ELSE 0 END) as negative"),
                DB::raw("AVG(rating) as avg_rating"),
                DB::raw("SUM(CASE WHEN is_responded = 1 THEN 1 ELSE 0 END) as responded")
            )
            ->groupBy(DB::raw("DATE_FORMAT(COALESCE(review_date, created_at), '%Y-%m')"));

        if ($businessId) {
            $query->where('business_id', $businessId);
        }

        $results = $query->whereIn(DB::raw("DATE_FORMAT(COALESCE(review_date, created_at), '%Y-%m')"), [$currentMonth, $previousMonth])
            ->get()
            ->keyBy('month');

        $current = $results[$currentMonth] ?? null;
        $previous = $results[$previousMonth] ?? null;

        return [
            'current' => $current ? [
                'month' => $current->month,
                'total' => $current->total,
                'positive' => $current->positive,
                'negative' => $current->negative,
                'avg_rating' => round($current->avg_rating ?? 0, 2),
                'response_rate' => $current->total > 0 
                    ? round(($current->responded / $current->total) * 100, 1) 
                    : 0,
            ] : null,
            'previous' => $previous ? [
                'month' => $previous->month,
                'total' => $previous->total,
                'positive' => $previous->positive,
                'negative' => $previous->negative,
                'avg_rating' => round($previous->avg_rating ?? 0, 2),
                'response_rate' => $previous->total > 0 
                    ? round(($previous->responded / $previous->total) * 100, 1) 
                    : 0,
            ] : null,
        ];
    }

    /**
     * Generate actionable insights
     */
    protected function generateInsights(
        $total,
        $positive,
        $negative,
        $responseRate,
        $avgRating,
        $responded,
        $unresponded
    ) {
        $insights = [];

        // Response rate insights
        if ($responseRate < 50) {
            $insights[] = [
                'type' => 'warning',
                'message' => 'Your response rate is below 50%. Responding to more reviews can improve your search ranking.',
                'action' => 'Start responding to unresponded reviews',
            ];
        } elseif ($responseRate >= 90) {
            $insights[] = [
                'type' => 'success',
                'message' => 'Excellent! You\'re responding to over 90% of your reviews.',
                'action' => null,
            ];
        }

        // Negative review insights
        if ($negative > 0 && $negative / max(1, $total) > 0.3) {
            $insights[] = [
                'type' => 'danger',
                'message' => 'More than 30% of your recent reviews are negative. This could hurt your reputation.',
                'action' => 'Review common complaints and address them',
            ];
        }

        // Unresponded reviews
        if ($unresponded > 5) {
            $insights[] = [
                'type' => 'warning',
                'message' => "You have {$unresponded} unresponded reviews. Unanswered reviews can discourage future customers.",
                'action' => 'Generate AI responses for unresponded reviews',
            ];
        }

        // Rating insights
        if ($avgRating > 0) {
            if ($avgRating >= 4.5) {
                $insights[] = [
                    'type' => 'success',
                    'message' => "Your average rating of {$avgRating} stars is excellent!",
                    'action' => null,
                ];
            } elseif ($avgRating < 3) {
                $insights[] = [
                    'type' => 'danger',
                    'message' => "Your average rating of {$avgRating} stars is below average. Focus on improving customer experience.",
                    'action' => 'Identify common complaints in negative reviews',
                ];
            }
        }

        // Positive ratio
        if ($total > 0) {
            $positiveRatio = $positive / $total;
            if ($positiveRatio >= 0.8) {
                $insights[] = [
                    'type' => 'success',
                    'message' => 'Most of your reviews are positive! Great job!',
                    'action' => null,
                ];
            }
        }

        return $insights;
    }
}
