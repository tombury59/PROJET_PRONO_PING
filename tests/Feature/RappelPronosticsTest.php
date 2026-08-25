<?php

namespace Tests\Feature;

use App\Models\MatchGame;
use App\Models\Pronostic;
use App\Models\User;
use App\Notifications\RappelPronostics;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RappelPronosticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_j2_reminder_is_sent_to_players_without_a_pronostic(): void
    {
        Notification::fake();

        // Clôture des pronostics dans ~36h => fenêtre J-2 [24h, 48h].
        $match = MatchGame::factory()->create(['date_heure' => now()->addHours(37)]);

        $admin = User::factory()->admin()->create();
        $sansProno = User::factory()->create();
        $avecProno = User::factory()->create();
        Pronostic::factory()->for($avecProno)->for($match, 'match')->create();

        app(NotificationService::class)->envoyerRappelsPronostics();

        Notification::assertSentTo(
            $sansProno,
            RappelPronostics::class,
            fn ($notification) => $notification->toArray($sansProno)['echeance'] === 'j2'
        );
        Notification::assertNotSentTo($avecProno, RappelPronostics::class);
        Notification::assertNotSentTo($admin, RappelPronostics::class);

        $this->assertTrue($match->fresh()->rappel_j2_envoye);
    }

    public function test_24h_reminder_is_sent_within_the_last_day(): void
    {
        Notification::fake();

        // Clôture dans ~12h => fenêtre 24h [0h, 24h].
        $match = MatchGame::factory()->create(['date_heure' => now()->addHours(13)]);
        $joueur = User::factory()->create();

        app(NotificationService::class)->envoyerRappelsPronostics();

        Notification::assertSentTo(
            $joueur,
            RappelPronostics::class,
            fn ($notification) => $notification->toArray($joueur)['echeance'] === '24h'
        );
        $this->assertTrue($match->fresh()->rappel_24h_envoye);
        $this->assertFalse($match->fresh()->rappel_j2_envoye);
    }

    public function test_reminder_is_not_sent_twice(): void
    {
        $match = MatchGame::factory()->create(['date_heure' => now()->addHours(37)]);
        $joueur = User::factory()->create();

        $service = app(NotificationService::class);
        $service->envoyerRappelsPronostics();
        $service->envoyerRappelsPronostics();

        $this->assertSame(1, $joueur->notifications()->where('type', RappelPronostics::class)->count());
    }

    public function test_no_reminder_when_deadline_is_far_away(): void
    {
        Notification::fake();

        MatchGame::factory()->create(['date_heure' => now()->addDays(5)]);
        $joueur = User::factory()->create();

        app(NotificationService::class)->envoyerRappelsPronostics();

        Notification::assertNotSentTo($joueur, RappelPronostics::class);
    }
}
