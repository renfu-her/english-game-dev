<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Events\TestEvent;
use Illuminate\Support\Facades\Broadcast;

echo "🔌 測試 Laravel Reverb 廣播功能\n";
echo "==============================\n\n";

// 1. 檢查配置
echo "📋 檢查廣播配置...\n";
$config = config('broadcasting');
echo "默認驅動: " . ($config['default'] ?? 'unknown') . "\n";

if (isset($config['connections']['reverb'])) {
    echo "✅ Reverb 配置存在\n";
    $reverbConfig = $config['connections']['reverb'];
    echo "   - 主機: {$reverbConfig['options']['host']}\n";
    echo "   - 端口: {$reverbConfig['options']['port']}\n";
    echo "   - 協議: {$reverbConfig['options']['scheme']}\n";
} else {
    echo "❌ Reverb 配置不存在\n";
    exit(1);
}

echo "\n";

// 2. 測試廣播
echo "📡 測試廣播功能...\n";

try {
    $event = new TestEvent('命令行測試訊息');
    echo "✅ 事件創建成功\n";
    
    event($event);
    echo "✅ 事件廣播成功\n";
    
    echo "廣播數據:\n";
    echo "- 頻道: test-channel\n";
    echo "- 事件名稱: test-event\n";
    echo "- 訊息: 命令行測試訊息\n";
    echo "- 時間戳: " . now()->toISOString() . "\n";
    
} catch (Exception $e) {
    echo "❌ 廣播失敗: " . $e->getMessage() . "\n";
    echo "錯誤詳情:\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n";

// 3. 測試頻道廣播
echo "🔌 測試頻道廣播...\n";

try {
    $data = [
        'message' => '頻道測試訊息',
        'timestamp' => now()->toISOString(),
        'test' => true
    ];
    
    Broadcast::channel('test-channel', $data);
    echo "✅ 頻道廣播成功\n";
    
} catch (Exception $e) {
    echo "❌ 頻道廣播失敗: " . $e->getMessage() . "\n";
    echo "錯誤詳情:\n";
    echo $e->getTraceAsString() . "\n";
}

echo "\n";
echo "✅ 廣播測試完成！\n";
echo "💡 提示: 如果廣播成功但前端沒有收到，請檢查:\n";
echo "   1. Reverb 服務是否正在運行\n";
echo "   2. 前端 WebSocket 連接是否正常\n";
echo "   3. 頻道訂閱是否正確\n"; 