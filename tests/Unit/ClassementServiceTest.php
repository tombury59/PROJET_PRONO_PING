<?php

namespace Tests\Unit;

use App\Models\MatchGame;
use App\Models\Phase;
use App\Models\Pronostic;
use App\Models\User;
use App\Services\ClassementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClassementServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_phase_with_reset_only_counts_its_own_points(): void
    {
        $phase1 = Phase::factory()->create([
            'date_debut' => now()->subMonths(6),
            'date_fin' => now()->subMonths(3),
            'reset_classement' => true,
        ]);
        $phase2 = Phase::factory()->create([
            'date_debut' => now()->subMonths(2),
            'date_fin' => now()->addMonth(),
            'reset_classement' => true,
        ]);

        $service = app(ClassementService::class);

        $phasesIncluses = $service->phasesPourClassement($phase2);

        $this->assertCount(1, $phasesIncluses);
        $this->assertSame($phase2->id, $phasesIncluses->first()->id);
    }

    public function test_phase_without_reset_chains_with_previous_phase(): void
    {
        $phase1 = Phase::factory()->create([
            'date_debut' => now()->subMonths(6),
            'date_fin' => now()->subMonths(3),
            'reset_classement' => true,
        ]);
        $phase2 = Phase::factory()->create([
            'date_debut' => now()->subMonths(2),
            'date_fin' => now()->addMonth(),
            'reset_classement' => false,
        ]);

        $service = app(ClassementService::class);

        $phasesIncluses = $service->phasesPourClassement($phase2);

        $this->assertCount(2, $phasesIncluses);
        $this->assertSame($phase1->id, $phasesIncluses->first()->id);
        $this->assertSame($phase2->id, $phasesIncluses->last()->id);
    }

    public function test_chain_stops_at_the_first_phase_with_reset_activated(): void
    {
        $phase1 = Phase::factory()->create([
            'date_debut' => now()->subMonths(9),
            'date_fin' => now()->subMonths(7),
            'reset_classement' => true,
        ]);
        $phase2 = Phase::factory()->create([
            'date_debut' => now()->subMonths(6),
            'date_fin' => now()->subMonths(4),
            'reset_classement' => true,
        ]);
        $phase3 = Phase::factory()->create([
            'date_debut' => now()->subMonths(3),
            'date_fin' => now()->addMonth(),
            'reset_classement' => false,
        ]);

        $service = app(ClassementService::class);

        $phasesIncluses = $service->phasesPourClassement($phase3);

        $this->assertCount(2, $phasesIncluses);
        $this->assertSame($phase2->id, $phasesIncluses->first()->id);
        $this->assertSame($phase3->id, $phasesIncluses->last()->id);
    }

    public function test_admins_are_excluded_from_the_classement(): void
    {
        $phase = Phase::factory()->create([
            'date_debut' => now()->subMonth(),
            'date_fin' => now()->addMonth(),
            'reset_classement' => true,
        ]);

        $joueur = User::factory()->create();
        $admin = User::factory()->admin()->create();

        $match = MatchGame::factory()->for($phase)->create(['resultat_saisi' => true]);
        Pronostic::factory()->for($joueur)->for($match, 'match')->create(['points_obtenus' => 3]);
        Pronostic::factory()->for($admin)->for($match, 'match')->create(['points_obtenus' => 3]);

        $classement = app(ClassementService::class)->pourPhase($phase);

        $this->assertNotNull($classement->firstWhere('user.id', $joueur->id));
        $this->assertNull($classement->firstWhere('user.id', $admin->id));
    }

    public function test_classement_counts_winners_found_and_exact_scores(): void
    {
        $phase = Phase::factory()->create([
            'date_debut' => now()->subMonth(),
            'date_fin' => now()->addMonth(),
            'reset_classement' => true,
        ]);

        $joueur = User::factory()->create();

        $matchExact = MatchGame::factory()->for($phase)->create(['resultat_saisi' => true]);
        $matchIssue = MatchGame::factory()->for($phase)->create(['resultat_saisi' => true]);
        $matchRate = MatchGame::factory()->for($phase)->create(['resultat_saisi' => true]);

        Pronostic::factory()->for($joueur)->for($matchExact, 'match')->create(['points_obtenus' => 3]);
        Pronostic::factory()->for($joueur)->for($matchIssue, 'match')->create(['points_obtenus' => 1]);
        Pronostic::factory()->for($joueur)->for($matchRate, 'match')->create(['points_obtenus' => 0]);

        $entree = app(ClassementService::class)->pourPhase($phase)->firstWhere('user.id', $joueur->id);

        // Vainqueurs trouvés : le score exact (3) + la bonne issue (1) = 2.
        $this->assertSame(2, $entree['bons_resultats']);
        $this->assertSame(1, $entree['scores_exacts']);
    }

    public function test_evolution_accumulates_points_by_match_date(): void
    {
        $phase = Phase::factory()->create([
            'date_debut' => now()->subMonth(),
            'date_fin' => now()->addMonth(),
            'reset_classement' => true,
        ]);

        $joueur = User::factory()->create();

        $match1 = MatchGame::factory()->for($phase)->create([
            'resultat_saisi' => true,
            'date_heure' => now()->subDays(10),
        ]);
        $match2 = MatchGame::factory()->for($phase)->create([
            'resultat_saisi' => true,
            'date_heure' => now()->subDays(3),
        ]);

        Pronostic::factory()->for($joueur)->for($match1, 'match')->create(['points_obtenus' => 3]);
        Pronostic::factory()->for($joueur)->for($match2, 'match')->create(['points_obtenus' => 1]);

        $evolution = app(ClassementService::class)->evolution($phase);

        $this->assertCount(2, $evolution['dates']);

        $serie = $evolution['series']->firstWhere('user.id', $joueur->id);
        $this->assertSame([3, 4], $serie['cumul']);
        $this->assertSame(4, $serie['final']);
    }

    public function test_evolution_is_empty_without_resolved_matches(): void
    {
        $phase = Phase::factory()->create([
            'date_debut' => now()->subMonth(),
            'date_fin' => now()->addMonth(),
            'reset_classement' => true,
        ]);

        $evolution = app(ClassementService::class)->evolution($phase);

        $this->assertSame([], $evolution['dates']);
        $this->assertTrue($evolution['series']->isEmpty());
    }

    public function test_points_are_cumulated_across_chained_phases(): void
    {
        $phase1 = Phase::factory()->create([
            'date_debut' => now()->subMonths(6),
            'date_fin' => now()->subMonths(3),
            'reset_classement' => true,
        ]);
        $phase2 = Phase::factory()->create([
            'date_debut' => now()->subMonths(2),
            'date_fin' => now()->addMonth(),
            'reset_classement' => false,
        ]);

        $joueur = User::factory()->create();

        $match1 = MatchGame::factory()->for($phase1)->create(['resultat_saisi' => true]);
        $match2 = MatchGame::factory()->for($phase2)->create(['resultat_saisi' => true]);

        Pronostic::factory()->for($joueur)->for($match1, 'match')->create(['points_obtenus' => 3]);
        Pronostic::factory()->for($joueur)->for($match2, 'match')->create(['points_obtenus' => 4]);

        $service = app(ClassementService::class);

        $classementPhase1 = $service->pourPhase($phase1);
        $classementPhase2 = $service->pourPhase($phase2);

        $this->assertSame(3, $classementPhase1->firstWhere('user.id', $joueur->id)['points']);
        $this->assertSame(7, $classementPhase2->firstWhere('user.id', $joueur->id)['points']);
    }
}
