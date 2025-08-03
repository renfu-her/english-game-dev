<?php

require_once 'vendor/autoload.php';

echo "🔍 Laravel Reverb 診斷工具\n";
echo "==========================\n\n";

// 1. 檢查 Laravel 環境
echo "📋 檢查 Laravel 環境...\n";
echo "Laravel 版本: " . app()->version() . "\n";
echo "PHP 版本: " . PHP_VERSION . "\n";
echo "環境: " . app()->environment() . "\n";
echo "APP_KEY: " . (config('app.key') ? '已設置' : '未設置') . "\n";

echo "\n";

// 2. 檢查廣播配置
echo "📡 檢查廣播配置...\n";
$broadcastingConfig = config('broadcasting');
echo "默認驅動: " . ($broadcastingConfig['default'] ?? 'unknown') . "\n";

if (isset($broadcastingConfig['connections']['reverb'])) {
    echo "✅ Reverb 配置存在\n";
    $reverbConfig = $broadcastingConfig['connections']['reverb'];
    echo "   - 驅動: {$reverbConfig['driver']}\n";
    echo "   - 主機: {$reverbConfig['options']['host']}\n";
    echo "   - 端口: {$reverbConfig['options']['port']}\n";
    echo "   - 協議: {$reverbConfig['options']['scheme']}\n";
    echo "   - APP_ID: " . ($reverbConfig['app_id'] ?? '未設置') . "\n";
    echo "   - APP_KEY: " . ($reverbConfig['key'] ? '已設置' : '未設置') . "\n";
    echo "   - APP_SECRET: " . ($reverbConfig['secret'] ? '已設置' : '未設置') . "\n";
} else {
    echo "❌ Reverb 配置不存在\n";
}

echo "\n";

// 3. 檢查 Reverb 配置
echo "🔧 檢查 Reverb 配置...\n";
$reverbConfig = config('reverb');
if ($reverbConfig) {
    echo "✅ Reverb 配置文件存在\n";
    if (isset($reverbConfig['servers']['reverb'])) {
        echo "✅ 服務器配置存在\n";
        $server = $reverbConfig['servers']['reverb'];
        echo "   - 主機: {$server['host']}\n";
        echo "   - 端口: {$server['port']}\n";
    }
    
    if (isset($reverbConfig['apps']['apps'])) {
        echo "✅ 應用程序配置存在\n";
        $apps = $reverbConfig['apps']['apps'];
        echo "   應用程序數量: " . count($apps) . "\n";
    }
} else {
    echo "❌ Reverb 配置文件不存在\n";
}

echo "\n";

// 4. 檢查環境變數
echo "🌍 檢查環境變數...\n";
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
    $value = env($var);
    if ($value) {
        echo "✅ {$var}: " . (strlen($value) > 20 ? substr($value, 0, 20) . '...' : $value) . "\n";
    } else {
        echo "⚠️   {$var}: 未設置\n";
    }
}

echo "\n";

// 5. 檢查服務器連接
echo "🌐 檢查服務器連接...\n";
$host = config('broadcasting.connections.reverb.options.host', '127.0.0.1');
$port = config('broadcasting.connections.reverb.options.port', 8080);
$scheme = config('broadcasting.connections.reverb.options.scheme', 'http');

$url = "{$scheme}://{$host}:{$port}";
echo "嘗試連接到: {$url}\n";

try {
    $response = \Illuminate\Support\Facades\Http::timeout(5)->get($url);
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

// 6. 檢查事件類別
echo "📝 檢查事件類別...\n";
try {
    $event = new \App\Events\TestEvent('診斷測試');
    echo "✅ TestEvent 類別可以正常實例化\n";
    
    $channels = $event->broadcastOn();
    echo "✅ 廣播頻道: " . count($channels) . " 個\n";
    
    $data = $event->broadcastWith();
    echo "✅ 廣播數據: " . json_encode($data, JSON_UNESCAPED_UNICODE) . "\n";
    
} catch (Exception $e) {
    echo "❌ TestEvent 類別有問題: " . $e->getMessage() . "\n";
}

echo "\n";

// 7. 檢查廣播驅動
echo "📡 檢查廣播驅動...\n";
try {
    $driver = \Illuminate\Support\Facades\Broadcast::driver();
    echo "✅ 廣播驅動: " . get_class($driver) . "\n";
    
    $connection = \Illuminate\Support\Facades\Broadcast::connection();
    echo "✅ 廣播連接: " . get_class($connection) . "\n";
    
} catch (Exception $e) {
    echo "❌ 廣播驅動有問題: " . $e->getMessage() . "\n";
}

echo "\n";

// 8. 總結
echo "📊 診斷總結:\n";
echo "==========================\n";

$issues = [];

if (!config('app.key')) {
    $issues[] = "APP_KEY 未設置";
}

if (!isset($broadcastingConfig['connections']['reverb'])) {
    $issues[] = "Reverb 配置不存在";
}

if (!config('reverb')) {
    $issues[] = "Reverb 配置文件不存在";
}

if (empty($issues)) {
    echo "✅ 配置看起來正常\n";
    echo "💡 建議:\n";
    echo "   1. 確保 Reverb 服務正在運行: php artisan reverb:start\n";
    echo "   2. 運行測試: php test-broadcast-simple.php\n";
    echo "   3. 檢查日誌: tail -f storage/logs/laravel.log\n";
} else {
    echo "❌ 發現以下問題:\n";
    foreach ($issues as $issue) {
        echo "   - {$issue}\n";
    }
    echo "\n💡 解決方案:\n";
    echo "   1. 設置 APP_KEY: php artisan key:generate\n";
    echo "   2. 檢查 .env 文件中的 Reverb 配置\n";
    echo "   3. 清除配置快取: php artisan config:clear\n";
}

echo "\n"; 