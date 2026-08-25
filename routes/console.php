<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Rappels de pronostics (J-2, 24h) + alerte admin sur les résultats à saisir.
// Toutes les heures : les fenêtres J-2 / 24h et les drapeaux d'envoi côté
// matchs garantissent qu'un rappel n'est envoyé qu'une seule fois.
Schedule::command('pronos:rappels')->hourly();
