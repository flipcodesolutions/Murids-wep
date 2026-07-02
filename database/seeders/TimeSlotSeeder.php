<?php

namespace Database\Seeders;

use App\Models\TimeSlot;
use Illuminate\Database\Seeder;

class TimeSlotSeeder extends Seeder
{
    public function run(): void
    {
        $timeSlots = [
            [
                'id' => 1,
                'name' => 'Morning',
                'start_time' => '05:00:00',
                'end_time' => '11:59:59',
                'notification_time' => '06:00:00',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Afternoon',
                'start_time' => '12:00:00',
                'end_time' => '16:59:59',
                'notification_time' => '01:00:00',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Evening',
                'start_time' => '17:00:00',
                'end_time' => '20:00:00',
                'notification_time' => '06:00:00',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'Night',
                'start_time' => '20:01:00',
                'end_time' => '23:59:59',
                'notification_time' => '09:00:00',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        TimeSlot::insert($timeSlots);
    }
}
