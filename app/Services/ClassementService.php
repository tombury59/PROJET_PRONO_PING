<?php

namespace App\Services;

use App\Models\Phase;
use App\Models\Pronostic;
use App\Models\ReponseBonus;
use App\Models\User;
use Illuminate\Support\Collection;

class ClassementService
{
    /**
     * @return Collection<int, array{user: User, points: int}>
     */
    public function pourPhase(Phase $phase): Collection
    {
        $pointsPronostics = Pronostic::query()
            ->join('matches', 'matches.id', '=', 'pronostics.match_id')
            ->where('matches.phase_id', $phase->id)
            ->selectRaw('pronostics.user_id, SUM(COALESCE(pronostics.points_obtenus, 0)) as total')
            ->groupBy('pronostics.user_id')
            ->pluck('total', 'user_id');

        $pointsBonus = ReponseBonus::query()
            ->join('questions_bonus', 'questions_bonus.id', '=', 'reponses_bonus.question_bonus_id')
            ->where('questions_bonus.phase_id', $phase->id)
            ->selectRaw('reponses_bonus.user_id, SUM(COALESCE(reponses_bonus.points_obtenus, 0)) as total')
            ->groupBy('reponses_bonus.user_id')
            ->pluck('total', 'user_id');

        return User::query()
            ->where('role', 'joueur')
            ->get()
            ->map(fn (User $user) => [
                'user' => $user,
                'points' => (int) ($pointsPronostics[$user->id] ?? 0) + (int) ($pointsBonus[$user->id] ?? 0),
            ])
            ->sortByDesc('points')
            ->values();
    }

    public function rangDe(User $user, Collection $classement): ?int
    {
        $index = $classement->search(fn (array $entree) => $entree['user']->id === $user->id);

        return $index === false ? null : $index + 1;
    }
}
