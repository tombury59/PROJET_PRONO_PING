<?php

namespace Database\Seeders;

use App\Models\MatchGame;
use App\Models\Phase;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MatchSeeder extends Seeder
{
    public function run(): void
    {
        [$phase1, $phase2, $phase3] = Phase::orderBy('date_debut')->get();

        // Phase 1 (terminée) : tous les matchs ont un résultat.
        $this->creerMatch($phase1, 'Julien', 'Marc', $phase1->date_debut->copy()->addDays(9), score: [3, 1]);
        $this->creerMatch($phase1, 'Camille', 'Nicolas', $phase1->date_debut->copy()->addDays(23), partenaires: ['Sophie', 'Laura'], score: [3, 2]);
        $this->creerMatch($phase1, 'Thomas', 'Emma', $phase1->date_debut->copy()->addDays(37), score: [3, 0]);
        $this->creerMatch($phase1, 'Kevin', 'Chloe', $phase1->date_debut->copy()->addDays(51), score: [1, 3]);
        $this->creerMatch($phase1, 'Julien', 'Sophie', $phase1->date_debut->copy()->addDays(65), score: [3, 2]);

        // Phase 2 (terminée) : tous les matchs ont un résultat.
        $this->creerMatch($phase2, 'Julien', 'Thomas', $phase2->date_debut->copy()->addDays(9), score: [3, 2]);
        $this->creerMatch($phase2, 'Sophie', 'Chloe', $phase2->date_debut->copy()->addDays(23), score: [3, 0]);
        $this->creerMatch($phase2, 'Marc', 'Kevin', $phase2->date_debut->copy()->addDays(37), partenaires: ['Nicolas', 'Emma'], score: [2, 3]);
        $this->creerMatch($phase2, 'Camille', 'Laura', $phase2->date_debut->copy()->addDays(51), score: [3, 1]);
        $this->creerMatch($phase2, 'Thomas', 'Kevin', $phase2->date_debut->copy()->addDays(65), score: [3, 0]);
        $this->creerMatch($phase2, 'Nicolas', 'Emma', $phase2->date_debut->copy()->addDays(79), score: [1, 3]);

        // Phase 3 (en cours) : mix de matchs joués, un en attente de résultat
        // (verrouillé mais pas encore saisi, pour déclencher la notification
        // admin), et des matchs à venir pour tester les pronostics.
        $this->creerMatch($phase3, 'Julien', 'Marc', now()->subDays(20), score: [3, 1]);
        $this->creerMatch($phase3, 'Sophie', 'Thomas', now()->subDays(13), partenaires: ['Nicolas', 'Kevin'], score: [3, 2]);
        $this->creerMatch($phase3, 'Camille', 'Emma', now()->subDays(6), score: [3, 0]);

        $this->creerMatch($phase3, 'Laura', 'Chloe', now()->subDay());

        $this->creerMatch($phase3, 'Julien', 'Nicolas', now()->addDays(7));
        $this->creerMatch($phase3, 'Marc', 'Sophie', now()->addDays(16), partenaires: ['Kevin', 'Emma']);
        $this->creerMatch($phase3, 'Thomas', 'Camille', now()->addDays(30));
    }

    private function creerMatch(
        Phase $phase,
        string $joueur1,
        string $joueur2,
        Carbon $dateHeure,
        ?array $partenaires = null,
        ?array $score = null,
    ): MatchGame {
        return MatchGame::create([
            'phase_id' => $phase->id,
            'joueur_1' => $joueur1,
            'joueur_1_partenaire' => $partenaires[0] ?? null,
            'joueur_2' => $joueur2,
            'joueur_2_partenaire' => $partenaires[1] ?? null,
            'date_heure' => $dateHeure,
            'date_fin_pronostics' => $dateHeure->copy()->subHour(),
            'score_j1' => $score[0] ?? null,
            'score_j2' => $score[1] ?? null,
            'resultat_saisi' => $score !== null,
        ]);
    }
}
