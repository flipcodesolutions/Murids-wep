<?php

namespace Database\Seeders;

use App\Models\UserProfile;
use Illuminate\Database\Seeder;

class UserProfileSeeder extends Seeder
{
    public function run(): void
    {
        $profiles = [
            [
                'id' => 1,
                'user_id' => 1,
                'religion_id' => 1,
                'notification_enabled' => true,
                'device_token' => 'device_token_101',
                'timezone' => 'Asia/Kolkata',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'user_id' => 2,
                'religion_id' => 2,
                'notification_enabled' => true,
                'device_token' => 'device_token_102',
                'timezone' => 'Asia/Kolkata',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'user_id' => 3,
                'religion_id' => 3,
                'notification_enabled' => true,
                'device_token' => 'device_token_103',
                'timezone' => 'Asia/Dubai',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'user_id' => 4,
                'religion_id' => 4,
                'notification_enabled' => false,
                'device_token' => 'device_token_104',
                'timezone' => 'Asia/Kolkata',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'user_id' => 5,
                'religion_id' => 5,
                'notification_enabled' => true,
                'device_token' => 'device_token_105',
                'timezone' => 'Asia/Kathmandu',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        UserProfile::insert($profiles);
    }
}
