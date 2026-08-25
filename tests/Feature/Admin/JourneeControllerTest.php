<?php

namespace Tests\Feature\Admin;

use App\Models\MatchGame;
use App\Models\Phase;
use App\Models\QuestionBonus;
use App\Models\User;
use App\Notifications\NouvelleJourneeDisponible;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class JourneeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_regular_user_cannot_access_the_journee_form(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin/journees/create')->assertForbidden();
    }

    public function test_admin_can_create_a_journee_with_several_rencontres_and_bonus(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create();
        $joueur = User::factory()->create();
        $phase = Phase::factory()->create();

        $dateA = now()->addWeek()->setTime(18, 0);
        $dateB = now()->addWeek()->addDay()->setTime(9, 0);

        $response = $this->actingAs($admin)->post('/admin/journees', [
            'phase_id' => $phase->id,
            'rencontres' => [
                ['equipe_1' => 'Lille 2', 'equipe_2' => 'Roubaix 3', 'nb_matchs' => 18, 'date_heure' => $dateA->format('Y-m-d H:i:s')],
                ['equipe_1' => 'Douai 2', 'equipe_2' => 'Lens 1', 'nb_matchs' => 14, 'date_heure' => $dateB->format('Y-m-d H:i:s')],
            ],
            'bonus' => [
                ['question' => "Combien d'équipes gagnantes ce week-end ?", 'description' => 'Sur les 2 rencontres.'],
                ['question' => '', 'description' => ''],
            ],
        ]);

        $response->assertRedirect(route('admin.matches.index', ['phase_id' => $phase->id]));

        $this->assertSame(2, MatchGame::where('phase_id', $phase->id)->count());
        $this->assertDatabaseHas('matches', [
            'equipe_1' => 'Lille 2',
            'equipe_2' => 'Roubaix 3',
            'nb_matchs' => 18,
        ]);

        // Fin des pronostics = 1h avant le match.
        $match = MatchGame::where('equipe_1', 'Lille 2')->firstOrFail();
        $this->assertSame(
            $dateA->copy()->subHour()->format('Y-m-d H:i'),
            $match->date_fin_pronostics->format('Y-m-d H:i')
        );

        // Seule la question bonus renseignée est créée.
        $this->assertSame(1, QuestionBonus::where('phase_id', $phase->id)->count());

        Notification::assertSentTo($joueur, NouvelleJourneeDisponible::class);
        Notification::assertNotSentTo($admin, NouvelleJourneeDisponible::class);
    }

    public function test_teams_must_differ_within_a_rencontre(): void
    {
        $admin = User::factory()->admin()->create();
        $phase = Phase::factory()->create();

        $response = $this->actingAs($admin)->post('/admin/journees', [
            'phase_id' => $phase->id,
            'rencontres' => [
                ['equipe_1' => 'Lille 2', 'equipe_2' => 'Lille 2', 'nb_matchs' => 18, 'date_heure' => now()->addWeek()->format('Y-m-d H:i:s')],
            ],
        ]);

        $response->assertSessionHasErrors('rencontres.0.equipe_2');
        $this->assertSame(0, MatchGame::count());
    }

    public function test_at_least_one_rencontre_is_required(): void
    {
        $admin = User::factory()->admin()->create();
        $phase = Phase::factory()->create();

        $response = $this->actingAs($admin)->post('/admin/journees', [
            'phase_id' => $phase->id,
            'rencontres' => [],
        ]);

        $response->assertSessionHasErrors('rencontres');
    }

    public function test_no_more_than_three_bonus_questions(): void
    {
        $admin = User::factory()->admin()->create();
        $phase = Phase::factory()->create();

        $response = $this->actingAs($admin)->post('/admin/journees', [
            'phase_id' => $phase->id,
            'rencontres' => [
                ['equipe_1' => 'A', 'equipe_2' => 'B', 'nb_matchs' => 18, 'date_heure' => now()->addWeek()->format('Y-m-d H:i:s')],
            ],
            'bonus' => [
                ['question' => 'Q1'],
                ['question' => 'Q2'],
                ['question' => 'Q3'],
                ['question' => 'Q4'],
            ],
        ]);

        $response->assertSessionHasErrors('bonus');
    }
}
