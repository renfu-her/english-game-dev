const WebSocket = require('ws');
const http = require('http');

// 創建 HTTP 服務器
const server = http.createServer();

// 創建 WebSocket 服務器
const wss = new WebSocket.Server({ server });

// 存儲連接的客戶端
const clients = new Map();
const channels = new Map();

console.log('WebSocket 服務器啟動中...');

wss.on('connection', function connection(ws, req) {
    console.log('新的 WebSocket 連接:', req.socket.remoteAddress);
    
    // 為每個連接分配唯一 ID
    const clientId = Date.now() + Math.random();
    clients.set(clientId, ws);
    
    // 存儲客戶端訂閱的頻道
    const clientChannels = new Set();
    
    ws.on('message', function incoming(message) {
        try {
            const data = JSON.parse(message);
            console.log('收到訊息:', data);
            
            if (data.event === 'pusher:subscribe') {
                const channel = data.data.channel;
                clientChannels.add(channel);
                
                // 將客戶端添加到頻道
                if (!channels.has(channel)) {
                    channels.set(channel, new Set());
                }
                channels.get(channel).add(clientId);
                
                console.log(`客戶端 ${clientId} 訂閱頻道: ${channel}`);
                
                // 發送訂閱成功確認
                ws.send(JSON.stringify({
                    event: 'pusher_internal:subscription_succeeded',
                    data: {},
                    channel: channel
                }));
            }
            
        } catch (error) {
            console.error('解析訊息錯誤:', error);
        }
    });
    
    ws.on('close', function() {
        console.log('客戶端斷開連接:', clientId);
        
        // 從所有頻道中移除客戶端
        clientChannels.forEach(channel => {
            if (channels.has(channel)) {
                channels.get(channel).delete(clientId);
                if (channels.get(channel).size === 0) {
                    channels.delete(channel);
                }
            }
        });
        
        // 從客戶端列表中移除
        clients.delete(clientId);
    });
    
    ws.on('error', function(error) {
        console.error('WebSocket 錯誤:', error);
    });
    
    // 發送連接成功訊息
    ws.send(JSON.stringify({
        event: 'pusher:connection_established',
        data: {
            socket_id: clientId.toString(),
            activity_timeout: 120
        }
    }));
});

// 廣播訊息到特定頻道
function broadcastToChannel(channel, event, data) {
    if (channels.has(channel)) {
        const message = JSON.stringify({
            event: event,
            data: data,
            channel: channel
        });
        
        channels.get(channel).forEach(clientId => {
            const client = clients.get(clientId);
            if (client && client.readyState === WebSocket.OPEN) {
                client.send(message);
            }
        });
        
        console.log(`廣播到頻道 ${channel}:`, event, data);
    }
}

// 啟動服務器
const PORT = 8080;
server.listen(PORT, '0.0.0.0', () => {
    console.log(`WebSocket 服務器運行在 ws://localhost:${PORT}`);
});

// 導出廣播函數供其他模組使用
module.exports = {
    broadcastToChannel
}; 