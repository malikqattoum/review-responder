<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\Review;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition(): array
    {
        $rating = $this->faker->numberBetween(1, 5);
        $sentiment = $rating >= 4 ? 'positive' : ($rating <= 2 ? 'negative' : 'neutral');

        return [
            'business_id' => Business::factory(),
            'external_id' => $this->faker->unique()->uuid(),
            'source' => $this->faker->randomElement(['google', 'yelp', 'manual']),
            'author_name' => $this->faker->name(),
            'rating' => $rating,
            'text' => $this->faker->sentence(10),
            'sentiment' => $sentiment,
            'review_date' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'is_responded' => false,
        ];
    }

    public function positive(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => $this->faker->numberBetween(4, 5),
            'sentiment' => 'positive',
        ]);
    }

    public function negative(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => $this->faker->numberBetween(1, 2),
            'sentiment' => 'negative',
        ]);
    }

    public function responded(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_responded' => true,
        ]);
    }
}
