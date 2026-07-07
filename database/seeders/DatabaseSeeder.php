<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
           ReligionSeeder::class,
           TimeSlotSeeder::class,
           QuestionSeeder::class,
            UserProfileSeeder::class,
            UserAnswerSeeder::class,
            NotificationSeeder::class,
        ]);
    }
}
