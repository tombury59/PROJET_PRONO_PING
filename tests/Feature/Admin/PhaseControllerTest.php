<?php

namespace Tests\Feature\Admin;

use App\Models\Phase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhaseControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_regular_user_cannot_access_phases_admin(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/phases');

        $response->assertForbidden();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/admin/phases');

        $response->assertRedirect('/login');
    }

    public function test_admin_can_view_phases_index(): void
    {
        $admin = User::factory()->admin()->create();
        Phase::factory()->create(['nom' => 'Phase 1']);

        $response = $this->actingAs($admin)->get('/admin/phases');

        $response->assertOk();
        $response->assertSee('Phase 1');
    }

    public function test_admin_can_create_a_phase(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/admin/phases', [
            'nom' => 'Phase automne',
            'date_debut' => '2026-09-01',
            'date_fin' => '2026-12-15',
            'reset_classement' => '1',
        ]);

        $response->assertRedirect(route('admin.phases.index'));
        $this->assertDatabaseHas('phases', [
            'nom' => 'Phase automne',
            'reset_classement' => true,
        ]);
    }

    public function test_date_fin_must_be_after_date_debut(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/admin/phases', [
            'nom' => 'Phase invalide',
            'date_debut' => '2026-12-15',
            'date_fin' => '2026-09-01',
        ]);

        $response->assertSessionHasErrors('date_fin');
    }

    public function test_admin_can_update_a_phase(): void
    {
        $admin = User::factory()->admin()->create();
        $phase = Phase::factory()->create(['nom' => 'Ancien nom']);

        $response = $this->actingAs($admin)->put("/admin/phases/{$phase->id}", [
            'nom' => 'Nouveau nom',
            'date_debut' => $phase->date_debut->format('Y-m-d'),
            'date_fin' => $phase->date_fin->format('Y-m-d'),
        ]);

        $response->assertRedirect(route('admin.phases.index'));
        $this->assertSame('Nouveau nom', $phase->fresh()->nom);
    }

    public function test_admin_can_delete_a_phase_without_matches(): void
    {
        $admin = User::factory()->admin()->create();
        $phase = Phase::factory()->create();

        $response = $this->actingAs($admin)->delete("/admin/phases/{$phase->id}");

        $response->assertRedirect(route('admin.phases.index'));
        $this->assertDatabaseMissing('phases', ['id' => $phase->id]);
    }

    public function test_admin_cannot_delete_a_phase_with_matches(): void
    {
        $admin = User::factory()->admin()->create();
        $phase = Phase::factory()->create();
        $phase->matches()->create([
            'joueur_1' => 'Alice',
            'joueur_2' => 'Bob',
            'date_heure' => now()->addDay(),
        ]);

        $response = $this->actingAs($admin)->delete("/admin/phases/{$phase->id}");

        $response->assertRedirect(route('admin.phases.index'));
        $this->assertDatabaseHas('phases', ['id' => $phase->id]);
    }
}
