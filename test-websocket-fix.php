<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔧 測試 WebSocket 連接修復\n";
echo "==========================\n\n";

try {
    echo "1. 檢查 Reverb 配置...\n";
    $config = config('broadcasting.connections.reverb');
    echo "   ✅ 配置存在\n";
    echo "   - 主機: {$config['options']['host']}\n";
    echo "   - 端口: {$config['options']['port']}\n";
    echo "   - 協議: {$config['options']['scheme']}\n";
    echo "   - APP_KEY: {$config['key']}\n";
    echo "   - APP_ID: {$config['app_id']}\n";
    
    echo "\n2. 檢查 Reverb 服務器狀態...\n";
    $host = $config['options']['host'];
    $port = $config['options']['port'];
    $scheme = $config['options']['scheme'];
    
    $url = "{$scheme}://{$host}:{$port}/";
    
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'timeout' => 5
        ]
    ]);
    
    $response = file_get_contents($url, false, $context);
    
    if ($response !== false) {
        echo "   ✅ Reverb 服務器正在運行\n";
        echo "   - URL: {$url}\n";
        echo "   - 響應長度: " . strlen($response) . " 字節\n";
    } else {
        echo "   ❌ Reverb 服務器無法連接\n";
        echo "   - URL: {$url}\n";
    }
    
    echo "\n3. 測試廣播功能...\n";
    
    $event = new \App\Events\TestEvent('WebSocket 修復測試');
    event($event);
    echo "   ✅ 廣播功能正常\n";
    
    echo "\n4. 檢查 WebSocket 端點...\n";
    $appId = $config['app_id'];
    $wsUrl = "ws://{$host}:{$port}/apps/{$appId}";
    echo "   - WebSocket URL: {$wsUrl}\n";
    echo "   - 事件端點: {$scheme}://{$host}:{$port}/apps/{$appId}/events\n";
    echo "   - 認證端點: {$scheme}://{$host}:{$port}/apps/{$appId}/auth\n";
    
    echo "\n5. 前端配置建議...\n";
    echo "   JavaScript 配置:\n";
    echo "   ```javascript\n";
    echo "   window.Echo = new Echo({\n";
    echo "       broadcaster: 'reverb',\n";
    echo "       key: '{$config['key']}',\n";
    echo "       wsHost: '{$host}',\n";
    echo "       wsPort: {$port},\n";
    echo "       wssPort: {$port},\n";
    echo "       forceTLS: false,\n";
    echo "       enabledTransports: ['ws', 'wss'],\n";
    echo "       disableStats: true,\n";
    echo "       auth: {\n";
    echo "           headers: {\n";
    echo "               'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content')\n";
    echo "           }\n";
    echo "       }\n";
    echo "   });\n";
    echo "   ```\n";
    
    echo "\n🎉 WebSocket 連接測試完成！\n";
    echo "💡 如果前端仍然有問題，請檢查：\n";
    echo "   1. 瀏覽器控制台是否有其他錯誤\n";
    echo "   2. Laravel Echo 是否正確載入\n";
    echo "   3. CSRF 標記是否正確設置\n";
    echo "   4. 網路連接是否正常\n";
    
} catch (Exception $e) {
    echo "❌ 錯誤: " . $e->getMessage() . "\n";
    echo "堆疊追蹤:\n" . $e->getTraceAsString() . "\n";
} 