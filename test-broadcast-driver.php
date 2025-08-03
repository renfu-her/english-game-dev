<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Broadcast;

echo "🔍 檢查廣播驅動\n";
echo "================\n\n";

try {
    // 檢查默認驅動
    $defaultDriver = config('broadcasting.default');
    echo "默認驅動: {$defaultDriver}\n";
    
    // 獲取廣播驅動實例
    $driver = Broadcast::driver();
    echo "驅動類別: " . get_class($driver) . "\n";
    
    // 檢查驅動類型
    if ($driver instanceof \Illuminate\Broadcasting\Broadcasters\ReverbBroadcaster) {
        echo "✅ 正確使用 ReverbBroadcaster\n";
    } elseif ($driver instanceof \Illuminate\Broadcasting\Broadcasters\PusherBroadcaster) {
        echo "❌ 錯誤使用 PusherBroadcaster\n";
    } else {
        echo "⚠️  使用其他驅動: " . get_class($driver) . "\n";
    }
    
    // 檢查配置
    $config = config('broadcasting.connections.reverb');
    if ($config) {
        echo "✅ Reverb 配置存在\n";
        echo "   - 主機: {$config['options']['host']}\n";
        echo "   - 端口: {$config['options']['port']}\n";
        echo "   - 協議: {$config['options']['scheme']}\n";
    } else {
        echo "❌ Reverb 配置不存在\n";
    }
    
} catch (Exception $e) {
    echo "❌ 錯誤: " . $e->getMessage() . "\n";
    echo "堆疊追蹤:\n" . $e->getTraceAsString() . "\n";
} 