<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MatchGame;
use App\Models\Phase;
use App\Models\QuestionBonus;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuestionBonusController extends Controller
{
    public function __construct(private readonly NotificationService $notificationService)
    {
    }

    public function index(Request $request): View
    {
        $phases = Phase::orderByDesc('date_debut')->get();
        $selectedPhaseId = $request->integer('phase_id') ?: $phases->first()?->id;

        $questions = QuestionBonus::with(['phase', 'match'])
            ->withCount('reponses')
            ->when($selectedPhaseId, fn ($query) => $query->where('phase_id', $selectedPhaseId))
            ->orderByDesc('id')
            ->get();

        return view('admin.questions-bonus.index', [
            'phases' => $phases,
            'questions' => $questions,
            'selectedPhaseId' => $selectedPhaseId,
        ]);
    }

    public function create(): View
    {
        $phases = Phase::orderByDesc('date_debut')->get();
        $matches = MatchGame::orderByDesc('date_heure')->get();

        return view('admin.questions-bonus.create', [
            'phases' => $phases,
            'matches' => $matches,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);

        $question = QuestionBonus::create($data);

        $this->notificationService->questionBonusCreee($question);

        return redirect()->route('admin.questions-bonus.index', ['phase_id' => $data['phase_id']])
            ->with('status', 'Question bonus créée.');
    }

    public function edit(QuestionBonus $question): View
    {
        $phases = Phase::orderByDesc('date_debut')->get();
        $matches = MatchGame::orderByDesc('date_heure')->get();

        return view('admin.questions-bonus.edit', [
            'question' => $question,
            'phases' => $phases,
            'matches' => $matches,
        ]);
    }

    public function update(Request $request, QuestionBonus $question): RedirectResponse
    {
        $data = $this->validateData($request);
        $ancienneReponse = $question->reponse_correcte;

        $question->update($data);

        if ($question->reponse_correcte !== null && $question->reponse_correcte !== $ancienneReponse) {
            foreach ($question->reponses as $reponse) {
                $reponse->setRelation('questionBonus', $question);
                $reponse->update(['points_obtenus' => $reponse->calculerPoints()]);
            }

            $this->notificationService->bonusResolu($question);
        }

        return redirect()->route('admin.questions-bonus.index', ['phase_id' => $question->phase_id])
            ->with('status', 'Question bonus mise à jour.');
    }

    public function destroy(QuestionBonus $question): RedirectResponse
    {
        if ($question->reponses()->exists()) {
            return redirect()->route('admin.questions-bonus.index', ['phase_id' => $question->phase_id])
                ->with('error', 'Impossible de supprimer une question qui a déjà des réponses.');
        }

        $phaseId = $question->phase_id;
        $question->delete();

        return redirect()->route('admin.questions-bonus.index', ['phase_id' => $phaseId])
            ->with('status', 'Question bonus supprimée.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'phase_id' => ['required', 'exists:phases,id'],
            'match_id' => ['nullable', 'exists:matches,id'],
            'question' => ['required', 'string', 'max:500'],
            'reponse_correcte' => ['nullable', 'string', 'max:255'],
        ]);
    }
}
