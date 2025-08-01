<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\Room;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $members = Member::all();
        
        if ($members->isEmpty()) {
            $this->command->info('需要先建立會員資料');
            return;
        }

        // 建立 5 個測試房間
        $roomNames = [
            '英語學習小組 A',
            '商務英語練習室',
            '旅遊英語交流',
            '校園英語角',
            '醫療英語專班',
        ];

        $categories = \App\Models\Category::all();
        
        if ($categories->isEmpty()) {
            $this->command->info('需要先建立分類資料');
            return;
        }

        foreach ($roomNames as $name) {
            Room::firstOrCreate(
                ['name' => $name],
                [
                    'code' => strtoupper(Str::random(6)),
                    'host_id' => $members->random()->id,
                    'category_id' => $categories->random()->id,
                    'max_players' => rand(4, 8),
                    'question_count' => rand(10, 20),
                    'difficulty' => ['easy', 'medium', 'hard'][rand(0, 2)],
                    'time_limit' => rand(20, 45),
                    'allow_skip' => true,
                    'show_explanation' => true,
                    'is_private' => false,
                    'status' => 'waiting',
                ]
            );
        }

        $this->command->info('已建立 5 個測試房間');
    }
}
