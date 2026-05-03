<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Creates the Filament admin login used after migrate:fresh --seed.
 *
 * Login URL: {APP_URL}/admin/login
 * Email: admin@bottles.local
 * Password: password
 *
 * Change the password in production; this seeder is for local/demo only.
 */
class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@bottles.local'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );
    }
}
