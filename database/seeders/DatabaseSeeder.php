<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database avec un jeu de données cohérent :
     * utilisateurs, phases, matchs (avec résultats pour les phases
     * passées), pronostics, questions bonus et notifications.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            // FakePhaseSeeder::class,
            // FakeMatchSeeder::class,
            // FakePronosticSeeder::class,
            // FakeQuestionBonusSeeder::class,
            // FakeNotificationSeeder::class,
        ]);
    }
}
