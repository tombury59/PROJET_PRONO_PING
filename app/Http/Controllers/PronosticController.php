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
            'prono_score_j1' => ['required', 'integer', 'min:0', 'max:3'],
            'prono_score_j2' => ['required', 'integer', 'min:0', 'max:3', 'different:prono_score_j1'],
        ]);

        Pronostic::updateOrCreate(
            ['user_id' => $request->user()->id, 'match_id' => $match->id],
            [
                'prono_vainqueur' => $data['prono_score_j1'] > $data['prono_score_j2'] ? 1 : 2,
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
}
