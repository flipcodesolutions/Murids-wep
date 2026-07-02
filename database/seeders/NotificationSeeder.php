<?php

namespace Database\Seeders;

use App\Models\Notification;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $notifications = [
            [
                'id' => 1,
                'user_id' => 1,
                'title' => 'Morning Prayer Reminder',
                'body' => "It's time for your morning prayer.",
                'type' => 'reminder',
                'sent_at' => '2026-07-01 06:00:00',
                'is_read' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'user_id' => 1,
                'title' => 'Evening Reflection',
                'body' => 'Have you thanked God today?',
                'type' => 'reminder',
                'sent_at' => '2026-07-01 18:00:00',
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'user_id' => 2,
                'title' => 'Morning Puja',
                'body' => 'Time for your morning Puja.',
                'type' => 'reminder',
                'sent_at' => '2026-07-01 06:30:00',
                'is_read' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'user_id' => 3,
                'title' => 'Fajr Reminder',
                'body' => 'Please offer your Fajr prayer.',
                'type' => 'reminder',
                'sent_at' => '2026-07-01 05:15:00',
                'is_read' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'user_id' => 3,
                'title' => 'Maghrib Reminder',
                'body' => 'Time for Maghrib prayer.',
                'type' => 'reminder',
                'sent_at' => '2026-07-01 18:20:00',
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'user_id' => 4,
                'title' => 'Nitnem Reminder',
                'body' => 'Begin your day with Nitnem.',
                'type' => 'reminder',
                'sent_at' => '2026-07-01 06:00:00',
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'user_id' => 5,
                'title' => 'Meditation Reminder',
                'body' => 'Take a few minutes to meditate.',
                'type' => 'reminder',
                'sent_at' => '2026-07-01 06:10:00',
                'is_read' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        Notification::insert($notifications);
    }
}
