<?php

namespace Database\Factories;

use App\Models\MatchGame;
use App\Models\Phase;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MatchGame>
 */
class MatchGameFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'phase_id' => Phase::factory(),
            'joueur_1' => fake()->firstName(),
            'joueur_2' => fake()->firstName(),
            'date_heure' => now()->addDays(3),
            'resultat_saisi' => false,
        ];
    }
}
