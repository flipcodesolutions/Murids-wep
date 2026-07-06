<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'id' => 1,
                'name' => 'Admin User',
                'email' => 'admin@murids.com',
                'provider' => 'google',
                'provider_id' => 'google_101',
                'email_verified_at' => '2026-07-03 14:00:00',
                'password' => bcrypt('123456'),
                'remember_token' => null,
                'created_at' => '2026-07-01 00:00:00',
                'updated_at' => '2026-07-01 00:00:00',
            ],
            [
                'id' => 2,
                'name' => 'John Smith',
                'email' => 'john@example.com',
                'provider' => 'google',
                'provider_id' => 'google_101',
                'email_verified_at' => '2026-07-01 08:00:00',
                'password' => null,
                'remember_token' => null,
                'created_at' => '2026-07-01 00:00:00',
                'updated_at' => '2026-07-01 00:00:00',
            ],
            [
                'id' => 3,
                'name' => 'Ravi Patel',
                'email' => 'ravi@example.com',
                'provider' => 'google',
                'provider_id' => 'google_102',
                'email_verified_at' => '2026-07-01 08:10:00',
                'password' => null,
                'remember_token' => null,
                'created_at' => '2026-07-01 00:00:00',
                'updated_at' => '2026-07-01 00:00:00',
            ],
            [
                'id' => 4,
                'name' => 'Ahmed Khan',
                'email' => 'ahmed@example.com',
                'provider' => 'google',
                'provider_id' => 'google_103',
                'email_verified_at' => '2026-07-01 08:15:00',
                'password' => null,
                'remember_token' => null,
                'created_at' => '2026-07-01 00:00:00',
                'updated_at' => '2026-07-01 00:00:00',
            ],
            [
                'id' => 5,
                'name' => 'Gurpreet Singh',
                'email' => 'gurpreet@example.com',
                'provider' => 'apple',
                'provider_id' => 'apple_201',
                'email_verified_at' => '2026-07-01 08:20:00',
                'password' => null,
                'remember_token' => null,
                'created_at' => '2026-07-01 00:00:00',
                'updated_at' => '2026-07-01 00:00:00',
            ],
            [
                'id' => 6,
                'name' => 'Tenzin Lama',
                'email' => 'tenzin@example.com',
                'provider' => 'google',
                'provider_id' => 'google_104',
                'email_verified_at' => '2026-07-01 08:25:00',
                'password' => null,
                'remember_token' => null,
                'created_at' => '2026-07-01 00:00:00',
                'updated_at' => '2026-07-01 00:00:00',
            ],
        ];

        User::insert($users);
    }
}
