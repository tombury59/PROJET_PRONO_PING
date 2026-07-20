<?php

namespace Database\Factories;

use App\Models\Phase;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Phase>
 */
class PhaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nom' => 'Phase '.fake()->unique()->word(),
            'date_debut' => now()->subMonth(),
            'date_fin' => now()->addMonths(3),
            'reset_classement' => true,
        ];
    }
}
