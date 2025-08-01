# TODO 清單

## 開發日誌

### 2025-07-31 - 儀表板視圖建立
- ✅ 建立管理員儀表板視圖 (`resources/views/admin/dashboard.blade.php`)
  - 使用 Bootstrap 5 和 Bootstrap Icons
  - 包含統計卡片（會員數、題目數、活躍房間、遊戲記錄）
  - 快速操作按鈕（管理會員、題目、房間、遊戲記錄）
  - 最近遊戲記錄和活躍房間列表
  - 響應式設計和美觀的漸層背景
  - 自動刷新功能（每30秒）

- ✅ 建立會員儀表板視圖 (`resources/views/member/dashboard.blade.php`)
  - 個人統計卡片（總遊戲數、正確答案、總答題數、正確率）
  - 快速開始按鈕（創建房間、加入房間、房間列表）
  - 個人遊戲記錄和成就統計
  - 頭像顯示和個人資料
  - 自動刷新功能（每60秒）

### 2025-08-01 - 房間代碼生成功能修正
- ✅ 修正 `createRoom()` 方法中的房間代碼生成問題
  - 加入自動生成唯一房間代碼功能
  - 使用 `strtoupper(substr(md5(uniqid()), 0, 6))` 生成6位大寫字母數字組合
  - 確保房間代碼唯一性（檢查資料庫中是否已存在）
  - 解決 `SQLSTATE[HY000]: General error: 1364 Field 'code' doesn't have a default value` 錯誤

### 2025-07-31 - 玩家準備狀態 WebSocket 系統建立
- ✅ 建立 `PlayerReadyStatusChanged` 事件
  - 處理玩家準備狀態變更的即時廣播
  - 包含玩家信息、準備狀態和時間戳

- ✅ 更新 GameController 加入準備狀態功能
  - 新增 `toggleReadyStatus()` 方法 - 切換玩家準備狀態
  - 新增 `setAllPlayersReady()` 方法 - 房主可設定所有玩家準備
  - 修正 `joinRoom()` 方法 - 新玩家預設為準備狀態

- ✅ 新增路由端點
  - `POST /game/toggle-ready/{room}` - 切換準備狀態
  - `POST /game/set-all-ready/{room}` - 設定所有玩家準備

- ✅ 更新房間視圖 (`room.blade.php`) 加入準備功能
  - 為每個玩家添加準備狀態指示器和切換按鈕
  - 加入 WebSocket 監聽 `player.ready_status_changed` 事件
  - 即時更新準備狀態指示器和按鈕文字
  - 新玩家加入時自動設為準備狀態並廣播

### 2025-07-31 - Laravel Reverb 即時通訊系統建立
- ✅ 建立廣播頻道系統 (`routes/channels.php`)
  - 會員私人頻道：`App.Models.Member.{id}`
  - 房間頻道：`room.{roomId}` - 只有房間內的玩家可以訂閱
  - 遊戲大廳頻道：`game.lobby` - 所有已登入會員可訂閱
  - 遊戲進行中頻道：`game.{roomId}` - 只有遊戲中的玩家可訂閱

- ✅ 建立遊戲事件系統
  - `PlayerJoinedRoom` - 玩家加入房間事件
  - `PlayerLeftRoom` - 玩家離開房間事件
  - `GameStarted` - 遊戲開始事件
  - `ChatMessage` - 聊天訊息事件
  - `QuestionDisplayed` - 題目顯示事件

- ✅ 更新 GameController 整合事件系統
  - 加入房間時廣播 `PlayerJoinedRoom` 事件
  - 開始遊戲時廣播 `GameStarted` 事件
  - 新增 `sendChatMessage()` 方法處理聊天訊息
  - 新增 `leaveRoom()` 方法處理玩家離開房間

- ✅ 更新房間視圖 (`room.blade.php`) 加入即時功能
  - 整合 Pusher.js 客戶端
  - 監聽玩家加入/離開事件
  - 監聽遊戲開始事件，自動跳轉到遊戲頁面
  - 即時聊天功能
  - 離開房間按鈕

- ✅ 建立遊戲進行中視圖 (`play.blade.php`)
  - 題目顯示區域
  - 計時器功能
  - 答案選項動態生成
  - 遊戲進度條
  - 即時聊天室
  - 玩家列表和分數顯示

- ✅ 新增路由端點
  - `POST /game/chat/{room}` - 發送聊天訊息
  - `POST /game/leave-room/{room}` - 離開房間

### 2025-07-31 - 遊戲房間錯誤修正
- ✅ 修正 `room.blade.php` 中的 null 錯誤
  - 在視圖中加入 null 檢查：`{{ $room->host->name ?? '未知' }}`
  - 在視圖中加入 null 檢查：`{{ $room->category->name ?? '未分類' }}`
  - 在視圖中加入 null 檢查：`{{ $player->member->name ?? '未知玩家' }}`
  - 避免 "Attempt to read property 'name' on null" 錯誤

### 2025-07-31 - 遊戲大廳錯誤修正
- ✅ 修正 `lobby.blade.php` 中的 null 錯誤
  - 在視圖中加入 null 檢查：`{{ $room->host->name ?? '未知' }}`
  - 在視圖中加入 null 檢查：`{{ $room->category->name ?? '未分類' }}`
  - 避免 "Attempt to read property 'name' on null" 錯誤

- ✅ 修正 GameController 中的關聯載入
  - 在 `lobby()` 方法中加入 `category` 關聯載入
  - 確保房間數據包含完整的關聯信息

- ✅ 修正 RoomSeeder 中的數據設定
  - 加入 `category_id` 欄位設定
  - 移除舊的 `settings` 欄位，使用新的欄位結構
  - 確保房間數據包含正確的分類關聯

### 2025-07-31 - CSRF 驗證配置修正
- ✅ 完全移除 CSRF 驗證（開發環境）
  - 在 `bootstrap/app.php` 中移除 VerifyCsrfToken 中間件
  - 解決 419 錯誤問題

- ✅ 簡化登出功能
  - 將登出路由從 POST 改為 GET 方法
  - 移除複雜的表單提交和 CSRF token
  - 直接使用連結進行登出
  - 更新會員和管理員儀表板視圖
  - 修正 `app.blade.php` 中的登出下拉選單

- ✅ 測試路由
  - 新增 CSRF 測試路由 `/test-csrf`
  - 驗證 CSRF 配置是否生效

### 2025-07-31 - GameRecord Resource 建立
- ✅ 建立 GameRecordResource 完整功能
  - 導航設定：遊戲管理 > 遊戲記錄
  - 表單欄位：房間、玩家、題目、答案、正確性、答題時間
  - 表格欄位：房間、玩家、題目、答案、正確性、答題時間、建立時間
  - 篩選功能：房間、玩家、正確性、答題時間範圍
  - 查看、編輯、刪除功能
  - 重導向設定（提交後回到列表頁）

- ✅ 建立相關 Factory 和 Seeder
  - GameRecordFactory：生成測試數據
  - RoomFactory：生成房間測試數據
  - GameRecordSeeder：建立50個遊戲記錄
  - RoomSeeder：建立5個測試房間
  - 更新 DatabaseSeeder 包含所有 Seeder

- ✅ 修正資料庫外鍵約束
  - 建立 migration 修正 rooms.host_id 指向 members 表
  - 修正 GameRecord 和 Room 模型加入 HasFactory trait
  - 解決 seeder 執行時的錯誤

## 已完成的工作

### 2025-01-27 - 前端開發完成
- ✅ 建立基礎布局文件 (`resources/views/layouts/app.blade.php`)
  - 整合 Bootstrap 5 CSS 和 JS
  - 整合 jQuery
  - 建立響應式導航欄
  - 添加自定義 CSS 樣式
  - 實現用戶認證狀態顯示

- ✅ 建立首頁視圖 (`resources/views/game/index.blade.php`)
  - 顯示最新遊戲記錄
  - 遊戲說明和特色介紹
  - 遊戲統計數據
  - 登入/註冊按鈕或開始遊戲按鈕

- ✅ 建立會員認證視圖
  - 登入頁面 (`resources/views/auth/member/login.blade.php`)
  - 註冊頁面 (`resources/views/auth/member/register.blade.php`)
  - 表單驗證和錯誤顯示
  - 響應式設計

- ✅ 建立遊戲大廳視圖 (`resources/views/game/lobby.blade.php`)
  - 房間列表顯示
  - 建立房間功能 (Modal 表單)
  - 遊戲設定選項 (難度、題目數量、時間限制等)
  - 加入房間功能
  - 遊戲說明

- ✅ 建立房間視圖 (`resources/views/game/room.blade.php`)
  - 房間設定顯示
  - 玩家列表和準備狀態
  - 房主控制功能 (開始遊戲)
  - 聊天室功能
  - 房間資訊

- ✅ 建立遊戲進行中視圖 (`resources/views/game/play.blade.php`)
  - 題目顯示和選項
  - 計時器功能
  - 答題處理和結果顯示
  - 排行榜顯示
  - 遊戲統計
  - 遊戲聊天
  - 跳過題目功能
  - 題目解釋顯示
  - 遊戲結束 Modal

- ✅ 建立遊戲控制器 (`app/Http/Controllers/GameController.php`)
  - 首頁顯示遊戲記錄
  - 遊戲大廳功能
  - 建立房間
  - 加入房間
  - 房間管理
  - 開始遊戲

- ✅ 更新路由配置 (`routes/web.php`)
  - 首頁路由
  - 遊戲相關路由 (大廳、房間、遊戲進行)
  - 認證中間件保護

## 技術特色

### 前端技術
- **Bootstrap 5**: 響應式 UI 框架
- **jQuery**: JavaScript 庫，用於 DOM 操作和事件處理
- **Bootstrap Icons**: 圖標庫
- **Blade 模板**: Laravel 模板引擎

### 功能特色
- **響應式設計**: 支援桌面和移動設備
- **即時聊天**: 房間內和遊戲中的聊天功能
- **遊戲進度追蹤**: 題目進度和計時器
- **排行榜系統**: 即時顯示玩家得分
- **遊戲設定**: 靈活的遊戲參數配置
- **用戶體驗**: 直觀的界面和流暢的交互

### 遊戲機制
- **多種難度**: 簡單、中等、困難
- **可配置選項**: 題目數量、答題時間、跳過題目、顯示解釋
- **即時反饋**: 答題結果和解釋
- **統計追蹤**: 正確/錯誤答案統計

## 下一步工作

### 後端整合
- [ ] 連接真實的題目數據庫
- [ ] 實現 WebSocket 即時通訊
- [ ] 完善遊戲邏輯和計分系統
- [ ] 添加遊戲記錄保存功能

### 已完成的工作

#### 2025-01-27 - API 認證優化
- ✅ 優化 `ApiAuthController` 的登入錯誤處理
  - 區分帳號不存在和密碼錯誤的情況
  - 帳號不存在時回覆「帳號沒有註冊」
  - 密碼錯誤時回覆「帳號或密碼輸入不正確」
  - 同時優化會員和管理員登入的錯誤處理邏輯

### 功能增強
- [ ] 添加音效和動畫效果
- [ ] 實現房間密碼保護
- [ ] 添加遊戲歷史記錄查詢
- [ ] 實現玩家個人資料頁面

### 測試和優化
- [ ] 前端功能測試
- [ ] 響應式設計測試
- [ ] 性能優化
- [ ] 瀏覽器兼容性測試
