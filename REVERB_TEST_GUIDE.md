# Laravel Reverb 測試指南

## 概述

本指南提供完整的 Laravel Reverb 測試方案，幫助您確認 Reverb 服務是否正確啟動和配置。

## 測試工具

### 1. PHPUnit 測試類別

我們建立了四個測試類別來全面測試 Reverb 功能：

#### ReverbConnectionTest
```bash
php artisan test tests/Feature/ReverbConnectionTest.php
```
- 測試配置是否正確
- 測試服務器連接
- 測試廣播配置
- 測試應用程序配置

#### ReverbBroadcastingTest
```bash
php artisan test tests/Feature/ReverbBroadcastingTest.php
```
- 測試事件廣播
- 測試頻道廣播
- 測試私有頻道
- 測試房間頻道

#### ReverbWebSocketTest
```bash
php artisan test tests/Feature/ReverbWebSocketTest.php
```
- 測試 WebSocket 端點
- 測試握手端點
- 測試認證端點
- 測試連接配置

#### ReverbIntegrationTest
```bash
php artisan test tests/Feature/ReverbIntegrationTest.php
```
- 完整工作流程測試
- 遊戲事件廣播測試
- 頻道訂閱測試
- 錯誤處理測試

### 2. Artisan 命令

#### 基本測試
```bash
php artisan reverb:test
```

#### 詳細測試
```bash
php artisan reverb:test --verbose
```

這個命令會：
- 檢查配置
- 測試服務器連接
- 測試廣播功能
- 測試 WebSocket 端點

### 3. Web 介面測試

訪問測試頁面：
```
http://your-domain.com/test-reverb
```

功能包括：
- 配置測試
- 服務器連接測試
- 廣播功能測試
- WebSocket 端點測試
- 頻道廣播測試
- 環境變數檢查
- 完整測試

### 4. 命令行腳本

```bash
php test-reverb-status.php
```

這個腳本會：
- 檢查配置
- 測試服務器連接
- 檢查環境變數
- 提供診斷信息

## 測試步驟

### 步驟 1: 檢查配置

確保以下配置正確：

#### .env 文件
```env
BROADCAST_CONNECTION=reverb
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_APP_ID=your-app-id
REVERB_HOST=127.0.0.1
REVERB_PORT=8080
REVERB_SCHEME=http
```

#### config/broadcasting.php
```php
'default' => env('BROADCAST_CONNECTION', 'reverb'),

'connections' => [
    'reverb' => [
        'driver' => 'reverb',
        'key' => env('REVERB_APP_KEY'),
        'secret' => env('REVERB_APP_SECRET'),
        'app_id' => env('REVERB_APP_ID'),
        'options' => [
            'host' => env('REVERB_HOST', '127.0.0.1'),
            'port' => env('REVERB_PORT', 8080),
            'scheme' => env('REVERB_SCHEME', 'http'),
        ],
    ],
],
```

### 步驟 2: 啟動 Reverb 服務

```bash
php artisan reverb:start
```

或者手動啟動：
```bash
php artisan reverb:start --host=0.0.0.0 --port=8080
```

### 步驟 3: 運行測試

#### 快速測試
```bash
php test-reverb-status.php
```

#### 完整測試
```bash
php artisan reverb:test --verbose
```

#### Web 介面測試
訪問 `http://your-domain.com/test-reverb`

### 步驟 4: 檢查結果

#### 成功指標
- ✅ 配置正確
- ✅ 服務器連接成功
- ✅ 廣播功能正常
- ✅ WebSocket 端點可達

#### 常見問題

**1. 服務器無法連接**
```
❌ 無法連接到服務器: Connection refused
```
**解決方案**：
- 確保 Reverb 服務正在運行
- 檢查端口是否被佔用
- 檢查防火牆設定

**2. 配置錯誤**
```
❌ 錯誤: 找不到 Reverb 配置
```
**解決方案**：
- 檢查 `config/broadcasting.php` 配置
- 確保環境變數正確設置
- 清除配置快取：`php artisan config:clear`

**3. 廣播失敗**
```
❌ 廣播失敗: [錯誤訊息]
```
**解決方案**：
- 檢查 Reverb 服務狀態
- 確認事件類別正確實現 `ShouldBroadcast`
- 檢查頻道配置

## 測試 API 端點

### 配置測試
```bash
curl http://your-domain.com/test-reverb/configuration
```

### 服務器連接測試
```bash
curl http://your-domain.com/test-reverb/server-connection
```

### 廣播測試
```bash
curl -X POST http://your-domain.com/test-reverb/broadcasting \
  -H "Content-Type: application/json" \
  -d '{"message": "測試訊息"}'
```

### WebSocket 端點測試
```bash
curl http://your-domain.com/test-reverb/websocket-endpoints
```

### 完整測試
```bash
curl http://your-domain.com/test-reverb/full-test
```

## 監控和日誌

### 查看 Reverb 日誌
```bash
tail -f storage/logs/laravel.log
```

### 檢查服務狀態
```bash
ps aux | grep reverb
```

### 檢查端口使用
```bash
netstat -tlnp | grep 8080
```

## 故障排除

### 1. 服務無法啟動
- 檢查 PHP 版本（需要 PHP 8.1+）
- 檢查 Laravel 版本（需要 Laravel 11+）
- 檢查依賴是否安裝：`composer install`

### 2. 連接超時
- 檢查網絡連接
- 檢查防火牆設定
- 嘗試使用不同的端口

### 3. 認證失敗
- 檢查 APP_KEY 是否設置
- 檢查 Reverb 應用程序配置
- 清除快取：`php artisan cache:clear`

### 4. 廣播不工作
- 檢查事件類別是否實現正確的介面
- 檢查頻道配置
- 確認前端 JavaScript 配置正確

## 最佳實踐

1. **定期測試**：建議在部署前後都運行測試
2. **監控日誌**：定期檢查日誌文件
3. **備份配置**：保存工作配置的備份
4. **版本控制**：將配置變更納入版本控制
5. **文檔記錄**：記錄任何配置變更

## 支援

如果遇到問題，請檢查：
1. Laravel 官方文檔
2. Reverb 官方文檔
3. 專案日誌文件
4. 測試工具的輸出信息

---

*最後更新：2025-08-01* 