<?php

namespace Tests\Feature\Admin;

use App\Models\MatchGame;
use App\Models\Phase;
use App\Models\Pronostic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MatchControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_regular_user_cannot_access_matches_admin(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/matches');

        $response->assertForbidden();
    }

    public function test_admin_can_view_matches_index(): void
    {
        $admin = User::factory()->admin()->create();
        $phase = Phase::factory()->create();
        MatchGame::factory()->for($phase)->create(['joueur_1' => 'Alice', 'joueur_2' => 'Bob']);

        $response = $this->actingAs($admin)->get('/admin/matches');

        $response->assertOk();
        $response->assertSee('Alice');
        $response->assertSee('Bob');
    }

    public function test_admin_can_create_a_match(): void
    {
        $admin = User::factory()->admin()->create();
        $phase = Phase::factory()->create();

        $response = $this->actingAs($admin)->post('/admin/matches', [
            'phase_id' => $phase->id,
            'joueur_1' => 'Alice',
            'joueur_2' => 'Bob',
            'date_heure' => now()->addWeek()->format('Y-m-d H:i:s'),
            'date_fin_pronostics' => now()->addWeek()->subHour()->format('Y-m-d H:i:s'),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('matches', [
            'phase_id' => $phase->id,
            'joueur_1' => 'Alice',
            'joueur_2' => 'Bob',
        ]);
    }

    public function test_players_must_be_different(): void
    {
        $admin = User::factory()->admin()->create();
        $phase = Phase::factory()->create();

        $response = $this->actingAs($admin)->post('/admin/matches', [
            'phase_id' => $phase->id,
            'joueur_1' => 'Alice',
            'joueur_2' => 'Alice',
            'date_heure' => now()->addWeek()->format('Y-m-d H:i:s'),
            'date_fin_pronostics' => now()->addWeek()->subHour()->format('Y-m-d H:i:s'),
        ]);

        $response->assertSessionHasErrors('joueur_2');
    }

    public function test_date_fin_pronostics_must_be_before_date_heure(): void
    {
        $admin = User::factory()->admin()->create();
        $phase = Phase::factory()->create();

        $response = $this->actingAs($admin)->post('/admin/matches', [
            'phase_id' => $phase->id,
            'joueur_1' => 'Alice',
            'joueur_2' => 'Bob',
            'date_heure' => now()->addWeek()->format('Y-m-d H:i:s'),
            'date_fin_pronostics' => now()->addWeek()->addHour()->format('Y-m-d H:i:s'),
        ]);

        $response->assertSessionHasErrors('date_fin_pronostics');
    }

    public function test_pushing_the_match_date_later_resyncs_the_untouched_lock_time(): void
    {
        $admin = User::factory()->admin()->create();
        // Match créé avec une heure proche : verrouillé dès la création.
        $match = MatchGame::factory()->create([
            'date_heure' => now()->addMinutes(10),
        ]);
        $this->assertTrue($match->isVerrouille());

        $nouvelleDateHeure = now()->addWeek();

        // Le formulaire renvoie l'ANCIENNE valeur par défaut de "fin des pronostics"
        // (simulateur : le JS de la page n'a pas resynchronisé ce champ, ou l'admin
        // n'a pas remarqué qu'il fallait le changer).
        $response = $this->actingAs($admin)->put("/admin/matches/{$match->id}", [
            'phase_id' => $match->phase_id,
            'joueur_1' => $match->joueur_1,
            'joueur_2' => $match->joueur_2,
            'date_heure' => $nouvelleDateHeure->format('Y-m-d H:i:s'),
            'date_fin_pronostics' => $match->date_heure->copy()->subHour()->format('Y-m-d H:i:s'),
        ]);

        $response->assertRedirect();
        $this->assertFalse($match->fresh()->isVerrouille());
        $this->assertSame(
            $nouvelleDateHeure->copy()->subHour()->format('Y-m-d H:i:s'),
            $match->fresh()->date_fin_pronostics->format('Y-m-d H:i:s')
        );
    }

    public function test_manually_customized_lock_time_is_respected_when_date_changes(): void
    {
        $admin = User::factory()->admin()->create();
        $match = MatchGame::factory()->create([
            'date_heure' => now()->addWeek(),
        ]);

        $nouvelleDateHeure = now()->addWeeks(2);
        $finPronosticsPersonnalisee = $nouvelleDateHeure->copy()->subHours(3);

        $response = $this->actingAs($admin)->put("/admin/matches/{$match->id}", [
            'phase_id' => $match->phase_id,
            'joueur_1' => $match->joueur_1,
            'joueur_2' => $match->joueur_2,
            'date_heure' => $nouvelleDateHeure->format('Y-m-d H:i:s'),
            'date_fin_pronostics' => $finPronosticsPersonnalisee->format('Y-m-d H:i:s'),
        ]);

        $response->assertRedirect();
        $this->assertSame(
            $finPronosticsPersonnalisee->format('Y-m-d H:i:s'),
            $match->fresh()->date_fin_pronostics->format('Y-m-d H:i:s')
        );
    }

    public function test_admin_can_create_a_double_match(): void
    {
        $admin = User::factory()->admin()->create();
        $phase = Phase::factory()->create();

        $response = $this->actingAs($admin)->post('/admin/matches', [
            'phase_id' => $phase->id,
            'joueur_1' => 'Alice',
            'joueur_1_partenaire' => 'Chloé',
            'joueur_2' => 'Bob',
            'joueur_2_partenaire' => 'Dan',
            'date_heure' => now()->addWeek()->format('Y-m-d H:i:s'),
            'date_fin_pronostics' => now()->addWeek()->subHour()->format('Y-m-d H:i:s'),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('matches', [
            'joueur_1' => 'Alice',
            'joueur_1_partenaire' => 'Chloé',
            'joueur_2' => 'Bob',
            'joueur_2_partenaire' => 'Dan',
        ]);

        $match = MatchGame::where('joueur_1', 'Alice')->firstOrFail();
        $this->assertTrue($match->estDouble());
        $this->assertSame('Alice / Chloé', $match->equipe1());
        $this->assertSame('Bob / Dan', $match->equipe2());
    }

    public function test_double_match_requires_both_partners(): void
    {
        $admin = User::factory()->admin()->create();
        $phase = Phase::factory()->create();

        $response = $this->actingAs($admin)->post('/admin/matches', [
            'phase_id' => $phase->id,
            'joueur_1' => 'Alice',
            'joueur_1_partenaire' => 'Chloé',
            'joueur_2' => 'Bob',
            'date_heure' => now()->addWeek()->format('Y-m-d H:i:s'),
            'date_fin_pronostics' => now()->addWeek()->subHour()->format('Y-m-d H:i:s'),
        ]);

        $response->assertSessionHasErrors('joueur_2_partenaire');
    }

    public function test_admin_cannot_delete_a_match_with_pronostics(): void
    {
        $admin = User::factory()->admin()->create();
        $match = MatchGame::factory()->create();
        Pronostic::factory()->for($match, 'match')->create();

        $response = $this->actingAs($admin)->delete("/admin/matches/{$match->id}");

        $response->assertRedirect();
        $this->assertDatabaseHas('matches', ['id' => $match->id]);
    }

    public function test_admin_can_delete_a_match_without_pronostics(): void
    {
        $admin = User::factory()->admin()->create();
        $match = MatchGame::factory()->create();

        $response = $this->actingAs($admin)->delete("/admin/matches/{$match->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('matches', ['id' => $match->id]);
    }

    public function test_admin_can_enter_a_result_and_points_are_calculated(): void
    {
        $admin = User::factory()->admin()->create();
        $match = MatchGame::factory()->create();

        $exactScorePlayer = Pronostic::factory()->for($match, 'match')->create([
            'prono_vainqueur' => 1,
            'prono_score_j1' => 3,
            'prono_score_j2' => 1,
        ]);

        $rightWinnerOnlyPlayer = Pronostic::factory()->for($match, 'match')->create([
            'prono_vainqueur' => 1,
            'prono_score_j1' => 3,
            'prono_score_j2' => 0,
        ]);

        $wrongPlayer = Pronostic::factory()->for($match, 'match')->create([
            'prono_vainqueur' => 2,
            'prono_score_j1' => 1,
            'prono_score_j2' => 3,
        ]);

        $response = $this->actingAs($admin)->put("/admin/matches/{$match->id}/resultat", [
            'score_j1' => 3,
            'score_j2' => 1,
        ]);

        $response->assertRedirect();
        $this->assertTrue($match->fresh()->resultat_saisi);
        $this->assertSame(3, $exactScorePlayer->fresh()->points_obtenus);
        $this->assertSame(1, $rightWinnerOnlyPlayer->fresh()->points_obtenus);
        $this->assertSame(0, $wrongPlayer->fresh()->points_obtenus);
    }

    public function test_result_scores_cannot_be_equal(): void
    {
        $admin = User::factory()->admin()->create();
        $match = MatchGame::factory()->create();

        $response = $this->actingAs($admin)->put("/admin/matches/{$match->id}/resultat", [
            'score_j1' => 2,
            'score_j2' => 2,
        ]);

        $response->assertSessionHasErrors('score_j2');
    }
}
