<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['candidate', 'employer', 'admin'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        $admin = User::firstOrCreate(
            ['email' => 'admin@pwd-e.local'],
            [
                'name' => 'PWD-E Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'active',
                'verified_at' => now(),
            ]
        );

        $admin->assignRole('admin');
    }
}
