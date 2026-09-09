<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PushSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    private array $payload = [
        'endpoint' => 'https://push.example.com/abc123',
        'keys' => [
            'p256dh' => 'BQ_test_public_key_value',
            'auth' => 'auth_token_value',
        ],
    ];

    public function test_guest_cannot_subscribe(): void
    {
        $this->postJson('/push/subscriptions', $this->payload)->assertUnauthorized();
    }

    public function test_a_user_can_store_a_push_subscription(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/push/subscriptions', $this->payload)
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('push_subscriptions', [
            'subscribable_id' => $user->id,
            'endpoint' => $this->payload['endpoint'],
        ]);
    }

    public function test_a_user_can_delete_a_push_subscription(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->postJson('/push/subscriptions', $this->payload);

        $this->actingAs($user)
            ->deleteJson('/push/subscriptions', ['endpoint' => $this->payload['endpoint']])
            ->assertOk();

        $this->assertDatabaseMissing('push_subscriptions', [
            'endpoint' => $this->payload['endpoint'],
        ]);
    }

    public function test_subscription_requires_endpoint_and_keys(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/push/subscriptions', ['endpoint' => 'https://x'])
            ->assertJsonValidationErrors(['keys.p256dh', 'keys.auth']);
    }
}
