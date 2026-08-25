<?php

namespace Tests\Feature;

use App\Models\MatchGame;
use App\Models\Pronostic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MatchPronosticsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_others_pronostics_are_hidden_before_the_match_starts(): void
    {
        $viewer = User::factory()->create();
        $match = MatchGame::factory()->create(['date_heure' => now()->addDay()]);

        $response = $this->actingAs($viewer)->get(route('matchs.pronostics', $match));

        $response->assertForbidden();
    }

    public function test_others_pronostics_are_visible_once_the_match_started(): void
    {
        $viewer = User::factory()->create();
        $match = MatchGame::factory()->create(['date_heure' => now()->subHour()]);

        $autre = User::factory()->create(['pseudo' => 'ChallengerX']);
        Pronostic::factory()->for($autre)->for($match, 'match')->create([
            'prono_score_j1' => 3,
            'prono_score_j2' => 2,
        ]);

        $response = $this->actingAs($viewer)->get(route('matchs.pronostics', $match));

        $response->assertOk();
        $response->assertSee('ChallengerX');
        $response->assertSee('3 - 2');
    }

    public function test_admin_pronostics_are_not_listed(): void
    {
        $viewer = User::factory()->create();
        $match = MatchGame::factory()->create(['date_heure' => now()->subHour()]);

        $admin = User::factory()->admin()->create(['pseudo' => 'AdminSecret']);
        Pronostic::factory()->for($admin)->for($match, 'match')->create();

        $response = $this->actingAs($viewer)->get(route('matchs.pronostics', $match));

        $response->assertOk();
        $response->assertDontSee('AdminSecret');
    }
}
