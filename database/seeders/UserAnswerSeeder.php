<?php

namespace Database\Seeders;

use App\Models\UserAnswer;
use Illuminate\Database\Seeder;

class UserAnswerSeeder extends Seeder
{
    public function run(): void
    {
        $answers = [
            ['id' => 1, 'user_id' => 1, 'question_id' => 1, 'religion_id' => 1, 'time_slot_id' => 1, 'answer' => true, 'answered_at' => '2026-07-01 06:15:00'],
            ['id' => 2, 'user_id' => 1, 'question_id' => 2, 'religion_id' => 1, 'time_slot_id' => 2, 'answer' => true, 'answered_at' => '2026-07-01 13:05:00'],
            ['id' => 3, 'user_id' => 1, 'question_id' => 3, 'religion_id' => 1, 'time_slot_id' => 3, 'answer' => false, 'answered_at' => '2026-07-01 18:30:00'],
            ['id' => 4, 'user_id' => 2, 'question_id' => 5, 'religion_id' => 2, 'time_slot_id' => 1, 'answer' => true, 'answered_at' => '2026-07-01 07:10:00'],
            ['id' => 5, 'user_id' => 2, 'question_id' => 8, 'religion_id' => 2, 'time_slot_id' => 4, 'answer' => true, 'answered_at' => '2026-07-01 21:15:00'],
            ['id' => 6, 'user_id' => 3, 'question_id' => 9, 'religion_id' => 3, 'time_slot_id' => 1, 'answer' => true, 'answered_at' => '2026-07-01 05:45:00'],
            ['id' => 7, 'user_id' => 3, 'question_id' => 10, 'religion_id' => 3, 'time_slot_id' => 2, 'answer' => true, 'answered_at' => '2026-07-01 14:10:00'],
            ['id' => 8, 'user_id' => 3, 'question_id' => 11, 'religion_id' => 3, 'time_slot_id' => 3, 'answer' => true, 'answered_at' => '2026-07-01 18:55:00'],
            ['id' => 9, 'user_id' => 4, 'question_id' => 13, 'religion_id' => 4, 'time_slot_id' => 1, 'answer' => false, 'answered_at' => '2026-07-01 06:40:00'],
            ['id' => 10, 'user_id' => 5, 'question_id' => 17, 'religion_id' => 5, 'time_slot_id' => 1, 'answer' => true, 'answered_at' => '2026-07-01 06:20:00'],
            ['id' => 11, 'user_id' => 5, 'question_id' => 18, 'religion_id' => 5, 'time_slot_id' => 2, 'answer' => true, 'answered_at' => '2026-07-01 14:00:00'],
            ['id' => 12, 'user_id' => 5, 'question_id' => 20, 'religion_id' => 5, 'time_slot_id' => 4, 'answer' => true, 'answered_at' => '2026-07-01 22:00:00'],
        ];

        $now = now();

        UserAnswer::insert(array_map(fn (array $answer) => [
            ...$answer,
            'created_at' => $now,
            'updated_at' => $now,
        ], $answers));
    }
}
