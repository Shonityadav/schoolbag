<?php

namespace Database\Seeders;

use App\Models\StudentClass;
use Illuminate\Database\Seeder;

class StudentClassSeeder extends Seeder
{
    public function run(): void
    {
        $classes = [
            ['name' => 'Class 1', 'level' => 1, 'icon' => '🌱', 'color' => '#34D399'],
            ['name' => 'Class 2', 'level' => 2, 'icon' => '🌿', 'color' => '#60A5FA'],
            ['name' => 'Class 3', 'level' => 3, 'icon' => '🌳', 'color' => '#A78BFA'],
            ['name' => 'Class 4', 'level' => 4, 'icon' => '🌲', 'color' => '#F472B6'],
            ['name' => 'Class 5', 'level' => 5, 'icon' => '🏔️', 'color' => '#FB923C'],
        ];

        foreach ($classes as $class) {
            StudentClass::firstOrCreate(['level' => $class['level']], $class);
        }
    }
}
