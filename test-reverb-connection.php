<?php

echo "🔍 測試 Reverb 連接\n";
echo "==================\n\n";

$url = "http://127.0.0.1:8080/";
echo "測試 URL: {$url}\n\n";

try {
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'timeout' => 10
        ]
    ]);
    
    $response = file_get_contents($url, false, $context);
    
    if ($response !== false) {
        echo "✅ 連接成功！\n";
        echo "響應內容: " . substr($response, 0, 200) . "\n";
    } else {
        echo "❌ 連接失敗\n";
    }
    
} catch (Exception $e) {
    echo "❌ 錯誤: " . $e->getMessage() . "\n";
}

echo "\n測試 apps 端點:\n";
$appsUrl = "http://127.0.0.1:8080/apps/208353/events";
echo "測試 URL: {$appsUrl}\n\n";

try {
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => json_encode(['test' => 'data']),
            'timeout' => 10
        ]
    ]);
    
    $response = file_get_contents($appsUrl, false, $context);
    
    if ($response !== false) {
        echo "✅ apps 端點連接成功！\n";
        echo "響應內容: " . substr($response, 0, 200) . "\n";
    } else {
        echo "❌ apps 端點連接失敗\n";
    }
    
} catch (Exception $e) {
    echo "❌ 錯誤: " . $e->getMessage() . "\n";
} 