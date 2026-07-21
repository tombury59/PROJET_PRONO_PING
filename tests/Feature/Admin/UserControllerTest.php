<?php

namespace Tests\Feature\Admin;

use App\Models\Pronostic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_regular_user_cannot_access_users_admin(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/utilisateurs');

        $response->assertForbidden();
    }

    public function test_admin_can_view_users_index(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->create(['pseudo' => 'julien']);

        $response = $this->actingAs($admin)->get('/admin/utilisateurs');

        $response->assertOk();
        $response->assertSee('julien');
    }

    public function test_index_shows_pronostics_count(): void
    {
        $admin = User::factory()->admin()->create();
        $joueur = User::factory()->create();
        Pronostic::factory()->for($joueur)->count(3)->create();

        $response = $this->actingAs($admin)->get('/admin/utilisateurs');

        $response->assertOk();
        $response->assertSee((string) 3);
    }

    public function test_admin_can_promote_a_joueur_to_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $joueur = User::factory()->create();

        $response = $this->actingAs($admin)->patch("/admin/utilisateurs/{$joueur->id}/role", [
            'role' => 'admin',
        ]);

        $response->assertRedirect();
        $this->assertTrue($joueur->fresh()->isAdmin());
    }

    public function test_admin_can_demote_another_admin_when_several_remain(): void
    {
        $admin = User::factory()->admin()->create();
        $autreAdmin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->patch("/admin/utilisateurs/{$autreAdmin->id}/role", [
            'role' => 'joueur',
        ]);

        $response->assertRedirect();
        $this->assertFalse($autreAdmin->fresh()->isAdmin());
    }

    public function test_admin_cannot_change_their_own_role(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->patch("/admin/utilisateurs/{$admin->id}/role", [
            'role' => 'joueur',
        ]);

        $response->assertRedirect();
        $this->assertTrue($admin->fresh()->isAdmin());
    }

    public function test_admin_can_delete_a_joueur(): void
    {
        $admin = User::factory()->admin()->create();
        $joueur = User::factory()->create();

        $response = $this->actingAs($admin)->delete("/admin/utilisateurs/{$joueur->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $joueur->id]);
    }

    public function test_admin_cannot_delete_themselves(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->delete("/admin/utilisateurs/{$admin->id}");

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_admin_can_delete_another_admin_when_several_remain(): void
    {
        $admin = User::factory()->admin()->create();
        $autreAdmin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->delete("/admin/utilisateurs/{$autreAdmin->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $autreAdmin->id]);
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_deleting_a_user_cascades_their_pronostics(): void
    {
        $admin = User::factory()->admin()->create();
        $joueur = User::factory()->create();
        $pronostic = Pronostic::factory()->for($joueur)->create();

        $this->actingAs($admin)->delete("/admin/utilisateurs/{$joueur->id}");

        $this->assertDatabaseMissing('pronostics', ['id' => $pronostic->id]);
    }
}
