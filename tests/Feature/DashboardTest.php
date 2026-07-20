<?php

namespace Tests\Feature;

use App\Models\MatchGame;
use App\Models\Phase;
use App\Models\Pronostic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_upcoming_matches_and_stats(): void
    {
        $user = User::factory()->create();
        $phase = Phase::factory()->create([
            'date_debut' => now()->subDays(5),
            'date_fin' => now()->addDays(5),
        ]);

        $upcoming = MatchGame::factory()->for($phase)->create([
            'joueur_1' => 'Alice',
            'joueur_2' => 'Bob',
            'date_heure' => now()->addDay(),
        ]);

        $played = MatchGame::factory()->for($phase)->create([
            'joueur_1' => 'Carla',
            'joueur_2' => 'Dan',
            'date_heure' => now()->subDay(),
            'resultat_saisi' => true,
            'score_j1' => 3,
            'score_j2' => 1,
        ]);

        Pronostic::factory()->for($user)->for($played, 'match')->create([
            'prono_vainqueur' => 1,
            'prono_score_j1' => 3,
            'prono_score_j2' => 1,
            'points_obtenus' => 3,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();
        $response->assertSee('Prochains matchs');
        $response->assertSee('Alice');
        $response->assertSee('Mes statistiques');
        $response->assertSee('Derniers résultats');
        $response->assertSee('Carla');
        $response->assertSee('Classement');
        $response->assertSee($user->pseudo);
    }

    public function test_dashboard_handles_no_current_phase_gracefully(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();
    }
}
