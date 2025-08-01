<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Symfony\Component\HttpFoundation\Response;

class CustomVerifyCsrfToken extends VerifyCsrfToken
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // 會員認證路由
        'member/login',
        'member/logout',
        'member/register',
        
        // 管理員認證路由
        'admin/login',
        'admin/logout',
        
        // API 路由（如果需要）
        'api/*',
        
        // 更寬鬆的匹配模式
        '*/login',
        '*/logout',
        '*/register',
    ];
}
