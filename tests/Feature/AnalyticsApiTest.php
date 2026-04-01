<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Business $business;
    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->business = Business::factory()->create(['user_id' => $this->user->id]);
        
        $this->token = $this->user->createToken('test')->plainTextToken;
    }

    public function test_analytics_requires_authentication(): void
    {
        $response = $this->getJson('/api/analytics?start_date=2026-01-01&end_date=2026-04-01');
        $response->assertStatus(401);
    }

    public function test_analytics_returns_summary(): void
    {
        // Create test reviews
        Review::factory()->create([
            'business_id' => $this->business->id,
            'rating' => 5,
            'sentiment' => 'positive',
            'text' => 'Great service!',
        ]);
        Review::factory()->create([
            'business_id' => $this->business->id,
            'rating' => 1,
            'sentiment' => 'negative',
            'text' => 'Terrible experience',
        ]);
        Review::factory()->create([
            'business_id' => $this->business->id,
            'rating' => 3,
            'sentiment' => 'neutral',
            'text' => 'It was okay',
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->getJson('/api/analytics?start_date=2026-01-01&end_date=2026-12-31');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'summary' => [
                    'total_reviews',
                    'positive_reviews',
                    'neutral_reviews',
                    'negative_reviews',
                    'responded_reviews',
                    'unresponded_reviews',
                    'average_rating',
                    'response_rate',
                ],
                'sentiment_by_week',
                'rating_distribution',
                'monthly_comparison',
            ]);
    }

    public function test_analytics_sentiment_breakdown(): void
    {
        // Create reviews for this specific business
        $this->business->reviews()->createMany([
            [
                'external_id' => 'ext-1',
                'source' => 'google',
                'author_name' => 'John',
                'rating' => 5,
                'text' => 'Great!',
                'sentiment' => 'positive',
                'review_date' => now()->format('Y-m-d'),
                'is_responded' => false,
            ],
            [
                'external_id' => 'ext-2',
                'source' => 'google',
                'author_name' => 'Jane',
                'rating' => 4,
                'text' => 'Good!',
                'sentiment' => 'positive',
                'review_date' => now()->format('Y-m-d'),
                'is_responded' => false,
            ],
            [
                'external_id' => 'ext-3',
                'source' => 'yelp',
                'author_name' => 'Bob',
                'rating' => 1,
                'text' => 'Bad!',
                'sentiment' => 'negative',
                'review_date' => now()->format('Y-m-d'),
                'is_responded' => false,
            ],
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->getJson("/api/analytics?business_id={$this->business->id}&start_date=2026-01-01&end_date=2026-12-31");

        $response->assertStatus(200);
        $data = $response->json();
        
        $this->assertGreaterThanOrEqual(3, $data['summary']['total_reviews']);
    }
}
