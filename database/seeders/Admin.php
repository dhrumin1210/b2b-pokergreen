<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class Admin extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = [
            'role_id' => config('site.roleIds.admin'),
            'name' => 'Pokergreen Admin',
            'email' => 'pokergreen-admin@yopmail.com',
            'password' => Hash::make('Password@123#'),
            'mobile' => '5555555555',
            'email_verified_at' => now(),
            'status' => config('site.user_status.active'),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $user = User::create($admin);
        $user->assignRole(config('site.roles.admin'));
    }
}