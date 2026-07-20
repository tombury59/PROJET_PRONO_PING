<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MatchGame;
use App\Models\Phase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MatchController extends Controller
{
    public function index(Request $request): View
    {
        $phases = Phase::orderByDesc('date_debut')->get();

        $selectedPhaseId = $request->integer('phase_id') ?: $phases->first()?->id;

        $matches = MatchGame::with('phase')
            ->withCount('pronostics')
            ->when($selectedPhaseId, fn ($query) => $query->where('phase_id', $selectedPhaseId))
            ->orderBy('date_heure')
            ->get();

        return view('admin.matches.index', [
            'phases' => $phases,
            'matches' => $matches,
            'selectedPhaseId' => $selectedPhaseId,
        ]);
    }

    public function create(): View
    {
        $phases = Phase::orderByDesc('date_debut')->get();

        return view('admin.matches.create', ['phases' => $phases]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'phase_id' => ['required', 'exists:phases,id'],
            'joueur_1' => ['required', 'string', 'max:255'],
            'joueur_2' => ['required', 'string', 'max:255', 'different:joueur_1'],
            'date_heure' => ['required', 'date'],
        ]);

        MatchGame::create($data);

        return redirect()->route('admin.matches.index', ['phase_id' => $data['phase_id']])
            ->with('status', 'Match créé.');
    }

    public function edit(MatchGame $match): View
    {
        $phases = Phase::orderByDesc('date_debut')->get();

        return view('admin.matches.edit', ['match' => $match, 'phases' => $phases]);
    }

    public function update(Request $request, MatchGame $match): RedirectResponse
    {
        $data = $request->validate([
            'phase_id' => ['required', 'exists:phases,id'],
            'joueur_1' => ['required', 'string', 'max:255'],
            'joueur_2' => ['required', 'string', 'max:255', 'different:joueur_1'],
            'date_heure' => ['required', 'date'],
        ]);

        $match->update($data);

        return redirect()->route('admin.matches.index', ['phase_id' => $match->phase_id])
            ->with('status', 'Match mis à jour.');
    }

    public function destroy(MatchGame $match): RedirectResponse
    {
        if ($match->pronostics()->exists()) {
            return redirect()->route('admin.matches.index', ['phase_id' => $match->phase_id])
                ->with('error', 'Impossible de supprimer un match qui a déjà des pronostics.');
        }

        $phaseId = $match->phase_id;
        $match->delete();

        return redirect()->route('admin.matches.index', ['phase_id' => $phaseId])
            ->with('status', 'Match supprimé.');
    }
}
