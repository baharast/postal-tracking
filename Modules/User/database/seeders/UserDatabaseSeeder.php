<?php

namespace Modules\User\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\User\Models\Role;
use Illuminate\Support\Str;

class UserDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole   = Role::create([
            'name' => 'admin',
            'password' => bcrypt('Admin@123')
        ]);

        Role::create([
            'name' => 'sender',
            'password' => bcrypt('Sender@123')
        ]);

        Role::create([
            'name' => 'carrier',
            'password' => bcrypt('Carrier@123')
        ]);

        User::create([
            'id'        => Str::uuid(),
            'name'      => 'Admin User',
            'email'     => 'admin@postal.local',
            'role_id'   => $adminRole->id
        ]);
    }
}
