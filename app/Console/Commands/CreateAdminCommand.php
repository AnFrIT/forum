<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminCommand extends Command
{
    protected $signature = 'admin:create {--email=admin@al-insaf.com} {--name=Admin} {--username=admin} {--password=admin123456}';

    protected $description = 'Create an administrator user quickly';

    public function handle()
    {
        $this->info('🚀 Creating Administrator User for AL-INSAF');
        $this->info('=========================================');

        $email = $this->option('email');
        $name = $this->option('name');
        $username = $this->option('username');
        $password = $this->option('password');

        try {
            // Check if user already exists by email
            $existingUser = User::where('email', $email)->first();

            if ($existingUser) {
                $this->info('👤 User already exists, promoting to admin...');

                $existingUser->update([
                    'is_admin' => true,
                    'is_moderator' => true,
                    'email_verified_at' => now(),
                    'username' => $existingUser->username ?: $username, // Keep existing or set new
                ]);

                $user = $existingUser;
                $this->info('✅ User promoted to administrator!');
            } else {
                $this->info('👤 Creating new admin user...');

                // Check if username is taken
                if (User::where('username', $username)->exists()) {
                    $username = $username.'_'.rand(100, 999);
                    $this->warn("Username taken, using: {$username}");
                }

                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'username' => $username,
                    'password' => Hash::make($password),
                    'email_verified_at' => now(),
                    'is_admin' => true,
                    'is_moderator' => true,
                    'preferred_language' => 'ru',
                ]);

                $this->info('✅ Admin user created successfully!');
            }

            // Display results
            $this->newLine();
            $this->info('📋 Admin Account Details:');
            $this->table(['Field', 'Value'], [
                ['ID', $user->id],
                ['Name', $user->name],
                ['Username', $user->username],
                ['Email', $user->email],
                ['Password', $password],
                ['Is Admin', $user->is_admin ? 'YES' : 'NO'],
                ['Created', $user->created_at->format('Y-m-d H:i:s')],
            ]);

            $this->newLine();
            $this->info('🔗 Access URLs:');
            $this->line('Login: http://localhost:8000/login');
            $this->line('Admin Panel: http://localhost:8000/admin');

            $this->newLine();
            $this->info('🎉 Setup completed successfully!');

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Error: '.$e->getMessage());
            $this->newLine();
            $this->error('💡 Try running: php artisan migrate first');

            return 1;
        }
    }
}
