<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            StudentClassSeeder::class,
            CourseSeeder::class,
            ChapterSeeder::class,
            BadgeSeeder::class,   // also seeds AutoRules
        ]);
    }
}

