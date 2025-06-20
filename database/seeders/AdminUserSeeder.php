<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $admin = User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@al-insaf.com',
            'password' => Hash::make('admin123456'),
            'email_verified_at' => now(),
            'is_admin' => true,
            'is_moderator' => true,
        ]);

    }
}
