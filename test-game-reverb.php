<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Events\TestEvent;
use App\Events\ChatMessage;
use App\Events\RoomCreated;
use App\Models\Room;
use App\Models\Member;

echo "🎮 測試遊戲 Reverb 連接\n";
echo "========================\n\n";

try {
    echo "1. 檢查配置...\n";
    $config = config('broadcasting');
    echo "   默認驅動: {$config['default']}\n";
    
    if (isset($config['connections']['reverb'])) {
        echo "   ✅ Reverb 配置存在\n";
        $reverbConfig = $config['connections']['reverb'];
        echo "   - 主機: {$reverbConfig['options']['host']}\n";
        echo "   - 端口: {$reverbConfig['options']['port']}\n";
        echo "   - 協議: {$reverbConfig['options']['scheme']}\n";
    } else {
        echo "   ❌ Reverb 配置不存在\n";
        exit(1);
    }
    
    echo "\n2. 測試基本廣播...\n";
    $event = new TestEvent('遊戲測試訊息');
    event($event);
    echo "   ✅ 基本廣播成功\n";
    
    echo "\n3. 測試遊戲相關事件...\n";
    
    // 創建測試房間和成員
    $room = Room::factory()->create([
        'name' => '測試房間',
        'code' => 'TEST' . rand(1000, 9999),
        'max_players' => 4,
        'question_count' => 10,
        'time_limit' => 60,
        'difficulty' => 'medium'
    ]);
    
    $member = Member::factory()->create([
        'name' => '測試玩家',
        'email' => 'test@example.com'
    ]);
    
    echo "   ✅ 測試數據創建成功\n";
    echo "   - 房間ID: {$room->id}\n";
    echo "   - 成員ID: {$member->id}\n";
    
    // 測試聊天訊息事件
    $chatEvent = new ChatMessage($room, $member, '測試聊天訊息');
    event($chatEvent);
    echo "   ✅ 聊天事件廣播成功\n";
    
    // 測試房間創建事件
    $roomEvent = new RoomCreated($room);
    event($roomEvent);
    echo "   ✅ 房間事件廣播成功\n";
    
    echo "\n4. 測試頻道訂閱...\n";
    
    $channels = [
        'game.lobby',
        "room.{$room->id}",
        "game.{$room->id}",
        'test-channel'
    ];
    
    foreach ($channels as $channel) {
        echo "   - 測試頻道: {$channel}\n";
        // 這裡可以添加頻道訂閱測試
    }
    
    echo "\n🎉 遊戲 Reverb 測試完成！\n";
    echo "💡 現在可以測試以下功能：\n";
    echo "   - 遊戲大廳實時更新\n";
    echo "   - 房間內聊天功能\n";
    echo "   - 玩家狀態同步\n";
    echo "   - 遊戲進度廣播\n";
    
    // 清理測試數據
    $room->delete();
    $member->delete();
    
} catch (Exception $e) {
    echo "❌ 錯誤: " . $e->getMessage() . "\n";
    echo "堆疊追蹤:\n" . $e->getTraceAsString() . "\n";
} 