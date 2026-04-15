<?php

namespace Database\Factories;

use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Member>
 */
class MemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->name(),
            'phone1' => fake()->numerify('+25670#######'),
            'tier' => fake()->randomElement(['silver', 'gold']),
            'nationality' => 'Ugandan',
            'gender' => fake()->randomElement(['male', 'female']),
            'marital_status' => fake()->randomElement(['single', 'married', 'divorced', 'widowed']),
            'date_of_birth' => fake()->dateTimeBetween('-60 years', '-18 years'),
        ];
    }
}
