<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Pseudos des joueurs, réutilisés comme noms de participants dans les
     * matchs créés par MatchSeeder, pour que les données restent cohérentes.
     */
    public const JOUEURS = [
        'julien', 'camille', 'marc', 'sophie', 'nicolas',
        'laura', 'thomas', 'emma', 'kevin', 'chloe',
    ];

    public function run(): void
    {
        User::factory()->admin()->create(['pseudo' => 'admin']);

        foreach (self::JOUEURS as $pseudo) {
            User::factory()->create(['pseudo' => $pseudo]);
        }
    }
}
