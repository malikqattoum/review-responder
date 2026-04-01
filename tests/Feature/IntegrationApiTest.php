<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IntegrationApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Business $business;
    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->business = Business::factory()->create(['user_id' => $this->user->id]);
        $this->token = $this->user->createToken('test')->plainTextToken;
    }

    public function test_integration_status_requires_auth(): void
    {
        $response = $this->getJson('/api/integrations/status');
        $response->assertStatus(401);
    }

    public function test_integration_status_returns_config_state(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->getJson('/api/integrations/status');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'google_configured',
                'google_has_real_key',
                'yelp_configured',
                'yelp_has_real_key',
            ]);
    }

    public function test_google_search_requires_auth(): void
    {
        $response = $this->postJson('/api/integrations/google/search', [
            'query' => 'test business',
        ]);
        $response->assertStatus(401);
    }

    public function test_google_search_validates_query(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->postJson('/api/integrations/google/search', []);

        $response->assertStatus(422);
    }

    public function test_google_sync_validates_business_id(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->postJson('/api/integrations/google/sync', [
            'place_id' => 'test_place_id',
        ]);

        $response->assertStatus(422);
    }

    public function test_yelp_search_requires_auth(): void
    {
        $response = $this->postJson('/api/integrations/yelp/search', [
            'query' => 'test business',
        ]);
        $response->assertStatus(401);
    }

    public function test_permissions_endpoint_requires_auth(): void
    {
        $response = $this->getJson("/api/businesses/{$this->business->id}/permissions");
        $response->assertStatus(401);
    }
}
