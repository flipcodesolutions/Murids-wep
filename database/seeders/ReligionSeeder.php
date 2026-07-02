<?php

namespace Database\Seeders;

use App\Models\Religion;
use Illuminate\Database\Seeder;

class ReligionSeeder extends Seeder
{
    public function run(): void
    {
        $religions = [
            [
                'id' => 1,
                'name' => 'Christianity',
                'description' => 'Christian daily prayer reminders and spiritual questions.',
                'image' => 'christianity.png',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Hinduism',
                'description' => 'Hindu daily spiritual practice questions.',
                'image' => 'hinduism.png',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Islam',
                'description' => 'Islamic prayer and worship reminders.',
                'image' => 'islam.png',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'Sikhism',
                'description' => 'Sikh daily Nitnem and seva reminders.',
                'image' => 'sikhism.png',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'name' => 'Buddhism',
                'description' => 'Daily mindfulness and meditation questions.',
                'image' => 'buddhism.png',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        Religion::insert($religions);
    }
}
