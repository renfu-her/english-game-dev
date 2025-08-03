<?php

echo "🔍 測試 Nginx Reverb 代理配置\n";
echo "==============================\n\n";

// 測試配置
$config = [
    'host' => '127.0.0.1',
    'port' => 8080,
    'app_id' => '208353',
    'app_key' => 'kflgj8sm4ycm4oyhslkv',
    'app_secret' => 'dfvyqipptgxvhbqzaqif'
];

echo "📋 測試配置:\n";
echo "- 主機: {$config['host']}\n";
echo "- 端口: {$config['port']}\n";
echo "- APP_ID: {$config['app_id']}\n";
echo "- APP_KEY: {$config['app_key']}\n";
echo "- APP_SECRET: " . substr($config['app_secret'], 0, 10) . "...\n\n";

// 測試端點
$endpoints = [
    'reverb_status' => "http://{$config['host']}:{$config['port']}/",
    'apps_events' => "http://{$config['host']}:{$config['port']}/apps/{$config['app_id']}/events",
    'apps_auth' => "http://{$config['host']}:{$config['port']}/apps/{$config['app_id']}/auth",
];

echo "🌐 測試端點連接:\n";

foreach ($endpoints as $name => $url) {
    echo "\n測試 {$name}: {$url}\n";
    
    try {
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => [
                    'Content-Type: application/json',
                    'User-Agent: Reverb-Test/1.0'
                ],
                'content' => json_encode(['test' => 'data']),
                'timeout' => 10
            ]
        ]);
        
        $response = file_get_contents($url, false, $context);
        
        if ($response !== false) {
            echo "✅ 連接成功\n";
            echo "   響應長度: " . strlen($response) . " 字節\n";
            if (strlen($response) < 200) {
                echo "   響應內容: " . substr($response, 0, 200) . "\n";
            }
        } else {
            echo "❌ 連接失敗\n";
        }
        
    } catch (Exception $e) {
        echo "❌ 錯誤: " . $e->getMessage() . "\n";
    }
}

echo "\n🔧 Nginx 配置建議:\n";
echo "==============================\n";
echo "1. 將 nginx-english-game-local.conf 複製到您的 nginx sites-available 目錄\n";
echo "2. 創建符號連結到 sites-enabled:\n";
echo "   sudo ln -s /etc/nginx/sites-available/english-game /etc/nginx/sites-enabled/\n";
echo "3. 測試配置:\n";
echo "   sudo nginx -t\n";
echo "4. 重新載入 nginx:\n";
echo "   sudo systemctl reload nginx\n";
echo "\n5. 確保 Reverb 服務器正在運行:\n";
echo "   php artisan reverb:start --host=127.0.0.1 --port=8080\n";
echo "\n6. 測試 WebSocket 連接:\n";
echo "   wscat -c ws://127.0.0.1:8080/apps/208353\n";

echo "\n💡 故障排除:\n";
echo "- 檢查 nginx 錯誤日誌: sudo tail -f /var/log/nginx/error.log\n";
echo "- 檢查 Reverb 服務器狀態: netstat -an | grep 8080\n";
echo "- 測試直接連接: curl http://127.0.0.1:8080/\n"; 