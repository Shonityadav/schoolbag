<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuestionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            'MCQ',
            'True/False',
            'Fill in the blanks',
            'Hard Word'
        ];

        foreach ($types as $type) {
            \App\Models\QuestionType::firstOrCreate(['type' => $type]);
        }
    }
}
