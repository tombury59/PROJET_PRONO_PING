<?php

namespace Tests\Feature;

use App\Models\MatchGame;
use App\Models\Phase;
use App\Models\Pronostic;
use App\Models\User;
use App\Notifications\NouveauMatchDisponible;
use App\Notifications\ResultatADeposer;
use App\Notifications\ResultatDisponible;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_match_cree_notifies_only_joueurs(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create();
        $joueur1 = User::factory()->create();
        $joueur2 = User::factory()->create();
        $match = MatchGame::factory()->create();

        app(NotificationService::class)->matchCree($match);

        Notification::assertSentTo([$joueur1, $joueur2], NouveauMatchDisponible::class);
        Notification::assertNotSentTo($admin, NouveauMatchDisponible::class);
    }

    public function test_resultat_saisi_notifies_each_pronostic_owner_with_their_points(): void
    {
        Notification::fake();

        $match = MatchGame::factory()->create([
            'resultat_saisi' => true,
            'score_j1' => 3,
            'score_j2' => 1,
        ]);

        $gagnant = User::factory()->create();
        Pronostic::factory()->for($gagnant)->for($match, 'match')->create([
            'prono_vainqueur' => 1,
            'prono_score_j1' => 3,
            'prono_score_j2' => 1,
            'points_obtenus' => 3,
        ]);

        app(NotificationService::class)->resultatSaisi($match);

        Notification::assertSentTo(
            $gagnant,
            ResultatDisponible::class,
            fn ($notification, $channels) => $notification->toArray($gagnant)['message'] === "Résultat de {$match->equipe1()} vs {$match->equipe2()} : 3-1. Tu as gagné 3 point(s)."
        );
    }

    public function test_verifier_resultats_en_attente_notifies_admins_for_locked_unresolved_matches(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create();

        $matchVerrouille = MatchGame::factory()->create([
            'date_heure' => now()->subMinutes(30),
            'resultat_saisi' => false,
        ]);

        MatchGame::factory()->create([
            'date_heure' => now()->addDay(),
            'resultat_saisi' => false,
        ]);

        app(NotificationService::class)->verifierResultatsEnAttente();

        Notification::assertSentTo($admin, ResultatADeposer::class, function ($notification) use ($matchVerrouille) {
            return $notification->toArray($notification)['match_id'] === $matchVerrouille->id;
        });
        Notification::assertSentToTimes($admin, ResultatADeposer::class, 1);
    }

    public function test_verifier_resultats_en_attente_does_not_duplicate_notifications(): void
    {
        $admin = User::factory()->admin()->create();

        MatchGame::factory()->create([
            'date_heure' => now()->subMinutes(30),
            'resultat_saisi' => false,
        ]);

        $service = app(NotificationService::class);
        $service->verifierResultatsEnAttente();
        $service->verifierResultatsEnAttente();

        $this->assertSame(1, $admin->notifications()->where('type', ResultatADeposer::class)->count());
    }

    public function test_admin_middleware_triggers_pending_results_check(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create();
        $phase = Phase::factory()->create();
        MatchGame::factory()->for($phase)->create([
            'date_heure' => now()->subMinutes(30),
            'resultat_saisi' => false,
        ]);

        $this->actingAs($admin)->get('/admin/phases');

        Notification::assertSentTo($admin, ResultatADeposer::class);
    }
}
