<?php

namespace Tests\Feature;

use App\Models\MatchGame;
use App\Models\Phase;
use App\Models\Pronostic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClassementControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_classement(): void
    {
        $response = $this->get('/classement');

        $response->assertRedirect('/login');
    }

    public function test_classement_ranks_players_by_points_for_current_phase(): void
    {
        $viewer = User::factory()->create();
        $phase = Phase::factory()->create([
            'date_debut' => now()->subDays(5),
            'date_fin' => now()->addDays(5),
        ]);
        $match = MatchGame::factory()->for($phase)->create(['resultat_saisi' => true]);

        $leader = User::factory()->create(['pseudo' => 'leader']);
        $second = User::factory()->create(['pseudo' => 'second']);

        Pronostic::factory()->for($leader)->for($match, 'match')->create(['points_obtenus' => 5]);
        Pronostic::factory()->for($second)->for($match, 'match')->create(['points_obtenus' => 2]);

        $response = $this->actingAs($viewer)->get('/classement');

        $response->assertOk();
        $content = $response->getContent();
        $this->assertTrue(strpos($content, 'leader') < strpos($content, 'second'));
    }

    public function test_classement_excludes_other_phases_by_default(): void
    {
        $viewer = User::factory()->create();

        $phaseActuelle = Phase::factory()->create([
            'date_debut' => now()->subDays(5),
            'date_fin' => now()->addDays(5),
        ]);
        $anciennePhase = Phase::factory()->create([
            'date_debut' => now()->subMonths(6),
            'date_fin' => now()->subMonths(3),
        ]);
        $ancienneMatch = MatchGame::factory()->for($anciennePhase)->create(['resultat_saisi' => true]);

        $joueur = User::factory()->create(['pseudo' => 'ancien']);
        Pronostic::factory()->for($joueur)->for($ancienneMatch, 'match')->create(['points_obtenus' => 10]);

        $response = $this->actingAs($viewer)->get('/classement?vue='.$phaseActuelle->id);

        $response->assertOk();
        $response->assertSee('ancien');

        preg_match('/ancien.*?<td[^>]*>\s*(\d+)\s*<\/td>/s', $response->getContent(), $matches);
        $this->assertSame('0', $matches[1] ?? null);
    }

    public function test_global_view_sums_points_across_all_phases(): void
    {
        $viewer = User::factory()->create();

        $phase1 = Phase::factory()->create(['date_debut' => now()->subMonths(6), 'date_fin' => now()->subMonths(3)]);
        $phase2 = Phase::factory()->create(['date_debut' => now()->subDays(5), 'date_fin' => now()->addDays(5)]);

        $match1 = MatchGame::factory()->for($phase1)->create(['resultat_saisi' => true]);
        $match2 = MatchGame::factory()->for($phase2)->create(['resultat_saisi' => true]);

        $joueur = User::factory()->create(['pseudo' => 'cumulard']);
        Pronostic::factory()->for($joueur)->for($match1, 'match')->create(['points_obtenus' => 3]);
        Pronostic::factory()->for($joueur)->for($match2, 'match')->create(['points_obtenus' => 4]);

        $response = $this->actingAs($viewer)->get('/classement?vue=global');

        $response->assertOk();
        $response->assertSee('cumulard');

        preg_match('/cumulard.*?<td[^>]*>\s*(\d+)\s*<\/td>/s', $response->getContent(), $matches);
        $this->assertSame('7', $matches[1] ?? null);
    }
}
