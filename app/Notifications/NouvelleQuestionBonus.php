<?php

namespace App\Notifications;

use App\Models\QuestionBonus;
use Illuminate\Notifications\Notification;

class NouvelleQuestionBonus extends Notification
{
    public function __construct(private readonly QuestionBonus $question)
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
            'message' => "Nouvelle question bonus disponible : « {$this->question->question} »",
            'url' => route('bonus.index'),
        ];
    }
}
