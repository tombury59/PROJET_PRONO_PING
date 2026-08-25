<?php

namespace App\Services;

use App\Models\MatchGame;
use App\Models\Phase;
use App\Models\Pronostic;
use App\Models\ReponseBonus;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ClassementService
{
    /**
     * @return Collection<int, array{user: User, points: int}>
     */
    public function pourPhase(Phase $phase): Collection
    {
        $phaseIds = $this->phasesPourClassement($phase)->pluck('id');

        return $this->calculer(
            fn ($query) => $query->whereIn('matches.phase_id', $phaseIds),
            fn ($query) => $query->whereIn('questions_bonus.phase_id', $phaseIds),
        );
    }

    /**
     * Les phases dont les points sont cumulés dans le classement de $phase :
     * on remonte les phases précédentes tant que leur reset n'est pas activé.
     * Une phase avec reset_classement = true "coupe" la chaîne (ses propres
     * points comptent, mais rien avant elle).
     *
     * @return Collection<int, Phase>
     */
    public function phasesPourClassement(Phase $phase): Collection
    {
        $phases = Phase::orderBy('date_debut')->get();
        $index = $phases->search(fn (Phase $p) => $p->id === $phase->id);

        if ($index === false) {
            return collect([$phase]);
        }

        $incluses = collect();

        for ($i = $index; $i >= 0; $i--) {
            $incluses->push($phases[$i]);

            if ($phases[$i]->reset_classement) {
                break;
            }
        }

        return $incluses->reverse()->values();
    }

    /**
     * Classement cumulé sur toutes les phases.
     *
     * @return Collection<int, array{user: User, points: int}>
     */
    public function global(): Collection
    {
        return $this->calculer(fn ($query) => $query, fn ($query) => $query);
    }

    /**
     * Évolution des points au fil des rencontres résolues de la ou des phases
     * concernées : pour chaque date de rencontre, le total cumulé de points de
     * pronostics (bonus exclus, faute de date de résolution) par joueur.
     *
     * @return array{dates: list<string>, series: Collection<int, array{user: User, cumul: list<int>, final: int}>}
     */
    public function evolution(Phase $phase): array
    {
        $phaseIds = $this->phasesPourClassement($phase)->pluck('id');

        $dates = MatchGame::whereIn('phase_id', $phaseIds)
            ->where('resultat_saisi', true)
            ->orderBy('date_heure')
            ->pluck('date_heure')
            ->map(fn ($date) => Carbon::parse($date)->format('Y-m-d'))
            ->unique()
            ->values();

        if ($dates->isEmpty()) {
            return ['dates' => [], 'series' => collect()];
        }

        $lignes = Pronostic::query()
            ->join('matches', 'matches.id', '=', 'pronostics.match_id')
            ->join('users', 'users.id', '=', 'pronostics.user_id')
            ->whereIn('matches.phase_id', $phaseIds)
            ->where('matches.resultat_saisi', true)
            ->where('users.role', 'joueur')
            ->whereNotNull('pronostics.points_obtenus')
            ->get(['pronostics.user_id', 'pronostics.points_obtenus', 'matches.date_heure']);

        $users = User::whereIn('id', $lignes->pluck('user_id')->unique())->get()->keyBy('id');

        $series = $lignes
            ->groupBy('user_id')
            ->map(function (Collection $rows, $userId) use ($dates, $users) {
                $parDate = [];
                foreach ($rows as $row) {
                    $jour = Carbon::parse($row->date_heure)->format('Y-m-d');
                    $parDate[$jour] = ($parDate[$jour] ?? 0) + (int) $row->points_obtenus;
                }

                $cumul = [];
                $total = 0;
                foreach ($dates as $jour) {
                    $total += $parDate[$jour] ?? 0;
                    $cumul[] = $total;
                }

                return ['user' => $users[$userId], 'cumul' => $cumul, 'final' => $total];
            })
            ->sortByDesc('final')
            ->values();

        return ['dates' => $dates->all(), 'series' => $series];
    }

    public function rangDe(User $user, Collection $classement): ?int
    {
        $index = $classement->search(fn (array $entree) => $entree['user']->id === $user->id);

        return $index === false ? null : $index + 1;
    }

    private function calculer(\Closure $filtrePronostics, \Closure $filtreBonus): Collection
    {
        $pointsPronostics = $filtrePronostics(
            Pronostic::query()
                ->join('matches', 'matches.id', '=', 'pronostics.match_id')
        )
            ->selectRaw('pronostics.user_id, SUM(COALESCE(pronostics.points_obtenus, 0)) as total')
            ->groupBy('pronostics.user_id')
            ->pluck('total', 'user_id');

        $pointsBonus = $filtreBonus(
            ReponseBonus::query()
                ->join('questions_bonus', 'questions_bonus.id', '=', 'reponses_bonus.question_bonus_id')
        )
            ->selectRaw('reponses_bonus.user_id, SUM(COALESCE(reponses_bonus.points_obtenus, 0)) as total')
            ->groupBy('reponses_bonus.user_id')
            ->pluck('total', 'user_id');

        // Vainqueurs trouvés = pronostics rapportant au moins 1 point (bonne
        // issue, score exact inclus). Scores exacts = pronostics à 3 points.
        $bonsResultats = $filtrePronostics(
            Pronostic::query()
                ->join('matches', 'matches.id', '=', 'pronostics.match_id')
        )
            ->where('pronostics.points_obtenus', '>=', 1)
            ->selectRaw('pronostics.user_id, COUNT(*) as total')
            ->groupBy('pronostics.user_id')
            ->pluck('total', 'user_id');

        $scoresExacts = $filtrePronostics(
            Pronostic::query()
                ->join('matches', 'matches.id', '=', 'pronostics.match_id')
        )
            // 3 pts, ou 6 si le joker a doublé le score exact.
            ->whereIn('pronostics.points_obtenus', [3, 6])
            ->selectRaw('pronostics.user_id, COUNT(*) as total')
            ->groupBy('pronostics.user_id')
            ->pluck('total', 'user_id');

        return User::query()
            ->where('role', 'joueur')
            ->get()
            ->map(fn (User $user) => [
                'user' => $user,
                'points' => (int) ($pointsPronostics[$user->id] ?? 0) + (int) ($pointsBonus[$user->id] ?? 0),
                'bons_resultats' => (int) ($bonsResultats[$user->id] ?? 0),
                'scores_exacts' => (int) ($scoresExacts[$user->id] ?? 0),
            ])
            ->sortByDesc('points')
            ->values();
    }
}
