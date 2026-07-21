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
