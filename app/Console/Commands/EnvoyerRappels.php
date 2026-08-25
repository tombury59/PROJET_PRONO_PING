<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use Illuminate\Console\Command;

class EnvoyerRappels extends Command
{
    protected $signature = 'pronos:rappels';

    protected $description = 'Envoie les rappels de pronostics aux joueurs (J-2, 24h) et alerte les admins des résultats à saisir.';

    public function handle(NotificationService $notificationService): int
    {
        $notificationService->envoyerRappelsPronostics();
        $notificationService->verifierResultatsEnAttente();

        $this->info('Rappels traités.');

        return self::SUCCESS;
    }
}
