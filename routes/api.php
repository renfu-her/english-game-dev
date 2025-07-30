<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ApiAuthController;

// 公開的 API 認證路由
Route::post('/member/login', [ApiAuthController::class, 'memberLogin']);
Route::post('/member/register', [ApiAuthController::class, 'memberRegister']);
Route::post('/admin/login', [ApiAuthController::class, 'adminLogin']);

// 需要認證的 API 路由
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [ApiAuthController::class, 'logout']);
    Route::get('/me', [ApiAuthController::class, 'me']);
    
    // 會員專用 API
    Route::middleware('auth:member')->group(function () {
        Route::get('/member/profile', function (Request $request) {
            return response()->json(['user' => $request->user()]);
        });
    });
    
    // 管理員專用 API
    Route::middleware('auth:admin')->group(function () {
        Route::get('/admin/profile', function (Request $request) {
            return response()->json(['user' => $request->user()]);
        });
    });
});
