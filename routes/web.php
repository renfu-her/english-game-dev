<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\MemberAuthController;
use App\Http\Controllers\Auth\AdminAuthController;

Route::get('/', [App\Http\Controllers\GameController::class, 'index'])->name('home');

// 測試路由
Route::get('/test', [App\Http\Controllers\TestController::class, 'index'])->name('test');
Route::get('/test/create-data', [App\Http\Controllers\TestController::class, 'createTestData'])->name('test.create-data');

// CSRF 測試路由
Route::post('/test-csrf', function () {
    return response()->json(['message' => 'CSRF 測試成功', 'timestamp' => now()]);
})->name('test.csrf');

// 遊戲路由
Route::prefix('game')->name('game.')->middleware('auth:member')->group(function () {
    Route::get('/lobby', [App\Http\Controllers\GameController::class, 'lobby'])->name('lobby');
    Route::post('/create-room', [App\Http\Controllers\GameController::class, 'createRoom'])->name('create-room');
    Route::post('/join-room/{room}', [App\Http\Controllers\GameController::class, 'joinRoom'])->name('join-room');
    Route::get('/room/{room}', [App\Http\Controllers\GameController::class, 'room'])->name('room');
    Route::post('/start-game/{room}', [App\Http\Controllers\GameController::class, 'startGame'])->name('start-game');
    Route::get('/play/{room}', [App\Http\Controllers\GameController::class, 'play'])->name('play');
    Route::post('/chat/{room}', [App\Http\Controllers\GameController::class, 'sendChatMessage'])->name('chat');
    Route::post('/leave-room/{room}', [App\Http\Controllers\GameController::class, 'leaveRoom'])->name('leave-room');
    Route::post('/toggle-ready/{room}', [App\Http\Controllers\GameController::class, 'toggleReadyStatus'])->name('toggle-ready');
    Route::post('/set-all-ready/{room}', [App\Http\Controllers\GameController::class, 'setAllPlayersReady'])->name('set-all-ready');
});

// 測試路由
Route::get('/test-auth', function () {
    return response()->json([
        'message' => '認證系統測試',
        'auth_guards' => [
            'web' => Auth::guard('web')->check() ? '已認證' : '未認證',
            'member' => Auth::guard('member')->check() ? '已認證' : '未認證',
            'admin' => Auth::guard('admin')->check() ? '已認證' : '未認證',
        ],
        'current_user' => [
            'web' => Auth::guard('web')->user(),
            'member' => Auth::guard('member')->user(),
            'admin' => Auth::guard('admin')->user(),
        ],
    ]);
});

// 會員認證路由
Route::prefix('member')->name('member.')->group(function () {
    Route::middleware('guest:member')->group(function () {
        Route::get('/login', [MemberAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [MemberAuthController::class, 'login']);
        Route::get('/register', [MemberAuthController::class, 'showRegistrationForm'])->name('register');
        Route::post('/register', [MemberAuthController::class, 'register']);
    });

    Route::middleware('auth:member')->group(function () {
        Route::get('/logout', [MemberAuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [MemberAuthController::class, 'dashboard'])->name('dashboard');
    });
});

// 管理員認證路由
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login']);
    });

    Route::middleware('auth:admin')->group(function () {
        Route::get('/logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [AdminAuthController::class, 'dashboard'])->name('dashboard');
    });
});
