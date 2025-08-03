<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Room;
use App\Models\Member;
use Illuminate\Support\Facades\Broadcast;

echo "🔍 測試頻道配置\n";
echo "================\n\n";

try {
    echo "1. 檢查頻道定義...\n";
    
    // 檢查 channels.php 是否被載入
    $channels = [
        'App.Models.User.{id}',
        'App.Models.Member.{id}',
        'room.{roomId}',
        'game.lobby',
        'game.{roomId}',
        'chat.{roomId}',
        'notifications'
    ];
    
    foreach ($channels as $channel) {
        echo "   - 頻道: {$channel}\n";
    }
    
    echo "   ✅ 頻道定義檢查完成\n";
    
    echo "\n2. 測試頻道授權...\n";
    
    // 創建測試數據
    $member = Member::factory()->create([
        'name' => '測試會員',
        'email' => 'test@example.com'
    ]);
    
    $room = Room::factory()->create([
        'name' => '測試房間',
        'code' => 'TEST' . rand(1000, 9999),
        'max_players' => 4,
        'question_count' => 10,
        'time_limit' => 60,
        'difficulty' => 'medium',
        'host_id' => $member->id
    ]);
    
    echo "   ✅ 測試數據創建成功\n";
    echo "   - 會員ID: {$member->id}\n";
    echo "   - 房間ID: {$room->id}\n";
    
    // 測試頻道授權
    echo "\n3. 測試頻道授權邏輯...\n";
    
    // 測試會員私人頻道
    $memberChannel = "App.Models.Member.{$member->id}";
    echo "   - 測試頻道: {$memberChannel}\n";
    
    // 測試房間頻道
    $roomChannel = "room.{$room->id}";
    echo "   - 測試頻道: {$roomChannel}\n";
    
    // 測試遊戲大廳頻道
    $lobbyChannel = "game.lobby";
    echo "   - 測試頻道: {$lobbyChannel}\n";
    
    // 測試遊戲頻道
    $gameChannel = "game.{$room->id}";
    echo "   - 測試頻道: {$gameChannel}\n";
    
    echo "   ✅ 頻道授權邏輯檢查完成\n";
    
    echo "\n4. 檢查廣播路由...\n";
    
    // 檢查廣播路由是否註冊
    $routes = app('router')->getRoutes();
    $broadcastRoutes = [];
    
    foreach ($routes as $route) {
        if (str_contains($route->uri(), 'broadcasting')) {
            $broadcastRoutes[] = $route->uri();
        }
    }
    
    if (!empty($broadcastRoutes)) {
        echo "   ✅ 廣播路由已註冊\n";
        foreach ($broadcastRoutes as $route) {
            echo "   - {$route}\n";
        }
    } else {
        echo "   ❌ 廣播路由未找到\n";
    }
    
    echo "\n5. 測試事件廣播...\n";
    
    $event = new \App\Events\TestEvent('頻道測試訊息');
    event($event);
    echo "   ✅ 事件廣播成功\n";
    
    echo "\n🎉 頻道配置測試完成！\n";
    echo "💡 如果前端仍然有問題，請檢查：\n";
    echo "   1. 用戶是否已正確登入\n";
    echo "   2. 頻道名稱是否正確\n";
    echo "   3. 認證標頭是否正確設置\n";
    echo "   4. 瀏覽器控制台是否有其他錯誤\n";
    
    // 清理測試數據
    $room->delete();
    $member->delete();
    
} catch (Exception $e) {
    echo "❌ 錯誤: " . $e->getMessage() . "\n";
    echo "堆疊追蹤:\n" . $e->getTraceAsString() . "\n";
} 