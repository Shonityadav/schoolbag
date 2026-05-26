<?php

namespace Database\Seeders;

use App\Models\Chapter;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Database\Seeder;

class ChapterSeeder extends Seeder
{
    public function run(): void
    {
        // Seed for English Class 4 (Course ID 19) where the user is currently previewing
        $englishClass1 = Course::find(19);

        if (!$englishClass1) return;

        $chaptersData = [
            [
                'title' => 'The Alphabet', 'order' => 0, 'unlock_threshold' => 70, 'xp_reward' => 50,
                'lessons' => [
                    ['title' => 'Letters A to M', 'type' => 'reading',   'duration_minutes' => 10, 'xp_reward' => 20,
                     'content' => "Welcome to English!\n\nLet's learn the alphabet.\n\nA is for Apple 🍎\nB is for Ball ⚽\nC is for Cat 🐱\nD is for Dog 🐶\nE is for Elephant 🐘\nF is for Fish 🐟\nG is for Goat 🐐\nH is for Hat 🎩\nI is for Ice cream 🍦\nJ is for Jar 🫙\nK is for Kite 🪁\nL is for Lion 🦁\nM is for Moon 🌙\n\nGreat job! You learned 13 letters!"],
                    ['title' => 'Letters N to Z', 'type' => 'reading',   'duration_minutes' => 10, 'xp_reward' => 20,
                     'content' => "Let's continue!\n\nN is for Nest 🪺\nO is for Orange 🍊\nP is for Parrot 🦜\nQ is for Queen 👑\nR is for Rainbow 🌈\nS is for Sun ☀️\nT is for Tiger 🐯\nU is for Umbrella ☂️\nV is for Van 🚐\nW is for Water 💧\nX is for Xylophone 🎵\nY is for Yak 🐄\nZ is for Zebra 🦓\n\nYou now know all 26 letters! Amazing!"],
                    ['title' => 'Alphabet Flashcards', 'type' => 'flashcard', 'duration_minutes' => 8, 'xp_reward' => 15,
                     'content' => "Practice time! Say each letter out loud:\n\nA B C D E F G H I J K L M\nN O P Q R S T U V W X Y Z\n\nNow say them backwards! Z Y X W V U T S R Q P O N M L K J I H G F E D C B A\n\nExcellent practice!"],
                ],
                'quiz' => [
                    'title' => 'Alphabet Quiz',
                    'questions' => [
                        ['question' => 'What comes after the letter D?', 'option_a' => 'E', 'option_b' => 'F', 'option_c' => 'B', 'option_d' => 'G', 'correct_option' => 'a'],
                        ['question' => 'Which letter does 🍎 Apple start with?', 'option_a' => 'P', 'option_b' => 'A', 'option_c' => 'M', 'option_d' => 'E', 'correct_option' => 'b'],
                        ['question' => 'How many letters are in the alphabet?', 'option_a' => '24', 'option_b' => '28', 'option_c' => '26', 'option_d' => '22', 'correct_option' => 'c'],
                        ['question' => 'Which letter comes before Z?', 'option_a' => 'X', 'option_b' => 'Y', 'option_c' => 'W', 'option_d' => 'V', 'correct_option' => 'b'],
                        ['question' => 'Z is for ___?', 'option_a' => 'Zoo', 'option_b' => 'Zebra', 'option_c' => 'Both', 'option_d' => 'Zero', 'correct_option' => 'c'],
                    ]
                ]
            ],
            [
                'title' => 'Simple Words', 'order' => 1, 'unlock_threshold' => 70, 'xp_reward' => 60,
                'lessons' => [
                    ['title' => 'Three Letter Words', 'type' => 'reading', 'duration_minutes' => 12, 'xp_reward' => 20,
                     'content' => "Three-letter words are easy and fun!\n\nCAT — the furry animal 🐱\nDOG — man's best friend 🐶\nBED — where you sleep 🛏️\nCUP — for drinking ☕\nHEN — a female bird 🐔\nPIG — pink animal 🐷\nBUS — big vehicle 🚌\nSUN — shines in the sky ☀️\n\nPractice writing each word three times!"],
                    ['title' => 'Read & Say Aloud', 'type' => 'dictation', 'duration_minutes' => 10, 'xp_reward' => 25,
                     'content' => "Say each word out loud clearly:\n\n1. CAT\n2. DOG\n3. BED\n4. CUP\n5. HEN\n\nNow make a sentence with each word:\n- The CAT is on the mat.\n- The DOG runs fast.\n- I sleep on my BED.\n- I drink from a CUP.\n- The HEN lays eggs.\n\nWonderful work!"],
                ],
                'quiz' => [
                    'title' => 'Simple Words Quiz',
                    'questions' => [
                        ['question' => 'Which is a three-letter word?', 'option_a' => 'Elephant', 'option_b' => 'Cat', 'option_c' => 'School', 'option_d' => 'Apple', 'correct_option' => 'b'],
                        ['question' => 'What word means a furry pet that says "Meow"?', 'option_a' => 'Dog', 'option_b' => 'Hen', 'option_c' => 'Cat', 'option_d' => 'Pig', 'correct_option' => 'c'],
                        ['question' => 'Complete: The ___ is shining. (round, bright, daytime)', 'option_a' => 'Moon', 'option_b' => 'Sun', 'option_c' => 'Star', 'option_d' => 'Rain', 'correct_option' => 'b'],
                        ['question' => 'Which vehicle carries many people?', 'option_a' => 'Cup', 'option_b' => 'Bed', 'option_c' => 'Bus', 'option_d' => 'Hen', 'correct_option' => 'c'],
                        ['question' => 'I drink water from a ___?', 'option_a' => 'Bed', 'option_b' => 'Dog', 'option_c' => 'Cup', 'option_d' => 'Sun', 'correct_option' => 'c'],
                    ]
                ]
            ],
            [
                'title' => 'Action Words', 'order' => 2, 'unlock_threshold' => 70, 'xp_reward' => 60,
                'lessons' => [
                    ['title' => 'Common Actions', 'type' => 'reading', 'duration_minutes' => 10, 'xp_reward' => 20,
                     'content' => "Let's learn some action words!\n\nRUN 🏃\nJUMP 🦘\nSWIM 🏊\nPLAY 🧸\nEAT 🍎\nSLEEP 😴\n\nThese words describe things we do!"],
                ],
                'quiz' => [
                    'title' => 'Action Quiz',
                    'questions' => [
                        ['question' => 'What do you do in a pool?', 'option_a' => 'Sleep', 'option_b' => 'Run', 'option_c' => 'Swim', 'option_d' => 'Eat', 'correct_option' => 'c'],
                        ['question' => 'What do you do with an apple?', 'option_a' => 'Play', 'option_b' => 'Eat', 'option_c' => 'Swim', 'option_d' => 'Jump', 'correct_option' => 'b'],
                    ]
                ]
            ],
            [
                'title' => 'Colors & Shapes', 'order' => 3, 'unlock_threshold' => 70, 'xp_reward' => 60,
                'lessons' => [
                    ['title' => 'Primary Colors', 'type' => 'reading', 'duration_minutes' => 10, 'xp_reward' => 20,
                     'content' => "RED 🔴\nBLUE 🔵\nYELLOW 🟡\n\nShapes:\nCIRCLE ⭕\nSQUARE 🟥\nTRIANGLE 🔺"],
                ],
                'quiz' => [
                    'title' => 'Colors Quiz',
                    'questions' => [
                        ['question' => 'Which is a color?', 'option_a' => 'Circle', 'option_b' => 'Red', 'option_c' => 'Square', 'option_d' => 'Dog', 'correct_option' => 'b'],
                        ['question' => 'Which shape is round?', 'option_a' => 'Triangle', 'option_b' => 'Square', 'option_c' => 'Rectangle', 'option_d' => 'Circle', 'correct_option' => 'd'],
                    ]
                ]
            ],
            [
                'title' => 'Basic Numbers', 'order' => 4, 'unlock_threshold' => 70, 'xp_reward' => 60,
                'lessons' => [
                    ['title' => 'Numbers 1 to 5', 'type' => 'reading', 'duration_minutes' => 10, 'xp_reward' => 20,
                     'content' => "One 1️⃣\nTwo 2️⃣\nThree 3️⃣\nFour 4️⃣\nFive 5️⃣"],
                ],
                'quiz' => [
                    'title' => 'Numbers Quiz',
                    'questions' => [
                        ['question' => 'What comes after One?', 'option_a' => 'Three', 'option_b' => 'Two', 'option_c' => 'Four', 'option_d' => 'Five', 'correct_option' => 'b'],
                        ['question' => 'What comes before Five?', 'option_a' => 'Two', 'option_b' => 'Three', 'option_c' => 'Four', 'option_d' => 'One', 'correct_option' => 'c'],
                    ]
                ]
            ],
        ];

        foreach ($chaptersData as $chData) {
            $chapter = Chapter::firstOrCreate(
                ['course_id' => $englishClass1->id, 'title' => $chData['title']],
                ['course_id' => $englishClass1->id, 'order' => $chData['order'],
                 'unlock_threshold' => $chData['unlock_threshold'], 'xp_reward' => $chData['xp_reward']]
            );

            foreach ($chData['lessons'] as $li => $lesData) {
                Lesson::firstOrCreate(
                    ['chapter_id' => $chapter->id, 'title' => $lesData['title']],
                    array_merge($lesData, ['chapter_id' => $chapter->id, 'order' => $li])
                );
            }

            if (isset($chData['quiz']) && !$chapter->quiz) {
                $quiz = Quiz::create([
                    'chapter_id'         => $chapter->id,
                    'title'              => $chData['quiz']['title'],
                    'time_limit_minutes' => 10,
                    'xp_reward'          => 30,
                ]);
                foreach ($chData['quiz']['questions'] as $qi => $qData) {
                    QuizQuestion::create(array_merge($qData, ['quiz_id' => $quiz->id, 'order' => $qi]));
                }
            }
        }
    }
}
