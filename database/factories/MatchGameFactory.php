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
        $dateHeure = now()->addDays(3);

        return [
            'phase_id' => Phase::factory(),
            'joueur_1' => fake()->firstName(),
            'joueur_2' => fake()->firstName(),
            'date_heure' => $dateHeure,
            'date_fin_pronostics' => $dateHeure->copy()->subHour(),
            'resultat_saisi' => false,
        ];
    }

    public function double(): static
    {
        return $this->state(fn () => [
            'joueur_1_partenaire' => fake()->firstName(),
            'joueur_2_partenaire' => fake()->firstName(),
        ]);
    }

    /**
     * Keep date_fin_pronostics consistent (1h before date_heure) whenever
     * date_heure is overridden but date_fin_pronostics is not.
     */
    public function configure(): static
    {
        return $this->afterMaking(function (\App\Models\MatchGame $match) {
            $match->date_fin_pronostics = $match->date_heure->copy()->subHour();
        });
    }
}
