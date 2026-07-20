<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MatchGame;
use App\Models\Phase;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class MatchController extends Controller
{
    public function __construct(private readonly NotificationService $notificationService)
    {
    }

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
        $data = $this->validateData($request);

        $match = MatchGame::create($data);

        $this->notificationService->matchCree($match);

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
        $data = $this->validateData($request);

        // Si la fin des pronostics n'a pas été personnalisée (elle vaut toujours
        // l'ancien "1h avant" par défaut) et que la date du match a changé, on la
        // resynchronise automatiquement pour éviter un match resté verrouillé
        // à cause d'une ancienne date oubliée dans ce champ.
        $ancienDefaut = $match->date_heure->copy()->subHour()->format('Y-m-d H:i:s');
        $nouvelleDateHeure = Carbon::parse($data['date_heure']);
        $finPronosticsSoumise = Carbon::parse($data['date_fin_pronostics'])->format('Y-m-d H:i:s');
        $dateHeureInchangee = $nouvelleDateHeure->format('Y-m-d H:i:s') === $match->date_heure->format('Y-m-d H:i:s');

        if ($finPronosticsSoumise === $ancienDefaut && ! $dateHeureInchangee) {
            $data['date_fin_pronostics'] = $nouvelleDateHeure->copy()->subHour();
        }

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

    private function validateData(Request $request): array
    {
        return $request->validate([
            'phase_id' => ['required', 'exists:phases,id'],
            'joueur_1' => ['required', 'string', 'max:255'],
            'joueur_1_partenaire' => ['nullable', 'string', 'max:255', 'different:joueur_1', 'required_with:joueur_2_partenaire'],
            'joueur_2' => ['required', 'string', 'max:255', 'different:joueur_1'],
            'joueur_2_partenaire' => ['nullable', 'string', 'max:255', 'different:joueur_2', 'different:joueur_1_partenaire', 'required_with:joueur_1_partenaire'],
            'date_heure' => ['required', 'date'],
            'date_fin_pronostics' => ['required', 'date', 'before:date_heure'],
        ]);
    }
}
