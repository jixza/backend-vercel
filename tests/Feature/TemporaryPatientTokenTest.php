<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\TemporaryPatientToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class TemporaryPatientTokenTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $patient;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->patient = Patient::factory()->create();
    }

    /** @test */
    public function can_generate_temporary_token()
    {
        $this->actingAs($this->user, 'sanctum');

        $response = $this->postJson("/api/patient/tokens/generate/{$this->patient->id}", [
            'expiration_minutes' => 30
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'status',
                    'message',
                    'data' => [
                        'token',
                        'expires_at',
                        'qr_url',
                        'patient'
                    ]
                ]);

        $this->assertDatabaseHas('temporary_patient_tokens', [
            'patient_id' => $this->patient->id,
            'created_by_user_id' => $this->user->id,
            'is_used' => false
        ]);
    }

    /** @test */
    public function can_access_patient_data_with_valid_token()
    {
        $token = TemporaryPatientToken::createForPatient(
            $this->patient->id,
            $this->user->id,
            60
        );

        $response = $this->getJson("/api/patient/token/{$token->token}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'status',
                    'message',
                    'data' => [
                        'patient_data',
                        'token_info'
                    ]
                ]);

        // Token should be marked as used after access
        $this->assertDatabaseHas('temporary_patient_tokens', [
            'id' => $token->id,
            'is_used' => true
        ]);
    }

    /** @test */
    public function cannot_access_patient_data_with_expired_token()
    {
        $token = TemporaryPatientToken::createForPatient(
            $this->patient->id,
            $this->user->id,
            -10 // Expired 10 minutes ago
        );

        $response = $this->getJson("/api/patient/token/{$token->token}");

        $response->assertStatus(401)
                ->assertJson([
                    'status' => 'error',
                    'message' => 'Invalid or expired token'
                ]);
    }

    /** @test */
    public function cannot_access_patient_data_with_used_token()
    {
        $token = TemporaryPatientToken::createForPatient(
            $this->patient->id,
            $this->user->id,
            60
        );

        // Mark token as used
        $token->markAsUsed();

        $response = $this->getJson("/api/patient/token/{$token->token}");

        $response->assertStatus(401)
                ->assertJson([
                    'status' => 'error',
                    'message' => 'Invalid or expired token'
                ]);
    }

    /** @test */
    public function can_revoke_specific_token()
    {
        $this->actingAs($this->user, 'sanctum');

        $token = TemporaryPatientToken::createForPatient(
            $this->patient->id,
            $this->user->id,
            60
        );

        $response = $this->deleteJson("/api/patient/tokens/revoke/{$token->token}");

        $response->assertStatus(200);

        $this->assertDatabaseHas('temporary_patient_tokens', [
            'id' => $token->id,
            'is_used' => true
        ]);
    }

    /** @test */
    public function can_get_active_tokens()
    {
        $this->actingAs($this->user, 'sanctum');

        // Create some tokens
        TemporaryPatientToken::createForPatient($this->patient->id, $this->user->id, 60);
        TemporaryPatientToken::createForPatient($this->patient->id, $this->user->id, 30);

        $response = $this->getJson('/api/patient/tokens/active');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'status',
                    'message',
                    'data' => [
                        '*' => [
                            'id',
                            'token',
                            'patient',
                            'created_at',
                            'expires_at'
                        ]
                    ]
                ]);
    }

    /** @test */
    public function can_revoke_all_tokens()
    {
        $this->actingAs($this->user, 'sanctum');

        // Create multiple tokens
        $token1 = TemporaryPatientToken::createForPatient($this->patient->id, $this->user->id, 60);
        $token2 = TemporaryPatientToken::createForPatient($this->patient->id, $this->user->id, 30);

        $response = $this->deleteJson('/api/patient/tokens/revoke-all');

        $response->assertStatus(200)
                ->assertJsonPath('data.revoked_count', 2);

        $this->assertDatabaseHas('temporary_patient_tokens', [
            'id' => $token1->id,
            'is_used' => true
        ]);

        $this->assertDatabaseHas('temporary_patient_tokens', [
            'id' => $token2->id,
            'is_used' => true
        ]);
    }

    /** @test */
    public function token_generation_revokes_existing_active_tokens()
    {
        $this->actingAs($this->user, 'sanctum');

        // Create existing token
        $existingToken = TemporaryPatientToken::createForPatient(
            $this->patient->id,
            $this->user->id,
            60
        );

        // Generate new token
        $response = $this->postJson("/api/patient/tokens/generate/{$this->patient->id}");

        $response->assertStatus(200);

        // Existing token should be revoked
        $this->assertDatabaseHas('temporary_patient_tokens', [
            'id' => $existingToken->id,
            'is_used' => true
        ]);
    }

    /** @test */
    public function cleanup_command_removes_old_expired_tokens()
    {
        // Create old expired token
        $oldToken = TemporaryPatientToken::createForPatient(
            $this->patient->id,
            $this->user->id,
            60
        );
        
        // Manually set it to be old
        $oldToken->update([
            'expires_at' => Carbon::now()->subDays(10),
            'created_at' => Carbon::now()->subDays(10)
        ]);

        // Create recent expired token
        $recentToken = TemporaryPatientToken::createForPatient(
            $this->patient->id,
            $this->user->id,
            60
        );
        $recentToken->update(['expires_at' => Carbon::now()->subHours(1)]);

        // Run cleanup command with 7 days threshold
        $this->artisan('tokens:cleanup --days=7')
             ->assertExitCode(0);

        // Old token should be deleted
        $this->assertDatabaseMissing('temporary_patient_tokens', [
            'id' => $oldToken->id
        ]);

        // Recent token should remain
        $this->assertDatabaseHas('temporary_patient_tokens', [
            'id' => $recentToken->id
        ]);
    }
}
