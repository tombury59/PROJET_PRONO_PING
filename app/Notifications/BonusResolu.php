<?php

namespace App\Notifications;

use App\Models\QuestionBonus;
use Illuminate\Notifications\Notification;

class BonusResolu extends Notification
{
    public function __construct(private readonly QuestionBonus $question, private readonly int $points)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'question_bonus_id' => $this->question->id,
            'message' => "Question bonus résolue : « {$this->question->question} » — la bonne réponse était « {$this->question->reponse_correcte} ». Tu as gagné {$this->points} point(s).",
            'url' => route('bonus.index'),
        ];
    }
}
