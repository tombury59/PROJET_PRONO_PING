<?php

namespace Tests\Feature;

use App\Models\MatchGame;
use App\Models\User;
use App\Notifications\NouveauMatchDisponible;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_visiting_a_notification_marks_it_as_read_and_redirects_to_its_url(): void
    {
        $user = User::factory()->create();
        $match = MatchGame::factory()->create();
        $user->notify(new NouveauMatchDisponible($match));

        $notification = $user->notifications()->first();
        $this->assertNull($notification->read_at);

        $response = $this->actingAs($user)->get(route('notifications.voir', $notification->id));

        $response->assertRedirect(route('pronostics.index'));
        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_user_cannot_view_another_users_notification(): void
    {
        $user = User::factory()->create();
        $autre = User::factory()->create();
        $match = MatchGame::factory()->create();
        $autre->notify(new NouveauMatchDisponible($match));

        $notification = $autre->notifications()->first();

        $response = $this->actingAs($user)->get(route('notifications.voir', $notification->id));

        $response->assertNotFound();
    }

    public function test_user_can_delete_their_own_notification(): void
    {
        $user = User::factory()->create();
        $match = MatchGame::factory()->create();
        $user->notify(new NouveauMatchDisponible($match));

        $notification = $user->notifications()->first();

        $response = $this->actingAs($user)->delete(route('notifications.destroy', $notification->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('notifications', ['id' => $notification->id]);
    }

    public function test_user_cannot_delete_another_users_notification(): void
    {
        $user = User::factory()->create();
        $autre = User::factory()->create();
        $match = MatchGame::factory()->create();
        $autre->notify(new NouveauMatchDisponible($match));

        $notification = $autre->notifications()->first();

        $response = $this->actingAs($user)->delete(route('notifications.destroy', $notification->id));

        $response->assertNotFound();
        $this->assertDatabaseHas('notifications', ['id' => $notification->id]);
    }
}
