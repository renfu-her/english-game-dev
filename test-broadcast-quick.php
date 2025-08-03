<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Events\TestEvent;
use Illuminate\Support\Facades\Broadcast;

echo "🔌 快速廣播測試\n";
echo "================\n\n";

try {
    echo "1. 檢查配置...\n";
    $config = config('broadcasting');
    echo "   默認驅動: {$config['default']}\n";
    
    echo "2. 創建事件...\n";
    $event = new TestEvent('快速測試訊息');
    echo "   事件創建成功\n";
    
    echo "3. 廣播事件...\n";
    Broadcast::dispatch($event);
    echo "   ✅ 廣播成功！\n";
    
    echo "\n🎉 廣播測試完成！\n";
    echo "💡 如果前端沒有收到訊息，請檢查：\n";
    echo "   - 前端 WebSocket 連接是否正確\n";
    echo "   - 頻道訂閱是否正確\n";
    echo "   - Reverb 服務器是否正在運行\n";
    
} catch (Exception $e) {
    echo "❌ 錯誤: " . $e->getMessage() . "\n";
    echo "堆疊追蹤:\n" . $e->getTraceAsString() . "\n";
} 