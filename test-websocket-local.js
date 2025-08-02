import { WebSocketServer } from 'ws';

console.log('🔌 測試本地 WebSocket 連接...');

const server = new WebSocketServer({ port: 3000});

server.on('connection', (ws) => {
    console.log('🔌 新連接建立');

    ws.on('message', (message) => {
        console.log('📨 收到訊息:', message.toString());
    });
});