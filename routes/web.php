<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\MemberAuthController;
use App\Http\Controllers\Auth\AdminAuthController;

Route::get('/', function () {
    return view('welcome');
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
        Route::post('/logout', [MemberAuthController::class, 'logout'])->name('logout');
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
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [AdminAuthController::class, 'dashboard'])->name('dashboard');
    });
});
