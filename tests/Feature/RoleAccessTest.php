<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Modules\User\Models\User;
use Modules\User\Database\Seeders\UserDatabaseSeeder;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
        $this->seed(UserDatabaseSeeder::class);
    }

    public function test_sender_can_create_package_but_carrier_cannot(): void
    {
        $sender = User::whereHas('role', fn($q)=>$q->where('name','sender'))->first();
        $carrier = User::whereHas('role', fn($q)=>$q->where('name','carrier'))->first();

        // sender create
        $this->actingAs($sender)->postJson('/api/v1/packages', [
            'origin_city'           => 'Tehran',
            'origin_address'        => 'Street A',
            'destination_city'      => 'Karaj',
            'destination_address'   => 'Street B',
            'weight_grams'          => 500,
        ])->assertCreated();

        // carrier create -> forbidden
        $this->actingAs($carrier)->postJson('/api/v1/packages', [
            'origin_city'           => 'Tehran',
            'origin_address'        => 'Street A',
            'destination_city'      => 'Karaj',
            'destination_address'   => 'Street B',
            'weight_grams'          => 500,
        ])->assertForbidden();
    }
}
