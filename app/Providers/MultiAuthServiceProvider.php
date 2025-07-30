<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class MultiAuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // 定義管理員權限
        Gate::define('admin', function ($user) {
            return $user instanceof \App\Models\User;
        });

        // 定義會員權限
        Gate::define('member', function ($user) {
            return $user instanceof \App\Models\Member;
        });

        // 設定認證回調
        Auth::viaRequest('member-token', function ($request) {
            return \App\Models\Member::where('id', $request->user()->id)->first();
        });

        Auth::viaRequest('admin-token', function ($request) {
            return \App\Models\User::where('id', $request->user()->id)->first();
        });
    }
}
