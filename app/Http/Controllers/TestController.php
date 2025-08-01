<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Member;
use App\Models\Room;
use App\Models\GameRecord;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index()
    {
        $data = [
            'categories' => Category::count(),
            'members' => Member::count(),
            'rooms' => Room::count(),
            'gameRecords' => GameRecord::count(),
            'sampleCategories' => Category::take(3)->get(),
            'sampleMembers' => Member::take(3)->get(),
        ];
        
        return response()->json($data);
    }
    
    public function createTestData()
    {
        // 創建測試房間
        $room = Room::create([
            'name' => '測試房間',
            'code' => 'TEST' . rand(100, 999),
            'host_id' => Member::first()->id,
            'category_id' => Category::first()->id,
            'max_players' => 4,
            'question_count' => 10,
            'difficulty' => 'medium',
            'time_limit' => 60,
            'allow_skip' => true,
            'show_explanation' => true,
            'status' => 'waiting',
        ]);
        
        return response()->json([
            'message' => '測試數據創建成功',
            'room_id' => $room->id,
            'room_name' => $room->name,
        ]);
    }
} 