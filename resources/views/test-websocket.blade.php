<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebSocket 測試</title>
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
        <button id="testNativeWebSocket">測試原生 WebSocket</button>
    </div>
    
    <h3>接收到的訊息:</h3>
    <div id="messages"></div>

    <script>
        $(document).ready(function() {
            // 純 JavaScript WebSocket 實現
            let ws = null;
            let reconnectAttempts = 0;
            const maxReconnectAttempts = 5;
            
            function connectWebSocket() {
                try {
                    console.log('嘗試連接到 WebSocket 服務器: ws://localhost:8888');
                    ws = new WebSocket('ws://localhost:8888');
                    
                    ws.onopen = function() {
                        $('#status').removeClass('disconnected').addClass('connected').text('連接狀態: 已連接');
                        addMessage('✅ WebSocket 連接成功！');
                        reconnectAttempts = 0;
                        
                        // 訂閱測試頻道
                        subscribeToChannel('test-channel');
                        subscribeToChannel('game.lobby');
                    };
                    
                    ws.onmessage = function(event) {
                        const data = JSON.parse(event.data);
                        addMessage('📨 收到訊息: ' + JSON.stringify(data));
                        handleWebSocketMessage(data);
                    };
                    
                    ws.onclose = function() {
                        $('#status').removeClass('connected').addClass('disconnected').text('連接狀態: 已斷開');
                        addMessage('❌ WebSocket 連接斷開');
                        if (reconnectAttempts < maxReconnectAttempts) {
                            reconnectAttempts++;
                            setTimeout(connectWebSocket, 2000);
                        }
                    };
                    
                    ws.onerror = function(error) {
                        addMessage('❌ WebSocket 錯誤: ' + error);
                    };
                    
                } catch (error) {
                    addMessage('❌ WebSocket 連接失敗: ' + error.message);
                }
            }
            
            function subscribeToChannel(channel) {
                if (ws && ws.readyState === WebSocket.OPEN) {
                    const message = {
                        event: 'pusher:subscribe',
                        data: {
                            channel: channel
                        }
                    };
                    ws.send(JSON.stringify(message));
                    addMessage('📡 訂閱頻道: ' + channel);
                }
            }
            
            function handleWebSocketMessage(data) {
                if (data.event === 'room.created') {
                    addMessage('🏠 房間建立: ' + data.data.message);
                } else if (data.event === 'room.deleted') {
                    addMessage('🗑️ 房間刪除: ' + data.data.message);
                } else if (data.event === 'room.status_changed') {
                    addMessage('🔄 房間狀態變更: ' + data.data.message);
                } else if (data.event === 'member.status_changed') {
                    addMessage('👤 會員狀態變更: ' + data.data.message);
                }
            }
            
            // 初始化連接
            connectWebSocket();
            
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

            $('#testNativeWebSocket').click(function() {
                addMessage('🔌 嘗試原生 WebSocket 連接到 ws://localhost:8888...');
                
                try {
                    const ws = new WebSocket('ws://localhost:8888');
                    
                    ws.onopen = function() {
                        addMessage('✅ 原生 WebSocket 連接成功！');
                        
                        // 嘗試訂閱頻道
                        const subscribeMessage = {
                            event: 'pusher:subscribe',
                            data: {
                                channel: 'test-channel'
                            }
                        };
                        
                        ws.send(JSON.stringify(subscribeMessage));
                        addMessage('📡 已發送訂閱請求到 test-channel');
                    };
                    
                    ws.onmessage = function(event) {
                        addMessage('📨 收到訊息: ' + event.data);
                    };
                    
                    ws.onerror = function(error) {
                        addMessage('❌ 原生 WebSocket 連接失敗: ' + error);
                    };
                    
                    ws.onclose = function() {
                        addMessage('🔌 原生 WebSocket 連接已關閉');
                    };
                } catch (error) {
                    addMessage('❌ 原生 WebSocket 錯誤: ' + error.message);
                }
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