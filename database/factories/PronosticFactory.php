<?php

namespace Database\Factories;

use App\Models\MatchGame;
use App\Models\Pronostic;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pronostic>
 */
class PronosticFactory extends Factory
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
            'match_id' => MatchGame::factory(),
            'prono_vainqueur' => 1,
            'prono_score_j1' => 3,
            'prono_score_j2' => 1,
        ];
    }
}
