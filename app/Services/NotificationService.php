<?php

namespace App\Services;

use App\Models\MatchGame;
use App\Models\QuestionBonus;
use App\Models\User;
use App\Notifications\BonusResolu;
use App\Notifications\NouveauMatchDisponible;
use App\Notifications\NouvelleQuestionBonus;
use App\Notifications\ResultatADeposer;
use App\Notifications\ResultatDisponible;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Notification;

class NotificationService
{
    public function matchCree(MatchGame $match): void
    {
        $joueurs = User::where('role', 'joueur')->get();

        Notification::send($joueurs, new NouveauMatchDisponible($match));
    }

    public function resultatSaisi(MatchGame $match): void
    {
        foreach ($match->pronostics as $pronostic) {
            $pronostic->user->notify(new ResultatDisponible($match, $pronostic->points_obtenus ?? 0));
        }

        // Le résultat est saisi : le rappel "résultat à déposer" pour ce
        // match n'a plus lieu d'être, pour aucun admin.
        // Note : filtrage en PHP plutôt qu'en SQL car la colonne `data`
        // est stockée en `text` et l'opérateur JSON natif (`->`) n'est
        // pas portable entre MySQL et PostgreSQL sur ce type de colonne.
        DatabaseNotification::where('type', ResultatADeposer::class)
            ->whereNull('read_at')
            ->get()
            ->filter(fn (DatabaseNotification $notification) => ($notification->data['match_id'] ?? null) == $match->id)
            ->each(fn (DatabaseNotification $notification) => $notification->update(['read_at' => now()]));
    }

    public function questionBonusCreee(QuestionBonus $question): void
    {
        $joueurs = User::where('role', 'joueur')->get();

        Notification::send($joueurs, new NouvelleQuestionBonus($question));
    }

    public function bonusResolu(QuestionBonus $question): void
    {
        foreach ($question->reponses as $reponse) {
            $reponse->user->notify(new BonusResolu($question, $reponse->points_obtenus ?? 0));
        }
    }

    /**
     * Notifie les admins des matchs verrouillés dont le résultat n'a pas
     * encore été saisi, une seule fois par match.
     */
    public function verifierResultatsEnAttente(): void
    {
        $matchsEnAttente = MatchGame::where('resultat_saisi', false)
            ->get()
            ->filter(fn (MatchGame $match) => $match->isVerrouille());

        if ($matchsEnAttente->isEmpty()) {
            return;
        }

        $dejaNotifies = DatabaseNotification::where('type', ResultatADeposer::class)
            ->pluck('data')
            ->map(fn ($data) => $data['match_id'] ?? null)
            ->filter()
            ->unique();

        $matchsANotifier = $matchsEnAttente->reject(fn (MatchGame $match) => $dejaNotifies->contains($match->id));

        if ($matchsANotifier->isEmpty()) {
            return;
        }

        $admins = User::where('role', 'admin')->get();

        foreach ($matchsANotifier as $match) {
            Notification::send($admins, new ResultatADeposer($match));
        }
    }
}
