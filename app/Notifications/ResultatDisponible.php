<?php

namespace App\Notifications;

use App\Models\MatchGame;
use Illuminate\Notifications\Notification;

class ResultatDisponible extends Notification
{
    public function __construct(private readonly MatchGame $match, private readonly int $points)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'match_id' => $this->match->id,
            'message' => "Résultat de {$this->match->equipe1()} vs {$this->match->equipe2()} : {$this->match->score_j1}-{$this->match->score_j2}. Tu as gagné {$this->points} point(s).",
            'url' => route('pronostics.index'),
        ];
    }
}
