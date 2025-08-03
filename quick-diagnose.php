<?php

require_once 'vendor/autoload.php';

echo "🔍 Laravel Reverb 快速診斷\n";
echo "==========================\n\n";

// 1. 檢查廣播配置
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

// 2. 檢查實際使用的廣播驅動
echo "🔧 檢查實際廣播驅動...\n";
try {
    $driver = \Illuminate\Support\Facades\Broadcast::driver();
    echo "✅ 當前廣播驅動: " . get_class($driver) . "\n";
    
    if (strpos(get_class($driver), 'PusherBroadcaster') !== false) {
        echo "❌ 問題: 正在使用 PusherBroadcaster 而不是 ReverbBroadcaster\n";
        echo "💡 解決方案: 檢查配置和清除快取\n";
    } elseif (strpos(get_class($driver), 'ReverbBroadcaster') !== false) {
        echo "✅ 正確: 正在使用 ReverbBroadcaster\n";
    } else {
        echo "⚠️  未知的廣播驅動: " . get_class($driver) . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ 無法獲取廣播驅動: " . $e->getMessage() . "\n";
}

echo "\n";

// 3. 檢查環境變數
echo "🌍 檢查關鍵環境變數...\n";
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
        echo "⚠️  {$var}: 未設置\n";
    }
}

echo "\n";

// 4. 建議
echo "💡 建議的修復步驟:\n";
echo "1. 清除配置快取: php artisan config:clear\n";
echo "2. 清除應用快取: php artisan cache:clear\n";
echo "3. 重新啟動 Reverb 服務: php artisan reverb:start\n";
echo "4. 檢查 .env 文件中的 BROADCAST_CONNECTION=reverb\n";
echo "5. 確保所有 REVERB_* 環境變數都已設置\n";

echo "\n"; 