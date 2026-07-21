<?php

namespace App\Http\Controllers;

use App\Models\Phase;
use App\Models\QuestionBonus;
use App\Models\ReponseBonus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BonusController extends Controller
{
    public function index(Request $request): View
    {
        $phase = Phase::courante();

        $questions = $phase
            ? QuestionBonus::where('phase_id', $phase->id)
                ->with(['match', 'reponses' => fn ($query) => $query->where('user_id', $request->user()->id)])
                ->orderBy('id')
                ->get()
            : collect();

        return view('bonus.index', [
            'phase' => $phase,
            'questions' => $questions,
        ]);
    }

    public function store(Request $request, QuestionBonus $question): RedirectResponse
    {
        abort_if($question->reponse_correcte !== null, 403, 'Cette question bonus est déjà résolue.');

        $data = $request->validate([
            'reponse' => ['required', 'string', 'max:255'],
        ]);

        ReponseBonus::updateOrCreate(
            ['user_id' => $request->user()->id, 'question_bonus_id' => $question->id],
            ['reponse' => $data['reponse']]
        );

        return back()->with('status', 'Réponse enregistrée.');
    }
}
