<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationSettingsApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test')->plainTextToken;
    }

    public function test_update_notification_settings(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->putJson('/api/user/notification-settings', [
            'notify_new_reviews' => true,
            'notify_negative_reviews' => true,
            'notification_email' => 'alerts@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Notification settings updated successfully',
            ]);

        $this->user->refresh();
        $this->assertTrue($this->user->notify_new_reviews);
        $this->assertTrue($this->user->notify_negative_reviews);
        $this->assertEquals('alerts@example.com', $this->user->notification_email);
    }

    public function test_notification_settings_require_auth(): void
    {
        $response = $this->putJson('/api/user/notification-settings', [
            'notify_new_reviews' => true,
        ]);
        $response->assertStatus(401);
    }

    public function test_notification_settings_partial_update(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->putJson('/api/user/notification-settings', [
            'notify_new_reviews' => false,
        ]);

        $response->assertStatus(200);

        $this->user->refresh();
        $this->assertFalse($this->user->notify_new_reviews);
    }
}
