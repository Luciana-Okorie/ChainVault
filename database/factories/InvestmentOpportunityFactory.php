<?php

namespace Database\Factories;

use App\Models\InvestmentOpportunity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InvestmentOpportunity>
 */
class InvestmentOpportunityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'target_amount' => 1000000.00,
            'minimum_investment' => 10000.00,
            'expected_return' => 15.00,
            'duration_months' => 12,
            'status' => 'open',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addMonths(12)->toDateString(),
        ];
    }
}