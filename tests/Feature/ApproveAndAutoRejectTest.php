<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Modules\User\Models\User;
use Modules\Package\Models\Package;
use Modules\ShipmentRequest\Models\ShipmentRequest;
use Modules\User\Database\Seeders\UserDatabaseSeeder;

class ApproveAndAutoRejectTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
        $this->seed(UserDatabaseSeeder::class);
    }

    public function test_sender_approves_one_request_and_others_are_auto_rejected(): void
    {
        $sender = User::whereHas('role', fn($q) => $q->where('name', 'sender'))->first();
        $carriers = User::whereHas('role', fn($q) => $q->where('name', 'carrier'))->take(2)->get();
        $package = Package::where('sender_id', $sender->id)->first();

        // Login as sender
        $token = $this->loginAs($sender->email, 'sender', 'Sender@123');

        // two carrier requests
        foreach ($carriers as $carrier) {
            $this->actingAs($carrier)
                ->postJson("/api/v1/packages/{$package->id}/requests")
                ->assertCreated();
        }

        $requestToApprove = ShipmentRequest::where('package_id', $package->id)->first();

        $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson("/api/v1/requests/{$requestToApprove->id}/approve")
            ->assertOk();

        $this->assertEquals('accepted', $requestToApprove->fresh()->status);
        $this->assertEquals('in_transit', $package->fresh()->status);

        $other = ShipmentRequest::where('package_id', $package->id)
            ->where('id', '!=', $requestToApprove->id)->first();
        $this->assertEquals('rejected', $other->status);
        $this->assertEquals('auto', $other->reject_reason);
    }

    private function loginAs(string $email, string $role, string $password): string
    {
        $response = $this->postJson('/api/v1/auth/login', compact('email', 'role', 'password'));
        $response->assertOk();
        return $response->json('data.token');
    }
}
