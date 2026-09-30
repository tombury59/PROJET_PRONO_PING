<?php

namespace Tests\Feature\Admin;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ApparenceControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_regular_user_cannot_access_apparence(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin/apparence')->assertForbidden();
    }

    public function test_guest_is_redirected(): void
    {
        $this->get('/admin/apparence')->assertRedirect('/login');
    }

    public function test_admin_can_view_apparence(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/admin/apparence')
            ->assertOk()
            ->assertSee('Couleurs')
            ->assertSee('Navbar (menu latéral)')
            ->assertSee('Logo');
    }

    public function test_admin_can_change_primary_color(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post('/admin/apparence', ['primary_color' => '#10b981'])
            ->assertRedirect(route('admin.apparence.edit'));

        $this->assertSame('#10b981', Setting::get('primary_color'));
    }

    public function test_admin_can_set_zone_colors_independently(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/admin/apparence', [
            'primary_color' => '#4f46e5',
            'color_header' => '#1e293b',
            'color_navbar' => '#0f172a',
            'color_page' => '#f8fafc',
        ])->assertRedirect(route('admin.apparence.edit'));

        $this->assertSame('#1e293b', Setting::get('color_header'));
        $this->assertSame('#0f172a', Setting::get('color_navbar'));
        $this->assertSame('#f8fafc', Setting::get('color_page'));
    }

    public function test_invalid_zone_color_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post('/admin/apparence', ['primary_color' => '#4f46e5', 'color_navbar' => 'nope'])
            ->assertSessionHasErrors('color_navbar');
    }

    public function test_invalid_color_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post('/admin/apparence', ['primary_color' => 'chartreuse'])
            ->assertSessionHasErrors('primary_color');
    }

    public function test_admin_can_upload_a_logo(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/admin/apparence', [
            'primary_color' => '#4f46e5',
            'logo' => UploadedFile::fake()->image('logo.png'),
        ])->assertRedirect(route('admin.apparence.edit'));

        $path = Setting::get('logo_path');
        $this->assertNotNull($path);
        Storage::disk('public')->assertExists($path);
    }

    public function test_admin_can_remove_the_logo(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();
        $path = UploadedFile::fake()->image('logo.png')->store('branding', 'public');
        Setting::set('logo_path', $path);

        $this->actingAs($admin)->post('/admin/apparence', [
            'primary_color' => '#4f46e5',
            'supprimer_logo' => '1',
        ])->assertRedirect(route('admin.apparence.edit'));

        $this->assertNull(Setting::get('logo_path'));
        Storage::disk('public')->assertMissing($path);
    }
}
