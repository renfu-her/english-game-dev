import WebSocket from 'ws';

console.log('🔌 測試本地 WebSocket 連接...');

const ws = new WebSocket('ws://127.0.0.1:3000');

ws.on('open', function open() {
    console.log('✅ WebSocket 連接成功！');
    
    // 測試訂閱頻道
    const subscribeMessage = {
        event: 'pusher:subscribe',
        data: {
            channel: 'test-channel'
        }
    };
    
    ws.send(JSON.stringify(subscribeMessage));
    console.log('📡 發送訂閱訊息:', subscribeMessage);
});

ws.on('message', function message(data) {
    console.log('📨 收到訊息:', data.toString());
});

ws.on('close', function close(code, reason) {
    console.log(`❌ WebSocket 連接關閉 (代碼: ${code}, 原因: ${reason})`);
});

ws.on('error', function error(err) {
    console.error('❌ WebSocket 錯誤:', err.message);
});

// 5秒後關閉連接
setTimeout(() => {
    console.log('🔄 測試完成，關閉連接');
    ws.close();
    process.exit(0);
}, 5000); 