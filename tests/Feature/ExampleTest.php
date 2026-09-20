<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\InvestmentOpportunity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'newuser@chainvault.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201);

        $response->assertJsonStructure([
            'message',
            'user' => [
                'id',
                'name',
                'email',
                'created_at',
                'updated_at',
            ],
        ]);

        $response->assertJsonPath(
            'message',
            'Registration successful.'
        );
    }

    public function test_user_can_login(): void
    {
        $user = User::factory()->create([
            'email' => 'login@chainvault.com',
            'password' => 'password123',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'login@chainvault.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'message',
            'user',
            'token',
        ]);

        $response->assertJsonPath(
            'message',
            'Login successful.'
        );
    }

    public function test_authenticated_user_can_access_profile(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/user');

        $response->assertStatus(200);

        $response->assertJson([
            'id' => $user->id,
            'email' => $user->email,
        ]);
    }

    public function test_unauthenticated_user_cannot_access_profile(): void
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(401);

        $response->assertJson([
            'message' => 'Unauthenticated.',
        ]);
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader(
            'Authorization',
            'Bearer ' . $token
        )->postJson('/api/logout');

        $response->assertStatus(200);

        $response->assertJson([
            'message' => 'Logout successful.',
        ]);
    }

        public function test_authenticated_user_can_update_investment_application(): void
    {
        $user = User::factory()->create();

        $opportunity = InvestmentOpportunity::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/investment-applications', [
                'investment_opportunity_id' => $opportunity->id,
                'amount' => 1000,
                'notes' => 'Initial application',
            ]);

        $response->assertStatus(201);

        $application = $response->json('data');

        $updateResponse = $this->actingAs($user, 'sanctum')
            ->putJson('/api/investment-applications/' . $application['id'], [
                'amount' => 2500,
                'status' => 'approved',
                'notes' => 'Updated application',
            ]);

        $updateResponse
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Investment application updated successfully.',
            ]);
    }

        public function test_user_cannot_update_another_users_investment_application(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $opportunity = InvestmentOpportunity::factory()->create();

        $application = $this->actingAs($owner, 'sanctum')
            ->postJson('/api/investment-applications', [
                'investment_opportunity_id' => $opportunity->id,
                'amount' => 1000,
                'notes' => 'Owner application',
            ]);

        $application->assertStatus(201);

        $applicationId = $application->json('data.id');

        $response = $this->actingAs($otherUser, 'sanctum')
            ->putJson('/api/investment-applications/' . $applicationId, [
                'amount' => 5000,
                'status' => 'approved',
                'notes' => 'Unauthorized update',
            ]);

        $response
            ->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized.',
            ]);
    }

        public function test_user_cannot_delete_another_users_investment_application(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $opportunity = InvestmentOpportunity::factory()->create();

        $application = $this->actingAs($owner, 'sanctum')
            ->postJson('/api/investment-applications', [
                'investment_opportunity_id' => $opportunity->id,
                'amount' => 1000,
                'notes' => 'Owner application',
            ]);

        $application->assertStatus(201);

        $applicationId = $application->json('data.id');

        $response = $this->actingAs($otherUser, 'sanctum')
            ->deleteJson('/api/investment-applications/' . $applicationId);

        $response
            ->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized.',
            ]);
    }

        public function test_user_cannot_create_investment_application_without_required_fields(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/investment-applications', []);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'investment_opportunity_id',
                'amount',
            ]);
    }

        public function test_user_cannot_apply_to_same_investment_opportunity_twice(): void
    {
        $user = User::factory()->create();

        $opportunity = InvestmentOpportunity::factory()->create();

        $firstResponse = $this->actingAs($user, 'sanctum')
            ->postJson('/api/investment-applications', [
                'investment_opportunity_id' => $opportunity->id,
                'amount' => 1000,
                'notes' => 'First application',
            ]);

        $firstResponse->assertStatus(201);

        $secondResponse = $this->actingAs($user, 'sanctum')
            ->postJson('/api/investment-applications', [
                'investment_opportunity_id' => $opportunity->id,
                'amount' => 2000,
                'notes' => 'Duplicate application',
            ]);

        $secondResponse->assertStatus(409);
    }

    public function test_unauthenticated_user_cannot_create_investment_application(): void
    {
        $opportunity = InvestmentOpportunity::factory()->create();

        $response = $this->postJson('/api/investment-applications', [
            'investment_opportunity_id' => $opportunity->id,
            'amount' => 1000,
            'notes' => 'Unauthorized application',
        ]);

        $response->assertStatus(401);
    }

        public function test_user_cannot_update_application_with_invalid_amount(): void
    {
        $user = User::factory()->create();

        $opportunity = InvestmentOpportunity::factory()->create();

        $application = $this->actingAs($user, 'sanctum')
            ->postJson('/api/investment-applications', [
                'investment_opportunity_id' => $opportunity->id,
                'amount' => 1000,
                'notes' => 'Valid application',
            ]);

        $application->assertStatus(201);

        $applicationId = $application->json('data.id');

        $response = $this->actingAs($user, 'sanctum')
            ->putJson('/api/investment-applications/' . $applicationId, [
                'amount' => 0,
            ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'amount',
            ]);
    }

        public function test_authenticated_user_can_create_investment_opportunity(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/investment-opportunities', [
                'title' => 'Agricultural Growth Fund',
                'description' => 'Investment opportunity for agricultural businesses.',
                'target_amount' => 1000000,
                'minimum_investment' => 10000,
                'expected_return' => 15,
                'duration_months' => 12,
                'status' => 'open',
                'start_date' => now()->toDateString(),
                'end_date' => now()->addMonths(12)->toDateString(),
            ]);

        $response->assertStatus(201);
        $response->assertJsonPath(
            'data.title',
            'Agricultural Growth Fund'
        );
    }

        public function test_authenticated_user_can_view_an_investment_opportunity(): void
    {
        $user = User::factory()->create();

        $opportunity = InvestmentOpportunity::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/investment-opportunities/' . $opportunity->id);

        $response->assertStatus(200);

        $response->assertJsonPath(
            'data.id',
            $opportunity->id
        );
    }

        public function test_authenticated_user_can_update_investment_opportunity(): void
    {
        $user = User::factory()->create();

        $opportunity = InvestmentOpportunity::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->putJson('/api/investment-opportunities/' . $opportunity->id, [
                'title' => 'Updated Agricultural Fund',
                'description' => 'Updated investment opportunity.',
                'target_amount' => 2000000,
                'minimum_investment' => 20000,
                'expected_return' => 20,
                'duration_months' => 18,
                'status' => 'open',
                'start_date' => now()->toDateString(),
                'end_date' => now()->addMonths(18)->toDateString(),
            ]);

        $response->assertStatus(200);

        $response->assertJsonPath(
            'data.title',
            'Updated Agricultural Fund'
        );
    }

        public function test_authenticated_user_can_delete_investment_opportunity(): void
    {
        $user = User::factory()->create();

        $opportunity = InvestmentOpportunity::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson('/api/investment-opportunities/' . $opportunity->id);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('investment_opportunities', [
            'id' => $opportunity->id,
        ]);
    }
}