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
            'equipe_1' => fake()->city().' '.fake()->numberBetween(1, 4),
            'equipe_2' => fake()->city().' '.fake()->numberBetween(1, 4),
            'nb_matchs' => 18,
            'date_heure' => $dateHeure,
            'date_fin_pronostics' => $dateHeure->copy()->subHour(),
            'resultat_saisi' => false,
        ];
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
