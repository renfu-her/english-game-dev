<?php
// 測試 WebSocket 廣播功能
$data = [
    'event' => 'test-event',
    'data' => [
        'message' => '這是一個測試訊息',
        'timestamp' => date('Y-m-d H:i:s'),
        'room_id' => 3
    ],
    'channel' => 'room.3'
];

// 發送到 WebSocket 服務器
$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/json',
        'content' => json_encode($data)
    ]
]);

$result = file_get_contents('http://localhost:8888/broadcast', false, $context);

echo "廣播測試結果: " . $result;
?> 