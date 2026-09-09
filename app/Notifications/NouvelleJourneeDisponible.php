<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class NouvelleJourneeDisponible extends Notification
{
    public function __construct(
        private readonly int $nbRencontres,
        private readonly int $nbQuestionsBonus = 0,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database', WebPushChannel::class];
    }

    private function message(): string
    {
        $message = "Nouvelle journée : {$this->nbRencontres} rencontre(s) à pronostiquer";

        if ($this->nbQuestionsBonus > 0) {
            $message .= " et {$this->nbQuestionsBonus} question(s) bonus";
        }

        return $message.' !';
    }

    public function toArray(object $notifiable): array
    {
        return [
            'nb_rencontres' => $this->nbRencontres,
            'nb_questions_bonus' => $this->nbQuestionsBonus,
            'message' => $this->message(),
            'url' => route('pronostics.index'),
        ];
    }

    public function toWebPush(object $notifiable, Notification $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title('Nouvelle journée')
            ->icon('/icons/icon-192.png')
            ->body($this->message())
            ->data(['url' => route('pronostics.index')]);
    }
}
