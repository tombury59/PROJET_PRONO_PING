<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AvatarTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_upload_an_avatar(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $response = $this->actingAs($user)->patch('/profile', [
            'pseudo' => $user->pseudo,
            'avatar' => UploadedFile::fake()->image('moi.jpg', 200, 200),
        ]);

        $response->assertRedirect(route('profile.edit'));

        $user->refresh();
        $this->assertNotNull($user->avatar_path);
        Storage::disk('public')->assertExists($user->avatar_path);
    }

    public function test_uploading_a_new_avatar_replaces_the_previous_one(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $this->actingAs($user)->patch('/profile', [
            'pseudo' => $user->pseudo,
            'avatar' => UploadedFile::fake()->image('a.jpg'),
        ]);
        $ancien = $user->refresh()->avatar_path;

        $this->actingAs($user)->patch('/profile', [
            'pseudo' => $user->pseudo,
            'avatar' => UploadedFile::fake()->image('b.jpg'),
        ]);

        $this->assertNotSame($ancien, $user->refresh()->avatar_path);
        Storage::disk('public')->assertMissing($ancien);
    }

    public function test_user_can_remove_their_avatar(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $this->actingAs($user)->patch('/profile', [
            'pseudo' => $user->pseudo,
            'avatar' => UploadedFile::fake()->image('a.jpg'),
        ]);
        $chemin = $user->refresh()->avatar_path;

        $this->actingAs($user)->patch('/profile', [
            'pseudo' => $user->pseudo,
            'supprimer_avatar' => '1',
        ]);

        $this->assertNull($user->refresh()->avatar_path);
        Storage::disk('public')->assertMissing($chemin);
    }

    public function test_a_non_image_file_is_rejected(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $response = $this->actingAs($user)->patch('/profile', [
            'pseudo' => $user->pseudo,
            'avatar' => UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf'),
        ]);

        $response->assertSessionHasErrors('avatar');
        $this->assertNull($user->refresh()->avatar_path);
    }

    public function test_initiales_fallback(): void
    {
        $this->assertSame('TB', User::factory()->make(['pseudo' => 'Tom Bury'])->initiales());
        $this->assertSame('MA', User::factory()->make(['pseudo' => 'Maxime'])->initiales());
    }
}
