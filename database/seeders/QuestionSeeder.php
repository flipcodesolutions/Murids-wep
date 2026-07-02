<?php

namespace Database\Seeders;

use App\Models\Question;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        $questions = [
            ['id' => 1, 'religion_id' => 1, 'time_slot_id' => 1, 'question' => 'Have you prayed this morning?', 'status' => true],
            ['id' => 2, 'religion_id' => 1, 'time_slot_id' => 2, 'question' => 'Have you read a Bible verse today?', 'status' => true],
            ['id' => 3, 'religion_id' => 1, 'time_slot_id' => 3, 'question' => 'Have you thanked God today?', 'status' => true],
            ['id' => 4, 'religion_id' => 1, 'time_slot_id' => 4, 'question' => 'Did you end your day with prayer?', 'status' => true],
            ['id' => 5, 'religion_id' => 2, 'time_slot_id' => 1, 'question' => 'Did you perform your morning Puja?', 'status' => true],
            ['id' => 6, 'religion_id' => 2, 'time_slot_id' => 2, 'question' => "Have you chanted God's name today?", 'status' => true],
            ['id' => 7, 'religion_id' => 2, 'time_slot_id' => 3, 'question' => 'Have you visited a temple today?', 'status' => true],
            ['id' => 8, 'religion_id' => 2, 'time_slot_id' => 4, 'question' => 'Did you perform evening Aarti?', 'status' => true],
            ['id' => 9, 'religion_id' => 3, 'time_slot_id' => 1, 'question' => 'Have you offered Fajr prayer?', 'status' => true],
            ['id' => 10, 'religion_id' => 3, 'time_slot_id' => 2, 'question' => 'Have you recited the Quran today?', 'status' => true],
            ['id' => 11, 'religion_id' => 3, 'time_slot_id' => 3, 'question' => 'Have you offered Maghrib prayer?', 'status' => true],
            ['id' => 12, 'religion_id' => 3, 'time_slot_id' => 4, 'question' => 'Have you offered Isha prayer?', 'status' => true],
            ['id' => 13, 'religion_id' => 4, 'time_slot_id' => 1, 'question' => 'Have you completed Nitnem this morning?', 'status' => true],
            ['id' => 14, 'religion_id' => 4, 'time_slot_id' => 2, 'question' => 'Did you remember Waheguru today?', 'status' => true],
            ['id' => 15, 'religion_id' => 4, 'time_slot_id' => 3, 'question' => 'Have you done Seva today?', 'status' => true],
            ['id' => 16, 'religion_id' => 4, 'time_slot_id' => 4, 'question' => 'Did you read Gurbani before sleep?', 'status' => true],
            ['id' => 17, 'religion_id' => 5, 'time_slot_id' => 1, 'question' => 'Did you meditate this morning?', 'status' => true],
            ['id' => 18, 'religion_id' => 5, 'time_slot_id' => 2, 'question' => 'Have you practiced mindfulness today?', 'status' => true],
            ['id' => 19, 'religion_id' => 5, 'time_slot_id' => 3, 'question' => 'Did you perform an act of compassion today?', 'status' => true],
            ['id' => 20, 'religion_id' => 5, 'time_slot_id' => 4, 'question' => 'Did you reflect peacefully before sleep?', 'status' => true],
        ];

        $now = now();

        Question::insert(array_map(fn (array $question) => [
            ...$question,
            'created_at' => $now,
            'updated_at' => $now,
        ], $questions));
    }
}
