<?php

namespace App\Http\Controllers;

use App\Models\MatchGame;
use App\Models\Phase;
use App\Services\ClassementService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, ClassementService $classementService): View
    {
        $user = $request->user();
        $phase = Phase::courante();

        $prochainsMatches = collect();
        $derniersResultats = collect();
        $classement = collect();
        $mesPoints = 0;
        $mesBonsResultats = 0;
        $mesScoresExacts = 0;
        $monRang = null;

        if ($phase) {
            $prochainsMatches = MatchGame::where('phase_id', $phase->id)
                ->where('date_heure', '>', now())
                ->with(['pronostics' => fn ($query) => $query->where('user_id', $user->id)])
                ->orderBy('date_heure')
                ->limit(6)
                ->get();

            $derniersResultats = MatchGame::where('phase_id', $phase->id)
                ->where('resultat_saisi', true)
                ->with(['pronostics' => fn ($query) => $query->where('user_id', $user->id)])
                ->orderByDesc('date_heure')
                ->limit(5)
                ->get();

            $classement = $classementService->pourPhase($phase);
            $monRang = $classementService->rangDe($user, $classement);
            $mesStats = $classement->firstWhere('user.id', $user->id);
            $mesPoints = $mesStats['points'] ?? 0;
            $mesBonsResultats = $mesStats['bons_resultats'] ?? 0;
            $mesScoresExacts = $mesStats['scores_exacts'] ?? 0;
        }

        return view('dashboard', [
            'phase' => $phase,
            'prochainsMatches' => $prochainsMatches,
            'derniersResultats' => $derniersResultats,
            'classement' => $classement->take(5),
            'mesPoints' => $mesPoints,
            'mesBonsResultats' => $mesBonsResultats,
            'mesScoresExacts' => $mesScoresExacts,
            'monRang' => $monRang,
            'nombreJoueurs' => $classement->count(),
        ]);
    }
}
