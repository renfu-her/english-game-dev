<?php

namespace Database\Seeders;

use App\Models\GameRecord;
use App\Models\Member;
use App\Models\Question;
use App\Models\Room;
use Illuminate\Database\Seeder;

class GameRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 確保有會員、房間和題目
        $members = Member::all();
        $rooms = Room::all();
        $questions = Question::all();

        if ($members->isEmpty() || $rooms->isEmpty() || $questions->isEmpty()) {
            $this->command->info('需要先建立會員、房間和題目資料');
            return;
        }

        // 建立 50 個遊戲記錄
        GameRecord::factory(50)->create([
            'room_id' => fn() => $rooms->random(),
            'user_id' => fn() => $members->random(),
            'question_id' => fn() => $questions->random(),
        ]);

        $this->command->info('已建立 50 個遊戲記錄');
    }
}
