<?php

namespace App\Http\Controllers;

use App\Models\MatchGame;
use App\Models\Phase;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class CalendrierController extends Controller
{
    private const COULEURS = [
        'border-indigo-400 bg-indigo-50 dark:border-indigo-500 dark:bg-indigo-900/20',
        'border-emerald-400 bg-emerald-50 dark:border-emerald-500 dark:bg-emerald-900/20',
        'border-amber-400 bg-amber-50 dark:border-amber-500 dark:bg-amber-900/20',
        'border-rose-400 bg-rose-50 dark:border-rose-500 dark:bg-rose-900/20',
        'border-sky-400 bg-sky-50 dark:border-sky-500 dark:bg-sky-900/20',
    ];

    public function index(Request $request): View
    {
        try {
            $mois = Carbon::createFromFormat('Y-m-d', $request->input('mois', now()->format('Y-m')).'-01')
                ->startOfMonth();
        } catch (\Exception) {
            $mois = now()->startOfMonth();
        }

        $mois->locale('fr');

        $debutGrille = $mois->copy()->startOfMonth()->startOfWeek(Carbon::MONDAY);
        $finGrille = $mois->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        $jours = collect();
        for ($jour = $debutGrille->copy(); $jour->lte($finGrille); $jour->addDay()) {
            $jours->push($jour->copy());
        }

        $toutesLesPhases = Phase::orderBy('date_debut')->get();
        $couleursParPhase = $toutesLesPhases->mapWithKeys(
            fn (Phase $phase, int $i) => [$phase->id => self::COULEURS[$i % count(self::COULEURS)]]
        );

        $phasesVisibles = $toutesLesPhases->filter(
            fn (Phase $phase) => $phase->date_debut->lte($finGrille) && $phase->date_fin->gte($debutGrille)
        );

        $matchesParJour = MatchGame::whereBetween('date_heure', [$debutGrille->copy()->startOfDay(), $finGrille->copy()->endOfDay()])
            ->orderBy('date_heure')
            ->get()
            ->groupBy(fn (MatchGame $match) => $match->date_heure->format('Y-m-d'));

        return view('calendrier.index', [
            'mois' => $mois,
            'jours' => $jours,
            'phasesVisibles' => $phasesVisibles,
            'couleursParPhase' => $couleursParPhase,
            'matchesParJour' => $matchesParJour,
            'moisPrecedent' => $mois->copy()->subMonth()->format('Y-m'),
            'moisSuivant' => $mois->copy()->addMonth()->format('Y-m'),
        ]);
    }
}
