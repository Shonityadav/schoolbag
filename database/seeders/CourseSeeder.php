<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\StudentClass;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            ['title' => 'English',     'icon' => '📖', 'color' => '#6C63FF'],
            ['title' => 'Mathematics', 'icon' => '🔢', 'color' => '#FF6584'],
            ['title' => 'Science',     'icon' => '🔬', 'color' => '#00D4AA'],
            ['title' => 'Hindi',       'icon' => '🅗',  'color' => '#FFD700'],
            ['title' => 'EVS',         'icon' => '🌍', 'color' => '#34D399'],
            ['title' => 'GK',          'icon' => '🌐', 'color' => '#60A5FA'],
        ];

        $classes = StudentClass::all();

        foreach ($classes as $cls) {
            foreach ($subjects as $i => $sub) {
                Course::firstOrCreate(
                    ['class_id' => $cls->id, 'title' => $sub['title']],
                    array_merge($sub, ['class_id' => $cls->id, 'order' => $i])
                );
            }
        }
    }
}
