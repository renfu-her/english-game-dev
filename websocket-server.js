import { WebSocketServer } from 'ws';
import http from 'http';

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
        // 參考 Cloudflare Workers 文檔的 WebSocket 處理方式
        const upgradeHeader = req.headers.upgrade;
        
        if (!upgradeHeader || upgradeHeader !== 'websocket') {
            // 普通 HTTP 請求
            res.writeHead(200, { 'Content-Type': 'text/plain' });
            res.end('WebSocket endpoint - 請使用 WebSocket 協議連接');
            return;
        }
        
        console.log('收到 WebSocket 升級請求');
        
        // 生成 WebSocket 密鑰響應
        const key = req.headers['sec-websocket-key'];
        const accept = require('crypto')
            .createHash('sha1')
            .update(key + '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')
            .digest('base64');
        
        res.writeHead(101, {
            'Upgrade': 'websocket',
            'Connection': 'Upgrade',
            'Sec-WebSocket-Accept': accept,
            'Sec-WebSocket-Protocol': req.headers['sec-websocket-protocol'] || ''
        });
        res.end();
        
        // 使用 WebSocketServer 處理升級
        const wss = new WebSocketServer({ noServer: true });
        
        wss.on('connection', function connection(ws) {
            handleWebSocketConnection(ws);
        });
        
        wss.handleUpgrade(req, req.socket, Buffer.alloc(0), function done(ws) {
            wss.emit('connection', ws, req);
        });
    } else {
        res.writeHead(404, { 'Content-Type': 'text/plain' });
        res.end('Not Found');
    }
});

// 存儲連接的客戶端
const clients = new Map();
const channels = new Map();

console.log('WebSocket 服務器啟動中...');

// 處理 WebSocket 連接的函數
function handleWebSocketConnection(ws) {
    console.log('新的 WebSocket 連接建立');
    
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
}

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
            if (client && client.readyState === 1) { // WebSocket.OPEN
                client.send(message);
            }
        });
        
        console.log(`廣播到頻道 ${channel}:`, event, data);
    }
}

// 啟動服務器
const PORT = process.env.PORT || 80; // 使用環境變數或默認 80 端口
const HOST = '0.0.0.0'; // 監聽所有網路介面
server.listen(PORT, HOST, () => {
    console.log(`WebSocket 服務器運行在端口 ${PORT}`);
    console.log(`本地連接: ws://localhost:${PORT}`);
    console.log(`Cloudflare 代理: 自動處理 WebSocket 流量`);
});

// 導出廣播函數供其他模組使用
export { broadcastToChannel }; 