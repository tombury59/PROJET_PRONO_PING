<?php

namespace Database\Seeders;

use App\Models\Phase;
use App\Models\QuestionBonus;
use App\Models\ReponseBonus;
use App\Models\User;
use Illuminate\Database\Seeder;

class QuestionBonusSeeder extends Seeder
{
    public function run(): void
    {
        [$phase1, $phase2, $phase3] = Phase::orderBy('date_debut')->get();
        $joueurs = User::where('role', 'joueur')->orderBy('id')->get();

        // Phases terminées : la question est résolue, les réponses ont leurs points.
        $this->creerQuestionResolue($phase1, 'Qui remportera le plus de matchs sur la phase ?', 'Julien', $joueurs);
        $this->creerQuestionResolue($phase2, 'Qui remportera le plus de matchs sur la phase ?', 'Thomas', $joueurs);

        // Phase en cours : la question est ouverte, pas encore de bonne réponse.
        $question = QuestionBonus::create([
            'phase_id' => $phase3->id,
            'match_id' => null,
            'question' => 'Qui remportera le plus de matchs sur la phase ?',
            'reponse_correcte' => null,
        ]);

        foreach ($joueurs->take(6) as $joueur) {
            ReponseBonus::create([
                'user_id' => $joueur->id,
                'question_bonus_id' => $question->id,
                'reponse' => $joueur->pseudo === 'julien' ? 'Julien' : 'Marc',
                'points_obtenus' => null,
            ]);
        }
    }

    private function creerQuestionResolue(Phase $phase, string $intitule, string $bonneReponse, $joueurs): void
    {
        $question = QuestionBonus::create([
            'phase_id' => $phase->id,
            'match_id' => null,
            'question' => $intitule,
            'reponse_correcte' => $bonneReponse,
        ]);

        foreach ($joueurs as $i => $joueur) {
            $reponse = ReponseBonus::create([
                'user_id' => $joueur->id,
                'question_bonus_id' => $question->id,
                'reponse' => $i % 3 === 0 ? $bonneReponse : 'Kevin',
            ]);

            $reponse->update(['points_obtenus' => $reponse->calculerPoints()]);
        }
    }
}
