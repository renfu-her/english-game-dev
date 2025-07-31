<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ApiAuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\GameController;
use App\Http\Controllers\Api\ProfileController;

// 公開的 API 認證路由
Route::post('/member/login', [ApiAuthController::class, 'memberLogin']);
Route::post('/member/register', [ApiAuthController::class, 'memberRegister']);
Route::post('/admin/login', [ApiAuthController::class, 'adminLogin']);

// 公開的遊戲記錄 API（首頁用）
Route::prefix('public')->group(function () {
    Route::get('/game-records', [GameController::class, 'publicGameRecords']);
    Route::get('/game-stats', [GameController::class, 'publicGameStats']);
});

// 需要認證的 API 路由
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [ApiAuthController::class, 'logout']);
    Route::get('/me', [ApiAuthController::class, 'me']);
    
    // 會員專用 API
    Route::middleware('auth:member')->group(function () {
        // 個人資料管理
        Route::prefix('profile')->group(function () {
            Route::get('/', [ProfileController::class, 'show']);
            Route::put('/', [ProfileController::class, 'update']);
            Route::post('/change-password', [ProfileController::class, 'changePassword']);
            Route::get('/statistics', [ProfileController::class, 'statistics']);
            Route::get('/achievements', [ProfileController::class, 'achievements']);
            Route::delete('/', [ProfileController::class, 'deleteAccount']);
        });

        // 分類管理
        Route::apiResource('categories', CategoryController::class);

        // 問題管理
        Route::prefix('questions')->group(function () {
            Route::get('/', [QuestionController::class, 'index']);
            Route::get('/random', [QuestionController::class, 'random']);
            Route::get('/{question}', [QuestionController::class, 'show']);
            Route::post('/', [QuestionController::class, 'store']);
            Route::put('/{question}', [QuestionController::class, 'update']);
            Route::delete('/{question}', [QuestionController::class, 'destroy']);
        });

        // 房間管理
        Route::prefix('rooms')->group(function () {
            Route::get('/', [RoomController::class, 'index']);
            Route::post('/', [RoomController::class, 'store']);
            Route::get('/{room}', [RoomController::class, 'show']);
            Route::put('/{room}', [RoomController::class, 'update']);
            Route::delete('/{room}', [RoomController::class, 'destroy']);
            Route::post('/{room}/join', [RoomController::class, 'join']);
            Route::post('/{room}/leave', [RoomController::class, 'leave']);
            Route::post('/{room}/toggle-ready', [RoomController::class, 'toggleReady']);
            Route::post('/{room}/start-game', [RoomController::class, 'startGame']);
        });

        // 遊戲管理
        Route::prefix('games')->group(function () {
            Route::get('/rooms/{room}/questions', [GameController::class, 'getQuestions']);
            Route::post('/rooms/{room}/submit-answer', [GameController::class, 'submitAnswer']);
            Route::post('/rooms/{room}/end-game', [GameController::class, 'endGame']);
            Route::get('/rooms/{room}/results', [GameController::class, 'getGameResults']);
            Route::get('/my-records', [GameController::class, 'myGameRecords']);
            Route::get('/leaderboard', [GameController::class, 'leaderboard']);
        });
    });
    
    // 管理員專用 API
    Route::middleware('auth:admin')->group(function () {
        Route::get('/admin/profile', function (Request $request) {
            return response()->json(['user' => $request->user()]);
        });
    });
});
