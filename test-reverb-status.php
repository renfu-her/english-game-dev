<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Http;

echo "🔌 Laravel Reverb 狀態檢查\n";
echo "========================\n\n";

// 1. 檢查配置
echo "📋 檢查配置...\n";
$config = require 'config/broadcasting.php';
$reverbConfig = require 'config/reverb.php';

if ($config['default'] === 'reverb') {
    echo "✅ 默認廣播驅動: reverb\n";
} else {
    echo "⚠️  默認廣播驅動: {$config['default']}\n";
}

if (isset($config['connections']['reverb'])) {
    echo "✅ Reverb 配置存在\n";
    $reverbConnection = $config['connections']['reverb'];
    echo "   - 主機: {$reverbConnection['options']['host']}\n";
    echo "   - 端口: {$reverbConnection['options']['port']}\n";
    echo "   - 協議: {$reverbConnection['options']['scheme']}\n";
} else {
    echo "❌ Reverb 配置不存在\n";
}

echo "\n";

// 2. 測試服務器連接
echo "🌐 測試服務器連接...\n";
$host = $reverbConnection['options']['host'] ?? '127.0.0.1';
$port = $reverbConnection['options']['port'] ?? 8080;
$scheme = $reverbConnection['options']['scheme'] ?? 'http';

$url = "{$scheme}://{$host}:{$port}";
echo "嘗試連接到: {$url}\n";

try {
    $response = Http::timeout(10)->get($url);
    echo "✅ 服務器響應: HTTP {$response->status()}\n";
    
    if ($response->successful()) {
        echo "✅ 服務器連接成功\n";
    } elseif ($response->status() === 404) {
        echo "⚠️  服務器運行中但端點不存在 (這是正常的)\n";
    } else {
        echo "⚠️  服務器響應異常: {$response->status()}\n";
    }
} catch (Exception $e) {
    echo "❌ 無法連接到服務器: " . $e->getMessage() . "\n";
    echo "   請確保 Reverb 服務器正在運行\n";
}

echo "\n";

// 3. 測試 WebSocket 端點
echo "🔌 測試 WebSocket 端點...\n";
$appId = $reverbConnection['app_id'] ?? 'your-app-id';

$endpoints = [
    "{$scheme}://{$host}:{$port}/apps/{$appId}/events",
    "{$scheme}://{$host}:{$port}/apps/{$appId}/auth",
];

foreach ($endpoints as $endpoint) {
    echo "測試端點: {$endpoint}\n";
    
    try {
        $response = Http::timeout(5)->post($endpoint, [
            'test' => 'data'
        ]);
        
        if (in_array($response->status(), [200, 404, 405])) {
            echo "✅ 端點可達 (HTTP {$response->status()})\n";
        } else {
            echo "⚠️  端點響應異常: HTTP {$response->status()}\n";
        }
    } catch (Exception $e) {
        echo "❌ 端點無法連接: " . $e->getMessage() . "\n";
    }
}

echo "\n";

// 4. 檢查環境變數
echo "🔧 檢查環境變數...\n";
$envVars = [
    'BROADCAST_CONNECTION',
    'REVERB_APP_KEY',
    'REVERB_APP_SECRET',
    'REVERB_APP_ID',
    'REVERB_HOST',
    'REVERB_PORT',
    'REVERB_SCHEME'
];

foreach ($envVars as $var) {
    $value = $_ENV[$var] ?? getenv($var);
    if ($value) {
        echo "✅ {$var}: " . (strlen($value) > 20 ? substr($value, 0, 20) . '...' : $value) . "\n";
    } else {
        echo "⚠️  {$var}: 未設置\n";
    }
}

echo "\n";

// 5. 總結
echo "📊 總結:\n";
echo "========================\n";

if ($config['default'] === 'reverb' && isset($config['connections']['reverb'])) {
    echo "✅ 配置正確\n";
} else {
    echo "❌ 配置有問題\n";
}

try {
    $response = Http::timeout(5)->get($url);
    if ($response->successful() || $response->status() === 404) {
        echo "✅ 服務器連接正常\n";
    } else {
        echo "⚠️  服務器連接異常\n";
    }
} catch (Exception $e) {
    echo "❌ 服務器無法連接\n";
}

echo "\n";
echo "💡 提示:\n";
echo "- 如果服務器無法連接，請運行: php artisan reverb:start\n";
echo "- 如果配置有問題，請檢查 .env 文件\n";
echo "- 運行完整測試: php artisan reverb:test --verbose\n"; 