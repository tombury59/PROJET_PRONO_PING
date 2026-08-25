<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class NouvelleJourneeDisponible extends Notification
{
    public function __construct(
        private readonly int $nbRencontres,
        private readonly int $nbQuestionsBonus = 0,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $message = "Nouvelle journée : {$this->nbRencontres} rencontre(s) à pronostiquer";

        if ($this->nbQuestionsBonus > 0) {
            $message .= " et {$this->nbQuestionsBonus} question(s) bonus";
        }

        return [
            'nb_rencontres' => $this->nbRencontres,
            'nb_questions_bonus' => $this->nbQuestionsBonus,
            'message' => $message.' !',
            'url' => route('pronostics.index'),
        ];
    }
}
