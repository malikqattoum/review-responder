<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BusinessFactory extends Factory
{
    protected $model = Business::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->company(),
            'address' => $this->faker->address(),
            'location_slug' => $this->faker->slug(3),
            'google_place_id' => $this->faker->optional()->uuid(),
            'yelp_business_id' => $this->faker->optional()->uuid(),
        ];
    }
}
