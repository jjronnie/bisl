<?php

namespace Database\Factories;

use App\Models\BulkSmsCampaign;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BulkSmsCampaign>
 */
class BulkSmsCampaignFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
            'message' => fake()->sentence(10),
            'total_recipients' => fake()->numberBetween(1, 100),
            'sent_count' => 0,
            'failed_count' => 0,
            'total_cost' => 0,
            'status' => 'pending',
            'created_by' => User::factory(),
        ];
    }

    public function processing(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'processing',
            'started_at' => now(),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'sent_count' => $attributes['total_recipients'] ?? 10,
            'completed_at' => now(),
        ]);
    }
}
