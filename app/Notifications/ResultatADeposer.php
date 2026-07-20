<?php

namespace App\Notifications;

use App\Models\MatchGame;
use Illuminate\Notifications\Notification;

class ResultatADeposer extends Notification
{
    public function __construct(private readonly MatchGame $match)
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
            'message' => "Le résultat de {$this->match->equipe1()} vs {$this->match->equipe2()} doit être saisi",
            'url' => route('admin.matches.edit', $this->match),
        ];
    }
}
