<?php

namespace Tests\Feature;

use App\Models\MatchGame;
use App\Models\Phase;
use App\Models\Pronostic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JokerTest extends TestCase
{
    use RefreshDatabase;

    public function test_joker_doubles_the_points_of_the_pronostic(): void
    {
        $match = MatchGame::factory()->create([
            'resultat_saisi' => true,
            'score_j1' => 12,
            'score_j2' => 6,
        ]);

        $prono = Pronostic::factory()->for($match, 'match')->create([
            'prono_vainqueur' => 1,
            'prono_score_j1' => 12,
            'prono_score_j2' => 6,
            'joker' => true,
        ]);
        $prono->setRelation('match', $match);

        // Score exact (3) doublé par le joker.
        $this->assertSame(6, $prono->calculerPoints());
    }

    public function test_a_player_can_place_and_remove_the_joker(): void
    {
        $user = User::factory()->create();
        $match = MatchGame::factory()->create(['date_heure' => now()->addDays(2)]);
        Pronostic::factory()->for($user)->for($match, 'match')->create();

        $this->actingAs($user)->post("/pronostics/{$match->id}/joker")->assertRedirect();
        $this->assertTrue(Pronostic::where('match_id', $match->id)->first()->joker);

        // Un second appel retire le joker.
        $this->actingAs($user)->post("/pronostics/{$match->id}/joker")->assertRedirect();
        $this->assertFalse(Pronostic::where('match_id', $match->id)->first()->joker);
    }

    public function test_only_one_joker_per_phase(): void
    {
        $user = User::factory()->create();
        $phase = Phase::factory()->create();

        $matchA = MatchGame::factory()->for($phase)->create(['date_heure' => now()->addDays(2)]);
        $matchB = MatchGame::factory()->for($phase)->create(['date_heure' => now()->addDays(3)]);
        Pronostic::factory()->for($user)->for($matchA, 'match')->create();
        Pronostic::factory()->for($user)->for($matchB, 'match')->create();

        $this->actingAs($user)->post("/pronostics/{$matchA->id}/joker");
        $this->actingAs($user)->post("/pronostics/{$matchB->id}/joker");

        $this->assertFalse(Pronostic::where('match_id', $matchA->id)->first()->joker);
        $this->assertTrue(Pronostic::where('match_id', $matchB->id)->first()->joker);
    }

    public function test_joker_cannot_be_set_without_a_pronostic(): void
    {
        $user = User::factory()->create();
        $match = MatchGame::factory()->create(['date_heure' => now()->addDays(2)]);

        $this->actingAs($user)->post("/pronostics/{$match->id}/joker")->assertForbidden();
    }

    public function test_joker_cannot_be_changed_once_locked(): void
    {
        $user = User::factory()->create();
        $match = MatchGame::factory()->create(['date_heure' => now()->addMinutes(30)]);
        Pronostic::factory()->for($user)->for($match, 'match')->create();

        $this->actingAs($user)->post("/pronostics/{$match->id}/joker")->assertForbidden();
    }
}
