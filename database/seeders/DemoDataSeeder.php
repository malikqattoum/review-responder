<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Business;
use App\Models\Review;
use App\Models\Response;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create demo user
        $user = User::firstOrCreate(
            ['email' => 'demo@example.com'],
            [
                'name' => 'Demo User',
                'password' => Hash::make('demo1234'),
                'subscription_tier' => 'free',
                'subscription_status' => 'free',
            ]
        );

        // Create demo business
        $business = Business::firstOrCreate(
            ['user_id' => $user->id, 'name' => "Demo User's Business"],
            [
                'address' => '123 Main Street, New York, NY 10001',
                'location_slug' => 'demo-user-' . uniqid(),
            ]
        );

        // Only seed reviews if none exist
        if (Review::where('business_id', $business->id)->count() > 0) {
            return;
        }

        $reviews = [
            [
                'external_id' => 'demo_rev_1',
                'source' => 'google',
                'author_name' => 'John Smith',
                'rating' => 5,
                'text' => 'Excellent service! Very professional and timely. Would highly recommend to anyone looking for quality work.',
                'sentiment' => 'positive',
                'review_date' => now()->subDays(3),
            ],
            [
                'external_id' => 'demo_rev_2',
                'source' => 'yelp',
                'author_name' => 'Sarah Johnson',
                'rating' => 4,
                'text' => 'Good service overall. The team was friendly and completed the work on time. Only minor issue was some cleanup after.',
                'sentiment' => 'positive',
                'review_date' => now()->subDays(5),
            ],
            [
                'external_id' => 'demo_rev_3',
                'source' => 'google',
                'author_name' => 'Mike Wilson',
                'rating' => 2,
                'text' => 'Not satisfied with the service. They were late and the work quality was below expectations. Disappointed.',
                'sentiment' => 'negative',
                'review_date' => now()->subDays(7),
            ],
            [
                'external_id' => 'demo_rev_4',
                'source' => 'yelp',
                'author_name' => 'Emily Brown',
                'rating' => 5,
                'text' => 'Absolutely fantastic! They went above and beyond. Already planning to use them again for my next project.',
                'sentiment' => 'positive',
                'review_date' => now()->subDays(10),
            ],
            [
                'external_id' => 'demo_rev_5',
                'source' => 'google',
                'author_name' => 'David Lee',
                'rating' => 3,
                'text' => 'Average service. Did the job but nothing special. Price was fair though.',
                'sentiment' => 'neutral',
                'review_date' => now()->subDays(15),
            ],
        ];

        foreach ($reviews as $reviewData) {
            $review = Review::create([
                'business_id' => $business->id,
                'external_id' => $reviewData['external_id'],
                'source' => $reviewData['source'],
                'author_name' => $reviewData['author_name'],
                'rating' => $reviewData['rating'],
                'text' => $reviewData['text'],
                'sentiment' => $reviewData['sentiment'],
                'review_date' => $reviewData['review_date'],
                'is_responded' => false,
            ]);

            // Add responses for some reviews
            if ($reviewData['sentiment'] === 'positive') {
                $review->responses()->create([
                    'body' => "Hi {$reviewData['author_name']}, thank you so much for this wonderful review! We're thrilled to hear about your positive experience. Your kind words mean the world to us, and we can't wait to see you again soon!",
                    'tone' => 'friendly',
                ]);
                $review->update(['is_responded' => true]);
            }
        }
    }
}
