import fetch from 'node-fetch';

// Cloudflare API 配置
const ZONE_ID = '162778a33ff0737dafba16d5e78e9e96'; // 從您的截圖中獲取
const API_TOKEN = process.env.CLOUDFLARE_API_TOKEN; // 需要設置環境變數

async function checkWebSocketSettings() {
    try {
        const response = await fetch(
            `https://api.cloudflare.com/client/v4/zones/${ZONE_ID}/settings/websockets`,
            {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${API_TOKEN}`,
                    'Content-Type': 'application/json'
                }
            }
        );

        const data = await response.json();
        console.log('Cloudflare WebSocket 設定:', data);
        
        if (data.success && data.result.value === 'on') {
            console.log('✅ WebSocket 已啟用');
        } else {
            console.log('❌ WebSocket 未啟用');
        }
    } catch (error) {
        console.error('檢查 WebSocket 設定時發生錯誤:', error);
    }
}

// 如果沒有 API Token，提供手動檢查說明
if (!API_TOKEN) {
    console.log('請設置 CLOUDFLARE_API_TOKEN 環境變數');
    console.log('或者手動在 Cloudflare 控制台檢查：');
    console.log('1. 登入 Cloudflare 控制台');
    console.log('2. 選擇您的域名');
    console.log('3. 進入 "Network" > "WebSockets"');
    console.log('4. 確保 WebSocket 設定為 "On"');
} else {
    checkWebSocketSettings();
} 