<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamManagementApiTest extends TestCase
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

    public function test_list_team_members_requires_auth(): void
    {
        $response = $this->getJson("/api/businesses/{$this->business->id}/team");
        $response->assertStatus(401);
    }

    public function test_owner_can_list_team_members(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->getJson("/api/businesses/{$this->business->id}/team");

        $response->assertStatus(200)
            ->assertJsonStructure(['members']);
    }

    public function test_owner_can_invite_new_user(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->postJson("/api/businesses/{$this->business->id}/team/invite", [
            'email' => 'newuser@example.com',
            'role' => 'manager',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Invitation sent! They will be added when they create an account.',
            ]);

        $this->assertDatabaseHas('team_members', [
            'business_id' => $this->business->id,
            'invite_email' => 'newuser@example.com',
            'role' => 'manager',
            'user_id' => null,
        ]);
    }

    public function test_owner_can_invite_existing_user(): void
    {
        $existingUser = User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->postJson("/api/businesses/{$this->business->id}/team/invite", [
            'email' => 'existing@example.com',
            'role' => 'viewer',
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => "{$existingUser->name} has been added to the team!"]);

        $this->assertDatabaseHas('team_members', [
            'business_id' => $this->business->id,
            'user_id' => $existingUser->id,
            'role' => 'viewer',
        ]);
    }

    public function test_cannot_invite_duplicate_member(): void
    {
        TeamMember::factory()->create([
            'business_id' => $this->business->id,
            'user_id' => $this->user->id,
            'invite_email' => $this->user->email,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->postJson("/api/businesses/{$this->business->id}/team/invite", [
            'email' => $this->user->email,
            'role' => 'manager',
        ]);

        $response->assertStatus(409);
    }

    public function test_cannot_remove_business_owner(): void
    {
        $member = TeamMember::factory()->create([
            'business_id' => $this->business->id,
            'user_id' => $this->business->user_id,
            'role' => 'admin',
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->deleteJson("/api/businesses/{$this->business->id}/team/{$member->id}");

        $response->assertStatus(400)
            ->assertJson(['error' => 'Cannot remove the business owner']);
    }

    public function test_get_permissions_returns_correct_roles(): void
    {
        // Test owner permissions
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->getJson("/api/businesses/{$this->business->id}/permissions");

        $response->assertStatus(200)
            ->assertJson([
                'role' => 'owner',
                'permissions' => [
                    'manage_business' => true,
                    'manage_reviews' => true,
                    'generate_responses' => true,
                    'manage_team' => true,
                    'billing' => true,
                ],
            ]);
    }

    public function test_team_member_permissions(): void
    {
        $memberUser = User::factory()->create();
        TeamMember::factory()->create([
            'business_id' => $this->business->id,
            'user_id' => $memberUser->id,
            'role' => 'manager',
        ]);

        $memberToken = $memberUser->createToken('test')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $memberToken,
            'Accept' => 'application/json',
        ])->getJson("/api/businesses/{$this->business->id}/permissions");

        $response->assertStatus(200)
            ->assertJson([
                'role' => 'manager',
                'permissions' => [
                    'manage_business' => true,
                    'manage_reviews' => true,
                    'generate_responses' => true,
                    'manage_team' => false,
                    'billing' => false,
                ],
            ]);
    }
}
