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

        // Phase 1 (terminée) : toutes les rencontres ont un résultat.
        $this->creerMatch($phase1, 'Lille 2', 'Roubaix 3', $phase1->date_debut->copy()->addDays(9), score: [12, 6]);
        $this->creerMatch($phase1, 'Wattignies 1', 'Douai 2', $phase1->date_debut->copy()->addDays(23), score: [9, 9]);
        $this->creerMatch($phase1, 'Tourcoing 4', 'Lens 1', $phase1->date_debut->copy()->addDays(37), score: [14, 4]);
        $this->creerMatch($phase1, 'Arras 2', 'Lille 3', $phase1->date_debut->copy()->addDays(51), score: [7, 11]);
        $this->creerMatch($phase1, 'Roubaix 3', 'Wattignies 1', $phase1->date_debut->copy()->addDays(65), score: [10, 8]);

        // Phase 2 (terminée) : toutes les rencontres ont un résultat.
        $this->creerMatch($phase2, 'Lille 2', 'Tourcoing 4', $phase2->date_debut->copy()->addDays(9), score: [11, 7]);
        $this->creerMatch($phase2, 'Douai 2', 'Arras 2', $phase2->date_debut->copy()->addDays(23), score: [15, 3]);
        $this->creerMatch($phase2, 'Lens 1', 'Roubaix 3', $phase2->date_debut->copy()->addDays(37), score: [8, 10]);
        $this->creerMatch($phase2, 'Wattignies 1', 'Lille 3', $phase2->date_debut->copy()->addDays(51), score: [12, 6]);
        $this->creerMatch($phase2, 'Tourcoing 4', 'Lens 1', $phase2->date_debut->copy()->addDays(65), score: [9, 9]);
        $this->creerMatch($phase2, 'Arras 2', 'Douai 2', $phase2->date_debut->copy()->addDays(79), score: [5, 13]);

        // Phase 3 (en cours) : mix de rencontres jouées, une en attente de
        // résultat (verrouillée mais pas encore saisie, pour déclencher la
        // notification admin), et des rencontres à venir pour les pronostics.
        $this->creerMatch($phase3, 'Lille 2', 'Roubaix 3', now()->subDays(20), score: [12, 6]);
        $this->creerMatch($phase3, 'Douai 2', 'Tourcoing 4', now()->subDays(13), score: [10, 8]);
        $this->creerMatch($phase3, 'Arras 2', 'Wattignies 1', now()->subDays(6), score: [14, 4]);

        $this->creerMatch($phase3, 'Lens 1', 'Lille 3', now()->subDay());

        $this->creerMatch($phase3, 'Lille 2', 'Douai 2', now()->addDays(7));
        $this->creerMatch($phase3, 'Roubaix 3', 'Arras 2', now()->addDays(16), nbMatchs: 14);
        $this->creerMatch($phase3, 'Tourcoing 4', 'Wattignies 1', now()->addDays(30));
    }

    private function creerMatch(
        Phase $phase,
        string $equipe1,
        string $equipe2,
        Carbon $dateHeure,
        int $nbMatchs = 18,
        ?array $score = null,
    ): MatchGame {
        return MatchGame::create([
            'phase_id' => $phase->id,
            'equipe_1' => $equipe1,
            'equipe_2' => $equipe2,
            'nb_matchs' => $nbMatchs,
            'date_heure' => $dateHeure,
            'date_fin_pronostics' => $dateHeure->copy()->subHour(),
            'score_j1' => $score[0] ?? null,
            'score_j2' => $score[1] ?? null,
            'resultat_saisi' => $score !== null,
        ]);
    }
}
