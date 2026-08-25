<?php

namespace App\Notifications;

use App\Models\MatchGame;
use Illuminate\Notifications\Notification;

class RappelPronostics extends Notification
{
    /**
     * @param  'j2'|'24h'  $echeance
     */
    public function __construct(
        private readonly MatchGame $match,
        private readonly string $echeance,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $rencontre = "{$this->match->equipe1()} vs {$this->match->equipe2()}";

        $message = $this->echeance === 'j2'
            ? "Plus que 2 jours pour pronostiquer {$rencontre} !"
            : "Dernières 24h pour pronostiquer {$rencontre} !";

        return [
            'match_id' => $this->match->id,
            'echeance' => $this->echeance,
            'message' => $message,
            'url' => route('pronostics.index'),
        ];
    }
}
