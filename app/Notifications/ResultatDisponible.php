<?php

namespace App\Notifications;

use App\Models\MatchGame;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class ResultatDisponible extends Notification
{
    public function __construct(private readonly MatchGame $match, private readonly int $points)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', WebPushChannel::class];
    }

    private function message(): string
    {
        return "Résultat de {$this->match->equipe1()} vs {$this->match->equipe2()} : {$this->match->score_j1}-{$this->match->score_j2}. Tu as gagné {$this->points} point(s).";
    }

    public function toArray(object $notifiable): array
    {
        return [
            'match_id' => $this->match->id,
            'message' => $this->message(),
            'url' => route('pronostics.index'),
        ];
    }

    public function toWebPush(object $notifiable, Notification $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title('Résultat disponible')
            ->icon('/icons/icon-192.png')
            ->body($this->message())
            ->data(['url' => route('pronostics.index')]);
    }
}
