<?php

namespace Database\Factories;

use App\Models\QuestionBonus;
use App\Models\ReponseBonus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReponseBonus>
 */
class ReponseBonusFactory extends Factory
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
            'question_bonus_id' => QuestionBonus::factory(),
            'reponse' => fake()->firstName(),
            'points_obtenus' => null,
        ];
    }
}
