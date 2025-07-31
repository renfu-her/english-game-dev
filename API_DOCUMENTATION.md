# 英文遊戲 API 文檔

## 📋 概述

本 API 提供英文學習遊戲平台的所有功能，包括會員管理、房間系統、遊戲邏輯等。

## 🔐 認證

### 認證方式
- 使用 Laravel Sanctum Token 認證
- 除了註冊和登入外，所有 API 都需要認證

### 認證流程
1. 會員註冊或登入取得 token
2. 在請求標頭中加入 `Authorization: Bearer {token}`
3. 使用 token 存取其他 API

## 📡 API 端點

### 公開 API（無需認證）

#### 取得公開遊戲記錄
```http
GET /api/public/game-records
```

**查詢參數：**
- `per_page`: 每頁數量（預設15，最大50）
- `category_id`: 分類 ID（可選）
- `difficulty`: 難度（easy/medium/hard，可選）
- `member_id`: 會員 ID（可選）

**回應：**
```json
{
    "success": true,
    "data": {
        "records": {
            "data": [
                {
                    "id": 1,
                    "room_id": 1,
                    "member_id": 1,
                    "question_id": 1,
                    "answer": "My name is John",
                    "is_correct": true,
                    "time_taken": 15,
                    "created_at": "2024-12-19T10:00:00.000000Z",
                    "member": {
                        "id": 1,
                        "name": "會員姓名",
                        "email": "member@example.com"
                    },
                    "room": {
                        "id": 1,
                        "name": "我的房間"
                    },
                    "question": {
                        "id": 1,
                        "question": "What's your name?",
                        "category": {
                            "id": 1,
                            "name": "日常生活"
                        }
                    }
                }
            ],
            "current_page": 1,
            "per_page": 15,
            "total": 100
        },
        "stats": {
            "total_records": 1000,
            "total_correct": 800,
            "total_members": 50,
            "total_rooms": 100
        }
    },
    "message": "公開遊戲記錄取得成功"
}
```

#### 取得公開遊戲統計
```http
GET /api/public/game-stats
```

**回應：**
```json
{
    "success": true,
    "data": {
        "today": {
            "total": 50,
            "correct": 40,
            "accuracy": 80.0
        },
        "week": {
            "total": 300,
            "correct": 240,
            "accuracy": 80.0
        },
        "month": {
            "total": 1200,
            "correct": 960,
            "accuracy": 80.0
        },
        "total": {
            "records": 5000,
            "correct": 4000,
            "accuracy": 80.0,
            "members": 100,
            "rooms": 200
        },
        "popular_categories": [
            {
                "category": {
                    "id": 1,
                    "name": "日常生活"
                },
                "count": 500
            }
        ]
    },
    "message": "遊戲統計取得成功"
}
```

### 認證相關 API

#### 會員註冊
```http
POST /api/member/register
```

**請求參數：**
```json
{
    "name": "會員姓名",
    "email": "member@example.com",
    "password": "密碼",
    "password_confirmation": "密碼確認"
}
```

**回應：**
```json
{
    "token": "1|abc123...",
    "user": {
        "id": 1,
        "name": "會員姓名",
        "email": "member@example.com"
    },
    "type": "member"
}
```

#### 會員登入
```http
POST /api/member/login
```

**請求參數：**
```json
{
    "email": "member@example.com",
    "password": "密碼"
}
```

**回應：**
```json
{
    "token": "1|abc123...",
    "user": {
        "id": 1,
        "name": "會員姓名",
        "email": "member@example.com"
    },
    "type": "member"
}
```

#### 登出
```http
POST /api/logout
```

**標頭：**
```
Authorization: Bearer {token}
```

**回應：**
```json
{
    "message": "已成功登出"
}
```

#### 取得當前用戶資料
```http
GET /api/me
```

**標頭：**
```
Authorization: Bearer {token}
```

**回應：**
```json
{
    "user": {
        "id": 1,
        "name": "會員姓名",
        "email": "member@example.com"
    },
    "type": "member"
}
```

### 個人資料管理 API

#### 取得個人資料
```http
GET /api/profile
```

**回應：**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "會員姓名",
        "email": "member@example.com",
        "avatar": "avatar.jpg",
        "bio": "個人簡介"
    },
    "message": "個人資料取得成功"
}
```

#### 更新個人資料
```http
PUT /api/profile
```

**請求參數：**
```json
{
    "name": "新姓名",
    "email": "newemail@example.com",
    "avatar": "new-avatar.jpg",
    "bio": "新的個人簡介"
}
```

#### 變更密碼
```http
POST /api/profile/change-password
```

**請求參數：**
```json
{
    "current_password": "當前密碼",
    "new_password": "新密碼",
    "new_password_confirmation": "新密碼確認"
}
```

#### 取得統計資料
```http
GET /api/profile/statistics
```

**回應：**
```json
{
    "success": true,
    "data": {
        "total_games": 10,
        "total_questions": 150,
        "correct_answers": 120,
        "accuracy": 80.0,
        "average_time": 25.5,
        "best_score": 120,
        "recent_games": [...]
    },
    "message": "統計資料取得成功"
}
```

#### 取得成就
```http
GET /api/profile/achievements
```

**回應：**
```json
{
    "success": true,
    "data": {
        "achievements": [
            {
                "name": "答題大師",
                "description": "已答過100題",
                "icon": "🎯",
                "unlocked": true
            }
        ],
        "total_achievements": 1
    },
    "message": "成就資料取得成功"
}
```

### 分類管理 API

#### 取得分類列表
```http
GET /api/categories
```

**回應：**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "日常生活",
            "slug": "daily-conversation",
            "description": "日常對話相關題目",
            "is_active": true
        }
    ],
    "message": "分類列表取得成功"
}
```

#### 取得單一分類
```http
GET /api/categories/{id}
```

#### 建立分類
```http
POST /api/categories
```

**請求參數：**
```json
{
    "name": "新分類",
    "description": "分類描述",
    "is_active": true
}
```

#### 更新分類
```http
PUT /api/categories/{id}
```

#### 刪除分類
```http
DELETE /api/categories/{id}
```

### 問題管理 API

#### 取得問題列表
```http
GET /api/questions?category_id=1&difficulty=easy&per_page=15
```

**查詢參數：**
- `category_id`: 分類 ID（可選）
- `difficulty`: 難度（easy/medium/hard，可選）
- `per_page`: 每頁數量（預設15）

#### 取得隨機問題
```http
GET /api/questions/random?category_id=1&difficulty=easy&count=10
```

**查詢參數：**
- `category_id`: 分類 ID（可選）
- `difficulty`: 難度（可選）
- `count`: 問題數量（預設10，最大50）

#### 取得單一問題
```http
GET /api/questions/{id}
```

#### 建立問題
```http
POST /api/questions
```

**請求參數：**
```json
{
    "category_id": 1,
    "question": "What's your name?",
    "correct_answer": "My name is John",
    "options": ["My name is John", "I am fine", "Thank you", "Goodbye"],
    "difficulty": "easy",
    "explanation": "This is a common greeting question.",
    "is_active": true
}
```

#### 更新問題
```http
PUT /api/questions/{id}
```

#### 刪除問題
```http
DELETE /api/questions/{id}
```

### 房間管理 API

#### 取得房間列表
```http
GET /api/rooms?status=waiting&category_id=1&per_page=15
```

**查詢參數：**
- `status`: 房間狀態（waiting/playing/finished，可選）
- `category_id`: 分類 ID（可選）
- `per_page`: 每頁數量（預設15）

#### 建立房間
```http
POST /api/rooms
```

**請求參數：**
```json
{
    "name": "我的房間",
    "category_id": 1,
    "max_players": 4,
    "question_count": 10,
    "time_limit": 30,
    "is_private": false,
    "password": "1234"
}
```

#### 取得房間詳情
```http
GET /api/rooms/{id}
```

#### 更新房間
```http
PUT /api/rooms/{id}
```

**注意：** 只有房主可以更新房間設定

#### 刪除房間
```http
DELETE /api/rooms/{id}
```

**注意：** 只有房主可以刪除房間

#### 加入房間
```http
POST /api/rooms/{id}/join
```

**請求參數：**
```json
{
    "password": "1234"
}
```

#### 離開房間
```http
POST /api/rooms/{id}/leave
```

#### 準備/取消準備
```http
POST /api/rooms/{id}/toggle-ready
```

#### 開始遊戲
```http
POST /api/rooms/{id}/start-game
```

**注意：** 只有房主可以開始遊戲

### 遊戲邏輯 API

#### 取得遊戲問題
```http
GET /api/games/rooms/{id}/questions
```

**注意：** 只返回問題和選項，不包含正確答案

#### 提交答案
```http
POST /api/games/rooms/{id}/submit-answer
```

**請求參數：**
```json
{
    "question_id": 1,
    "answer": "My name is John",
    "time_taken": 15
}
```

**回應：**
```json
{
    "success": true,
    "data": {
        "is_correct": true,
        "correct_answer": "My name is John",
        "explanation": "This is a common greeting question."
    },
    "message": "答案正確！"
}
```

#### 結束遊戲
```http
POST /api/games/rooms/{id}/end-game
```

#### 取得遊戲結果
```http
GET /api/games/rooms/{id}/results
```

#### 取得我的遊戲記錄
```http
GET /api/games/my-records?per_page=15
```

#### 取得排行榜
```http
GET /api/games/leaderboard?per_page=20
```

## 🔧 錯誤處理

### 錯誤回應格式
```json
{
    "success": false,
    "message": "錯誤訊息",
    "errors": {
        "field": ["欄位錯誤訊息"]
    }
}
```

### 常見 HTTP 狀態碼
- `200`: 成功
- `201`: 建立成功
- `400`: 請求錯誤
- `401`: 未認證
- `403`: 權限不足
- `404`: 資源不存在
- `422`: 驗證錯誤
- `500`: 伺服器錯誤

## 📝 使用範例

### JavaScript 範例

```javascript
// 登入
const loginResponse = await fetch('/api/member/login', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        email: 'member@example.com',
        password: 'password'
    })
});

const loginData = await loginResponse.json();
const token = loginData.token;

// 使用 token 存取 API
const profileResponse = await fetch('/api/profile', {
    headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
    }
});

const profileData = await profileResponse.json();
```

### PHP 範例

```php
// 登入
$response = Http::post('/api/member/login', [
    'email' => 'member@example.com',
    'password' => 'password'
]);

$token = $response->json('token');

// 使用 token 存取 API
$profile = Http::withToken($token)->get('/api/profile');
```

## 🔒 安全性注意事項

1. **Token 安全**：妥善保管 token，不要在客戶端程式碼中硬編碼
2. **HTTPS**：生產環境請使用 HTTPS
3. **輸入驗證**：所有輸入都會進行驗證，請確保資料格式正確
4. **權限檢查**：某些操作需要特定權限，請確認用戶身份

## 📞 支援

如有問題或建議，請聯繫開發團隊。 