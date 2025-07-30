<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MultiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $guard = null): Response
    {
        // 如果沒有指定 guard，嘗試自動檢測
        if (!$guard) {
            // 檢查是否為管理員路由
            if ($request->is('admin/*') || $request->is('backend/*')) {
                $guard = 'admin';
            } else {
                $guard = 'member';
            }
        }

        // 檢查是否已認證
        if (!Auth::guard($guard)->check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            // 根據 guard 重定向到對應的登入頁面
            if ($guard === 'admin') {
                return redirect()->route('admin.login');
            } else {
                return redirect()->route('member.login');
            }
        }

        return $next($request);
    }
}
