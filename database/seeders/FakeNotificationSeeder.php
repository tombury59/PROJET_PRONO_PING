<?php

namespace Database\Seeders;

use App\Models\MatchGame;
use App\Models\Phase;
use App\Services\NotificationService;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Réutilise le vrai NotificationService pour que les notifications
     * générées restent cohérentes avec les matchs/pronostics déjà en base.
     */
    public function run(): void
    {
        $service = app(NotificationService::class);

        $phaseCourante = Phase::orderByDesc('date_debut')->first();

        // "Nouveau match disponible" pour les matchs de la phase en cours.
        MatchGame::where('phase_id', $phaseCourante->id)
            ->get()
            ->each(fn (MatchGame $match) => $service->matchCree($match));

        // "Résultat disponible" pour tous les matchs déjà résultés.
        MatchGame::where('resultat_saisi', true)
            ->get()
            ->each(fn (MatchGame $match) => $service->resultatSaisi($match));

        // "Résultat à déposer" pour les admins (match verrouillé sans résultat).
        $service->verifierResultatsEnAttente();
    }
}
