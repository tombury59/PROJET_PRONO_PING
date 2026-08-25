<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MatchGame;
use App\Models\Phase;
use App\Models\QuestionBonus;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class JourneeController extends Controller
{
    public function __construct(private readonly NotificationService $notificationService)
    {
    }

    public function create(Request $request): View
    {
        $phases = Phase::orderByDesc('date_debut')->get();

        // Créneau par défaut : le prochain samedi 18h (créneau le plus courant).
        $defautDateHeure = now()->next(Carbon::SATURDAY)->setTime(18, 0);

        return view('admin.journees.create', [
            'phases' => $phases,
            'phaseIdPreremplie' => Phase::courante()?->id,
            'defautDateHeure' => $defautDateHeure->format('Y-m-d\TH:i'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'phase_id' => ['required', 'exists:phases,id'],
            'rencontres' => ['required', 'array', 'min:1'],
            'rencontres.*.equipe_1' => ['required', 'string', 'max:255'],
            'rencontres.*.equipe_2' => ['required', 'string', 'max:255'],
            'rencontres.*.nb_matchs' => ['required', 'integer', 'in:14,18'],
            'rencontres.*.date_heure' => ['required', 'date'],
            'bonus' => ['nullable', 'array', 'max:3'],
            'bonus.*.question' => ['nullable', 'string', 'max:500'],
            'bonus.*.description' => ['nullable', 'string', 'max:1000'],
        ]);

        // Une équipe ne peut pas s'affronter elle-même (contrôle croisé
        // impossible avec les règles wildcard classiques).
        $validator->after(function ($validator) use ($request) {
            foreach ($request->input('rencontres', []) as $index => $rencontre) {
                $e1 = trim($rencontre['equipe_1'] ?? '');
                $e2 = trim($rencontre['equipe_2'] ?? '');

                if ($e1 !== '' && $e1 === $e2) {
                    $validator->errors()->add("rencontres.$index.equipe_2", 'Les deux équipes doivent être différentes.');
                }
            }
        });

        $data = $validator->validate();

        // Seules les questions bonus dont l'intitulé est renseigné sont créées.
        $bonus = collect($data['bonus'] ?? [])
            ->filter(fn ($ligne) => filled($ligne['question'] ?? null))
            ->values();

        DB::transaction(function () use ($data, $bonus) {
            foreach ($data['rencontres'] as $rencontre) {
                $dateHeure = Carbon::parse($rencontre['date_heure']);

                MatchGame::create([
                    'phase_id' => $data['phase_id'],
                    'equipe_1' => $rencontre['equipe_1'],
                    'equipe_2' => $rencontre['equipe_2'],
                    'nb_matchs' => $rencontre['nb_matchs'],
                    'date_heure' => $dateHeure,
                    'date_fin_pronostics' => $dateHeure->copy()->subHour(),
                ]);
            }

            foreach ($bonus as $ligne) {
                QuestionBonus::create([
                    'phase_id' => $data['phase_id'],
                    'question' => $ligne['question'],
                    'description' => $ligne['description'] ?? null,
                ]);
            }
        });

        $this->notificationService->journeeCree(count($data['rencontres']), $bonus->count());

        return redirect()->route('admin.matches.index', ['phase_id' => $data['phase_id']])
            ->with('status', 'Journée créée : '.count($data['rencontres']).' rencontre(s)'
                .($bonus->count() ? ' et '.$bonus->count().' question(s) bonus' : '').'.');
    }
}
