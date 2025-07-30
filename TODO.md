# 英文遊戲專案 TODO

## 📋 專案概述
建立一個多人連線的英文學習遊戲平台，支援即時互動、題目練習和競賽模式。

## 🎯 核心功能

### 1. 會員系統
- [ ] 會員註冊
- [ ] 會員登入/登出
- [ ] 會員資料管理
- [ ] 密碼重設
- [ ] 會員權限管理
- [ ] 會員統計資料（遊戲次數、正確率等）
- [ ] 建立 Member Model（會員）
- [ ] 更新 User Model（管理者）
- [ ] 設定多認證系統（User 和 Member）
- [ ] 更新所有關聯關係

### 2. 題目分類系統
- [ ] 日常生活（Daily Conversation）
- [ ] 旅遊與交通（Travel & Transport）
- [ ] 商業英語（Business English）
- [ ] 校園生活（Campus Life）
- [ ] 健康與醫療（Health & Medical）

### 3. 遊戲系統
- [ ] 房間創建與管理
- [ ] 多人連線支援
- [ ] 即時聊天功能
- [ ] 題目隨機選擇
- [ ] 計時器功能
- [ ] 遊戲結果統計

### 4. 題目類型
- [ ] 選擇題（4選1）
- [ ] 填空題（1-3個位置，5個選項）

## 🏗️ 技術架構

### 後端技術棧
- Laravel 12
- Filament Admin Panel
- Laravel Sanctum (API認證)
- MySQL (資料庫)
- Laravel Reverb (即時通訊)

### 資料庫設定
- **資料庫類型**: MySQL
- **資料庫名稱**: english-game-dev
- **主機**: 127.0.0.1
- **埠號**: 3306
- **使用者**: root
- **密碼**: (空)

### 前端技術棧
- Blade Templates
- Bootstrap 5
- jQuery
- WebSocket (即時通訊)

### 需要安裝的套件
- [ ] `laravel/reverb` (Laravel 官方 WebSocket 伺服器)
- [ ] `pusher/pusher-php-server` (Pusher 客戶端，用於前端連接)
- [ ] `predis/predis` (Redis客戶端，用於快取和佇列)
- [ ] `league/csv` (CSV 處理套件)
- [ ] MySQL 資料庫 (已透過 Laragon 安裝)

## 🔄 即時通訊功能 (Laravel Reverb)

### WebSocket 事件
- [ ] 玩家加入/離開房間
- [ ] 聊天訊息
- [ ] 遊戲開始/結束
- [ ] 題目更新
- [ ] 答案提交
- [ ] 計時器更新
- [ ] 遊戲結果

### 廣播頻道
- [ ] `room.{roomCode}` - 房間專用頻道
- [ ] `user.{userId}` - 個人通知頻道

### Laravel Reverb 設定
- [ ] 安裝 Laravel Reverb
- [ ] 設定 Reverb 伺服器
- [ ] 配置廣播驅動程式
- [ ] 設定前端 WebSocket 連接
- [ ] 建立事件類別
- [ ] 設定頻道認證

## 🛠️ 開發階段規劃

### 第一階段：基礎架構
- [ ] 設定 Laravel 專案
- [ ] 安裝必要套件
- [ ] 確認 MySQL 資料庫連接 (已設定)
- [ ] 建立資料庫結構 (Migration)
- [ ] 設定 Filament Admin
- [ ] 建立基礎 API 端點

### 第二階段：會員系統
- [x] 建立 Member Model 和 Migration
- [x] 更新 User Model（移除遊戲統計欄位）
- [x] 設定多認證系統（config/auth.php）
- [x] 建立會員管理介面
- [x] 更新所有關聯關係（Room, GameRecord, ChatMessage）
- [x] 實作會員註冊/登入
- [x] 設定權限系統
- [x] 建立個人資料頁面
- [x] 建立多認證控制器和路由
- [x] 建立 API 認證系統
- [x] 測試多認證系統功能

### 第三階段：題目管理
- [x] 建立分類管理
- [x] 建立題目管理介面
- [x] 建立 CSV 題目匯入功能
- [x] 建立題目匯入驗證
- [x] 建立題目匯入錯誤處理
- [x] 建立題目 API
- [x] 測試 CSV 匯入功能

### 第四階段：房間系統
- [ ] 實作房間創建/加入
- [ ] 建立房間管理介面
- [ ] 實作房間狀態管理
- [ ] 建立房間 API

### 第五階段：遊戲邏輯
- [ ] 實作題目隨機選擇
- [ ] 建立計時器系統
- [ ] 實作答案驗證
- [ ] 建立分數計算

### 第六階段：即時通訊 (Laravel Reverb)
- [ ] 安裝並設定 Laravel Reverb
- [ ] 建立 WebSocket 事件類別
- [ ] 實作即時聊天功能
- [ ] 實作遊戲狀態同步
- [ ] 設定頻道認證
- [ ] 建立前端 WebSocket 連接

### 第七階段：前端開發
- [ ] 建立 Blade 模板
- [ ] 實作 Bootstrap 5 介面
- [ ] 加入 jQuery 互動
- [ ] 實作響應式設計
- [ ] 整合 WebSocket 連接

### 第八階段：測試與優化
- [ ] 單元測試
- [ ] 整合測試
- [ ] 效能優化
- [ ] 安全性檢查

## 🔒 安全性考量

### WebSocket 安全
- [ ] 頻道認證
- [ ] 連接驗證
- [ ] 訊息驗證
- [ ] 防止濫用

## 📊 效能優化

### 即時通訊優化
- [ ] 連接池管理
- [ ] 訊息佇列
- [ ] 頻寬控制
- [ ] 錯誤處理
- [ ] Reverb 伺服器優化

## 📝 CSV 題目匯入功能

### CSV 檔案格式
```csv
category_slug,type,question,correct_answer,options,explanation,difficulty
daily-conversation,choice,"What's your name?","My name is John","['My name is John', 'I am fine', 'Thank you', 'Goodbye']","This is a common greeting question.",easy
travel-transport,fill,"I want to go to the ___ station.","airport","['airport', 'bus', 'train', 'subway', 'taxi']","Airport is the correct word for air travel.",medium
business-english,choice,"When is the meeting?","It's at 2 PM","['It's at 2 PM', 'I don't know', 'Maybe later', 'Not sure']","Asking about meeting time.",easy
```

### CSV 欄位說明
- **category_slug**: 分類代碼（必須與資料庫中的 slug 對應）
- **type**: 題目類型（choice/fill）
- **question**: 題目內容
- **correct_answer**: 正確答案
- **options**: 選項陣列（JSON 格式）
- **explanation**: 解釋說明
- **difficulty**: 難度等級（easy/medium/hard）

### 匯入功能規劃
- [ ] 建立 CSV 上傳介面
- [ ] 建立 CSV 格式驗證
- [ ] 建立資料驗證（分類存在性、格式正確性）
- [ ] 建立批次匯入功能
- [ ] 建立匯入進度顯示
- [ ] 建立匯入錯誤報告
- [ ] 建立匯入歷史記錄

### 匯入流程
1. 上傳 CSV 檔案
2. 驗證檔案格式
3. 驗證資料內容
4. 批次匯入資料庫
5. 顯示匯入結果
6. 生成錯誤報告（如有）

### 錯誤處理
- [ ] 分類不存在錯誤
- [ ] 資料格式錯誤
- [ ] 重複題目檢查
- [ ] 必填欄位驗證
- [ ] 選項格式驗證

## 🚀 部署考量

### 擴展性
- [ ] Reverb 叢集設定

---

*最後更新：2024年12月*
