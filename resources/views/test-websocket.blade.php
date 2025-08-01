<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebSocket 測試</title>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .status { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .connected { background-color: #d4edda; color: #155724; }
        .disconnected { background-color: #f8d7da; color: #721c24; }
        .message { background-color: #d1ecf1; color: #0c5460; padding: 10px; margin: 5px 0; border-radius: 3px; }
        #messages { height: 300px; overflow-y: auto; border: 1px solid #ccc; padding: 10px; }
    </style>
</head>
<body>
    <h1>WebSocket 連接測試</h1>
    
    <div id="status" class="status disconnected">連接狀態: 未連接</div>
    
    <div>
        <button id="testEvent">測試廣播事件</button>
        <button id="clearMessages">清除訊息</button>
    </div>
    
    <h3>接收到的訊息:</h3>
    <div id="messages"></div>

    <script>
        $(document).ready(function() {
            // 初始化 Laravel Reverb (使用 Pusher 協議)
            const pusher = new Pusher('{{ config("broadcasting.connections.reverb.key") }}', {
                wsHost: '{{ config("broadcasting.connections.reverb.options.host") }}',
                wsPort: {{ config("broadcasting.connections.reverb.options.port") }},
                wssPort: {{ config("broadcasting.connections.reverb.options.port") }},
                forceTLS: false,
                enabledTransports: ['ws'], // 只使用 WS，不使用 WSS
                disableStats: true,
                cluster: 'mt1', // 任意值，因為我們使用自定義主機
                encrypted: false, // 禁用加密
            });

            // 連接狀態監聽
            pusher.connection.bind('connected', function() {
                $('#status').removeClass('disconnected').addClass('connected').text('連接狀態: 已連接');
                addMessage('✅ WebSocket 連接成功！');
            });

            pusher.connection.bind('disconnected', function() {
                $('#status').removeClass('connected').addClass('disconnected').text('連接狀態: 已斷開');
                addMessage('❌ WebSocket 連接斷開');
            });

            // 訂閱測試頻道
            const testChannel = pusher.subscribe('test-channel');
            
            testChannel.bind('test-event', function(data) {
                addMessage('📨 收到測試事件: ' + JSON.stringify(data));
            });

            // 訂閱遊戲大廳頻道
            const lobbyChannel = pusher.subscribe('game.lobby');
            
            lobbyChannel.bind('room.created', function(data) {
                addMessage('🏠 房間建立: ' + data.message);
            });

            lobbyChannel.bind('room.deleted', function(data) {
                addMessage('🗑️ 房間刪除: ' + data.message);
            });

            lobbyChannel.bind('room.status_changed', function(data) {
                addMessage('🔄 房間狀態變更: ' + data.message);
            });

            lobbyChannel.bind('member.status_changed', function(data) {
                addMessage('👤 會員狀態變更: ' + data.message);
            });

            // 測試按鈕
            $('#testEvent').click(function() {
                $.ajax({
                    url: '/test-broadcast',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        addMessage('📤 測試事件已發送');
                    },
                    error: function(xhr) {
                        addMessage('❌ 發送測試事件失敗: ' + xhr.responseText);
                    }
                });
            });

            $('#clearMessages').click(function() {
                $('#messages').empty();
            });

            function addMessage(text) {
                const time = new Date().toLocaleTimeString();
                const messageHtml = `<div class="message"><small>${time}</small><br>${text}</div>`;
                $('#messages').append(messageHtml);
                $('#messages').scrollTop($('#messages')[0].scrollHeight);
            }

            addMessage('🚀 頁面載入完成，正在連接 WebSocket...');
        });
    </script>
</body>
</html> 