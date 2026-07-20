<?php

namespace Tests\Feature;

use App\Models\MatchGame;
use App\Models\Phase;
use App\Models\Pronostic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PronosticControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_pronostics(): void
    {
        $response = $this->get('/pronostics');

        $response->assertRedirect('/login');
    }

    public function test_index_shows_matches_of_current_phase(): void
    {
        $user = User::factory()->create();
        $phase = Phase::factory()->create([
            'date_debut' => now()->subDays(5),
            'date_fin' => now()->addDays(5),
        ]);
        MatchGame::factory()->for($phase)->create([
            'joueur_1' => 'Alice',
            'joueur_2' => 'Bob',
            'date_heure' => now()->addDay(),
        ]);

        $response = $this->actingAs($user)->get('/pronostics');

        $response->assertOk();
        $response->assertSee('Alice');
        $response->assertSee('Bob');
    }

    public function test_user_can_submit_a_pronostic_for_an_unlocked_match(): void
    {
        $user = User::factory()->create();
        $match = MatchGame::factory()->create(['date_heure' => now()->addDays(2)]);

        $response = $this->actingAs($user)->post("/pronostics/{$match->id}", [
            'prono_score_j1' => 3,
            'prono_score_j2' => 1,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pronostics', [
            'user_id' => $user->id,
            'match_id' => $match->id,
            'prono_vainqueur' => 1,
            'prono_score_j1' => 3,
            'prono_score_j2' => 1,
        ]);
    }

    public function test_user_can_update_their_pronostic_before_lock(): void
    {
        $user = User::factory()->create();
        $match = MatchGame::factory()->create(['date_heure' => now()->addDays(2)]);
        Pronostic::factory()->for($user)->for($match, 'match')->create([
            'prono_vainqueur' => 1,
            'prono_score_j1' => 3,
            'prono_score_j2' => 0,
        ]);

        $response = $this->actingAs($user)->post("/pronostics/{$match->id}", [
            'prono_score_j1' => 1,
            'prono_score_j2' => 3,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pronostics', [
            'user_id' => $user->id,
            'match_id' => $match->id,
            'prono_vainqueur' => 2,
            'prono_score_j1' => 1,
            'prono_score_j2' => 3,
        ]);
        $this->assertSame(1, Pronostic::where('user_id', $user->id)->where('match_id', $match->id)->count());
    }

    public function test_pronostic_is_rejected_once_match_is_locked(): void
    {
        $user = User::factory()->create();
        $match = MatchGame::factory()->create(['date_heure' => now()->addMinutes(30)]);

        $response = $this->actingAs($user)->post("/pronostics/{$match->id}", [
            'prono_score_j1' => 3,
            'prono_score_j2' => 1,
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('pronostics', [
            'user_id' => $user->id,
            'match_id' => $match->id,
        ]);
    }

    public function test_scores_cannot_be_equal(): void
    {
        $user = User::factory()->create();
        $match = MatchGame::factory()->create(['date_heure' => now()->addDays(2)]);

        $response = $this->actingAs($user)->post("/pronostics/{$match->id}", [
            'prono_score_j1' => 2,
            'prono_score_j2' => 2,
        ]);

        $response->assertSessionHasErrors('prono_score_j2');
    }
}
