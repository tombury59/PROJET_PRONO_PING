<?php

namespace App\Http\Controllers;

use App\Models\MatchGame;
use App\Models\Phase;
use App\Models\Pronostic;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PronosticController extends Controller
{
    public function index(Request $request): View
    {
        $phase = Phase::courante();

        $matches = $phase
            ? MatchGame::where('phase_id', $phase->id)
                ->with(['pronostics' => fn ($query) => $query->where('user_id', $request->user()->id)])
                ->orderBy('date_heure')
                ->get()
            : collect();

        $estAFaire = fn (MatchGame $match) => ! $match->resultat_saisi && ! $match->isVerrouille() && ! $match->pronostics->first();

        $matchesAFaire = $matches->filter($estAFaire)->values();
        $matchesTraites = $matches->reject($estAFaire)->values();

        return view('pronostics.index', [
            'phase' => $phase,
            'matches' => $matches,
            'matchesAFaire' => $matchesAFaire,
            'matchesTraites' => $matchesTraites,
        ]);
    }

    public function store(Request $request, MatchGame $match): RedirectResponse|JsonResponse
    {
        abort_if($match->isVerrouille(), 403, 'Ce match est verrouillé, le pronostic ne peut plus être modifié.');

        $data = $request->validate([
            'prono_score_j1' => ['required', 'integer', 'min:0', 'max:'.$match->nb_matchs],
            'prono_score_j2' => ['required', 'integer', 'min:0', 'max:'.$match->nb_matchs],
        ]);

        $vainqueur = match (true) {
            $data['prono_score_j1'] > $data['prono_score_j2'] => 1,
            $data['prono_score_j1'] < $data['prono_score_j2'] => 2,
            default => 0,
        };

        Pronostic::updateOrCreate(
            ['user_id' => $request->user()->id, 'match_id' => $match->id],
            [
                'prono_vainqueur' => $vainqueur,
                'prono_score_j1' => $data['prono_score_j1'],
                'prono_score_j2' => $data['prono_score_j2'],
            ]
        );

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Pronostic enregistré.',
                'match_id' => $match->id,
                'prono_score_j1' => $data['prono_score_j1'],
                'prono_score_j2' => $data['prono_score_j2'],
            ]);
        }

        return back()->with('status', 'Pronostic enregistré.');
    }

    public function joker(Request $request, MatchGame $match): RedirectResponse
    {
        abort_if($match->isVerrouille(), 403, 'Ce match est verrouillé, le joker ne peut plus être modifié.');

        $user = $request->user();

        $pronostic = Pronostic::where('user_id', $user->id)->where('match_id', $match->id)->first();

        abort_unless($pronostic, 403, "Fais d'abord ton pronostic avant de poser le joker.");

        // Retrait du joker s'il est déjà sur ce match.
        if ($pronostic->joker) {
            $pronostic->update(['joker' => false]);

            return back()->with('status', 'Joker retiré.');
        }

        // Un seul joker par phase : on le retire des autres pronostics de la
        // même phase avant de le poser ici.
        Pronostic::where('user_id', $user->id)
            ->where('joker', true)
            ->whereHas('match', fn ($query) => $query->where('phase_id', $match->phase_id))
            ->update(['joker' => false]);

        $pronostic->update(['joker' => true]);

        return back()->with('status', 'Joker ×2 placé sur '.$match->equipe1().' vs '.$match->equipe2().'.');
    }
}
