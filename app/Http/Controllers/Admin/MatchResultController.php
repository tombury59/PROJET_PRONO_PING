<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MatchGame;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MatchResultController extends Controller
{
    public function update(Request $request, MatchGame $match): RedirectResponse
    {
        $data = $request->validate([
            'score_j1' => ['required', 'integer', 'min:0'],
            'score_j2' => ['required', 'integer', 'min:0', 'different:score_j1'],
        ]);

        $match->update([
            'score_j1' => $data['score_j1'],
            'score_j2' => $data['score_j2'],
            'resultat_saisi' => true,
        ]);

        foreach ($match->pronostics as $pronostic) {
            $pronostic->setRelation('match', $match);
            $pronostic->update(['points_obtenus' => $pronostic->calculerPoints()]);
        }

        return redirect()->route('admin.matches.index', ['phase_id' => $match->phase_id])
            ->with('status', 'Résultat enregistré, les points ont été calculés.');
    }
}
