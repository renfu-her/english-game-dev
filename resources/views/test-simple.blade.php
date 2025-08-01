<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>簡單 WebSocket 測試</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .status { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .connected { background-color: #d4edda; color: #155724; }
        .disconnected { background-color: #f8d7da; color: #721c24; }
        .message { background-color: #d1ecf1; color: #0c5460; padding: 10px; margin: 5px 0; border-radius: 3px; }
        #messages { height: 300px; overflow-y: auto; border: 1px solid #ccc; padding: 10px; }
        button { padding: 10px 20px; margin: 5px; }
    </style>
</head>
<body>
    <h1>簡單 WebSocket 測試</h1>
    
    <div id="status" class="status disconnected">連接狀態: 未連接</div>
    
    <div>
        <button onclick="testConnection()">測試連接</button>
        <button onclick="sendTestMessage()">發送測試訊息</button>
        <button onclick="clearMessages()">清除訊息</button>
    </div>
    
    <h3>訊息記錄:</h3>
    <div id="messages"></div>

    <script>
        let ws = null;
        
        function testConnection() {
            addMessage('🔌 嘗試連接到 ws://localhost:8888...');
            
            try {
                ws = new WebSocket('ws://localhost:8888');
                
                ws.onopen = function() {
                    document.getElementById('status').className = 'status connected';
                    document.getElementById('status').textContent = '連接狀態: 已連接';
                    addMessage('✅ WebSocket 連接成功！');
                    
                    // 訂閱測試頻道
                    subscribeToChannel('test-channel');
                };
                
                ws.onmessage = function(event) {
                    const data = JSON.parse(event.data);
                    addMessage('📨 收到: ' + JSON.stringify(data));
                };
                
                ws.onclose = function() {
                    document.getElementById('status').className = 'status disconnected';
                    document.getElementById('status').textContent = '連接狀態: 已斷開';
                    addMessage('❌ WebSocket 連接斷開');
                };
                
                ws.onerror = function(error) {
                    addMessage('❌ WebSocket 錯誤: ' + error);
                };
                
            } catch (error) {
                addMessage('❌ 連接失敗: ' + error.message);
            }
        }
        
        function subscribeToChannel(channel) {
            if (ws && ws.readyState === 1) {
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
        
        function sendTestMessage() {
            if (ws && ws.readyState === 1) {
                const message = {
                    event: 'test-message',
                    data: {
                        text: '這是一個測試訊息',
                        timestamp: new Date().toISOString()
                    }
                };
                ws.send(JSON.stringify(message));
                addMessage('📤 發送測試訊息');
            } else {
                addMessage('❌ WebSocket 未連接');
            }
        }
        
        function clearMessages() {
            document.getElementById('messages').innerHTML = '';
        }
        
        function addMessage(text) {
            const time = new Date().toLocaleTimeString();
            const messageHtml = `<div class="message"><small>${time}</small><br>${text}</div>`;
            document.getElementById('messages').innerHTML += messageHtml;
            document.getElementById('messages').scrollTop = document.getElementById('messages').scrollHeight;
        }
        
        // 頁面載入時自動測試連接
        window.onload = function() {
            addMessage('🚀 頁面載入完成');
            setTimeout(testConnection, 1000);
        };
    </script>
</body>
</html> 