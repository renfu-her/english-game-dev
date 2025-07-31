# 英文遊戲專案 TODO

## 📋 專案概述
建立一個多人連線的英文學習遊戲平台，支援即時互動、題目練習和競賽模式。

## 📅 開發日誌

### 2024-12-19
- **上午**：
  - ✅ 修正 Filament Resource 的 Create/Edit 頁面重導向功能
  - ✅ 優化 RoomResource 的房主選擇功能（搜尋、預載入、多欄位顯示）
  - ✅ 建立完整的房間遊戲設定系統
  - ✅ 實作房間代碼自動生成功能
  - ✅ 優化房間表格顯示（設定摘要、篩選器）
- **下午**：
  - ✅ 更新 TODO.md 記錄所有優化工作
  - ✅ 建立開發進度追蹤系統
  - ✅ 記錄技術實現細節
- **晚上**：
  - ✅ 建立完整的 API 控制器系統
  - ✅ 實作會員認證 API（登入、註冊、登出）
  - ✅ 建立分類管理 API
  - ✅ 建立問題管理 API（包含隨機問題功能）
  - ✅ 建立房間管理 API（創建、加入、離開、準備、開始遊戲）
  - ✅ 建立遊戲邏輯 API（取得問題、提交答案、結束遊戲、結果統計）
  - ✅ 建立個人資料管理 API（資料更新、密碼變更、統計資料、成就系統）
  - ✅ 建立排行榜 API
  - ✅ 修正所有模型關聯（Member、Room、GameRecord、RoomPlayer）
  - ✅ 更新 API 路由配置
  - ✅ 設定 Member Guard 認證
  - ✅ 建立 migration 將 room_players 表格的 user_id 改為 member_id
  - ✅ 建立公開遊戲記錄 API（首頁用，無需認證）
  - ✅ 建立公開遊戲統計 API（首頁用，無需認證）

### 2024-12-18
- **上午**：
  - ✅ 建立 CSV 題目匯入功能
  - ✅ 實作 QuestionImportService 服務類別
  - ✅ 建立 20 個測試題目
- **下午**：
  - ✅ 測試 CSV 匯入功能
  - ✅ 修正 JSON 格式問題
  - ✅ 驗證匯入結果

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

### 第三階段補充：Filament Resource 優化
- [x] 修正所有 Resource 的 Create/Edit 頁面重導向功能
- [x] 優化 QuestionResource 的 CSV 匯入介面
- [x] 優化 CategoryResource 的導航和表單
- [x] 優化 UserResource 的管理員管理功能
- [x] 優化 MemberResource 的會員管理功能
- [x] 優化 RoomResource 的房主選擇和遊戲設定功能

### 第四階段：房間系統
- [x] 建立房間管理介面
- [x] 優化房主選擇功能（搜尋、預載入、多欄位顯示）
- [x] 建立完整的遊戲設定系統
- [x] 實作房間代碼自動生成功能
- [x] 優化房間表格顯示（設定摘要、篩選器）
- [x] 建立房間建立/編輯頁面重導向功能
- [x] 實作房間創建/加入 API
- [x] 實作房間狀態管理 API

### 第五階段：遊戲邏輯
- [x] 實作題目隨機選擇
- [x] 建立計時器系統
- [x] 實作答案驗證
- [x] 建立分數計算
- [x] 實作遊戲記錄系統
- [x] 建立排行榜功能

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

## 📋 Filament Resource 優化記錄

### 已完成的優化工作

#### 2024-12-19 更新記錄
- ✅ **TODO.md 更新** - 記錄 Filament Resource 優化工作
- ✅ **開發進度追蹤** - 更新第四階段房間系統完成項目
- ✅ **技術文檔記錄** - 詳細記錄所有優化細節和技術實現
- ✅ **專案管理** - 建立完整的開發進度追蹤系統

#### 1. 統一重導向功能
- ✅ 所有 Resource 的 Create/Edit 頁面都加入 `getRedirectUrl()` 方法
- ✅ 建立/編輯記錄後自動回到列表頁面
- ✅ 提升使用者體驗，避免停留在空白頁面

#### 2. QuestionResource 優化
- ✅ 建立 CSV 匯入專用頁面 (`QuestionImport`)
- ✅ 實作 `QuestionImportService` 服務類別
- ✅ 完整的 CSV 格式驗證和錯誤處理
- ✅ 建立命令列匯入工具 (`ImportQuestions`)
- ✅ 成功匯入 20 個測試題目

#### 3. CategoryResource 優化
- ✅ 正確設定導航群組和標籤
- ✅ 優化表單欄位和驗證
- ✅ 建立分類與題目的關聯顯示

#### 4. UserResource 優化
- ✅ 移除遊戲統計欄位（移至 Member）
- ✅ 專注於管理員帳號管理
- ✅ 優化表單和表格顯示

#### 5. MemberResource 優化
- ✅ 新增會員管理功能
- ✅ 包含遊戲統計欄位（總遊戲數、正確答案、總答案）
- ✅ 實作準確率計算 (`getAccuracyAttribute()`)
- ✅ 優化頭像上傳功能

#### 6. RoomResource 優化
- ✅ **房主選擇功能**：
  - 預載入所有會員選項
  - 多欄位搜尋（姓名、電子郵件）
  - 顯示格式：`姓名 (電子郵件)`
  - 自訂搜尋提示文字
- ✅ **遊戲設定系統**：
  - 題目分類多選（5個分類）
  - 題目數量設定（5-50題）
  - 難度等級選擇（簡單/中等/困難/混合）
  - 答題時間限制（10-120秒）
  - 遊戲選項開關（跳過、解釋、自動開始）
- ✅ **房間代碼功能**：
  - 自動生成 6 位隨機代碼
  - 手動生成按鈕
  - 唯一性驗證
- ✅ **表格顯示優化**：
  - 設定摘要顯示（`10題 | 30秒 | 混合`）
  - 房主篩選器
  - 房主資訊描述（電子郵件）

### 技術細節

#### 表單組件使用
- `Forms\Components\Section` - 分組顯示
- `Forms\Components\Select` - 下拉選擇（支援搜尋、預載入）
- `Forms\Components\TextInput` - 文字輸入（支援後綴動作）
- `Forms\Components\Toggle` - 開關選項
- `Forms\Components\Repeater` - 重複項目（選項管理）

#### 表格組件使用
- `Tables\Columns\TextColumn` - 文字欄位（支援格式化、搜尋）
- `Tables\Columns\BadgeColumn` - 標籤欄位（支援顏色、格式化）
- `Tables\Columns\IconColumn` - 圖示欄位（布林值顯示）
- `Tables\Filters\SelectFilter` - 下拉篩選器

#### 資料處理
- `mutateFormDataBeforeCreate()` - 建立前資料處理
- `getRedirectUrl()` - 自訂重導向路徑
- 關聯查詢和預載入
- JSON 欄位處理

## 📝 開發規範

### 每次動作記錄規範
1. **即時記錄** - 每次完成功能後立即更新 TODO.md
2. **詳細描述** - 記錄具體的技術實現和功能特點
3. **進度追蹤** - 更新對應階段的完成狀態
4. **技術文檔** - 記錄重要的技術細節供未來參考
5. **開發日誌** - 按日期記錄每日開發進度

### 記錄格式
- ✅ **功能名稱** - 簡要描述
- 📅 **日期時間** - 記錄完成時間
- 🔧 **技術細節** - 記錄實現方式
- 📋 **進度更新** - 更新對應階段狀態

---

*最後更新：2024年12月19日*
