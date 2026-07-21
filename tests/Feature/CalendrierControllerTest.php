<?php

namespace Tests\Feature;

use App\Models\MatchGame;
use App\Models\Phase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalendrierControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_calendrier(): void
    {
        $response = $this->get('/calendrier');

        $response->assertRedirect('/login');
    }

    public function test_calendrier_shows_matches_of_the_current_month(): void
    {
        $user = User::factory()->create();
        $phase = Phase::factory()->create([
            'date_debut' => now()->subMonth(),
            'date_fin' => now()->addMonth(),
        ]);
        MatchGame::factory()->for($phase)->create([
            'joueur_1' => 'Alice',
            'joueur_2' => 'Bob',
            'date_heure' => now()->startOfMonth()->addDays(5)->setTime(19, 0),
        ]);

        $response = $this->actingAs($user)->get('/calendrier');

        $response->assertOk();
        $response->assertSee('Alice');
        $response->assertSee('Bob');
        $response->assertSee($phase->nom);
    }

    public function test_calendrier_can_navigate_to_another_month(): void
    {
        $user = User::factory()->create();
        $moisSuivant = now()->addMonth();

        $phase = Phase::factory()->create([
            'date_debut' => now(),
            'date_fin' => now()->addMonths(2),
        ]);
        MatchGame::factory()->for($phase)->create([
            'joueur_1' => 'Camille',
            'joueur_2' => 'Marc',
            'date_heure' => $moisSuivant->copy()->startOfMonth()->addDays(3)->setTime(19, 0),
        ]);

        $response = $this->actingAs($user)->get('/calendrier?mois='.$moisSuivant->format('Y-m'));

        $response->assertOk();
        $response->assertSee('Camille');
        $response->assertSee('Marc');
    }

    public function test_calendrier_ignores_matches_outside_the_visible_grid(): void
    {
        $user = User::factory()->create();
        $phase = Phase::factory()->create([
            'date_debut' => now()->subMonths(6),
            'date_fin' => now()->addMonths(6),
        ]);
        MatchGame::factory()->for($phase)->create([
            'joueur_1' => 'Lointain1',
            'joueur_2' => 'Lointain2',
            'date_heure' => now()->addMonths(3),
        ]);

        $response = $this->actingAs($user)->get('/calendrier');

        $response->assertOk();
        $response->assertDontSee('Lointain1');
    }

    public function test_invalid_mois_parameter_falls_back_to_current_month(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/calendrier?mois=not-a-date');

        $response->assertOk();
    }

    public function test_admin_sees_a_link_to_create_a_match_prefilled_with_the_day(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/calendrier');

        $response->assertOk();
        $jourDuMois = now()->startOfMonth()->format('Y-m-d');
        $response->assertSee('admin/matches/create?date='.$jourDuMois, false);
    }

    public function test_regular_user_does_not_see_the_create_match_link(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/calendrier');

        $response->assertOk();
        $response->assertDontSee('admin/matches/create?date=', false);
    }

    public function test_create_form_prefills_date_and_matching_phase_from_query(): void
    {
        $admin = User::factory()->admin()->create();
        $phase = Phase::factory()->create([
            'date_debut' => now()->subMonth(),
            'date_fin' => now()->addMonth(),
        ]);

        $date = now()->addDays(3)->format('Y-m-d');

        $response = $this->actingAs($admin)->get('/admin/matches/create?date='.$date);

        $response->assertOk();
        $response->assertSee('value="'.$date.'T19:00"', false);
        $response->assertSee('value="'.$phase->id.'" selected', false);
    }
}
