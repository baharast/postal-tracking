<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\User\Models\Role;
use Modules\User\Models\User;
use Modules\Package\Models\Package;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Roles with passwords (requirement: password inside role/user_types)
        $roles = [
            ['name' => 'admin',   'password' => Hash::make('Admin@123')],
            ['name' => 'sender',  'password' => Hash::make('Sender@123')],
            ['name' => 'carrier', 'password' => Hash::make('Carrier@123')],
        ];
        foreach ($roles as $r) {
            Role::updateOrCreate(['name' => $r['name']], ['password' => $r['password']]);
        }

        // One sender
        $sender = User::updateOrCreate(
            ['email' => 'sender1@postal.local'],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Sender 1',
                'role_id' => Role::where('name', 'sender')->value('id'),
            ]
        );

        // Two carriers
        $carrierRoleId = Role::where('name', 'carrier')->value('id');
        $carrier1 = User::updateOrCreate(
            ['email' => 'carrier1@postal.local'],
            ['id' => (string)Str::uuid(), 'name' => 'Carrier 1', 'role_id' => $carrierRoleId]
        );
        $carrier2 = User::updateOrCreate(
            ['email' => 'carrier2@postal.local'],
            ['id' => (string)Str::uuid(), 'name' => 'Carrier 2', 'role_id' => $carrierRoleId]
        );

        // One package for sender (status=created)
        Package::updateOrCreate(
            ['tracking_code' => 'seed-track-1'],
            [
                'id' => (string) Str::uuid(),
                'sender_id' => $sender->id,
                'carrier_id' => null,
                'tracking_code' => (string) Str::uuid(),
                'status' => 'created',
                'origin_city' => 'Tehran',
                'origin_address' => 'Valiasr St.',
                'destination_city' => 'Karaj',
                'destination_address' => 'Main Blvd.',
                'weight_grams' => 1200,
            ]
        );
    }
}
