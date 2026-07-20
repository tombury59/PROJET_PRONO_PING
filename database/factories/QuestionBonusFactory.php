<?php

namespace Database\Factories;

use App\Models\Phase;
use App\Models\QuestionBonus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QuestionBonus>
 */
class QuestionBonusFactory extends Factory
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
            'match_id' => null,
            'question' => 'Qui remportera le plus de matchs sur la phase ?',
            'reponse_correcte' => null,
        ];
    }
}
