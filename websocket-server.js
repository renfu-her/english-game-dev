import { WebSocketServer } from 'ws';
import http from 'http';
import crypto from 'crypto';

// 創建 HTTP 服務器
const server = http.createServer((req, res) => {
    // 處理廣播請求
    if (req.method === 'POST' && req.url === '/broadcast') {
        let body = '';
        req.on('data', chunk => {
            body += chunk.toString();
        });
        req.on('end', () => {
            try {
                const data = JSON.parse(body);
                broadcastToChannel(data.channel, data.event, data.data);
                res.writeHead(200, { 'Content-Type': 'application/json' });
                res.end(JSON.stringify({ success: true, message: '廣播成功' }));
            } catch (error) {
                res.writeHead(400, { 'Content-Type': 'application/json' });
                res.end(JSON.stringify({ success: false, error: error.message }));
            }
        });
    } else if (req.url === '/ws' || req.url === '/') {
        // 檢查是否為 WebSocket 升級請求
        const upgrade = req.headers.upgrade;
        const connection = req.headers.connection;
        
        if (upgrade && upgrade.toLowerCase() === 'websocket' && 
            connection && connection.toLowerCase().includes('upgrade')) {
            
            console.log('收到 WebSocket 升級請求');
            
            // 使用 WebSocketServer 處理升級
            const wss = new WebSocketServer({ noServer: true });
            
            wss.on('connection', function connection(ws) {
                handleWebSocketConnection(ws);
            });
            
            wss.handleUpgrade(req, req.socket, Buffer.alloc(0), function done(ws) {
                wss.emit('connection', ws, req);
            });
        } else {
            // 普通 HTTP 請求 - 返回 426 狀態碼（需要升級）
            res.writeHead(426, { 
                'Content-Type': 'text/plain',
                'Upgrade': 'websocket'
            });
            res.end('Expected Upgrade: websocket');
        }
    } else {
        res.writeHead(404, { 'Content-Type': 'text/plain' });
        res.end('Not Found');
    }
});

// 存儲連接的客戶端
const clients = new Map();
const channels = new Map();

console.log('WebSocket 服務器啟動中...');

// 處理 WebSocket 連接的函數（參考 Cloudflare Workers 事件處理）
function handleWebSocketConnection(ws) {
    console.log('新的 WebSocket 連接建立');
    
    // 為每個連接分配唯一 ID
    const clientId = Date.now() + Math.random();
    clients.set(clientId, ws);
    
    // 存儲客戶端訂閱的頻道
    const clientChannels = new Set();
    
    // 發送連接成功訊息（參考 Cloudflare Workers 標準）
    ws.send(JSON.stringify({
        event: 'pusher:connection_established',
        data: {
            socket_id: clientId.toString(),
            activity_timeout: 120
        }
    }));
    
    // 處理訊息事件（參考 Cloudflare Workers addEventListener 模式）
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
    
    // 處理關閉事件
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
    
    // 處理錯誤事件
    ws.on('error', function(error) {
        console.error('WebSocket 錯誤:', error);
    });
}

// 廣播訊息到特定頻道（參考 Cloudflare Workers 訊息發送模式）
function broadcastToChannel(channel, event, data) {
    if (channels.has(channel)) {
        const message = JSON.stringify({
            event: event,
            data: data,
            channel: channel
        });
        
        channels.get(channel).forEach(clientId => {
            const client = clients.get(clientId);
            if (client && client.readyState === 1) { // WebSocket.OPEN
                client.send(message);
            }
        });
        
        console.log(`廣播到頻道 ${channel}:`, event, data);
    }
}

// 啟動服務器
const PORT = process.env.PORT || 3000;
const HOST = '0.0.0.0';
server.listen(PORT, HOST, () => {
    console.log(`WebSocket 服務器運行在端口 ${PORT}`);
    console.log(`本地連接: ws://localhost:${PORT}`);
    console.log(`Nginx 代理: wss://english-game.ai-tracks.com/ws`);
    console.log(`參考 Cloudflare Workers WebSocket 標準實現`);
});

// 導出廣播函數供其他模組使用
export { broadcastToChannel }; 