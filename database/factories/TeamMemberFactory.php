<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeamMemberFactory extends Factory
{
    protected $model = TeamMember::class;

    public function definition(): array
    {
        return [
            'business_id' => Business::factory(),
            'user_id' => User::factory(),
            'role' => $this->faker->randomElement(['admin', 'manager', 'viewer']),
            'invite_email' => $this->faker->safeEmail(),
            'invite_token' => null,
            'invited_at' => now(),
            'accepted_at' => now(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => null,
            'invite_token' => \Illuminate\Support\Str::random(32),
            'accepted_at' => null,
        ]);
    }
}
