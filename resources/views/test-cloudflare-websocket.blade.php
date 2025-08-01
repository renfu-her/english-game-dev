<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cloudflare WebSocket 測試</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
            background: #1a1a1a; 
            color: #fff; 
        }
        .status { 
            padding: 15px; 
            margin: 10px 0; 
            border-radius: 8px; 
        }
        .connected { 
            background-color: #2d5a2d; 
            border: 1px solid #4caf50; 
        }
        .disconnected { 
            background-color: #5a2d2d; 
            border: 1px solid #f44336; 
        }
        .testing { 
            background-color: #2d4a5a; 
            border: 1px solid #2196f3; 
        }
        .message { 
            background-color: #2a2a2a; 
            padding: 10px; 
            margin: 5px 0; 
            border-radius: 5px; 
            border-left: 4px solid #2196f3; 
        }
        #messages { 
            height: 400px; 
            overflow-y: auto; 
            border: 1px solid #333; 
            padding: 15px; 
            background: #2a2a2a; 
            border-radius: 8px; 
        }
        button { 
            padding: 12px 24px; 
            margin: 5px; 
            background: #2196f3; 
            color: white; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
        }
        button:hover { 
            background: #1976d2; 
        }
        .info { 
            background-color: #2d4a5a; 
            border: 1px solid #2196f3; 
            padding: 15px; 
            border-radius: 8px; 
            margin: 10px 0; 
        }
    </style>
</head>
<body>
    <h1>🌐 Cloudflare WebSocket 連接測試</h1>
    
    <div class="info">
        <h3>測試說明</h3>
        <p>此頁面將測試通過 Cloudflare 的 WebSocket 連接：</p>
        <ul>
                         <li>使用 Cloudflare 域名: english-game.ai-tracks.com</li>
            <li>WebSocket 路徑: /ws</li>
            <li>協議: WSS (Cloudflare 自動處理 SSL)</li>
            <li>測試連接狀態和消息傳輸</li>
        </ul>
    </div>
    
    <div id="status" class="status disconnected">連接狀態: 未連接</div>
    
    <div>
        <button onclick="testConnection()">🔌 測試連接</button>
        <button onclick="sendTestMessage()">📤 發送測試訊息</button>
        <button onclick="clearMessages()">🗑️ 清除訊息</button>
    </div>
    
    <h3>📋 連接日誌:</h3>
    <div id="messages"></div>

    <script>
        let ws = null;
        let connectionAttempts = 0;
        const maxAttempts = 3;
        
        function testConnection() {
            addMessage('🚀 開始測試 Cloudflare WebSocket 連接...');
            
            // 使用 Cloudflare 域名和 /ws 路徑
                         const wsUrl = 'wss://english-game.ai-tracks.com/ws';
            
            addMessage(`📍 連接地址: ${wsUrl}`);
                         addMessage(`🌐 當前域名: english-game.ai-tracks.com`);
            addMessage(`🔒 協議: WSS (Cloudflare SSL)`);
            
            try {
                document.getElementById('status').className = 'status testing';
                document.getElementById('status').textContent = '連接狀態: 連接中...';
                
                ws = new WebSocket(wsUrl);
                
                ws.onopen = function() {
                    document.getElementById('status').className = 'status connected';
                    document.getElementById('status').textContent = '連接狀態: 已連接 ✅';
                    addMessage('✅ WebSocket 連接成功！');
                    addMessage('🎉 Cloudflare WebSocket 配置正確');
                    connectionAttempts = 0;
                    
                    // 測試訂閱頻道
                    subscribeToChannel('test-channel');
                };
                
                ws.onmessage = function(event) {
                    const data = JSON.parse(event.data);
                    addMessage(`📨 收到訊息: ${JSON.stringify(data, null, 2)}`);
                };
                
                ws.onclose = function(event) {
                    document.getElementById('status').className = 'status disconnected';
                    document.getElementById('status').textContent = '連接狀態: 已斷開 ❌';
                    addMessage(`❌ WebSocket 連接斷開 (代碼: ${event.code}, 原因: ${event.reason})`);
                    
                    // 自動重試
                    if (connectionAttempts < maxAttempts) {
                        connectionAttempts++;
                        addMessage(`🔄 嘗試重新連接 (${connectionAttempts}/${maxAttempts})...`);
                        setTimeout(testConnection, 2000);
                    }
                };
                
                ws.onerror = function(error) {
                    addMessage(`❌ WebSocket 錯誤: ${error}`);
                    addMessage('💡 請檢查 Cloudflare WebSocket 設定是否啟用');
                };
                
            } catch (error) {
                addMessage(`❌ 連接失敗: ${error.message}`);
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
                addMessage(`📡 訂閱頻道: ${channel}`);
            }
        }
        
        function sendTestMessage() {
            if (ws && ws.readyState === 1) {
                const message = {
                    event: 'test-message',
                    data: {
                        text: '這是一個通過 Cloudflare 的測試訊息',
                        timestamp: new Date().toISOString(),
                                                 domain: 'english-game.ai-tracks.com'
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
        
        // 頁面載入時自動測試
        window.onload = function() {
            addMessage('🚀 頁面載入完成，準備測試 Cloudflare WebSocket...');
            setTimeout(testConnection, 1000);
        };
    </script>
</body>
</html> 