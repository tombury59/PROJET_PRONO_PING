<?php

namespace Database\Seeders;

use App\Models\MatchGame;
use App\Models\Pronostic;
use App\Models\User;
use Illuminate\Database\Seeder;

class PronosticSeeder extends Seeder
{
    public function run(): void
    {
        $joueurs = User::where('role', 'joueur')->orderBy('id')->get();
        $matches = MatchGame::orderBy('date_heure')->get();

        foreach ($matches as $matchIndex => $match) {
            if ($match->resultat_saisi) {
                foreach ($joueurs as $i => $joueur) {
                    // Le match id casse la symétrie du cycle : sans lui, un
                    // nombre de matchs résolus multiple de 3 par phase donne
                    // à chaque joueur exactement un pronostic de chaque type
                    // (3+1+0 pts), donc un classement artificiellement à égalité.
                    $this->creerPronosticResolu($joueur, $match, $i * 3 + $matchIndex + $match->id);
                }

                continue;
            }

            // Matchs pas encore résultés : seule une partie des joueurs a
            // déjà pronostiqué, pour refléter un engagement réaliste et
            // varié (certains joueurs oublient ou pronostiquent en retard).
            foreach ($joueurs as $i => $joueur) {
                if (($i + $matchIndex) % 2 !== 0) {
                    continue;
                }

                Pronostic::create([
                    'user_id' => $joueur->id,
                    'match_id' => $match->id,
                    'prono_vainqueur' => 1,
                    'prono_score_j1' => 11,
                    'prono_score_j2' => 7,
                ]);
            }
        }
    }

    private function creerPronosticResolu(User $joueur, MatchGame $match, int $seed): void
    {
        $vainqueurReel = $match->vainqueur();
        $pattern = $seed % 5;

        if ($pattern === 0) {
            // Score exact.
            [$scoreJ1, $scoreJ2] = [$match->score_j1, $match->score_j2];
        } elseif (in_array($pattern, [1, 2], true)) {
            // Bonne issue, score approximatif.
            [$scoreJ1, $scoreJ2] = match ($vainqueurReel) {
                1 => [10, 8],
                2 => [8, 10],
                default => [8, 8],
            };

            if ($scoreJ1 === $match->score_j1 && $scoreJ2 === $match->score_j2) {
                [$scoreJ1, $scoreJ2] = match ($vainqueurReel) {
                    1 => [12, 6],
                    2 => [6, 12],
                    default => [7, 7],
                };
            }
        } else {
            // Mauvaise issue.
            [$scoreJ1, $scoreJ2] = $vainqueurReel === 1 ? [6, 12] : [12, 6];
        }

        $vainqueur = match (true) {
            $scoreJ1 > $scoreJ2 => 1,
            $scoreJ1 < $scoreJ2 => 2,
            default => 0,
        };

        $pronostic = Pronostic::create([
            'user_id' => $joueur->id,
            'match_id' => $match->id,
            'prono_vainqueur' => $vainqueur,
            'prono_score_j1' => $scoreJ1,
            'prono_score_j2' => $scoreJ2,
        ]);

        $pronostic->setRelation('match', $match);
        $pronostic->update(['points_obtenus' => $pronostic->calculerPoints()]);
    }
}
