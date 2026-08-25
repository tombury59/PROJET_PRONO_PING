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
        MatchGame::factory()->for($phase)->create(['equipe_1' => 'Lille 2', 'equipe_2' => 'Roubaix 3']);

        $response = $this->actingAs($admin)->get('/admin/matches');

        $response->assertOk();
        $response->assertSee('Lille 2');
        $response->assertSee('Roubaix 3');
    }

    public function test_admin_can_create_a_match(): void
    {
        $admin = User::factory()->admin()->create();
        $phase = Phase::factory()->create();

        $response = $this->actingAs($admin)->post('/admin/matches', [
            'phase_id' => $phase->id,
            'equipe_1' => 'Lille 2',
            'equipe_2' => 'Roubaix 3',
            'nb_matchs' => 18,
            'date_heure' => now()->addWeek()->format('Y-m-d H:i:s'),
            'date_fin_pronostics' => now()->addWeek()->subHour()->format('Y-m-d H:i:s'),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('matches', [
            'phase_id' => $phase->id,
            'equipe_1' => 'Lille 2',
            'equipe_2' => 'Roubaix 3',
            'nb_matchs' => 18,
        ]);
    }

    public function test_teams_must_be_different(): void
    {
        $admin = User::factory()->admin()->create();
        $phase = Phase::factory()->create();

        $response = $this->actingAs($admin)->post('/admin/matches', [
            'phase_id' => $phase->id,
            'equipe_1' => 'Lille 2',
            'equipe_2' => 'Lille 2',
            'nb_matchs' => 18,
            'date_heure' => now()->addWeek()->format('Y-m-d H:i:s'),
            'date_fin_pronostics' => now()->addWeek()->subHour()->format('Y-m-d H:i:s'),
        ]);

        $response->assertSessionHasErrors('equipe_2');
    }

    public function test_nb_matchs_must_be_a_valid_format(): void
    {
        $admin = User::factory()->admin()->create();
        $phase = Phase::factory()->create();

        $response = $this->actingAs($admin)->post('/admin/matches', [
            'phase_id' => $phase->id,
            'equipe_1' => 'Lille 2',
            'equipe_2' => 'Roubaix 3',
            'nb_matchs' => 20,
            'date_heure' => now()->addWeek()->format('Y-m-d H:i:s'),
            'date_fin_pronostics' => now()->addWeek()->subHour()->format('Y-m-d H:i:s'),
        ]);

        $response->assertSessionHasErrors('nb_matchs');
    }

    public function test_date_fin_pronostics_must_be_before_date_heure(): void
    {
        $admin = User::factory()->admin()->create();
        $phase = Phase::factory()->create();

        $response = $this->actingAs($admin)->post('/admin/matches', [
            'phase_id' => $phase->id,
            'equipe_1' => 'Lille 2',
            'equipe_2' => 'Roubaix 3',
            'nb_matchs' => 18,
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
            'equipe_1' => $match->equipe_1,
            'equipe_2' => $match->equipe_2,
            'nb_matchs' => $match->nb_matchs,
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
            'equipe_1' => $match->equipe_1,
            'equipe_2' => $match->equipe_2,
            'nb_matchs' => $match->nb_matchs,
            'date_heure' => $nouvelleDateHeure->format('Y-m-d H:i:s'),
            'date_fin_pronostics' => $finPronosticsPersonnalisee->format('Y-m-d H:i:s'),
        ]);

        $response->assertRedirect();
        $this->assertSame(
            $finPronosticsPersonnalisee->format('Y-m-d H:i:s'),
            $match->fresh()->date_fin_pronostics->format('Y-m-d H:i:s')
        );
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
        $match = MatchGame::factory()->create(['nb_matchs' => 18]);

        $exactScorePlayer = Pronostic::factory()->for($match, 'match')->create([
            'prono_vainqueur' => 1,
            'prono_score_j1' => 12,
            'prono_score_j2' => 6,
        ]);

        $rightWinnerOnlyPlayer = Pronostic::factory()->for($match, 'match')->create([
            'prono_vainqueur' => 1,
            'prono_score_j1' => 10,
            'prono_score_j2' => 8,
        ]);

        $wrongPlayer = Pronostic::factory()->for($match, 'match')->create([
            'prono_vainqueur' => 2,
            'prono_score_j1' => 6,
            'prono_score_j2' => 12,
        ]);

        $response = $this->actingAs($admin)->put("/admin/matches/{$match->id}/resultat", [
            'score_j1' => 12,
            'score_j2' => 6,
        ]);

        $response->assertRedirect();
        $this->assertTrue($match->fresh()->resultat_saisi);
        $this->assertSame(3, $exactScorePlayer->fresh()->points_obtenus);
        $this->assertSame(1, $rightWinnerOnlyPlayer->fresh()->points_obtenus);
        $this->assertSame(0, $wrongPlayer->fresh()->points_obtenus);
    }

    public function test_result_can_be_a_draw_and_points_reward_the_exact_and_the_outcome(): void
    {
        $admin = User::factory()->admin()->create();
        $match = MatchGame::factory()->create(['nb_matchs' => 18]);

        // Prono nul exact (9-9) => 3 pts.
        $exactDraw = Pronostic::factory()->for($match, 'match')->create([
            'prono_vainqueur' => 0,
            'prono_score_j1' => 9,
            'prono_score_j2' => 9,
        ]);

        // Prono nul mais pas le bon score (8-8) => bonne issue => 1 pt.
        $wrongScoreDraw = Pronostic::factory()->for($match, 'match')->create([
            'prono_vainqueur' => 0,
            'prono_score_j1' => 8,
            'prono_score_j2' => 8,
        ]);

        $response = $this->actingAs($admin)->put("/admin/matches/{$match->id}/resultat", [
            'score_j1' => 9,
            'score_j2' => 9,
        ]);

        $response->assertRedirect();
        $this->assertTrue($match->fresh()->resultat_saisi);
        $this->assertSame(3, $exactDraw->fresh()->points_obtenus);
        $this->assertSame(1, $wrongScoreDraw->fresh()->points_obtenus);
    }

    public function test_result_score_cannot_exceed_the_format(): void
    {
        $admin = User::factory()->admin()->create();
        $match = MatchGame::factory()->create(['nb_matchs' => 14]);

        $response = $this->actingAs($admin)->put("/admin/matches/{$match->id}/resultat", [
            'score_j1' => 15,
            'score_j2' => 0,
        ]);

        $response->assertSessionHasErrors('score_j1');
    }
}
