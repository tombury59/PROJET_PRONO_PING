<?php

namespace App\Http\Controllers;

use App\Models\MatchGame;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MatchPronosticsController extends Controller
{
    public function show(Request $request, MatchGame $match): View
    {
        // Les pronostics des autres ne sont dévoilés qu'une fois le match
        // commencé, pour ne pas s'influencer avant l'échéance.
        abort_unless(
            $match->date_heure->isPast(),
            403,
            "Les pronostics des autres joueurs seront visibles une fois le match commencé."
        );

        $pronostics = $match->pronostics()
            ->whereHas('user', fn ($query) => $query->where('role', 'joueur'))
            ->with('user')
            ->get()
            ->sortBy([
                ['points_obtenus', 'desc'],
                ['user.pseudo', 'asc'],
            ])
            ->values();

        return view('matchs.pronostics', [
            'match' => $match,
            'pronostics' => $pronostics,
        ]);
    }
}
