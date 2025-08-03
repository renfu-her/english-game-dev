<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔧 簡單 Echo 測試\n";
echo "================\n\n";

try {
    echo "1. 檢查 Echo 配置...\n";
    $config = config('broadcasting.connections.reverb');
    echo "   - 主機: {$config['options']['host']}\n";
    echo "   - 端口: {$config['options']['port']}\n";
    echo "   - APP_KEY: {$config['key']}\n";
    echo "   - APP_ID: {$config['app_id']}\n";
    
    echo "\n2. 生成前端 JavaScript 配置...\n";
    echo "   ```javascript\n";
    echo "   // 確保在頁面載入完成後執行\n";
    echo "   document.addEventListener('DOMContentLoaded', function() {\n";
    echo "       console.log('開始初始化 Echo...');\n";
    echo "       \n";
    echo "       // 檢查 Echo 是否可用\n";
    echo "       if (typeof Echo === 'undefined') {\n";
    echo "           console.error('Echo 未定義！請檢查 Laravel Echo 是否正確載入');\n";
    echo "           return;\n";
    echo "       }\n";
    echo "       \n";
    echo "       console.log('Echo 可用，開始配置...');\n";
    echo "       \n";
    echo "       // 配置 Echo\n";
    echo "       window.Echo = new Echo({\n";
    echo "           broadcaster: 'reverb',\n";
    echo "           key: '{$config['key']}',\n";
    echo "           wsHost: '{$config['options']['host']}',\n";
    echo "           wsPort: {$config['options']['port']},\n";
    echo "           wssPort: {$config['options']['port']},\n";
    echo "           forceTLS: false,\n";
    echo "           enabledTransports: ['ws', 'wss'],\n";
    echo "           disableStats: true,\n";
    echo "           auth: {\n";
    echo "               headers: {\n";
    echo "                   'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content')\n";
    echo "               }\n";
    echo "           }\n";
    echo "       });\n";
    echo "       \n";
    echo "       console.log('Echo 配置完成');\n";
    echo "       \n";
    echo "       // 測試連接\n";
    echo "       if (window.Echo) {\n";
    echo "           console.log('Echo 實例創建成功');\n";
    echo "           \n";
    echo "           // 測試訂閱公共頻道\n";
    echo "           try {\n";
    echo "               window.Echo.channel('test-channel')\n";
    echo "                   .listen('.test-event', (e) => {\n";
    echo "                       console.log('收到測試事件:', e);\n";
    echo "                   });\n";
    echo "               console.log('測試頻道訂閱成功');\n";
    echo "           } catch (error) {\n";
    echo "               console.error('頻道訂閱失敗:', error);\n";
    echo "           }\n";
    echo "       } else {\n";
    echo "           console.error('Echo 實例創建失敗');\n";
    echo "       }\n";
    echo "   });\n";
    echo "   ```\n";
    
    echo "\n3. 檢查要點...\n";
    echo "   ✅ 確保 Laravel Echo 在 Echo 初始化之前載入\n";
    echo "   ✅ 確保 CSRF 標記存在於頁面中\n";
    echo "   ✅ 確保用戶已登入（對於需要認證的頻道）\n";
    echo "   ✅ 確保 Reverb 服務器正在運行\n";
    
    echo "\n4. 調試步驟...\n";
    echo "   1. 打開瀏覽器開發者工具\n";
    echo "   2. 檢查 Console 標籤中的錯誤訊息\n";
    echo "   3. 檢查 Network 標籤中的 WebSocket 連接\n";
    echo "   4. 確認 Echo 對象是否正確初始化\n";
    
    echo "\n5. 常見問題解決...\n";
    echo "   - 如果 Echo 未定義：檢查 Laravel Echo 腳本是否正確載入\n";
    echo "   - 如果連接失敗：檢查 Reverb 服務器狀態\n";
    echo "   - 如果認證失敗：檢查用戶登入狀態和 CSRF 標記\n";
    echo "   - 如果頻道訂閱失敗：檢查頻道名稱和授權邏輯\n";
    
} catch (Exception $e) {
    echo "❌ 錯誤: " . $e->getMessage() . "\n";
    echo "堆疊追蹤:\n" . $e->getTraceAsString() . "\n";
} 